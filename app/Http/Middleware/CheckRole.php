<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter.');
        }

        // 🔥 CORRECTION : Définir $userRole
        $userRole = $user->role;

        // 1. Accepter à la fois 'employee' et 'employe'
        if (($role === 'employe' && $userRole === 'employee') || 
            ($role === 'employee' && $userRole === 'employe')) {
            return $next($request);
        }

        // 2. Si l'utilisateur a une méthode hasRole (package spatie/permission)
        if (method_exists($user, 'hasRole')) {
            $normalizedRole = $this->normalizeRoleSlug($role);
            if ($user->hasRole($normalizedRole)) {
                return $next($request);
            }
        }
        // 3. Sinon, vérifier directement l'attribut role
        else if ($user->role === $role) {
            return $next($request);
        }

        abort(403, "Accès refusé. Rôle '{$role}' requis. Votre rôle: '{$userRole}'");
    }

    /**
     * Normalise les slugs de rôle pour la compatibilité
     */
    private function normalizeRoleSlug(string $role): string
    {
        $mapping = [
            'super_admin' => 'super-admin',
            'company_admin' => 'company-admin',
            'station_manager' => 'station-manager',
            'admin' => 'company-admin',
            'manager' => 'station-manager',
            'employee' => 'employee',
            'employe' => 'employee',  // Ajouté
            'technician' => 'technician',
        ];

        return $mapping[$role] ?? $role;
    }

    /**
     * Vérifier si l'utilisateur a le rôle requis (version simplifiée)
     */
    private function checkUserRole($user, string $role): bool
    {
        // Normaliser le rôle pour la comparaison
        $normalizedRole = $this->normalizeRole($role);
        $normalizedUserRole = $this->normalizeRole($user->role);
        
        return $normalizedRole === $normalizedUserRole;
    }

    /**
     * Normaliser les rôles (pour accepter les deux orthographes)
     */
    private function normalizeRole(string $role): string
    {
        // Convertir en minuscule et supprimer les espaces
        $role = strtolower(trim($role));
        
        // Mapping des variantes
        $variants = [
            'employee' => 'employee',  // Version anglaise
            'employe' => 'employee',   // Version française → anglaise
            'employé' => 'employee',   // Avec accent
            'employée' => 'employee',  // Féminin
        ];
        
        return $variants[$role] ?? $role;
    }
}