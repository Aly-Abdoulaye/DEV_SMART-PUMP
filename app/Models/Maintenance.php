<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'pump_id',
        'technician_id',
        'station_id',
        'status',
        'priority',
        'scheduled_date',
        'started_at',
        'completed_at',
        'resolution_notes'
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relations
    public function pump()
    {
        return $this->belongsTo(Pump::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    // Méthodes utilitaires
    public function isOverdue()
    {
        return $this->scheduled_date && $this->scheduled_date->isPast() && $this->status !== 'completed';
    }

    public function getDurationAttribute()
    {
        if ($this->started_at && $this->completed_at) {
            return $this->started_at->diffInHours($this->completed_at);
        }
        return null;
    }
}