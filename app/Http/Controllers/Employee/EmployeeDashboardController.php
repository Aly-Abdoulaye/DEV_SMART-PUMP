<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeDashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = Auth::user();

        // Données simulées pour l'employé
        $data = [
            'user' => $user,
            'ventes_jour' => 185000,
            'volume_vendu' => 420,
            'nombre_ventes' => 32,
            'commission_jour' => 9250,
            'objectif_atteint' => 75,
            'produit_plus_vendu' => 'Essence',
            'dernieres_ventes' => [
                ['heure' => '14:30', 'type' => 'Essence', 'volume' => 25, 'montant' => 18750, 'pompe' => 'P1'],
                ['heure' => '14:15', 'type' => 'Diesel', 'volume' => 40, 'montant' => 26000, 'pompe' => 'P3'],
                ['heure' => '13:45', 'type' => 'Essence', 'volume' => 15, 'montant' => 11250, 'pompe' => 'P2'],
                ['heure' => '13:20', 'type' => 'Kérosène', 'volume' => 10, 'montant' => 6500, 'pompe' => 'P4'],
            ]
        ];

        return view('employee.dashboard', $data);
    }
}
