<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'pump_id',
        'station_id',
        'user_id',
        'customer_id',
        'volume',
        'unit_price',
        'total_amount',
        'start_index',
        'end_index',
        'payment_method',
        'status',
        'sale_date'
    ];

    protected $casts = [
        'sale_date' => 'datetime',
    ];

    // Relations
    public function pump()
    {
        return $this->belongsTo(Pump::class);
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}