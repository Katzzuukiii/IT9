# Database Functions and Triggers Documentation

## Functions

### 1. `calculate_age(dob DATE) -> INT`
**Purpose:** Calculates a patient's current age from their date of birth.
**Usage:** 
```sql
SELECT calculate_age(date_of_birth) AS age FROM patients WHERE id = 1;
```
**Benefit:** Provides accurate age calculations in queries without needing PHP logic.

### 2. `get_appointment_count_by_status(start_date, end_date, status_name) -> INT`
**Purpose:** Returns the count of appointments with a specific status within a date range.
**Usage:**
```sql
SELECT get_appointment_count_by_status('2026-05-01', '2026-05-31', 'completed') AS completed_appointments;
```
**Benefit:** Useful for generating reports and analytics on appointment statuses.

### 3. `calculate_total_revenue(start_date, end_date) -> DECIMAL`
**Purpose:** Calculates total revenue from completed transactions within a date range.
**Usage:**
```sql
SELECT calculate_total_revenue('2026-05-01', '2026-05-31') AS monthly_revenue;
```
**Benefit:** Enables financial reporting and revenue tracking.

### 4. `get_doctor_utilization_hours(doctor_id, start_date, end_date) -> DECIMAL`
**Purpose:** Calculates total hours a doctor has spent in appointments.
**Usage:**
```sql
SELECT get_doctor_utilization_hours(1, '2026-05-01', '2026-05-31') AS hours_booked;
```
**Benefit:** Helps track doctor productivity and resource allocation.

---

## Triggers

### 1. `update_inventory_status_on_quantity_change`
**Timing:** AFTER UPDATE on inventories
**Logic:** Automatically updates inventory status based on quantity and expiry date:
- **out_of_stock:** When quantity = 0
- **low_stock:** When quantity ≤ reorder_level
- **expired:** When expiry_date < current date
- **in_stock:** Otherwise

**Benefit:** Ensures inventory status is always accurate without manual updates.

### 2. `set_initial_inventory_status`
**Timing:** BEFORE INSERT on inventories
**Logic:** Sets the initial status when new inventory items are created.
**Benefit:** Prevents null status values and ensures consistency from item creation.

### 3. `set_appointment_completed_at`
**Timing:** BEFORE UPDATE on appointments
**Logic:** Automatically sets `completed_at` timestamp when appointment status changes to "completed".
**Benefit:** Ensures accurate completion timestamps without manual entry.

### 4. `prevent_doctor_double_booking`
**Timing:** BEFORE INSERT on appointments
**Logic:** Validates that a doctor isn't already scheduled during the proposed appointment time slot.
**Error:** Throws error: "Doctor is already booked during this time slot"
**Benefit:** Prevents scheduling conflicts and double-booking doctors.

### 5. `prevent_room_double_booking`
**Timing:** BEFORE INSERT on appointments
**Logic:** Validates that a room isn't already reserved during the proposed time slot.
**Error:** Throws error: "Room is already booked during this time slot"
**Benefit:** Ensures rooms aren't overbooked and prevents scheduling conflicts.

### 6. `create_transaction_on_appointment_complete`
**Timing:** AFTER UPDATE on appointments
**Logic:** Automatically creates a transaction record when an appointment:
- Status changes to "completed"
- Has a total_fee > 0
- Doesn't already have a transaction

**Benefit:** Automatically generates billing records without manual entry, ensuring no completed appointments are missing transactions.

### 7. `validate_appointment_times`
**Timing:** BEFORE INSERT on appointments
**Logic:** Validates:
- Start time must be before end time
- Cannot schedule appointments in the past

**Errors:**
- "Appointment start time must be before end time"
- "Cannot schedule appointment in the past"

**Benefit:** Prevents invalid appointment data at the database level.

### 8. `update_inactive_patients`
**Timing:** AFTER UPDATE on appointments
**Logic:** Marks patients as "inactive" if they haven't had any completed or no-show appointments in the last 365 days.
**Benefit:** Automatically tracks patient engagement and helps identify inactive patient records.

---

## How to Use These in Your Application

### In Laravel Controllers
```php
use Illuminate\Support\Facades\DB;

// Get doctor hours for the month
$hours = DB::select('SELECT get_doctor_utilization_hours(?, ?, ?) as hours', 
    [$doctorId, '2026-05-01', '2026-05-31']
)[0]->hours;

// Get monthly revenue
$revenue = DB::select('SELECT calculate_total_revenue(?, ?) as revenue', 
    ['2026-05-01', '2026-05-31']
)[0]->revenue;

// Get appointment statistics
$completed = DB::select('SELECT get_appointment_count_by_status(?, ?, ?) as count', 
    ['2026-05-01', '2026-05-31', 'completed']
)[0]->count;
```

### Error Handling
The double-booking triggers will throw exceptions if constraints are violated. Handle these in your controller:
```php
try {
    $appointment->save();
} catch (\Exception $e) {
    if (strpos($e->getMessage(), 'already booked') !== false) {
        return response()->json(['error' => 'Time slot is not available'], 422);
    }
    throw $e;
}
```

---

---

# Advanced Clinic Triggers & Functions (Migration 2)

## Additional Triggers (8 new)

### 9. `update_inventory_on_transaction`
**Timing:** AFTER INSERT on transactions
**Logic:** Automatically decrements inventory quantity when a transaction is completed.
**Benefit:** Keeps inventory accurate without manual updates.

### 10. `audit_patient_changes`
**Timing:** AFTER UPDATE on patients
**Logic:** Logs all patient data changes (email, phone, status) to an audit_logs table.
**Benefit:** Maintains complete audit trail for compliance and troubleshooting.

### 11. `create_inventory_alert_on_low_stock`
**Timing:** AFTER UPDATE on inventories
**Logic:** Creates alerts when inventory reaches critical levels:
- Low stock alert when quantity ≤ reorder_level
- Out of stock alert when quantity = 0

**Benefit:** Automatic alert generation for inventory management.

### 12. `create_appointment_confirmation_notification`
**Timing:** AFTER UPDATE on appointments
**Logic:** Creates a notification when appointment status changes to "confirmed".
**Benefit:** Patients automatically receive confirmation notifications.

### 13. `log_transaction_creation`
**Timing:** AFTER INSERT on transactions
**Logic:** Logs all transaction details to transaction_logs table.
**Benefit:** Complete transaction audit trail for billing verification.

### 14. `validate_doctor_availability`
**Timing:** BEFORE INSERT on appointments
**Logic:** Prevents scheduling appointments with inactive or on-leave doctors.
**Error:** "Selected doctor is not available for appointments"
**Benefit:** Prevents invalid scheduling before it happens.

### 15. `update_patient_stats_on_appointment` & `update_patient_stats_on_completion`
**Timing:** AFTER INSERT and AFTER UPDATE on appointments
**Logic:** Auto-updates patient_statistics table tracking:
- Total appointments
- Completed appointments
- Cancelled appointments
- No-show count
- Last appointment date

**Benefit:** Maintains real-time patient statistics for analytics.

### 16. `create_patient_stats_on_registration`
**Timing:** AFTER INSERT on patients
**Logic:** Creates initial patient_statistics record when patient registers.
**Benefit:** Ensures all patients have statistics tracking.

## Additional Functions (3 new)

### 5. `get_patient_risk_score(patient_id) -> INT`
**Purpose:** Calculates a risk score (0-100) based on patient's no-show and cancellation history.
**Usage:**
```sql
SELECT get_patient_risk_score(1) AS risk_score;
```
**Benefit:** Identify unreliable patients for follow-up strategies.

### 6. `count_available_slots(doctor_id, appointment_date) -> INT`
**Purpose:** Returns number of available appointment slots for a doctor on a date.
**Usage:**
```sql
SELECT count_available_slots(1, '2026-05-15') AS available_slots;
```
**Benefit:** Quickly check appointment availability.

### 7. `get_patient_bill_summary(patient_id) -> DECIMAL`
**Purpose:** Calculates total pending bills for a patient.
**Usage:**
```sql
SELECT get_patient_bill_summary(1) AS pending_bill;
```
**Benefit:** Quick billing status check.

## New Support Tables

### `audit_logs`
Tracks all patient data modifications for compliance and auditing.

### `inventory_alerts`
Stores inventory alerts (low stock, out of stock, expired) with resolution status.

### `notifications`
Stores appointment confirmations and system notifications for patients.

### `transaction_logs`
Complete transaction history for billing verification.

### `patient_statistics`
Real-time patient engagement metrics and appointment history.

---

# Laravel Service Classes

## ClinicAnalyticsService
Comprehensive analytics service wrapping all database functions.

**Key Methods:**
```php
// Get patient metrics
$riskScore = $service->getPatientRiskScore($patientId);
$stats = $service->getPatientStats($patientId);
$reliabilityScore = $service->calculatePatientReliabilityScore($patientId);

// Appointments
$slots = $service->getAvailableSlots($doctorId, '2026-05-15');
$stats = $service->getAppointmentStats('2026-05-01', '2026-05-31');

// Billing
$pending = $service->getPatientPendingBill($patientId);
$revenue = $service->getTotalRevenue('2026-05-01', '2026-05-31');

// Doctor performance
$hours = $service->getDoctorUtilizationHours($doctorId, '2026-05-01', '2026-05-31');

// Recommendations
$recommendations = $service->getFollowUpRecommendations($patientId);
```

## NotificationService
Manages patient notifications and alerts.

**Key Methods:**
```php
$notifications = $service->getUnreadNotifications($patientId);
$unreadCount = $service->countUnreadNotifications($patientId);
$service->markAsRead($notificationId);
$service->markAllAsRead($patientId);
$service->deleteNotification($notificationId);
```

## InventoryAlertService
Manages inventory alerts and stock warnings.

**Key Methods:**
```php
$alerts = $service->getUnresolvedAlerts();
$critical = $service->getCriticalInventoryItems();
$lowStock = $service->getLowStockItems();
$service->resolveAlert($alertId);
$reorderNeeded = $service->getItemsNeedingReorder();
```

## Usage Example in Controller

```php
use App\Services\ClinicAnalyticsService;
use App\Services\NotificationService;
use App\Services\InventoryAlertService;

class DashboardController extends Controller
{
    public function __construct(
        private ClinicAnalyticsService $analytics,
        private NotificationService $notifications,
        private InventoryAlertService $inventory
    ) {}

    public function index()
    {
        $patientId = auth()->user()->id;

        return view('dashboard', [
            'unreadNotifications' => $this->notifications->countUnreadNotifications($patientId),
            'pendingBill' => $this->analytics->getPatientPendingBill($patientId),
            'riskScore' => $this->analytics->getPatientRiskScore($patientId),
            'followUp' => $this->analytics->getFollowUpRecommendations($patientId),
            'inventoryAlerts' => $this->inventory->countUnresolvedAlerts(),
            'criticalItems' => $this->inventory->getCriticalInventoryItems(),
        ]);
    }
}
```

## Benefits Summary
✅ **Data Integrity:** Triggers prevent invalid data at the database level  
✅ **Automation:** Automatic status updates and timestamp recording  
✅ **Performance:** Functions provide efficient database-level calculations  
✅ **Conflict Prevention:** Prevents double-booking and overlapping appointments  
✅ **Billing Automation:** Automatically creates transaction records  
✅ **Reporting:** Enables comprehensive analytics and revenue tracking  
✅ **Patient Engagement:** Tracks inactive patients automatically  
✅ **Audit Trail:** Complete history of all patient and transaction changes  
✅ **Smart Alerts:** Automatic inventory and appointment notifications  
✅ **Risk Assessment:** Identifies unreliable patients for follow-up  
✅ **Real-time Metrics:** Patient statistics update automatically with every appointment  
