<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Function 1: Calculate patient age from date of birth
        DB::unprepared('
            CREATE FUNCTION IF NOT EXISTS calculate_age(dob DATE)
            RETURNS INT
            DETERMINISTIC
            READS SQL DATA
            BEGIN
                DECLARE age INT;
                SET age = YEAR(CURDATE()) - YEAR(dob);
                IF MONTH(CURDATE()) < MONTH(dob) OR (MONTH(CURDATE()) = MONTH(dob) AND DAY(CURDATE()) < DAY(dob)) THEN
                    SET age = age - 1;
                END IF;
                RETURN age;
            END
        ');

        // Function 2: Get appointment status statistics for a date range
        DB::unprepared('
            CREATE FUNCTION IF NOT EXISTS get_appointment_count_by_status(
                start_date DATETIME,
                end_date DATETIME,
                status_name VARCHAR(50)
            )
            RETURNS INT
            READS SQL DATA
            BEGIN
                DECLARE count INT;
                SELECT COUNT(*) INTO count
                FROM appointments
                WHERE created_at BETWEEN start_date AND end_date
                AND status = status_name
                AND deleted_at IS NULL;
                RETURN COALESCE(count, 0);
            END
        ');

        // Function 3: Calculate total revenue for a date range
        DB::unprepared('
            CREATE FUNCTION IF NOT EXISTS calculate_total_revenue(
                start_date DATE,
                end_date DATE
            )
            RETURNS DECIMAL(15, 2)
            READS SQL DATA
            BEGIN
                DECLARE total DECIMAL(15, 2);
                SELECT SUM(amount) INTO total
                FROM transactions
                WHERE DATE(created_at) BETWEEN start_date AND end_date
                AND status = "completed"
                AND deleted_at IS NULL;
                RETURN COALESCE(total, 0);
            END
        ');

        // Function 4: Get doctor utilization (hours booked)
        DB::unprepared('
            CREATE FUNCTION IF NOT EXISTS get_doctor_utilization_hours(
                doctor_id BIGINT,
                start_date DATE,
                end_date DATE
            )
            RETURNS DECIMAL(10, 2)
            READS SQL DATA
            BEGIN
                DECLARE hours DECIMAL(10, 2);
                SELECT SUM(TIMESTAMPDIFF(HOUR, start_time, end_time)) INTO hours
                FROM appointments
                WHERE doctor_id = doctor_id
                AND DATE(start_time) BETWEEN start_date AND end_date
                AND status NOT IN ("cancelled", "no_show")
                AND deleted_at IS NULL;
                RETURN COALESCE(hours, 0);
            END
        ');

        // Trigger 1: Auto-update inventory status based on quantity
        DB::unprepared('
            CREATE TRIGGER IF NOT EXISTS update_inventory_status_on_quantity_change
            AFTER UPDATE ON inventories
            FOR EACH ROW
            BEGIN
                DECLARE new_status VARCHAR(50);
                IF NEW.quantity = 0 THEN
                    SET new_status = "out_of_stock";
                ELSEIF NEW.quantity <= NEW.reorder_level THEN
                    SET new_status = "low_stock";
                ELSE
                    SET new_status = "in_stock";
                END IF;
                
                IF NEW.expiry_date IS NOT NULL AND NEW.expiry_date < CURDATE() THEN
                    SET new_status = "expired";
                END IF;
                
                IF NEW.status != new_status THEN
                    UPDATE inventories SET status = new_status WHERE id = NEW.id;
                END IF;
            END
        ');

        // Trigger 2: Insert trigger for inventory (set initial status)
        DB::unprepared('
            CREATE TRIGGER IF NOT EXISTS set_initial_inventory_status
            BEFORE INSERT ON inventories
            FOR EACH ROW
            BEGIN
                IF NEW.quantity = 0 THEN
                    SET NEW.status = "out_of_stock";
                ELSEIF NEW.quantity <= NEW.reorder_level THEN
                    SET NEW.status = "low_stock";
                ELSE
                    SET NEW.status = "in_stock";
                END IF;
            END
        ');

        // Trigger 3: Auto-set completed_at when appointment is marked as completed
        DB::unprepared('
            CREATE TRIGGER IF NOT EXISTS set_appointment_completed_at
            BEFORE UPDATE ON appointments
            FOR EACH ROW
            BEGIN
                IF NEW.status = "completed" AND OLD.status != "completed" AND NEW.completed_at IS NULL THEN
                    SET NEW.completed_at = NOW();
                END IF;
            END
        ');

        // Trigger 4: Prevent double-booking of doctors and rooms
        DB::unprepared('
            CREATE TRIGGER IF NOT EXISTS prevent_doctor_double_booking
            BEFORE INSERT ON appointments
            FOR EACH ROW
            BEGIN
                DECLARE conflict_count INT;
                SELECT COUNT(*) INTO conflict_count
                FROM appointments
                WHERE doctor_id = NEW.doctor_id
                AND (
                    (NEW.start_time < end_time AND NEW.end_time > start_time)
                )
                AND status NOT IN ("cancelled", "no_show")
                AND deleted_at IS NULL;
                
                IF conflict_count > 0 THEN
                    SIGNAL SQLSTATE "45000"
                    SET MESSAGE_TEXT = "Doctor is already booked during this time slot";
                END IF;
            END
        ');

        // Trigger 5: Prevent double-booking of rooms
        DB::unprepared('
            CREATE TRIGGER IF NOT EXISTS prevent_room_double_booking
            BEFORE INSERT ON appointments
            FOR EACH ROW
            BEGIN
                DECLARE conflict_count INT;
                SELECT COUNT(*) INTO conflict_count
                FROM appointments
                WHERE room_id = NEW.room_id
                AND (
                    (NEW.start_time < end_time AND NEW.end_time > start_time)
                )
                AND status NOT IN ("cancelled", "no_show")
                AND deleted_at IS NULL;
                
                IF conflict_count > 0 THEN
                    SIGNAL SQLSTATE "45000"
                    SET MESSAGE_TEXT = "Room is already booked during this time slot";
                END IF;
            END
        ');

        // Trigger 6: Auto-create transaction record when appointment is completed with fees
        DB::unprepared('
            CREATE TRIGGER IF NOT EXISTS create_transaction_on_appointment_complete
            AFTER UPDATE ON appointments
            FOR EACH ROW
            BEGIN
                DECLARE existing_trans INT;
                IF NEW.status = "completed" AND OLD.status != "completed" 
                    AND NEW.total_fee IS NOT NULL AND NEW.total_fee > 0 THEN
                    
                    SELECT COUNT(*) INTO existing_trans
                    FROM transactions
                    WHERE appointment_id = NEW.id AND deleted_at IS NULL;
                    
                    IF existing_trans = 0 THEN
                        INSERT INTO transactions (
                            appointment_id,
                            patient_id,
                            doctor_id,
                            type,
                            description,
                            quantity,
                            unit_price,
                            amount,
                            status,
                            created_at,
                            updated_at
                        ) VALUES (
                            NEW.id,
                            NEW.patient_id,
                            NEW.doctor_id,
                            "consultation",
                            CONCAT("Consultation fee for appointment on ", DATE(NEW.start_time)),
                            1,
                            NEW.total_fee,
                            NEW.total_fee,
                            "pending",
                            NOW(),
                            NOW()
                        );
                    END IF;
                END IF;
            END
        ');

        // Trigger 7: Validate appointment times
        DB::unprepared('
            CREATE TRIGGER IF NOT EXISTS validate_appointment_times
            BEFORE INSERT ON appointments
            FOR EACH ROW
            BEGIN
                IF NEW.start_time >= NEW.end_time THEN
                    SIGNAL SQLSTATE "45000"
                    SET MESSAGE_TEXT = "Appointment start time must be before end time";
                END IF;
                
                IF NEW.start_time < NOW() THEN
                    SIGNAL SQLSTATE "45000"
                    SET MESSAGE_TEXT = "Cannot schedule appointment in the past";
                END IF;
            END
        ');

        // Trigger 8: Update patient status to inactive if no appointments for 1 year
        DB::unprepared('
            CREATE TRIGGER IF NOT EXISTS update_inactive_patients
            AFTER UPDATE ON appointments
            FOR EACH ROW
            BEGIN
                UPDATE patients
                SET status = "inactive"
                WHERE id = NEW.patient_id
                AND status = "active"
                AND DATEDIFF(CURDATE(), (
                    SELECT MAX(created_at)
                    FROM appointments
                    WHERE patient_id = NEW.patient_id
                    AND status IN ("completed", "no_show")
                )) > 365
                AND deleted_at IS NULL;
            END
        ');
    }

    public function down(): void
    {
        // Drop triggers
        DB::unprepared('DROP TRIGGER IF EXISTS update_inventory_status_on_quantity_change');
        DB::unprepared('DROP TRIGGER IF EXISTS set_initial_inventory_status');
        DB::unprepared('DROP TRIGGER IF EXISTS set_appointment_completed_at');
        DB::unprepared('DROP TRIGGER IF EXISTS prevent_doctor_double_booking');
        DB::unprepared('DROP TRIGGER IF EXISTS prevent_room_double_booking');
        DB::unprepared('DROP TRIGGER IF EXISTS create_transaction_on_appointment_complete');
        DB::unprepared('DROP TRIGGER IF EXISTS validate_appointment_times');
        DB::unprepared('DROP TRIGGER IF EXISTS update_inactive_patients');

        // Drop functions
        DB::unprepared('DROP FUNCTION IF EXISTS calculate_age');
        DB::unprepared('DROP FUNCTION IF EXISTS get_appointment_count_by_status');
        DB::unprepared('DROP FUNCTION IF EXISTS calculate_total_revenue');
        DB::unprepared('DROP FUNCTION IF EXISTS get_doctor_utilization_hours');
    }
};
