<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 🔥 CORRECTION COMPLÈTE : Utiliser les bonnes méthodes de rôle

        if ($user->isSuperAdmin()) {
            return redirect()->route('super-admin.dashboard');
        } elseif ($user->isCompanyAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isStationManager()) {
            return redirect()->route('manager.dashboard');
        } elseif ($user->isEmployee()) {
            return redirect()->route('employee.dashboard');
        } elseif ($user->isTechnician()) {
            return redirect()->route('technician.dashboard');
        }

        // Fallback avec informations de débogage
        $roleInfo = $user->role ? "Rôle: {$user->role->name} (slug: {$user->role->slug})" : "Aucun rôle associé";
        abort(403, "Rôle non reconnu. {$roleInfo}");
    }
}
