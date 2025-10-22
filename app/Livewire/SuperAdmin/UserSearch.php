<?php
// app/Livewire/SuperAdmin/UserSearch.php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Role;
use App\Models\Company;

class UserSearch extends Component
{
    use WithPagination;

    public $search = '';
    public $roleFilter = '';
    public $statusFilter = '';
    public $companyFilter = '';
    public $perPage = 20;

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'companyFilter' => ['except' => ''],
        'perPage' => ['except' => 20]
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingCompanyFilter()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'roleFilter', 'statusFilter', 'companyFilter']);
        $this->resetPage();
    }

    public function toggleUserStatus($userId)
    {
        $user = User::findOrFail($userId);

        // Empêcher la désactivation de son propre compte
        if ($user->id === auth()->id()) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Vous ne pouvez pas désactiver votre propre compte.'
            ]);
            return;
        }

        $user->update(['is_active' => !$user->is_active]);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $user->is_active ? 'Utilisateur activé avec succès.' : 'Utilisateur désactivé avec succès.'
        ]);
    }

    public function render()
    {
        $query = User::with(['role', 'company'])
                    ->withCount(['loginHistory']);

        // Appliquer les filtres
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->roleFilter) {
            $query->where('role_id', $this->roleFilter);
        }

        if ($this->statusFilter === 'active') {
            $query->where('is_active', true);
        } elseif ($this->statusFilter === 'inactive') {
            $query->where('is_active', false);
        }

        if ($this->companyFilter) {
            $query->where('company_id', $this->companyFilter);
        }

        $users = $query->latest()->paginate($this->perPage);
        $roles = Role::active()->ordered()->get();
        $companies = Company::where('is_active', true)->get();

        // Statistiques en temps réel
        $stats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
            'recent' => User::where('last_login_at', '>=', now()->subDays(7))->count(),
        ];

        return view('livewire.super-admin.user-search', compact('users', 'roles', 'companies', 'stats'));
    }
}
