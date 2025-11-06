<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagerDashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        // Récupérer l'utilisateur connecté et sa station
        $user = Auth::user();
        $station = $user->station; // Supposant une relation User -> Station

        // Données simulées pour le dashboard
        $data = [
            'station' => $station,
            'ventes_jour' => 285000,
            'volume_vendu' => 1250,
            'depenses_jour' => 45000,
            'clients_servis' => 89,
            'niveau_essence' => 65,
            'niveau_diesel' => 45,
            'niveau_kerosene' => 15,
        ];

        return view('manager.dashboard', $data);
    }
}
