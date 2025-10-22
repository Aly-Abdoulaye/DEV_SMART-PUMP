<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address', 
        'phone',
        'email',
        'logo',
        'subscription_plan',
        'subscription_expires_at',
        'is_active',
        'primary_color',
        'secondary_color',
        'alert_threshold',
        'low_stock_alert',
        'maintenance_alert_days',
        'business_rules'
    ];

    protected $casts = [
        'subscription_expires_at' => 'datetime',
        'alert_threshold' => 'decimal:2',
        'low_stock_alert' => 'decimal:2',
        'maintenance_alert_days' => 'integer',
    ];

    // Relations
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function stations()
    {
        return $this->hasMany(Station::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    // AJOUTER CETTE RELATION POUR LES PAIEMENTS
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // Méthodes utilitaires
    public function activeStations()
    {
        return $this->hasMany(Station::class)->where('is_active', true);
    }

    public function activeUsers()
    {
        return $this->hasMany(User::class)->where('is_active', true);
    }

    // Méthodes pour les abonnements
    public function isSubscriptionActive()
    {
        return $this->is_active && $this->subscription_expires_at->isFuture();
    }

    public function isSubscriptionExpired()
    {
        return $this->subscription_expires_at->isPast();
    }

    public function getSubscriptionStatusColor()
    {
        if (!$this->is_active) {
            return 'danger';
        }
        
        if ($this->isSubscriptionExpired()) {
            return 'warning';
        }
        
        $daysLeft = now()->diffInDays($this->subscription_expires_at);
        
        if ($daysLeft <= 7) {
            return 'warning';
        }
        
        return 'success';
    }

    public function getSubscriptionStatusText()
    {
        if (!$this->is_active) {
            return 'Suspendu';
        }
        
        if ($this->isSubscriptionExpired()) {
            return 'Expiré';
        }
        
        $daysLeft = now()->diffInDays($this->subscription_expires_at);
        
        if ($daysLeft <= 7) {
            return 'Bientôt expiré';
        }
        
        return 'Actif';
    }

    // Méthode pour obtenir le dernier paiement
    public function getLastPayment()
    {
        return $this->payments()->latest()->first();
    }

    // Méthode pour obtenir le revenu total
    public function getTotalRevenue()
    {
        return $this->payments()->where('status', 'completed')->sum('amount');
    }

    // Scope pour les entreprises actives
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope pour les entreprises avec abonnement expiré
    public function scopeExpired($query)
    {
        return $query->where('subscription_expires_at', '<', now());
    }

    // Scope pour les entreprises avec abonnement bientôt expiré
    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereBetween('subscription_expires_at', [now(), now()->addDays($days)]);
    }
}