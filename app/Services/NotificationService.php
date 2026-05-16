<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Get unread notifications for a patient
     */
    public function getUnreadNotifications(int $patientId): array
    {
        return DB::table('notifications')
            ->where('patient_id', $patientId)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Get all notifications for a patient with pagination
     */
    public function getPatientNotifications(int $patientId, int $perPage = 20, int $page = 1)
    {
        return DB::table('notifications')
            ->where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Mark single notification as read
     */
    public function markAsRead(int $notificationId): bool
    {
        return DB::table('notifications')
            ->where('id', $notificationId)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]) > 0;
    }

    /**
     * Mark all notifications as read for a patient
     */
    public function markAllAsRead(int $patientId): int
    {
        return DB::table('notifications')
            ->where('patient_id', $patientId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Delete notification
     */
    public function deleteNotification(int $notificationId): bool
    {
        return DB::table('notifications')
            ->where('id', $notificationId)
            ->delete() > 0;
    }

    /**
     * Clear old notifications (older than 30 days)
     */
    public function clearOldNotifications(int $daysOld = 30): int
    {
        return DB::table('notifications')
            ->where('created_at', '<', now()->subDays($daysOld))
            ->delete();
    }

    /**
     * Get notification counts by type
     */
    public function getNotificationCounts(int $patientId): array
    {
        return DB::table('notifications')
            ->where('patient_id', $patientId)
            ->groupBy('type')
            ->selectRaw('type, COUNT(*) as count')
            ->pluck('count', 'type')
            ->toArray();
    }

    /**
     * Count unread notifications
     */
    public function countUnreadNotifications(int $patientId): int
    {
        return DB::table('notifications')
            ->where('patient_id', $patientId)
            ->where('is_read', false)
            ->count();
    }
}
