# Medical Clinic Management System

A comprehensive Laravel-based management system for medical clinics with appointment scheduling, patient records, billing, inventory, staff management, and advanced reporting.

## 🎯 System Features

### 1. **Patient Registration** ✅
- Register new patients with comprehensive personal details
- Track medical history and allergies
- Emergency contact information
- Status management (active/inactive)
- Full search and filtering capabilities

### 2. **Appointment Scheduling** ✅
- Schedule appointments with automatic conflict detection
- Prevent double-booking of doctors and rooms
- Automatic fee calculation based on duration
- Appointment status tracking (scheduled/completed/cancelled)
- Real-time availability display
- Reschedule and cancel functionality

### 3. **Consultation & Medical Records** ✅
- Record consultation details during appointments
- Track diagnoses and treatments
- Manage prescriptions
- Maintain complete patient medical history
- Access historical medical records

### 4. **Billing & Payment Process** ✅
- Automatic bill generation based on appointment fees
- Multiple payment methods support (cash/digital/check)
- Transaction management (pending/completed/refunded)
- Payment history tracking
- Financial transaction reports

### 5. **Inventory Management** ✅
- Track medicines and medical supplies
- Stock-in and stock-out operations
- Low stock alerts
- Expiration date tracking
- Inventory valuation
- Automatic status updates

### 6. **User & Staff Management** ✅
- Role-based access control (Admin/Doctor/Staff)
- Staff member creation and management
- Active/Inactive status management
- Contact information and address tracking
- Password management with security

### 7. **Report Generation** ✅
- **Dashboard Reports**: Key metrics overview
- **Daily Patient Visits**: Visit statistics by date
- **Income Reports**: Financial performance analysis
- **Inventory Status**: Stock levels and valuation
- **Doctor Performance**: Appointment and revenue metrics
- **Transaction Reports**: Detailed financial history

## 🏗️ Architecture

```
┌─────────────────────────────────────────────┐
│        User Interface (Blade Views)          │
├─────────────────────────────────────────────┤
│ Controllers → Requests → Models → Database  │
└─────────────────────────────────────────────┘
```

### Technology Stack
- **Framework**: Laravel 11+
- **Database**: MySQL/PostgreSQL
- **ORM**: Eloquent
- **Frontend**: Blade Templates
- **Styling**: Tailwind CSS
- **Authentication**: Laravel Fortify

## 📊 Database Structure

### Core Tables
- `patients` - Patient information and medical history
- `doctors` - Doctor profiles and specializations
- `rooms` - Consultation rooms and facilities
- `appointments` - Appointment scheduling and records
- `inventories` - Medical supplies and equipment
- `transactions` - Financial records
- `users` - Staff and administration accounts

### Relationships
```
Patient → Appointments ← Doctor
                ↓
           Transactions → Inventory
           
User (Staff Management)
  - Role: Admin, Doctor, Staff
  - Status: Active, Inactive
```

## 🚀 Quick Start

### Prerequisites
- PHP 8.1+
- Composer
- MySQL 5.7+ or PostgreSQL 10+
- Node.js & npm (for frontend assets)

### Installation

1. **Clone the Repository**
   ```bash
   git clone [repository-url]
   cd MedClinicSystem
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Configuration**
   Edit `.env` and configure your database:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=med_clinic
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Run Migrations**
   ```bash
   php artisan migrate
   ```

6. **Seed Sample Data (Optional)**
   ```bash
   php artisan db:seed
   ```

7. **Build Frontend Assets**
   ```bash
   npm run build
   ```

8. **Start Development Server**
   ```bash
   php artisan serve
   ```

Access the application at `http://localhost:8000`

## 📱 Usage

### Patient Registration
1. Navigate to **Patients** → **Add Patient**
2. Fill in personal and medical information
3. Save patient record
4. View patient profile and history

### Scheduling Appointments
1. Go to **Appointments** → **New Appointment**
2. Select patient, doctor, and room
3. Choose appointment date and time
4. System automatically checks availability
5. Fees calculated automatically
6. Confirm and save appointment

### Managing Inventory
1. Access **Inventory** section
2. Add new items with details
3. Track stock-in and stock-out operations
4. Monitor low stock alerts
5. View inventory reports

### Staff Management
1. Navigate to **Staff** section
2. Add new staff member with role assignment
3. Set active/inactive status
4. Manage contact information
5. View staff directory

### Generating Reports
1. Go to **Reports** dashboard
2. Select desired report type
3. Choose date range
4. View statistics and analytics
5. Download or print reports

## 🔐 Security Features

- Role-based access control
- Password hashing and encryption
- CSRF protection
- SQL injection prevention
- Secure payment transaction handling

## 📊 Key Features in Detail

### Schedule Conflict Prevention
The system intelligently prevents double-booking by:
- Checking doctor availability for selected time slot
- Verifying room availability simultaneously
- Automatically detecting overlapping appointments
- Validating time constraints before saving

### Automatic Fee Calculation
```
Consultation Fee = Doctor Hourly Rate × Duration
Room Fee = Room Hourly Rate × Duration
Total Fee = Consultation Fee + Room Fee + Additional Charges
```

### Real-time Availability
- Live doctor availability status
- Room booking status display
- Immediate fee preview
- Dynamic slot selection

### Comprehensive Reporting
- Date range filtering
- Multi-level data aggregation
- Summary statistics
- Detailed breakdowns
- Export capabilities

## 📁 Project Structure

```
MedClinicSystem/
├── app/
│   ├── Models/
│   │   ├── Patient.php
│   │   ├── Doctor.php
│   │   ├── Appointment.php
│   │   ├── Transaction.php
│   │   ├── Inventory.php
│   │   └── User.php
│   └── Http/
│       ├── Controllers/
│       │   ├── PatientController.php
│       │   ├── AppointmentController.php
│       │   ├── StaffController.php
│       │   ├── ReportController.php
│       │   └── ...
│       └── Requests/
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   └── views/
│       ├── patients/
│       ├── appointments/
│       ├── staff/
│       ├── reports/
│       └── ...
└── routes/
    └── web.php
```

## 🛣️ API Routes

### Patient Management
- `GET /patients` - List all patients
- `POST /patients` - Create patient
- `GET /patients/{id}` - View patient
- `PUT /patients/{id}` - Update patient
- `DELETE /patients/{id}` - Delete patient

### Appointment Management
- `GET /appointments` - List appointments
- `POST /appointments` - Create appointment
- `PUT /appointments/{id}` - Update appointment
- `POST /appointments/{id}/cancel` - Cancel appointment
- `POST /appointments/{id}/complete` - Complete appointment

### Staff Management
- `GET /staff` - List staff
- `POST /staff` - Add staff member
- `PUT /staff/{id}` - Update staff
- `DELETE /staff/{id}` - Delete staff

### Reports
- `GET /reports` - Reports dashboard
- `GET /reports/daily-visits` - Daily visit statistics
- `GET /reports/income` - Income reports
- `GET /reports/inventory` - Inventory status
- `GET /reports/doctor-performance` - Doctor metrics

## 📝 Documentation

For detailed implementation information, see:
- [FEATURE_IMPLEMENTATION.md](./FEATURE_IMPLEMENTATION.md) - Complete feature guide
- [ARCHITECTURE.md](./ARCHITECTURE.md) - System architecture
- [QUICK_START.md](./QUICK_START.md) - Quick start guide
- [IMPLEMENTATION_GUIDE.md](./IMPLEMENTATION_GUIDE.md) - Implementation details

## 🐛 Troubleshooting

### Migration Issues
```bash
php artisan migrate:reset
php artisan migrate
```

### Cache Issues
```bash
php artisan cache:clear
php artisan config:cache
```

### Asset Issues
```bash
npm run dev
php artisan storage:link
```

## 📧 Support

For issues and questions:
1. Check documentation files
2. Review error messages and logs
3. Check database configuration
4. Verify all dependencies are installed

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 👥 Authors

Medical Clinic System Development Team

---

**Last Updated**: May 2026
**Version**: 1.0.0
