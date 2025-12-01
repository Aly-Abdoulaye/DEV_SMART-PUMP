<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuelPriceHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'fuel_id',
        'old_price',
        'new_price',
        'change_date',
        'changed_by',
        'change_reason'
    ];

    protected $casts = [
        'change_date' => 'datetime',
        'old_price' => 'decimal:2',
        'new_price' => 'decimal:2',
    ];

    /**
     * Valeurs par défaut
     */
    protected $attributes = [
        'change_reason' => 'Ajustement de prix', // ⭐ Valeur par défaut
    ];

    /**
     * Relation avec le carburant
     */
    public function fuel()
    {
        return $this->belongsTo(Fuel::class, 'fuel_id');
    }

    /**
     * Relation avec l'utilisateur qui a modifié le prix
     */
    public function changedByUser()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}