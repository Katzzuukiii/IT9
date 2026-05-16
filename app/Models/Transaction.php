<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'appointment_id',
        'patient_id',
        'doctor_id',
        'inventory_id',
        'type',
        'description',
        'quantity',
        'unit_price',
        'amount',
        'status',
        'payment_method',
        'reference_number',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    // Relationships
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeByPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where('reference_number', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%")
                    ->orWhereHas('patient', function ($q) use ($term) {
                        $q->where('first_name', 'like', "%{$term}%")
                          ->orWhere('last_name', 'like', "%{$term}%");
                    });
    }

    /**
     * Mark transaction as completed
     */
    public function markAsCompleted()
    {
        $this->status = 'completed';
        $this->save();

        // Decrease inventory if this is an inventory transaction
        if ($this->inventory_id) {
            $this->inventory->decreaseQuantity($this->quantity);
        }
    }

    /**
     * Refund transaction
     */
    public function refund()
    {
        $this->status = 'refunded';
        $this->save();

        // Increase inventory if this is an inventory transaction
        if ($this->inventory_id) {
            $this->inventory->increaseQuantity($this->quantity);
        }
    }

    /**
     * Cancel transaction
     */
    public function cancel()
    {
        $this->status = 'cancelled';
        $this->save();
    }
}
