<?php
// app/Policies/SalePolicy.php

namespace App\Policies;

use App\Models\User;
use App\Models\Sale;
use App\Models\Station;
use Illuminate\Auth\Access\Response;

class SalePolicy
{
    /**
     * Vérifier si l'utilisateur peut voir la vente
     */
    public function view(User $user, Sale $sale): bool
    {
        // Pour admin : vérifier que la vente appartient à une de ses stations
        // CORRECTION : Utiliser company_id
        if ($user->role === 'admin') {
            $companyId = $user->company_id;
            $stationIds = Station::where('company_id', $companyId)
                                ->pluck('id')
                                ->toArray();
            
            return in_array($sale->station_id, $stationIds);
        }
        
        return false;
    }

    /**
     * Vérifier si l'utilisateur peut annuler la vente
     */
    public function cancel(User $user, Sale $sale): bool
    {
        // Seuls les admins peuvent annuler les ventes complètes
        return $user->role === 'admin' && $sale->status === 'completed';
    }
}