<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'room_id',
        'start_time',
        'end_time',
        'reason',
        'notes',
        'diagnosis',
        'treatment_plan',
        'status',
        'consultation_fee',
        'total_fee',
        'completed_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'completed_at' => 'datetime',
        'consultation_fee' => 'decimal:2',
        'total_fee' => 'decimal:2',
    ];

    // Relationships
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // Accessors
    public function getDurationInMinutesAttribute()
    {
        return $this->start_time->diffInMinutes($this->end_time);
    }

    public function getDurationInHoursAttribute()
    {
        return $this->start_time->diffInHours($this->end_time);
    }

    public function getIsUpcomingAttribute()
    {
        return $this->start_time->isFuture() && $this->status !== 'cancelled';
    }

    public function getIsOverdueAttribute()
    {
        return $this->start_time->isPast() && $this->status === 'scheduled';
    }

    public function getDoctorDisplayNameAttribute()
    {
        return $this->doctor ? $this->doctor->full_name : 'No Doctor Assigned';
    }

    // Date Formatters (month/day/year format)
    public function getFormattedStartTimeAttribute()
    {
        return $this->start_time->format('m/d/Y H:i');
    }

    public function getFormattedEndTimeAttribute()
    {
        return $this->end_time->format('m/d/Y H:i');
    }

    public function getFormattedStartDateAttribute()
    {
        return $this->start_time->format('m/d/Y');
    }

    public function getFormattedStartTimeOnlyAttribute()
    {
        return $this->start_time->format('h:i A');
    }

    public function getFormattedEndTimeOnlyAttribute()
    {
        return $this->end_time->format('h:i A');
    }

    public function getFormattedStartDateDayAttribute()
    {
        return $this->start_time->format('l, m/d/Y H:i');
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>', now())
                     ->where('status', '!=', 'cancelled')
                     ->orderBy('start_time', 'asc');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed')
                     ->orderBy('completed_at', 'desc');
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('start_time', $date);
    }

    public function scopeByDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    public function scopeByPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSearch($query, $term)
    {
        return $query->whereHas('patient', function ($q) use ($term) {
            $q->where('first_name', 'like', "%{$term}%")
              ->orWhere('last_name', 'like', "%{$term}%");
        })
        ->orWhereHas('doctor', function ($q) use ($term) {
            $q->where('first_name', 'like', "%{$term}%")
              ->orWhere('last_name', 'like', "%{$term}%");
        });
    }

    /**
     * Calculate consultation fee based on doctor's hourly rate
     */
    public function calculateConsultationFee()
    {
        if (!$this->doctor) {
            $this->consultation_fee = 0;
            return;
        }
        
        $durationInHours = $this->duration_in_hours;
        $this->consultation_fee = $this->doctor->hourly_rate * $durationInHours;
    }

    /**
     * Calculate room fee
     */
    public function calculateRoomFee()
    {
        $durationInHours = $this->duration_in_hours;
        return $this->room->hourly_rate * $durationInHours;
    }

    /**
     * Calculate total fee (consultation + room + additional services/inventory)
     */
    public function calculateTotalFee()
    {
        $total = 0;

        // Add consultation fee
        if (!$this->consultation_fee) {
            $this->calculateConsultationFee();
        }
        $total += $this->consultation_fee;

        // Add room fee
        $total += $this->calculateRoomFee();

        // Add transaction fees (inventory, services, etc.)
        $transactionTotal = $this->transactions()
            ->where('status', 'completed')
            ->sum('amount');
        $total += $transactionTotal;

        $this->total_fee = $total;
        return $total;
    }

    /**
     * Mark appointment as completed
     */
    public function markAsCompleted()
    {
        $this->status = 'completed';
        $this->completed_at = now();
        $this->save();
    }

    /**
     * Cancel appointment
     */
    public function cancel($reason = null)
    {
        $this->status = 'cancelled';
        $this->cancellation_reason = $reason;
        $this->save();
    }
}
