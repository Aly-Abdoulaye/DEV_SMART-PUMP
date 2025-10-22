<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_number',
        'amount',
        'currency',
        'status',
        'payment_method',
        'transaction_id',
        'start_date',
        'end_date',
        'company_id',
        'subscription_plan_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'metadata',
        'notes',
        'paid_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'invoice_date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'metadata' => 'array'
    ];

    /// Relations
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    // Méthodes utilitaires
    public function isOverdue()
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'completed';
    }

    public function isPaid()
    {
        return $this->status === 'completed';
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'completed' => 'success',
            'pending' => 'warning',
            'failed' => 'danger',
            'refunded' => 'info',
            default => 'secondary'
        };
    }

    public function getPaymentMethodIconAttribute()
    {
        return match($this->payment_method) {
            'bank_transfer' => 'fas fa-university',
            'mobile_money' => 'fas fa-mobile-alt',
            'card' => 'fas fa-credit-card',
            'cash' => 'fas fa-money-bill-wave',
            default => 'fas fa-money-bill'
        };
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->where('status', 'pending');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    // Générer un numéro de paiement unique
    public static function generatePaymentNumber()
    {
        $prefix = 'PAY';
        $date = now()->format('Ym');
        $lastPayment = self::where('payment_number', 'like', "{$prefix}{$date}%")->latest()->first();
        
        $sequence = $lastPayment ? (int)substr($lastPayment->payment_number, -4) + 1 : 1;
        
        return "{$prefix}{$date}" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    // Générer un numéro de facture
    public static function generateInvoiceNumber()
    {
        $prefix = 'INV';
        $date = now()->format('Ym');
        $lastInvoice = self::where('invoice_number', 'like', "{$prefix}{$date}%")->latest()->first();
        
        $sequence = $lastInvoice ? (int)substr($lastInvoice->invoice_number, -4) + 1 : 1;
        
        return "{$prefix}{$date}" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}