# Medical Clinic Appointment & Resource Management System

## Project Overview
A professional Laravel MVC-based system for managing a medical clinic with comprehensive appointment scheduling, resource management, and financial tracking.

## Features Implemented

### ✅ Core Models
- **Patient**: Full patient information with medical history, allergies, emergency contacts
- **Doctor**: Doctor profiles with specialization, license, hourly rates, working hours
- **Room**: Medical rooms/consultation areas with equipment tracking
- **Appointment**: Intelligent scheduling with conflict detection
- **Inventory**: Medical supplies, medications, equipment tracking
- **Transaction**: Financial tracking for appointments, services, and inventory

### ✅ Advanced Features

#### 1. **Schedule Conflict Prevention**
The system prevents double-booking through intelligent validation:

```php
// Doctor availability check (App/Models/Doctor.php)
public function isAvailableAt($startTime, $endTime, $excludeAppointmentId = null)
{
    // Checks for overlapping appointments
    // Returns false if doctor already has booking
}

// Room availability check (App/Models/Room.php)
public function isAvailableAt($startTime, $endTime, $excludeAppointmentId = null)
{
    // Checks for room conflicts
}
```

**Implementation in Controller:**
```php
// AppointmentController.php
private function validateScheduleConflict($request, $excludeAppointmentId = null)
{
    $doctor = Doctor::find($request->input('doctor_id'));
    $room = Room::find($request->input('room_id'));
    
    // Returns false if conflicts found
    if (!$doctor->isAvailableAt(...)) return false;
    if (!$room->isAvailableAt(...)) return false;
    
    return true;
}
```

#### 2. **Automatic Fee Calculation**
The system calculates fees based on:
- Doctor's hourly rate × appointment duration
- Room's hourly rate × appointment duration
- Additional inventory/service charges
- Automatic recalculation on appointment updates

```php
// Appointment.php
public function calculateConsultationFee()
{
    $durationInHours = $this->duration_in_hours;
    $this->consultation_fee = $this->doctor->hourly_rate * $durationInHours;
}

public function calculateTotalFee()
{
    $total = 0;
    $total += $this->consultation_fee ?? $this->calculateConsultationFee();
    $total += $this->calculateRoomFee();
    $total += $this->transactions()->where('status', 'completed')->sum('amount');
    $this->total_fee = $total;
    return $total;
}
```

#### 3. **Real-time Availability Display**
JavaScript-driven fee preview that updates as user selects times:
- Shows doctor hourly rate
- Shows room hourly rate
- Calculates duration
- Displays estimated total fee

Located in: `resources/views/appointments/create.blade.php`

### ✅ Complete CRUD Operations

#### Patients
- List all patients with search and filtering
- Create new patient with medical history
- View detailed patient profile with appointment history
- Edit patient information
- Track medical history and allergies

Files:
- `app/Http/Controllers/PatientController.php`
- `app/Http/Requests/StorePatientRequest.php`
- `app/Http/Requests/UpdatePatientRequest.php`
- `resources/views/patients/`

#### Doctors
- Manage doctor profiles and specializations
- Assign doctors to rooms
- Track working hours and availability
- View doctor schedule and appointments

Files:
- `app/Http/Controllers/DoctorController.php`
- `app/Http/Requests/StoreDoctorRequest.php`
- `app/Http/Requests/UpdateDoctorRequest.php`

#### Rooms
- Manage clinic rooms with types and equipment
- Track room availability
- Assign doctors to rooms
- View room schedule

Files:
- `app/Http/Controllers/RoomController.php`
- `app/Http/Requests/StoreRoomRequest.php`
- `app/Http/Requests/UpdateRoomRequest.php`

#### Appointments
- Schedule appointments with automatic conflict checking
- View appointment details with fee breakdown
- Update appointments with fee recalculation
- Cancel or mark as complete
- Track appointment status (scheduled, confirmed, completed, cancelled)

Files:
- `app/Http/Controllers/AppointmentController.php`
- `app/Http/Requests/StoreAppointmentRequest.php`
- `app/Http/Requests/UpdateAppointmentRequest.php`
- `resources/views/appointments/create.blade.php` (includes fee calculator)
- `resources/views/appointments/show.blade.php` (includes fee breakdown)

#### Inventory
- Track medical supplies and equipment
- Monitor stock levels and reorder points
- Track expiry dates
- Auto-update inventory status (in stock, low stock, out of stock, expired)

Files:
- `app/Http/Controllers/InventoryController.php`
- `app/Http/Requests/StoreInventoryRequest.php`
- `app/Http/Requests/UpdateInventoryRequest.php`

#### Transactions
- Record appointment fees, services, and inventory usage
- Track payments and refunds
- Generate financial reports
- Link transactions to appointments

Files:
- `app/Http/Controllers/TransactionController.php`
- `app/Http/Requests/StoreTransactionRequest.php`
- `app/Http/Requests/UpdateTransactionRequest.php`

### ✅ Database Structure

**Migrations Created:**
- `2026_04_25_000001_create_patients_table.php`
- `2026_04_25_000002_create_doctors_table.php`
- `2026_04_25_000003_create_rooms_table.php`
- `2026_04_25_000004_create_inventories_table.php`
- `2026_04_25_000005_create_appointments_table.php`
- `2026_04_25_000006_create_transactions_table.php`
- `2026_04_25_000007_create_doctor_room_table.php` (Many-to-many pivot)

### ✅ Relationships

```
Patient
  → hasMany Appointments
  → hasMany Transactions

Doctor
  → hasMany Appointments
  → belongsToMany Rooms (through doctor_room)
  → hasMany Transactions

Room
  → hasMany Appointments
  → belongsToMany Doctors (through doctor_room)

Appointment
  → belongsTo Patient
  → belongsTo Doctor
  → belongsTo Room
  → hasMany Transactions

Inventory
  → hasMany Transactions

Transaction
  → belongsTo Appointment (nullable)
  → belongsTo Patient
  → belongsTo Doctor (nullable)
  → belongsTo Inventory (nullable)
```

### ✅ Form Validation

All form requests include comprehensive validation:

**Patient:**
- Email and phone uniqueness (except on update)
- Required fields validation
- Date format and constraints
- Status enum validation

**Doctor:**
- License number uniqueness
- Hourly rate numeric validation
- Experience years within range (0-70)
- Working days array validation

**Appointment:**
- Future date enforcement
- End time after start time
- Doctor/Room existence validation

**Inventory:**
- Item code uniqueness
- Quantity and price numeric validation
- Expiry date in future

**Transaction:**
- Required numeric fields
- Valid status enums
- Reference number generation

### ✅ UI/UX Features

**Modern Dashboard:**
- Tailwind CSS for responsive design
- Font Awesome icons throughout
- Gradient sidebar with modern aesthetics
- Auto-dismissing alert notifications

**Key Components:**
- Stats cards showing summary data
- Search and filter functionality
- Status badges with color coding
- Pagination on all list views
- Real-time fee calculator
- Modal-like confirmations

**Files:**
- `resources/views/layouts/app.blade.php` - Main layout with sidebar

### ✅ Routes

Complete RESTful routing with additional custom routes:

```php
// Patients
GET    /patients              // list
GET    /patients/create       // form
POST   /patients              // store
GET    /patients/{patient}    // show
GET    /patients/{patient}/edit // form
PUT    /patients/{patient}    // update
DELETE /patients/{patient}    // delete
GET    /patients/search       // search

// Doctors
GET    /doctors               // list
GET    /doctors/create        // form
POST   /doctors               // store
GET    /doctors/{doctor}      // show
GET    /doctors/{doctor}/edit // form
PUT    /doctors/{doctor}      // update
DELETE /doctors/{doctor}      // delete
GET    /doctors/{doctor}/schedule // view schedule

// Rooms
GET    /rooms                 // list
GET    /rooms/create          // form
POST   /rooms                 // store
GET    /rooms/{room}          // show
GET    /rooms/{room}/edit     // form
PUT    /rooms/{room}          // update
DELETE /rooms/{room}          // delete

// Appointments
GET    /appointments          // list
GET    /appointments/create   // form with conflict prevention
POST   /appointments          // store with validation
GET    /appointments/{appointment} // show with fee breakdown
GET    /appointments/{appointment}/edit // update form
PUT    /appointments/{appointment}  // update
DELETE /appointments/{appointment}  // delete
POST   /appointments/{appointment}/complete // mark complete
POST   /appointments/{appointment}/cancel // cancel
GET    /appointments/available-doctors // AJAX endpoint
GET    /appointments/available-rooms   // AJAX endpoint

// Inventory
GET    /inventories           // list
GET    /inventories/low-stock // filter
GET    /inventories/expired   // filter
POST   /inventories/{inventory}/restock // update quantity

// Transactions
GET    /transactions          // list
GET    /transactions/report   // financial report
GET    /transactions/appointment/{appointment} // by appointment
POST   /transactions/{transaction}/complete // mark completed
POST   /transactions/{transaction}/refund  // process refund
```

### ✅ Seeders & Factories

**Factories Created:**
- `PatientFactory` - Generates 20 test patients
- `DoctorFactory` - Generates 15 doctors with specializations
- `RoomFactory` - Generates 10 rooms
- `InventoryFactory` - Generates 30 inventory items

**Seeders Created:**
- `PatientSeeder`
- `DoctorSeeder`
- `RoomSeeder`
- `InventorySeeder`

Run with: `php artisan db:seed`

## Getting Started

### 1. Install Dependencies
```bash
composer install
npm install
```

### 2. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Database Setup
```bash
php artisan migrate
php artisan db:seed
```

### 4. Compile Assets
```bash
npm run dev
```

### 5. Run Server
```bash
php artisan serve
```

### 6. Access Application
- **URL:** http://localhost:8000
- **Test User:** test@example.com
- **Password:** password

## Key Implementation Details

### Schedule Conflict Prevention Algorithm

The system uses overlapping date range detection:

```sql
WHERE start_time < NEW.end_time AND end_time > NEW.start_time
```

This catches:
- Appointments that start during another's duration
- Appointments that end during another's duration
- Appointments that completely contain another
- Appointments that are completely contained by another

### Fee Calculation Logic

1. **Consultation Fee** = Doctor's hourly rate × appointment duration in hours
2. **Room Fee** = Room's hourly rate × appointment duration in hours
3. **Additional Fees** = Sum of all completed transactions
4. **Total Fee** = Consultation Fee + Room Fee + Additional Fees

Fees are automatically recalculated when:
- Creating a new appointment
- Updating appointment times
- Completing transactions
- Editing transactions

### Inventory Management

**Automatic Status Updates:**
- Expires → "expired" status
- Quantity = 0 → "out_of_stock"
- Quantity ≤ reorder_level → "low_stock"
- Otherwise → "in_stock"

Updates trigger when:
- Creating/updating inventory
- Decreasing quantity
- Increasing quantity (restock)
- Completing transactions using inventory

## Views to Complete

The following view templates follow the same pattern and can be created:

**Doctors Views:**
- `resources/views/doctors/index.blade.php` - List with specialization filter
- `resources/views/doctors/create.blade.php` - Create form
- `resources/views/doctors/edit.blade.php` - Edit form
- `resources/views/doctors/show.blade.php` - Doctor profile with schedule

**Rooms Views:**
- `resources/views/rooms/index.blade.php` - List by type
- `resources/views/rooms/create.blade.php` - Create form
- `resources/views/rooms/edit.blade.php` - Edit form
- `resources/views/rooms/show.blade.php` - Room details and schedule

**Appointments Views:**
- `resources/views/appointments/edit.blade.php` - Edit appointment form
- `resources/views/appointments/schedule.blade.php` - Calendar view

**Inventory Views:**
- `resources/views/inventories/index.blade.php` - List with status filtering
- `resources/views/inventories/create.blade.php` - Create form
- `resources/views/inventories/edit.blade.php` - Edit form
- `resources/views/inventories/show.blade.php` - Stock tracking
- `resources/views/inventories/low-stock.blade.php` - Low stock alert
- `resources/views/inventories/expired.blade.php` - Expired items

**Transaction Views:**
- `resources/views/transactions/index.blade.php` - Transaction list
- `resources/views/transactions/create.blade.php` - Create transaction
- `resources/views/transactions/edit.blade.php` - Edit transaction
- `resources/views/transactions/show.blade.php` - Transaction details
- `resources/views/transactions/report.blade.php` - Financial reports

## API Endpoints for Frontend Integration

```javascript
// Check doctor availability
GET /appointments/available-doctors?start_time=2026-05-01 09:00&end_time=2026-05-01 10:00

// Check room availability
GET /appointments/available-rooms?start_time=2026-05-01 09:00&end_time=2026-05-01 10:00

// Both return JSON arrays of available resources
```

## Testing

Run tests with:
```bash
php artisan test
```

## Security Considerations

- All routes protected by auth middleware
- CSRF token validation on all forms
- Input sanitization and validation
- SQL injection prevention through Eloquent ORM
- Authorization checks in controllers (can be enhanced with Policies)

## Performance Optimizations

- Eager loading relationships to prevent N+1 queries
- Pagination on all list views
- Database indexes on foreign keys and commonly filtered columns
- Soft deletes for data preservation

## Future Enhancements

1. **SMS Notifications** - Appointment reminders via SMS
2. **Email Notifications** - Automated email confirmations
3. **Calendar UI** - Full calendar integration for visual scheduling
4. **Patient Portal** - Self-service appointment booking
5. **Advanced Reporting** - PDF reports and analytics
6. **Multi-clinic Support** - Multiple clinic management
7. **Insurance Integration** - Insurance claim processing
8. **Telemedicine** - Video consultation support

## Support & Documentation

For detailed method documentation, refer to the docstrings in:
- `app/Models/*` - Model methods and relationships
- `app/Http/Controllers/*` - Controller methods and logic
- `database/migrations/*` - Table structures
- `database/factories/*` - Factory definitions

---

**Built with Laravel, Tailwind CSS, and Font Awesome**
