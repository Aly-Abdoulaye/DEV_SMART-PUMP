<?php
// app/Http/Controllers/Admin/CompanyUserController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Station;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CompanyUserController extends Controller
{
    /**
     * Liste des utilisateurs de l'entreprise
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $role = $request->get('role');

        $users = User::byCompany(auth()->user()->company_id)
            ->with(['role', 'company'])
            ->when($search, function($query, $search) {
                return $query->search($search);
            })
            ->when($role, function($query, $role) {
                return $query->whereHas('role', function($q) use ($role) {
                    $q->where('slug', $role);
                });
            })
            ->orderBy('name')
            ->paginate(20);

        $roles = Role::whereIn('slug', ['station-manager', 'employee', 'technician'])
            ->active()
            ->ordered()
            ->get();

        return view('admin.users.index', compact('users', 'roles', 'search'));
    }

    /**
     * Formulaire de création d'utilisateur
     */
    public function create()
    {
        $roles = Role::whereIn('slug', ['station-manager', 'employee', 'technician'])
            ->active()
            ->ordered()
            ->get();

        $stations = Station::where('company_id', auth()->user()->company_id)
            ->active()
            ->get();

        return view('admin.users.create', compact('roles', 'stations'));
    }

    /**
     * Sauvegarde du nouvel utilisateur
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role_id' => 'required|exists:roles,id',
            'station_ids' => 'nullable|array',
            'station_ids.*' => 'exists:stations,id,company_id,' . auth()->user()->company_id,
            'notes' => 'nullable|string|max:500',
        ]);

        // Vérifier que le rôle est autorisé
        $role = Role::find($validated['role_id']);
        if (!$role || in_array($role->slug, ['super-admin', 'company-admin'])) {
            return back()->with('error', 'Rôle non autorisé');
        }

        $tempPassword = \Str::random(12);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($tempPassword),
            'role_id' => $validated['role_id'],
            'company_id' => auth()->user()->company_id,
            'is_active' => true,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Pour l'instant, on ne gère pas l'assignation des stations
        // Cette fonctionnalité sera ajoutée plus tard avec une table pivot
        // if (!empty($validated['station_ids']) && $user->isStationManager()) {
        //     // À implémenter avec table pivot
        // }

        return redirect()->route('admin.users.index')
            ->with('success', "Utilisateur créé avec succès. Mot de passe temporaire: $tempPassword");
    }

    /**
     * Formulaire d'édition
     */
    public function edit(User $user)
    {
        // Vérifier que l'utilisateur appartient à la même entreprise
        if ($user->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $roles = Role::whereIn('slug', ['station-manager', 'employee', 'technician'])
            ->active()
            ->ordered()
            ->get();

        $stations = Station::where('company_id', auth()->user()->company_id)
            ->active()
            ->get();

        // Pour l'instant, pas de stations assignées
        $userStations = [];

        return view('admin.users.edit', compact('user', 'roles', 'stations', 'userStations'));
    }

    /**
     * Mise à jour de l'utilisateur
     */
    public function update(Request $request, User $user)
    {
        if ($user->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'station_ids' => 'nullable|array',
            'station_ids.*' => 'exists:stations,id,company_id,' . auth()->user()->company_id,
            'notes' => 'nullable|string|max:500',
        ]);

        // Vérifier le rôle
        $role = Role::find($validated['role_id']);
        if (!$role || in_array($role->slug, ['super-admin', 'company-admin'])) {
            return back()->with('error', 'Rôle non autorisé');
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Pour l'instant, on ne gère pas l'assignation des stations
        // if ($user->isStationManager()) {
        //     // À implémenter avec table pivot
        // }

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur modifié avec succès');
    }

    /**
     * Activation/Désactivation
     */
    public function toggleStatus(User $user)
    {
        if ($user->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas désactiver votre propre compte');
        }

        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'activé' : 'désactivé';
        return back()->with('success', "Utilisateur $status avec succès");
    }

    /**
     * Réinitialisation du mot de passe
     */
    public function resetPassword(User $user)
    {
        if ($user->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $tempPassword = \Str::random(12);
        $user->update([
            'password' => Hash::make($tempPassword)
        ]);

        return back()->with('success', "Mot de passe réinitialisé. Nouveau mot de passe: $tempPassword");
    }

    /**
     * Suppression d'utilisateur
     */
    public function destroy(User $user)
    {
        if ($user->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte');
        }

        if (!$user->canBeDeleted()) {
            $errors = $user->getDeletionErrors();
            return back()->with('error', implode(' ', $errors));
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé avec succès');
    }
}
