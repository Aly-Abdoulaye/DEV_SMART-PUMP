<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pump extends Model
{
    use HasFactory;

    protected $fillable = [
        'pump_number',
        'nozzle_number',
        'tank_id',
        'station_id',
        'status',
        'initial_index',
        'current_index'
    ];

    protected $casts = [
        'initial_index' => 'decimal:2',
        'current_index' => 'decimal:2'
    ];

    // Relations
    public function tank()
    {
        return $this->belongsTo(Tank::class);
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', '!=', 'active');
    }

    public function scopeByStation($query, $stationId)
    {
        return $query->where('station_id', $stationId);
    }

    public function scopeOperational($query)
    {
        return $query->where('status', 'active');
    }

    // Accessors
    public function getTotalSalesAttribute()
    {
        return $this->current_index - $this->initial_index;
    }

    public function getIsActiveAttribute()
    {
        return $this->status === 'active';
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'active' => 'success',
            'maintenance' => 'warning',
            'broken' => 'danger',
            'inactive' => 'secondary',
            default => 'secondary'
        };
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'active' => 'Active',
            'maintenance' => 'Maintenance',
            'broken' => 'En Panne',
            'inactive' => 'Inactive',
            default => 'Inconnu'
        };
    }

    public function getFuelTypeAttribute()
    {
        return $this->tank ? $this->tank->fuel_type : 'Non assignée';
    }

    // Méthodes utilitaires
    public function isOperational()
    {
        return $this->status === 'active';
    }

    public function canBeUsed()
    {
        return $this->isOperational() && $this->tank && $this->tank->is_active;
    }

    public function updateIndex($newIndex)
    {
        if ($newIndex >= $this->current_index) {
            $this->current_index = $newIndex;
            return $this->save();
        }
        return false;
    }

    public function hasTank()
    {
        return !is_null($this->tank_id);
    }

    public function getUsagePercentage()
    {
        if ($this->initial_index == 0) return 0;
        return (($this->current_index - $this->initial_index) / $this->initial_index) * 100;
    }
}
