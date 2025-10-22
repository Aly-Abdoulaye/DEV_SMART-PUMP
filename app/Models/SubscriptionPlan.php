<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'monthly_price',
        'annual_price',
        'setup_fee',
        'max_stations',
        'max_users',
        'max_customers',
        'has_advanced_reports',
        'has_api_access',
        'has_premium_support',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'monthly_price' => 'decimal:2',
        'annual_price' => 'decimal:2',
        'setup_fee' => 'decimal:2',
        'has_advanced_reports' => 'boolean',
        'has_api_access' => 'boolean',
        'has_premium_support' => 'boolean',
        'is_active' => 'boolean'
    ];

    // Relations
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function companies()
    {
        return $this->hasMany(Company::class, 'subscription_plan', 'name');
    }

    // Méthodes utilitaires
    public function getYearlySavingsAttribute()
    {
        $monthlyTotal = $this->monthly_price * 12;
        return $monthlyTotal - $this->annual_price;
    }

    public function getYearlySavingsPercentageAttribute()
    {
        if ($this->monthly_price == 0) return 0;
        
        $monthlyTotal = $this->monthly_price * 12;
        return round((($monthlyTotal - $this->annual_price) / $monthlyTotal) * 100, 1);
    }

    public function getPriceForPeriod($period)
    {
        return match($period) {
            'monthly' => $this->monthly_price,
            'annual' => $this->annual_price,
            default => $this->monthly_price
        };
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('monthly_price');
    }
}