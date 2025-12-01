<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // ← AJOUT IMPORTANT
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\FuelPriceHistory; // ← Vérifiez cet import

class Fuel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'description', 'price', 'is_active', 'company_id'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    // Palette de couleurs harmonieuses pour les carburants
    const COLOR_PALETTE = [
        '#FF6B6B', // Rouge vif - Essence
        '#4ECDC4', // Turquoise - Gazole
        '#45B7D1', // Bleu clair - Super
        '#96CEB4', // Vert menthe - Bioéthanol
        '#FFEAA7', // Jaune pâle - GPL
        '#DDA0DD', // Violet - Kérosène
        '#98D8C8', // Vert eau - AdBlue
        '#F7DC6F', // Or - Premium
        '#BB8FCE', // Lavande - SP98
        '#85C1E9', // Bleu ciel - SP95
        '#F8C471', // Orange - E85
        '#82E0AA', // Vert printemps - Bio diesel
    ];

    // Accesseur pour la couleur automatique
    public function getColorAttribute()
    {
        // Génère une couleur basée sur l'ID pour la consistance
        $colors = self::COLOR_PALETTE;
        return $colors[($this->id - 1) % count($colors)] ?? '#007bff';
    }

    // Méthode pour obtenir une couleur par nom de carburant (cohérent)
    public function getColorByName($name)
    {
        $colorMap = [
            'essence' => '#FF6B6B',
            'gazole' => '#4ECDC4', 
            'diesel' => '#4ECDC4',
            'super' => '#45B7D1',
            'bio' => '#96CEB4',
            'gpl' => '#FFEAA7',
            'kérosène' => '#DDA0DD',
            'adblue' => '#98D8C8',
            'premium' => '#F7DC6F',
            'sp98' => '#BB8FCE',
            'sp95' => '#85C1E9',
            'e85' => '#F8C471',
        ];

        $nameLower = strtolower($name);
        
        foreach ($colorMap as $key => $color) {
            if (str_contains($nameLower, $key)) {
                return $color;
            }
        }

        // Fallback à la palette basée sur l'ID
        return $this->getColorAttribute();
    }

    // Relations
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function tanks(): HasMany
    {
        return $this->hasMany(Tank::class, 'fuel_type', 'name');
    }

    public function priceHistory(): HasMany
    {
        return $this->hasMany(FuelPriceHistory::class);
    }

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    // Méthodes utilitaires
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 0, ',', ' ') . ' FCFA';
    }

    public function getAutoColorAttribute()
    {
        return $this->getColorByName($this->name);
    }

    public function updatePrice($newPrice, $reason, $changedBy)
    {
        // CHOISISSEZ LE BON MODÈLE POUR L'HISTORIQUE :
        
        // Si vous utilisez PriceHistory
        FuelPriceHistory::create([
            'fuel_id' => $this->id,
            'old_price' => $this->price,
            'new_price' => $newPrice,
            'change_reason' => $reason,
            'changed_by' => $changedBy
        ]);
        
        // OU si vous utilisez FuelPriceHistory
        // FuelPriceHistory::create([
        //     'fuel_id' => $this->id,
        //     'old_price' => $this->price,
        //     'new_price' => $newPrice,
        //     'change_reason' => $reason,
        //     'changed_by' => $changedBy
        // ]);

        // Mettre à jour le prix
        $this->update(['price' => $newPrice]);

        return $this;
    }
}