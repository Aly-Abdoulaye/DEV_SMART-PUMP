<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Filtres
        $roleFilter = $request->get('role');
        $statusFilter = $request->get('status');
        $companyFilter = $request->get('company_id');
        $search = $request->get('search');

        $query = User::with(['company', 'role'])
                    ->withCount(['loginHistory']);

        // Application des filtres
        if ($roleFilter) {
            $query->where('role_id', $roleFilter);
        }

        if ($statusFilter === 'active') {
            $query->where('is_active', true);
        } elseif ($statusFilter === 'inactive') {
            $query->where('is_active', false);
        }

        if ($companyFilter) {
            $query->where('company_id', $companyFilter);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(25);
        $companies = Company::where('is_active', true)->get();
        $roles = Role::all();

        // Statistiques
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'inactive_users' => User::where('is_active', false)->count(),
            'recent_logins' => User::where('last_login_at', '>=', now()->subDays(7))->count(),
        ];

        return view('super-admin.users.index', compact(
            'users',
            'companies',
            'roles',
            'stats',
            'roleFilter',
            'statusFilter',
            'companyFilter',
            'search'
        ));
    }

    public function create()
    {
        $companies = Company::where('is_active', true)->get();
        $roles = Role::all();

        return view('super-admin.users.create', compact('companies', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => 'required|exists:roles,id',
            'company_id' => 'nullable|exists:companies,id',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
            'company_id' => $validated['company_id'],
            'is_active' => $validated['is_active'] ?? true,
            'notes' => $validated['notes'],
            'email_verified_at' => now(), // Auto-verification pour le Super Admin
        ]);

        return redirect()->route('super-admin.users.show', $user)
            ->with('success', 'Utilisateur créé avec succès.');
    }

    public function show(User $user)
    {
        $user->load(['company', 'role', 'loginHistory' => function($query) {
            $query->latest()->limit(10);
        }]);

        return view('super-admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $companies = Company::where('is_active', true)->get();
        $roles = Role::all();

        return view('super-admin.users.edit', compact('user', 'companies', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'role_id' => 'required|exists:roles,id',
            'company_id' => 'nullable|exists:companies,id',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        $user->update($validated);

        return redirect()->route('super-admin.users.show', $user)
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    public function updatePassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Mot de passe mis à jour avec succès.');
    }

    public function toggleStatus(User $user)
    {
        $user->update([
            'is_active' => !$user->is_active,
        ]);

        $status = $user->is_active ? 'activé' : 'désactivé';
        return back()->with('success', "Utilisateur {$status} avec succès.");
    }

    public function destroy(User $user)
    {
        // Empêcher la suppression de son propre compte
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return redirect()->route('super-admin.users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }

    public function bulkActions(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'users' => 'required|array',
            'users.*' => 'exists:users,id',
        ]);

        $users = User::whereIn('id', $validated['users']);

        switch ($validated['action']) {
            case 'activate':
                $users->update(['is_active' => true]);
                $message = 'Utilisateurs activés avec succès.';
                break;
            case 'deactivate':
                $users->update(['is_active' => false]);
                $message = 'Utilisateurs désactivés avec succès.';
                break;
            case 'delete':
                // Empêcher la suppression de son propre compte
                $users->where('id', '!=', auth()->id())->delete();
                $message = 'Utilisateurs supprimés avec succès.';
                break;
        }

        return back()->with('success', $message);
    }
}
