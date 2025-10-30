<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tank extends Model
{
    use HasFactory;

    protected $fillable = [
        'fuel_type',
        'capacity',
        'current_volume',
        'min_threshold',
        'station_id',
        'is_active'
    ];

    // Relations
    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function pumps()
    {
        return $this->hasMany(Pump::class);
    }

    // Méthodes utilitaires
    public function getAvailableCapacityAttribute()
    {
        return $this->capacity - $this->current_volume;
    }

    public function isLow()
    {
        return $this->current_volume <= $this->min_threshold;
    }

    public function getPercentageAttribute()
    {
        return ($this->current_volume / $this->capacity) * 100;
    }
    public function scopeActive($query)
{
    return $query->where('is_active', true);
}
}
