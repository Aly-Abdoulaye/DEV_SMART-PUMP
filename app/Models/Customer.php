<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'company_name',
        'credit_limit',
        'current_balance',
        'company_id',
        'is_active'
    ];

    // Relations
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    // Méthodes utilitaires
    public function getAvailableCreditAttribute()
    {
        return $this->credit_limit - $this->current_balance;
    }

    public function hasSufficientCredit($amount)
    {
        return ($this->current_balance + $amount) <= $this->credit_limit;
    }
}
