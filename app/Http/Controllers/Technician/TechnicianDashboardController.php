<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TechnicianDashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = Auth::user();

        // Données simulées pour le technicien
        $data = [
            'user' => $user,
            'interventions_en_cours' => 3,
            'interventions_terminees' => 12,
            'equipements_surveilles' => 28,
            'taux_reussite' => 94,
            'alertes_actives' => [
                ['id' => 1, 'type' => 'Pompe', 'equipement' => 'Pompe 2', 'station' => 'Station Principale', 'niveau' => 'critique', 'date' => now()->subHours(2)],
                ['id' => 2, 'type' => 'Volucompteur', 'equipement' => 'VC-03', 'station' => 'Station Nord', 'niveau' => 'moyen', 'date' => now()->subHours(5)],
            ],
            'interventions_recentes' => [
                ['id' => 1, 'equipement' => 'Pompe 1', 'station' => 'Station Principale', 'type' => 'Maintenance', 'status' => 'terminée', 'date' => now()->subDays(1)],
                ['id' => 2, 'equipement' => 'Pistolet 3', 'station' => 'Station Sud', 'type' => 'Réparation', 'status' => 'en cours', 'date' => now()->subHours(3)],
                ['id' => 3, 'equipement' => 'Volucompteur 2', 'station' => 'Station Ouest', 'type' => 'Calibration', 'status' => 'planifiée', 'date' => now()->addDays(1)],
            ],
            'etat_equipements' => [
                ['type' => 'Pompes', 'total' => 15, 'actifs' => 12, 'maintenance' => 2, 'panne' => 1],
                ['type' => 'Pistolets', 'total' => 45, 'actifs' => 42, 'maintenance' => 2, 'panne' => 1],
                ['type' => 'Volucompteurs', 'total' => 15, 'actifs' => 14, 'maintenance' => 1, 'panne' => 0],
            ]
        ];

        return view('technician.dashboard', $data);
    }
}
