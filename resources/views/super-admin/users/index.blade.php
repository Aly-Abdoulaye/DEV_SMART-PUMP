@extends('layouts.super-admin')

@section('title', 'Gestion des Utilisateurs')
@section('page-title', 'Utilisateurs du Système')

@section('page-actions')
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('super-admin.users.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-1"></i>Nouvel Utilisateur
            </a>
            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#filtersModal">
                <i class="fas fa-filter me-1"></i>Filtres
            </button>
        </div>
    </div>
@endsection

@section('content')
<!-- Cartes de statistiques -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Utilisateurs</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_users'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Utilisateurs Actifs</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_users'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Connexions (7j)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['recent_logins'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-sign-in-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Comptes Inactifs</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['inactive_users'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-times fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtres actifs -->
@if($roleFilter || $statusFilter || $companyFilter || $search)
<div class="row mb-3">
    <div class="col-12">
        <div class="card border-info shadow">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-info"><i class="fas fa-filter me-1"></i>Filtres actifs:</small>
                        @if($search)<span class="badge bg-info ms-2">Recherche: "{{ $search }}"</span>@endif
                        @if($roleFilter)<span class="badge bg-info ms-2">Rôle: {{ $roles->firstWhere('id', $roleFilter)->name ?? 'Inconnu' }}</span>@endif
                        @if($statusFilter)<span class="badge bg-info ms-2">Statut: {{ $statusFilter === 'active' ? 'Actif' : 'Inactif' }}</span>@endif
                        @if($companyFilter)<span class="badge bg-info ms-2">Entreprise: {{ $companies->firstWhere('id', $companyFilter)->name ?? 'Inconnue' }}</span>@endif
                    </div>
                    <a href="{{ route('super-admin.users.index') }}" class="btn btn-sm btn-outline-info">
                        <i class="fas fa-times me-1"></i>Effacer
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Liste des utilisateurs -->
<div class="card shadow">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Liste des Utilisateurs</h6>
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="selectAll">
            <label class="form-check-label small" for="selectAll">Tout sélectionner</label>
        </div>
    </div>
    <div class="card-body">
        @if($users->count() > 0)
            <form id="bulkActionsForm" action="{{ route('super-admin.users.bulk-actions') }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="50">
                                    <input type="checkbox" class="form-check-input" id="selectAllCheckbox">
                                </th>
                                <th>Utilisateur</th>
                                <th>Rôle</th>
                                <th>Entreprise</th>
                                <th>Contact</th>
                                <th>Dernière connexion</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input user-checkbox" name="users[]" value="{{ $user->id }}">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3">
                                            <div class="avatar-title bg-{{ $user->is_active ? 'primary' : 'secondary' }} rounded-circle text-white">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div>
                                            <strong>{{ $user->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $user->role->color ?? 'secondary' }}">
                                        {{ $user->role->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    @if($user->company)
                                        <strong>{{ $user->company->name }}</strong>
                                    @else
                                        <span class="text-muted">Aucune</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->phone)
                                        <i class="fas fa-phone me-1 text-muted"></i>{{ $user->phone }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->last_login_at)
                                        <span class="text-success">
                                            {{ $user->last_login_at->format('d/m/Y H:i') }}
                                        </span>
                                        <br>
                                        <small class="text-muted">{{ $user->last_login_at->diffForHumans() }}</small>
                                    @else
                                        <span class="text-muted">Jamais connecté</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $user->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('super-admin.users.show', $user) }}" class="btn btn-info" title="Détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('super-admin.users.edit', $user) }}" class="btn btn-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('super-admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-{{ $user->is_active ? 'warning' : 'success' }}"
                                                    title="{{ $user->is_active ? 'Désactiver' : 'Activer' }}">
                                                <i class="fas fa-{{ $user->is_active ? 'pause' : 'play' }}"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Actions groupées -->
                <div class="mt-3 p-3 bg-light rounded d-none" id="bulkActionsPanel">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <strong id="selectedCount">0</strong> utilisateur(s) sélectionné(s)
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="btn-group">
                                <button type="submit" name="action" value="activate" class="btn btn-success btn-sm">
                                    <i class="fas fa-play me-1"></i>Activer
                                </button>
                                <button type="submit" name="action" value="deactivate" class="btn btn-warning btn-sm">
                                    <i class="fas fa-pause me-1"></i>Désactiver
                                </button>
                                <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Supprimer les utilisateurs sélectionnés ?')">
                                    <i class="fas fa-trash me-1"></i>Supprimer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-users fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">Aucun utilisateur trouvé</h4>
                <p class="text-muted">Aucun utilisateur ne correspond à vos critères de recherche.</p>
                <a href="{{ route('super-admin.users.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Créer le premier utilisateur
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Modal Filtres -->
<div class="modal fade" id="filtersModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filtrer les Utilisateurs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="GET" action="{{ route('super-admin.users.index') }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="search" class="form-label">Recherche</label>
                        <input type="text" class="form-control" id="search" name="search"
                               value="{{ $search }}" placeholder="Nom, email, téléphone...">
                    </div>
                    <div class="mb-3">
                        <label for="role_id" class="form-label">Rôle</label>
                        <select class="form-select" id="role_id" name="role">
                            <option value="">Tous les rôles</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ $roleFilter == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="company_id" class="form-label">Entreprise</label>
                        <select class="form-select" id="company_id" name="company_id">
                            <option value="">Toutes les entreprises</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ $companyFilter == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Statut</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="status_all"
                                       value="" {{ !$statusFilter ? 'checked' : '' }}>
                                <label class="form-check-label" for="status_all">Tous</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="status_active"
                                       value="active" {{ $statusFilter === 'active' ? 'checked' : '' }}>
                                <label class="form-check-label" for="status_active">Actifs</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="status_inactive"
                                       value="inactive" {{ $statusFilter === 'inactive' ? 'checked' : '' }}>
                                <label class="form-check-label" for="status_inactive">Inactifs</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Appliquer les filtres</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Gestion de la sélection multiple
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    const bulkActionsPanel = document.getElementById('bulkActionsPanel');
    const selectedCount = document.getElementById('selectedCount');

    function updateBulkActions() {
        const selectedCountValue = document.querySelectorAll('.user-checkbox:checked').length;
        selectedCount.textContent = selectedCountValue;

        if (selectedCountValue > 0) {
            bulkActionsPanel.classList.remove('d-none');
        } else {
            bulkActionsPanel.classList.add('d-none');
        }
    }

    selectAllCheckbox.addEventListener('change', function() {
        userCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkActions();
    });

    userCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });

    // Empêcher la soumission si aucun utilisateur n'est sélectionné
    document.getElementById('bulkActionsForm').addEventListener('submit', function(e) {
        const selectedCount = document.querySelectorAll('.user-checkbox:checked').length;
        if (selectedCount === 0) {
            e.preventDefault();
            alert('Veuillez sélectionner au moins un utilisateur.');
        }
    });
});
</script>
@endpush
