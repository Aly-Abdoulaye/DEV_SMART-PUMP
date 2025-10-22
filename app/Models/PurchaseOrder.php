<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_id',
        'station_id',
        'total_amount',
        'remaining_amount',
        'status',
        'valid_until',
        'notes'
    ];

    protected $casts = [
        'valid_until' => 'date',
    ];

    // Relations
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    // Méthodes utilitaires
    public function isExpired()
    {
        return $this->valid_until->isPast();
    }

    public function isFullyUsed()
    {
        return $this->remaining_amount <= 0;
    }
}