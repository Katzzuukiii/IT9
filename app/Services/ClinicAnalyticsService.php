<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Inventory;

class ClinicAnalyticsService
{
    /**
     * Get patient risk score (based on no-show and cancellation history)
     */
    public function getPatientRiskScore(int $patientId): int
    {
        $result = DB::select(
            'SELECT get_patient_risk_score(?) as risk_score',
            [$patientId]
        );

        return $result[0]->risk_score ?? 0;
    }

    /**
     * Get available appointment slots for a doctor on a specific date
     */
    public function getAvailableSlots(int $doctorId, string $date): int
    {
        $result = DB::select(
            'SELECT count_available_slots(?, ?) as slots',
            [$doctorId, $date]
        );

        return $result[0]->slots ?? 0;
    }

    /**
     * Get pending bill amount for a patient
     */
    public function getPatientPendingBill(int $patientId): float
    {
        $result = DB::select(
            'SELECT get_patient_bill_summary(?) as pending_amount',
            [$patientId]
        );

        return (float) ($result[0]->pending_amount ?? 0);
    }

    /**
     * Get patient age from database function
     */
    public function getPatientAge(string $dateOfBirth): int
    {
        $result = DB::select(
            'SELECT calculate_age(?) as age',
            [$dateOfBirth]
        );

        return $result[0]->age ?? 0;
    }

    /**
     * Get appointment statistics for a date range
     */
    public function getAppointmentStats($startDate, $endDate): array
    {
        $statuses = ['scheduled', 'confirmed', 'completed', 'cancelled', 'no_show'];
        $stats = [];

        foreach ($statuses as $status) {
            $result = DB::select(
                'SELECT get_appointment_count_by_status(?, ?, ?) as count',
                [$startDate, $endDate, $status]
            );
            $stats[$status] = $result[0]->count ?? 0;
        }

        return $stats;
    }

    /**
     * Get total revenue for a period
     */
    public function getTotalRevenue($startDate, $endDate): float
    {
        $result = DB::select(
            'SELECT calculate_total_revenue(?, ?) as revenue',
            [$startDate, $endDate]
        );

        return (float) ($result[0]->revenue ?? 0);
    }

    /**
     * Get doctor utilization hours
     */
    public function getDoctorUtilizationHours(int $doctorId, $startDate, $endDate): float
    {
        $result = DB::select(
            'SELECT get_doctor_utilization_hours(?, ?, ?) as hours',
            [$doctorId, $startDate, $endDate]
        );

        return (float) ($result[0]->hours ?? 0);
    }

    /**
     * Get patient statistics from the patient_statistics table
     */
    public function getPatientStats(int $patientId): ?array
    {
        return DB::table('patient_statistics')
            ->where('patient_id', $patientId)
            ->first()
            ? (array) DB::table('patient_statistics')->where('patient_id', $patientId)->first()
            : null;
    }

    /**
     * Get unresolved inventory alerts
     */
    public function getUnresolvedInventoryAlerts(): array
    {
        return DB::table('inventory_alerts')
            ->join('inventories', 'inventory_alerts.inventory_id', '=', 'inventories.id')
            ->where('inventory_alerts.is_resolved', false)
            ->select(
                'inventory_alerts.id',
                'inventory_alerts.alert_type',
                'inventory_alerts.message',
                'inventories.name',
                'inventories.quantity',
                'inventories.reorder_level',
                'inventory_alerts.created_at'
            )
            ->get()
            ->toArray();
    }

    /**
     * Get unread notifications for a patient
     */
    public function getPatientNotifications(int $patientId, bool $unreadOnly = true): array
    {
        $query = DB::table('notifications')->where('patient_id', $patientId);

        if ($unreadOnly) {
            $query->where('is_read', false);
        }

        return $query->orderBy('created_at', 'desc')->get()->toArray();
    }

    /**
     * Resolve an inventory alert
     */
    public function resolveInventoryAlert(int $alertId): bool
    {
        return DB::table('inventory_alerts')
            ->where('id', $alertId)
            ->update([
                'is_resolved' => true,
                'resolved_at' => now(),
            ]) > 0;
    }

    /**
     * Mark notification as read
     */
    public function markNotificationAsRead(int $notificationId): bool
    {
        return DB::table('notifications')
            ->where('id', $notificationId)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]) > 0;
    }

    /**
     * Get audit trail for a patient
     */
    public function getPatientAuditTrail(int $patientId, $limit = 50): array
    {
        return DB::table('audit_logs')
            ->where('table_name', 'patients')
            ->where('record_id', $patientId)
            ->orderBy('changed_at', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get transaction logs for a patient
     */
    public function getPatientTransactionLogs(int $patientId, $limit = 100): array
    {
        return DB::table('transaction_logs')
            ->where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Calculate patient rating based on reliability (opposite of risk score)
     */
    public function calculatePatientReliabilityScore(int $patientId): int
    {
        $riskScore = $this->getPatientRiskScore($patientId);
        return 100 - $riskScore; // Higher score = more reliable
    }

    /**
     * Get recommendations for patient follow-up
     */
    public function getFollowUpRecommendations(int $patientId): array
    {
        $stats = $this->getPatientStats($patientId);
        $riskScore = $this->getPatientRiskScore($patientId);
        $recommendations = [];

        if (!$stats) {
            return ['New patient - consider initial consultation'];
        }

        // High no-show risk
        if ($riskScore > 50) {
            $recommendations[] = 'Send reminder 24 hours before appointment';
            $recommendations[] = 'Consider phone confirmation 1 day prior';
        }

        // Long time since last appointment
        if ($stats['last_appointment_at']) {
            $daysSince = now()->diffInDays($stats['last_appointment_at']);
            if ($daysSince > 180) {
                $recommendations[] = "Patient hasn't had an appointment in {$daysSince} days - send check-up reminder";
            }
        }

        // High pending bill
        $pendingBill = $this->getPatientPendingBill($patientId);
        if ($pendingBill > 1000) {
            $recommendations[] = "Outstanding bill of {$pendingBill} - contact patient for payment";
        }

        if (empty($recommendations)) {
            $recommendations[] = 'Patient is compliant and reliable';
        }

        return $recommendations;
    }
}
