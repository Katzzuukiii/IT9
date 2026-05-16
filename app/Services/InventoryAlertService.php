<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryAlertService
{
    /**
     * Get all unresolved inventory alerts
     */
    public function getUnresolvedAlerts(): array
    {
        return DB::table('inventory_alerts')
            ->join('inventories', 'inventory_alerts.inventory_id', '=', 'inventories.id')
            ->where('inventory_alerts.is_resolved', false)
            ->select(
                'inventory_alerts.id',
                'inventory_alerts.alert_type',
                'inventory_alerts.message',
                'inventory_alerts.created_at',
                'inventories.id as inventory_id',
                'inventories.name',
                'inventories.quantity',
                'inventories.reorder_level'
            )
            ->orderBy('inventory_alerts.created_at', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Get alerts by type
     */
    public function getAlertsByType(string $type): array
    {
        return DB::table('inventory_alerts')
            ->join('inventories', 'inventory_alerts.inventory_id', '=', 'inventories.id')
            ->where('inventory_alerts.is_resolved', false)
            ->where('inventory_alerts.alert_type', $type)
            ->select(
                'inventory_alerts.id',
                'inventory_alerts.message',
                'inventory_alerts.created_at',
                'inventories.name',
                'inventories.quantity'
            )
            ->get()
            ->toArray();
    }

    /**
     * Resolve an inventory alert
     */
    public function resolveAlert(int $alertId): bool
    {
        $result = DB::table('inventory_alerts')
            ->where('id', $alertId)
            ->update([
                'is_resolved' => true,
                'resolved_at' => now(),
            ]);

        Log::info("Inventory alert {$alertId} resolved");
        return $result > 0;
    }

    /**
     * Resolve all alerts for a specific inventory
     */
    public function resolveAllAlertsForInventory(int $inventoryId): int
    {
        return DB::table('inventory_alerts')
            ->where('inventory_id', $inventoryId)
            ->where('is_resolved', false)
            ->update([
                'is_resolved' => true,
                'resolved_at' => now(),
            ]);
    }

    /**
     * Get critical inventory items (out of stock or expired)
     */
    public function getCriticalInventoryItems(): array
    {
        return DB::table('inventory_alerts')
            ->join('inventories', 'inventory_alerts.inventory_id', '=', 'inventories.id')
            ->where('inventory_alerts.is_resolved', false)
            ->whereIn('inventory_alerts.alert_type', ['out_of_stock', 'expired'])
            ->select(
                'inventories.id',
                'inventories.name',
                'inventories.quantity',
                'inventory_alerts.alert_type',
                'inventory_alerts.message'
            )
            ->get()
            ->toArray();
    }

    /**
     * Get low stock items
     */
    public function getLowStockItems(): array
    {
        return DB::table('inventory_alerts')
            ->join('inventories', 'inventory_alerts.inventory_id', '=', 'inventories.id')
            ->where('inventory_alerts.is_resolved', false)
            ->where('inventory_alerts.alert_type', 'low_stock')
            ->select(
                'inventories.id',
                'inventories.name',
                'inventories.quantity',
                'inventories.reorder_level',
                'inventories.supplier'
            )
            ->get()
            ->toArray();
    }

    /**
     * Count unresolved alerts
     */
    public function countUnresolvedAlerts(): int
    {
        return DB::table('inventory_alerts')
            ->where('is_resolved', false)
            ->count();
    }

    /**
     * Count alerts by type
     */
    public function countAlertsByType(): array
    {
        return DB::table('inventory_alerts')
            ->where('is_resolved', false)
            ->groupBy('alert_type')
            ->selectRaw('alert_type, COUNT(*) as count')
            ->pluck('count', 'alert_type')
            ->toArray();
    }

    /**
     * Get alert history for an inventory item
     */
    public function getInventoryAlertHistory(int $inventoryId, int $limit = 50): array
    {
        return DB::table('inventory_alerts')
            ->where('inventory_id', $inventoryId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Create manual alert for inventory
     */
    public function createManualAlert(int $inventoryId, string $type, string $message): int
    {
        return DB::table('inventory_alerts')
            ->insertGetId([
                'inventory_id' => $inventoryId,
                'alert_type' => $type,
                'message' => $message,
                'created_at' => now(),
            ]);
    }

    /**
     * Get recent alerts (last 24 hours)
     */
    public function getRecentAlerts(int $hours = 24): array
    {
        return DB::table('inventory_alerts')
            ->join('inventories', 'inventory_alerts.inventory_id', '=', 'inventories.id')
            ->where('inventory_alerts.created_at', '>=', now()->subHours($hours))
            ->select(
                'inventory_alerts.*',
                'inventories.name'
            )
            ->orderBy('inventory_alerts.created_at', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Get expected reorder items based on consumption rate
     */
    public function getItemsNeedingReorder(): array
    {
        return DB::table('inventories')
            ->whereRaw('quantity <= reorder_level')
            ->where('status', '!=', 'out_of_stock')
            ->select(
                'id',
                'name',
                'quantity',
                'reorder_level',
                'supplier',
                'unit_price'
            )
            ->get()
            ->toArray();
    }
}
