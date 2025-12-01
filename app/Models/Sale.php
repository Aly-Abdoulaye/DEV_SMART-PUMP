<?php
// app/Models/Sale.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
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
        'volume' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'start_index' => 'decimal:3',
        'end_index' => 'decimal:3',
    ];

    // Accesseur pour le numéro de ticket
    public function getNumeroTicketAttribute()
    {
        return 'TKT' . str_pad($this->id, 8, '0', STR_PAD_LEFT);
    }

    // Accesseur pour le libellé du mode de paiement
    public function getPaymentMethodLabelAttribute()
    {
        $labels = [
            'cash' => 'Espèces',
            'customer_account' => 'Compte client',
            'card' => 'Carte bancaire',
            'mobile_money' => 'Mobile Money',
        ];

        return $labels[$this->payment_method] ?? $this->payment_method;
    }

    // Accesseur pour le libellé du statut
    public function getStatusLabelAttribute()
    {
        $labels = [
            'completed' => 'Complète',
            'cancelled' => 'Annulée',
            'pending' => 'En attente',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    // Relations
    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    public function pump(): BelongsTo
    {
        return $this->belongsTo(Pump::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    // Scope pour les ventes d'aujourd'hui
    public function scopeToday($query)
    {
        return $query->whereDate('sale_date', today());
    }

    // Scope pour les ventes complètes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}