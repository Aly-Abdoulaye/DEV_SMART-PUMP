<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'company_id',
        'is_active'
    ];

    // Relations
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function tanks()
    {
        return $this->hasMany(Tank::class);
    }

    public function pumps()
    {
        return $this->hasMany(Pump::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function managers()
    {
        return $this->hasMany(User::class)->where('role', 'manager');
    }

    public function employees()
    {
        return $this->hasMany(User::class)->where('role', 'employee');
    }

    public function technicians()
    {
        return $this->hasMany(User::class)->where('role', 'technician');
    }

    public function scopeActive($query)
{
    return $query->where('is_active', true);
}
}
