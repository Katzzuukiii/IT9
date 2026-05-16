# Medical Clinic System - Architecture & Technical Documentation

## System Architecture Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                    USER INTERFACE (BLADE VIEWS)                 │
├─────────────────────────────────────────────────────────────────┤
│  Patients │ Doctors │ Rooms │ Appointments │ Inventory │ Trans. │
└────────────────────────┬──────────────────────────────────────┘
                         │
┌────────────────────────▼──────────────────────────────────────┐
│              CONTROLLERS (Request Handling)                    │
├────────────────────────────────────────────────────────────────┤
│ PatientCtrl│DoctorCtrl│RoomCtrl│AppointmentCtrl│InventoryCtrl │
│                        + TransactionCtrl                       │
└────────────────────────┬──────────────────────────────────────┘
                         │
┌────────────────────────▼──────────────────────────────────────┐
│          FORM REQUESTS (Validation Layer)                      │
├────────────────────────────────────────────────────────────────┤
│  Store/Update Requests for all Models with Custom Rules       │
└────────────────────────┬──────────────────────────────────────┘
                         │
┌────────────────────────▼──────────────────────────────────────┐
│              MODELS (Business Logic)                           │
├────────────────────────────────────────────────────────────────┤
│  Patient  │  Doctor  │  Room  │  Appointment  │  Inventory    │
│                    + Transaction                              │
│                                                                │
│  ⚡ Key Methods:                                             │
│  - isAvailableAt()  [Doctor, Room]                           │
│  - calculateTotalFee()  [Appointment]                         │
│  - updateStatus()  [Inventory]                               │
│  - validateScheduleConflict()  [Appointment]                 │
└────────────────────────┬──────────────────────────────────────┘
                         │
┌────────────────────────▼──────────────────────────────────────┐
│          DATABASE (Eloquent ORM)                               │
├────────────────────────────────────────────────────────────────┤
│  MySQL/PostgreSQL with Migrations & Soft Deletes              │
│                                                                │
│  Tables:                                                       │
│  • patients          • inventories                            │
│  • doctors           • transactions                           │
│  • rooms             • doctor_room (pivot)                    │
│  • appointments                                               │
└────────────────────────────────────────────────────────────────┘
```

## Request/Response Flow

### Appointment Creation (With Conflict Detection)

```
┌─────────────────────────────────────────────────────────────┐
│ 1. User submits appointment form                             │
│    (Patient ID, Doctor ID, Room ID, Start Time, End Time)   │
└──────────────────────┬──────────────────────────────────────┘
                       │
┌──────────────────────▼──────────────────────────────────────┐
│ 2. StoreAppointmentRequest validates:                        │
│    ✓ All required fields present                            │
│    ✓ End time > Start time                                  │
│    ✓ Start time in future                                   │
│    ✓ IDs exist in database                                  │
└──────────────────────┬──────────────────────────────────────┘
                       │
┌──────────────────────▼──────────────────────────────────────┐
│ 3. AppointmentController::store() checks conflicts:         │
│                                                              │
│    Doctor.isAvailableAt(start_time, end_time)              │
│    └─► Query: appointments WHERE                            │
│        (start_time < new.end_time AND                       │
│         end_time > new.start_time AND                       │
│         doctor_id = ? AND                                   │
│         status != 'cancelled')                              │
│                                                              │
│    Room.isAvailableAt(start_time, end_time)                │
│    └─► Same query logic for room_id                        │
└──────────────────────┬──────────────────────────────────────┘
                       │
         ┌─────────────┴──────────────┐
         │                            │
    ✓ PASS                       ✗ FAIL
         │                            │
┌────────▼─────────────┐    ┌────────▼──────────────┐
│ 4. Calculate fees:   │    │ Return error:        │
│    - Doctor fee      │    │ "Schedule conflict:  │
│    - Room fee        │    │  Doctor/Room not     │
│    - Total fee       │    │  available"          │
│                      │    │                      │
│ 5. Save appointment  │    │ Redirect with        │
│    to database       │    │ validation errors    │
│                      │    │                      │
│ 6. Redirect to show  │    │                      │
│    with success msg  │    │                      │
└──────────────────────┘    └────────────────────────┘
```

## Fee Calculation Algorithm

```
appointment.start_time = 2026-05-15 09:00
appointment.end_time   = 2026-05-15 10:30
duration_in_hours = 1.5

doctor.hourly_rate = $100
room.hourly_rate   = $50

CALCULATION:
───────────────────────────────────────
Consultation Fee = 100 × 1.5 = $150.00
Room Fee         = 50 × 1.5  = $75.00
───────────────────────────────────────

For each completed transaction:
  Additional Fees += transaction.amount

───────────────────────────────────────
TOTAL FEE = 150.00 + 75.00 + Additional Fees
───────────────────────────────────────
```

## Database Relationships Diagram

```
                 Patient
                   │
        ┌──────────┼──────────┐
        │          │          │
     Appoint    Trans        (Personal Info)
        │          │
        └──────────┼──────────┐
                   │          │
                Doctor ◄──────┘
                   │
              (Pivot)
                   │
            doctor_room
                   │
                 Room
                   │
              Appointment
                   │
            Transaction ────► Inventory
```

## Conflict Detection Logic

### Problem: Prevent Double Booking

**Scenario 1: New booking overlaps existing**
```
Existing:  |====== Doctor ======|  (09:00 - 11:00)
New:       |===== New Appt ====|   (10:00 - 12:00)
           └─────── CONFLICT ──┘
```

**Scenario 2: New booking completely contains existing**
```
Existing:  |==== Doctor ====|      (10:00 - 11:00)
New:       |======= New Appt =====| (09:00 - 12:00)
           └────── CONFLICT ───────┘
```

**Scenario 3: New booking completely contained by existing**
```
Existing:  |======== Doctor ========| (09:00 - 12:00)
New:       |==== New Appt ====|     (10:00 - 11:00)
           └──── CONFLICT ─────┘
```

### Solution: Range Overlap Query

```sql
-- Check if new appointment [new.start_time, new.end_time]
-- overlaps with ANY existing non-cancelled appointment

SELECT COUNT(*) FROM appointments
WHERE doctor_id = ?
  AND status != 'cancelled'
  AND id != ?  -- Exclude current appointment if updating
  AND (
    -- New starts before existing ends AND
    start_time < DATE_ADD(?, INTERVAL 0 SECOND)
    -- New ends after existing starts
    AND end_time > DATE_ADD(?, INTERVAL 0 SECOND)
  );

-- If COUNT > 0: CONFLICT DETECTED
```

## Inventory Status Tracking

```
Inventory Item Created
        │
        ▼
Has expiry_date?
  YES ──► Check if past today ──► YES ──► Status = "expired" ──┐
  NO  ──► Continue                                              │
        │                                                       │
        ▼                                                       │
    Is quantity = 0?                                           │
      YES ──► Status = "out_of_stock" ──┐                      │
      NO  ──► Continue              │                          │
             │                       │                          │
             ▼                       │                          │
    Is quantity <= reorder_level?   │                          │
      YES ──► Status = "low_stock" ──┼──────────────┐           │
      NO  ──► Status = "in_stock" ────────────────┼─────┐      │
                                    │             │     │      │
                                    ◄─────────────┴─────┴──────┘
                                    │
                                    ▼
                        Show status badge to user
                        ✓ Green (in stock)
                        ⚠ Yellow (low stock)
                        ✗ Red (out/expired)
```

## Form Validation Hierarchy

```
User Input
    │
    ▼
HTML5 Browser Validation
    │
    ▼
POST Request to Server
    │
    ▼
FormRequest Class Validation
├─► Required field check
├─► Data type validation
├─► String length validation
├─► Unique constraint check
├─► Email format validation
├─► Date range validation
├─► Enum validation
└─► Custom rule validation
    │
    ├─► ✓ PASS ──► Controller Logic
    │               │
    │               ▼
    │          Model Methods
    │               │
    │               ▼
    │          Save to Database
    │               │
    │               ▼
    │          Redirect with success
    │
    └─► ✗ FAIL ──► Redirect with errors
                    Preserve form data
                    Display error messages
```

## Transaction Management Flow

```
Appointment Created
        │
        ▼
Add Services/Inventory
        │
        ▼
Create Transactions
├─► Type: consultation, service, inventory, etc.
├─► Link to: Appointment, Patient, Doctor, Inventory
├─► Status: pending → completed
└─► Amount: calculated
        │
        ▼
Mark Transaction Complete
        │
        ├─► If inventory: Decrease stock
        │
        ├─► Update Appointment.total_fee
        │
        └─► Record in database
        │
        ▼
Refund Transaction
        │
        ├─► If inventory: Increase stock
        │
        ├─► Update Appointment.total_fee
        │
        └─► Mark as refunded
```

## Security Layers

```
┌─────────────────────────────────────────────┐
│  Layer 1: Route Middleware                  │
│  ✓ auth - require login                    │
│  ✓ verified - require email verification   │
└─────────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────┐
│  Layer 2: Form Request Validation           │
│  ✓ Type checking                            │
│  ✓ Required fields                          │
│  ✓ Uniqueness constraints                   │
│  ✓ Custom rule validation                   │
└─────────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────┐
│  Layer 3: Controller Logic                  │
│  ✓ Business rule validation                 │
│  ✓ Authorization checks                     │
│  ✓ Conflict detection                       │
└─────────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────┐
│  Layer 4: Eloquent ORM                      │
│  ✓ Query builder (prevents SQL injection)   │
│  ✓ Mass assignment protection               │
│  ✓ Timestamps & soft deletes                │
└─────────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────┐
│  Layer 5: Database Constraints              │
│  ✓ Foreign key integrity                    │
│  ✓ Unique constraints                       │
│  ✓ Not null constraints                     │
│  ✓ Data type validation                     │
└─────────────────────────────────────────────┘
```

## Performance Optimizations

### Query Optimization
```php
// ❌ Bad: N+1 queries
$appointments = Appointment::all();
foreach ($appointments as $apt) {
    echo $apt->doctor->name;  // Separate query per loop
}

// ✅ Good: Eager loading
$appointments = Appointment::with(['doctor', 'patient', 'room'])
                            ->paginate(15);
```

### Index Strategy
```sql
-- Recommended database indexes:
CREATE INDEX idx_appointments_doctor_id ON appointments(doctor_id);
CREATE INDEX idx_appointments_patient_id ON appointments(patient_id);
CREATE INDEX idx_appointments_room_id ON appointments(room_id);
CREATE INDEX idx_appointments_start_time ON appointments(start_time);
CREATE INDEX idx_appointments_status ON appointments(status);
CREATE INDEX idx_transactions_appointment_id ON transactions(appointment_id);
CREATE INDEX idx_transactions_patient_id ON transactions(patient_id);
```

### Pagination
- All list views limited to 15 items per page
- Reduces memory usage on large datasets
- Improves response times

## Extensibility Points

### Adding New Feature: SMS Notifications

```php
// Create event
php artisan make:event AppointmentScheduled

// Create listener
php artisan make:listener SendAppointmentNotification --event=AppointmentScheduled

// In Appointment model
protected $dispatchesEvents = [
    'created' => AppointmentScheduled::class,
];

// In listener
public function handle(AppointmentScheduled $event)
{
    $phone = $event->appointment->patient->phone;
    // Send SMS using Twilio, AWS SNS, etc.
}
```

### Adding New Doctor Specialization

```php
// 1. Add to database as enum or record:
// Already handled - 'specialization' is string field

// 2. Add to factory:
// DatabaseFactories/DoctorFactory.php
'specialization' => $this->faker->randomElement([
    'General Practice',
    'Cardiology',
    // ... add more
    'Your New Specialty'
])

// 3. Add to form request if using enum
// App/Http/Requests/StoreDoctorRequest.php
'specialization' => 'required|in:General Practice,Cardiology,Your New Specialty'
```

## Scalability Considerations

### Current Implementation
- Single database
- Suitable for: 1-2 clinics, < 10,000 appointments/month

### Future Scaling
1. **Multi-clinic support**: Add clinic_id foreign key
2. **Database replication**: Master-slave setup
3. **Caching layer**: Redis for frequently accessed data
4. **Async processing**: Queue for heavy operations
5. **API expansion**: RESTful API for mobile apps
6. **Microservices**: Separate services for billing, scheduling, etc.

## Testing Strategy

### Unit Tests (to be created)
```php
// Test: Doctor availability check
public function test_doctor_not_available_with_overlapping_appointment()
{
    $doctor = Doctor::factory()->create();
    $existing = Appointment::factory()
                ->create(['doctor_id' => $doctor->id]);
    
    $available = $doctor->isAvailableAt(
        $existing->start_time->addMinutes(10),
        $existing->end_time->addMinutes(10)
    );
    
    $this->assertFalse($available);
}

// Test: Fee calculation
public function test_appointment_total_fee_calculation()
{
    $doctor = Doctor::factory()->create(['hourly_rate' => 100]);
    $room = Room::factory()->create(['hourly_rate' => 50]);
    $apt = Appointment::factory()->create([
        'doctor_id' => $doctor->id,
        'room_id' => $room->id,
        'start_time' => now(),
        'end_time' => now()->addHours(2)
    ]);
    
    $apt->calculateTotalFee();
    $this->assertEquals(300, $apt->total_fee); // (100+50)*2
}
```

## Deployment Checklist

- [ ] Run migrations: `php artisan migrate --env=production`
- [ ] Generate app key: `php artisan key:generate`
- [ ] Clear cache: `php artisan cache:clear`
- [ ] Seed test data: `php artisan db:seed --class=PatientSeeder`
- [ ] Compile assets: `npm run build`
- [ ] Set up cron for task scheduling
- [ ] Configure email driver for notifications
- [ ] Set up HTTPS certificate
- [ ] Configure backup strategy
- [ ] Monitor database performance

---

**Last Updated:** April 25, 2026
**Framework:** Laravel 11.x
**PHP Version:** 8.1+
**Database:** MySQL/PostgreSQL
