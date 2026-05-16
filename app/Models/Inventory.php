<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'item_code',
        'category',
        'description',
        'quantity',
        'reorder_level',
        'unit_price',
        'unit',
        'expiry_date',
        'last_restocked',
        'supplier',
        'status',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'last_restocked' => 'date',
        'unit_price' => 'decimal:2',
    ];

    // Relationships
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // Accessors
    public function getIsLowStockAttribute()
    {
        return $this->quantity <= $this->reorder_level;
    }

    public function getIsExpiredAttribute()
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    // Scopes
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity', '<=', 'reorder_level');
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }

    public function scopeSearch($query, $term)
    {
        return $query->where('name', 'like', "%{$term}%")
                    ->orWhere('item_code', 'like', "%{$term}%")
                    ->orWhere('category', 'like', "%{$term}%");
    }

    /**
     * Update inventory status based on quantity
     */
    public function updateStatus()
    {
        if ($this->is_expired) {
            $this->status = 'expired';
        } elseif ($this->quantity === 0) {
            $this->status = 'out_of_stock';
        } elseif ($this->quantity <= $this->reorder_level) {
            $this->status = 'low_stock';
        } else {
            $this->status = 'in_stock';
        }

        $this->save();
    }

    /**
     * Decrease inventory quantity
     */
    public function decreaseQuantity($quantity)
    {
        $this->quantity -= $quantity;
        $this->updateStatus();
    }

    /**
     * Increase inventory quantity
     */
    public function increaseQuantity($quantity)
    {
        $this->quantity += $quantity;
        $this->last_restocked = now();
        $this->updateStatus();
    }
}
