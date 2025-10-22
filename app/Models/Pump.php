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

    // Méthodes utilitaires
    public function getTotalSalesAttribute()
    {
        return $this->current_index - $this->initial_index;
    }

    public function isOperational()
    {
        return $this->status === 'active';
    }
}
