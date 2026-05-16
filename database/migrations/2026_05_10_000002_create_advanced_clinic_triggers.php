<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Additional Trigger 9: Track inventory usage in transactions
        DB::unprepared('
            CREATE TRIGGER IF NOT EXISTS update_inventory_on_transaction
            AFTER INSERT ON transactions
            FOR EACH ROW
            BEGIN
                IF NEW.inventory_id IS NOT NULL AND NEW.type = "inventory" AND NEW.status = "completed" THEN
                    UPDATE inventories 
                    SET quantity = quantity - NEW.quantity
                    WHERE id = NEW.inventory_id;
                END IF;
            END
        ');

        // Additional Trigger 10: Log all patient data changes for audit trail
        DB::unprepared('
            CREATE TABLE IF NOT EXISTS audit_logs (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                table_name VARCHAR(255),
                record_id BIGINT UNSIGNED,
                action VARCHAR(50),
                old_values LONGTEXT,
                new_values LONGTEXT,
                changed_by VARCHAR(255),
                changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_table_record (table_name, record_id),
                INDEX idx_changed_at (changed_at)
            )
        ');

        DB::unprepared('
            CREATE TRIGGER IF NOT EXISTS audit_patient_changes
            AFTER UPDATE ON patients
            FOR EACH ROW
            BEGIN
                INSERT INTO audit_logs (
                    table_name,
                    record_id,
                    action,
                    old_values,
                    new_values,
                    changed_at
                ) VALUES (
                    "patients",
                    NEW.id,
                    "UPDATE",
                    CONCAT(
                        "email:", OLD.email, "|",
                        "phone:", OLD.phone, "|",
                        "status:", OLD.status
                    ),
                    CONCAT(
                        "email:", NEW.email, "|",
                        "phone:", NEW.phone, "|",
                        "status:", NEW.status
                    ),
                    NOW()
                );
            END
        ');

        // Additional Trigger 11: Alert when inventory reaches critical level
        DB::unprepared('
            CREATE TABLE IF NOT EXISTS inventory_alerts (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                inventory_id BIGINT UNSIGNED,
                alert_type VARCHAR(50),
                message LONGTEXT,
                is_resolved BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                resolved_at TIMESTAMP NULL,
                FOREIGN KEY (inventory_id) REFERENCES inventories(id),
                INDEX idx_inventory_unresolved (inventory_id, is_resolved)
            )
        ');

        DB::unprepared('
            CREATE TRIGGER IF NOT EXISTS create_inventory_alert_on_low_stock
            AFTER UPDATE ON inventories
            FOR EACH ROW
            BEGIN
                DECLARE existing_alert INT;
                IF NEW.quantity <= NEW.reorder_level AND NEW.quantity > 0 THEN
                    SELECT COUNT(*) INTO existing_alert
                    FROM inventory_alerts
                    WHERE inventory_id = NEW.id
                    AND alert_type = "low_stock"
                    AND is_resolved = FALSE;
                    
                    IF existing_alert = 0 THEN
                        INSERT INTO inventory_alerts (
                            inventory_id,
                            alert_type,
                            message
                        ) VALUES (
                            NEW.id,
                            "low_stock",
                            CONCAT(NEW.name, " stock is low. Current: ", NEW.quantity, ", Reorder level: ", NEW.reorder_level)
                        );
                    END IF;
                END IF;
                
                IF NEW.quantity = 0 THEN
                    INSERT INTO inventory_alerts (
                        inventory_id,
                        alert_type,
                        message
                    ) VALUES (
                        NEW.id,
                        "out_of_stock",
                        CONCAT(NEW.name, " is out of stock")
                    );
                END IF;
            END
        ');

        // Additional Trigger 12: Create appointment reminder notifications
        DB::unprepared('
            CREATE TABLE IF NOT EXISTS notifications (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT UNSIGNED,
                patient_id BIGINT UNSIGNED,
                appointment_id BIGINT UNSIGNED,
                type VARCHAR(50),
                title VARCHAR(255),
                message LONGTEXT,
                is_read BOOLEAN DEFAULT FALSE,
                read_at TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (patient_id) REFERENCES patients(id),
                FOREIGN KEY (appointment_id) REFERENCES appointments(id),
                INDEX idx_patient_unread (patient_id, is_read),
                INDEX idx_created_at (created_at)
            )
        ');

        DB::unprepared('
            CREATE TRIGGER IF NOT EXISTS create_appointment_confirmation_notification
            AFTER UPDATE ON appointments
            FOR EACH ROW
            BEGIN
                IF NEW.status = "confirmed" AND OLD.status != "confirmed" THEN
                    INSERT INTO notifications (
                        patient_id,
                        appointment_id,
                        type,
                        title,
                        message,
                        created_at
                    ) VALUES (
                        NEW.patient_id,
                        NEW.id,
                        "appointment_confirmed",
                        "Appointment Confirmed",
                        CONCAT("Your appointment has been confirmed for ", DATE(NEW.start_time), " at ", TIME(NEW.start_time)),
                        NOW()
                    );
                END IF;
            END
        ');

        // Additional Trigger 13: Log all transaction attempts (failed and successful)
        DB::unprepared('
            CREATE TABLE IF NOT EXISTS transaction_logs (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                transaction_id BIGINT UNSIGNED,
                patient_id BIGINT UNSIGNED,
                amount DECIMAL(15, 2),
                status VARCHAR(50),
                payment_method VARCHAR(100),
                error_message LONGTEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (patient_id) REFERENCES patients(id),
                INDEX idx_patient_created (patient_id, created_at)
            )
        ');

        DB::unprepared('
            CREATE TRIGGER IF NOT EXISTS log_transaction_creation
            AFTER INSERT ON transactions
            FOR EACH ROW
            BEGIN
                INSERT INTO transaction_logs (
                    transaction_id,
                    patient_id,
                    amount,
                    status,
                    payment_method,
                    created_at
                ) VALUES (
                    NEW.id,
                    NEW.patient_id,
                    NEW.amount,
                    NEW.status,
                    NEW.payment_method,
                    NOW()
                );
            END
        ');

        // Additional Trigger 14: Validate doctor qualifications before appointment
        DB::unprepared('
            CREATE TRIGGER IF NOT EXISTS validate_doctor_availability
            BEFORE INSERT ON appointments
            FOR EACH ROW
            BEGIN
                DECLARE doc_status VARCHAR(50);
                SELECT status INTO doc_status
                FROM doctors
                WHERE id = NEW.doctor_id;
                
                IF doc_status = "inactive" OR doc_status = "on_leave" THEN
                    SIGNAL SQLSTATE "45000"
                    SET MESSAGE_TEXT = "Selected doctor is not available for appointments";
                END IF;
            END
        ');

        // Additional Trigger 15: Calculate and update patient statistics
        DB::unprepared('
            CREATE TABLE IF NOT EXISTS patient_statistics (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                patient_id BIGINT UNSIGNED UNIQUE,
                total_appointments INT DEFAULT 0,
                completed_appointments INT DEFAULT 0,
                cancelled_appointments INT DEFAULT 0,
                no_show_count INT DEFAULT 0,
                total_spent DECIMAL(15, 2) DEFAULT 0,
                last_appointment_at TIMESTAMP NULL,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
                INDEX idx_total_spent (total_spent)
            )
        ');

        DB::unprepared('
            CREATE TRIGGER IF NOT EXISTS update_patient_stats_on_appointment
            AFTER INSERT ON appointments
            FOR EACH ROW
            BEGIN
                INSERT INTO patient_statistics (patient_id, total_appointments)
                VALUES (NEW.patient_id, 1)
                ON DUPLICATE KEY UPDATE
                total_appointments = total_appointments + 1,
                updated_at = NOW();
            END
        ');

        DB::unprepared('
            CREATE TRIGGER IF NOT EXISTS update_patient_stats_on_completion
            AFTER UPDATE ON appointments
            FOR EACH ROW
            BEGIN
                IF NEW.status = "completed" AND OLD.status != "completed" THEN
                    UPDATE patient_statistics
                    SET completed_appointments = completed_appointments + 1,
                        last_appointment_at = NOW(),
                        updated_at = NOW()
                    WHERE patient_id = NEW.patient_id;
                ELSEIF NEW.status = "cancelled" AND OLD.status != "cancelled" THEN
                    UPDATE patient_statistics
                    SET cancelled_appointments = cancelled_appointments + 1,
                        updated_at = NOW()
                    WHERE patient_id = NEW.patient_id;
                ELSEIF NEW.status = "no_show" AND OLD.status != "no_show" THEN
                    UPDATE patient_statistics
                    SET no_show_count = no_show_count + 1,
                        updated_at = NOW()
                    WHERE patient_id = NEW.patient_id;
                END IF;
            END
        ');

        // Additional Trigger 16: Create patient entry in statistics on patient creation
        DB::unprepared('
            CREATE TRIGGER IF NOT EXISTS create_patient_stats_on_registration
            AFTER INSERT ON patients
            FOR EACH ROW
            BEGIN
                INSERT INTO patient_statistics (patient_id)
                VALUES (NEW.id);
            END
        ');

        // Additional Function 5: Get patient risk score based on no-show history
        DB::unprepared('
            CREATE FUNCTION IF NOT EXISTS get_patient_risk_score(patient_id BIGINT)
            RETURNS INT
            READS SQL DATA
            BEGIN
                DECLARE no_show_count INT;
                DECLARE cancelled_count INT;
                DECLARE total_count INT;
                DECLARE risk_score INT DEFAULT 0;
                
                SELECT COALESCE(no_show_count, 0), 
                       COALESCE(cancelled_appointments, 0),
                       COALESCE(total_appointments, 0)
                INTO no_show_count, cancelled_count, total_count
                FROM patient_statistics
                WHERE patient_statistics.patient_id = patient_id;
                
                IF total_count > 0 THEN
                    SET risk_score = ROUND(((no_show_count + cancelled_count) / total_count) * 100);
                END IF;
                
                RETURN COALESCE(risk_score, 0);
            END
        ');

        // Additional Function 6: Get available appointment slots
        DB::unprepared('
            CREATE FUNCTION IF NOT EXISTS count_available_slots(
                doctor_id BIGINT,
                appointment_date DATE
            )
            RETURNS INT
            READS SQL DATA
            BEGIN
                DECLARE booked_count INT;
                DECLARE max_slots INT DEFAULT 8;
                
                SELECT COUNT(*) INTO booked_count
                FROM appointments
                WHERE doctor_id = doctor_id
                AND DATE(start_time) = appointment_date
                AND status NOT IN ("cancelled", "no_show")
                AND deleted_at IS NULL;
                
                RETURN GREATEST(0, max_slots - booked_count);
            END
        ');

        // Additional Function 7: Get patient bill summary
        DB::unprepared('
            CREATE FUNCTION IF NOT EXISTS get_patient_bill_summary(patient_id BIGINT)
            RETURNS DECIMAL(15, 2)
            READS SQL DATA
            BEGIN
                DECLARE pending_amount DECIMAL(15, 2);
                
                SELECT COALESCE(SUM(amount), 0) INTO pending_amount
                FROM transactions
                WHERE patient_id = patient_id
                AND status = "pending"
                AND deleted_at IS NULL;
                
                RETURN COALESCE(pending_amount, 0);
            END
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS update_inventory_on_transaction');
        DB::unprepared('DROP TRIGGER IF EXISTS audit_patient_changes');
        DB::unprepared('DROP TRIGGER IF EXISTS create_inventory_alert_on_low_stock');
        DB::unprepared('DROP TRIGGER IF EXISTS create_appointment_confirmation_notification');
        DB::unprepared('DROP TRIGGER IF EXISTS log_transaction_creation');
        DB::unprepared('DROP TRIGGER IF EXISTS validate_doctor_availability');
        DB::unprepared('DROP TRIGGER IF EXISTS update_patient_stats_on_appointment');
        DB::unprepared('DROP TRIGGER IF EXISTS update_patient_stats_on_completion');
        DB::unprepared('DROP TRIGGER IF EXISTS create_patient_stats_on_registration');

        DB::unprepared('DROP FUNCTION IF EXISTS get_patient_risk_score');
        DB::unprepared('DROP FUNCTION IF EXISTS count_available_slots');
        DB::unprepared('DROP FUNCTION IF EXISTS get_patient_bill_summary');

        DB::unprepared('DROP TABLE IF EXISTS audit_logs');
        DB::unprepared('DROP TABLE IF EXISTS inventory_alerts');
        DB::unprepared('DROP TABLE IF EXISTS notifications');
        DB::unprepared('DROP TABLE IF EXISTS transaction_logs');
        DB::unprepared('DROP TABLE IF EXISTS patient_statistics');
    }
};
