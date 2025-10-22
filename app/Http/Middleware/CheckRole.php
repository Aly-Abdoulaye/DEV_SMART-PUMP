<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
     public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter.');
        }

        // 🔥 CORRECTION : Normaliser le rôle demandé
        // Convertir super_admin → super-admin
        $normalizedRole = $this->normalizeRoleSlug($role);

        if ($user->hasRole($normalizedRole)) {
            return $next($request);
        }

        $userRole = $user->normalized_role_slug ?? 'non défini';
        abort(403, "Accès refusé. Rôle '{$normalizedRole}' requis. Votre rôle: '{$userRole}'");
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
            // Les autres rôles sans variation
            'admin' => 'company-admin',        // admin → company-admin
            'manager' => 'station-manager',    // manager → station-manager
            'employee' => 'employee',
            'technician' => 'technician',
        ];

        return $mapping[$role] ?? $role;
    }

    /**
     * Vérifier si l'utilisateur a le rôle requis
     */
    private function checkUserRole($user, string $role): bool
    {
        $roleMap = [
            'super_admin' => 'isSuperAdmin',
            'admin' => 'isAdmin',
            'manager' => 'isManager',
            'employee' => 'isEmployee',
            'technician' => 'isTechnician',
        ];

        // Vérifier si le rôle demandé existe dans le mapping
        if (isset($roleMap[$role])) {
            $method = $roleMap[$role];
            return $user->$method();
        }

        // Si le rôle n'existe pas dans le mapping, refuser l'accès
        return false;
    }
}
