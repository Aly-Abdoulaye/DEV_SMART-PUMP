{{-- resources/views/livewire/super-admin/user-search.blade.php --}}

<div>
    <!-- En-tête avec statistiques -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Utilisateurs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active'] }}</div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['recent'] }}</div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['inactive'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-times fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barre de recherche et filtres -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Recherche</label>
                    <input type="text"
                           class="form-control"
                           id="search"
                           wire:model.live="search"
                           placeholder="Nom, email, téléphone...">
                </div>

                <div class="col-md-2">
                    <label for="roleFilter" class="form-label">Rôle</label>
                    <select class="form-select" id="roleFilter" wire:model.live="roleFilter">
                        <option value="">Tous les rôles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="statusFilter" class="form-label">Statut</label>
                    <select class="form-select" id="statusFilter" wire:model.live="statusFilter">
                        <option value="">Tous</option>
                        <option value="active">Actifs</option>
                        <option value="inactive">Inactifs</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="companyFilter" class="form-label">Entreprise</label>
                    <select class="form-select" id="companyFilter" wire:model.live="companyFilter">
                        <option value="">Toutes les entreprises</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="perPage" class="form-label">Par page</label>
                    <select class="form-select" id="perPage" wire:model.live="perPage">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>

            <!-- Filtres actifs -->
            @if($search || $roleFilter || $statusFilter || $companyFilter)
            <div class="row mt-3">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-info"><i class="fas fa-filter me-1"></i>Filtres actifs:</small>
                            @if($search)<span class="badge bg-info ms-2">Recherche: "{{ $search }}"</span>@endif
                            @if($roleFilter)<span class="badge bg-info ms-2">Rôle: {{ $roles->firstWhere('id', $roleFilter)->name ?? 'Inconnu' }}</span>@endif
                            @if($statusFilter)<span class="badge bg-info ms-2">Statut: {{ $statusFilter === 'active' ? 'Actif' : 'Inactif' }}</span>@endif
                            @if($companyFilter)<span class="badge bg-info ms-2">Entreprise: {{ $companies->firstWhere('id', $companyFilter)->name ?? 'Inconnue' }}</span>@endif
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-info" wire:click="resetFilters">
                            <i class="fas fa-times me-1"></i>Effacer
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Liste des utilisateurs -->
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                Liste des Utilisateurs
                <span class="badge bg-secondary">{{ $users->total() }}</span>
            </h6>
            <a href="{{ route('super-admin.users.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-1"></i>Nouvel Utilisateur
            </a>
        </div>
        <div class="card-body">
            @if($users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
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
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3">
                                            <div class="avatar-title bg-{{ $user->is_active ? 'primary' : 'secondary' }} rounded-circle text-white">
                                                {{ $user->initials }}
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
                                        <button type="button"
                                                class="btn btn-{{ $user->is_active ? 'warning' : 'success' }}"
                                                title="{{ $user->is_active ? 'Désactiver' : 'Activer' }}"
                                                wire:click="toggleUserStatus({{ $user->id }})"
                                                wire:confirm="Êtes-vous sûr de vouloir {{ $user->is_active ? 'désactiver' : 'activer' }} cet utilisateur ?">
                                            <i class="fas fa-{{ $user->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

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

    <!-- Scripts Livewire -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            // Notification handler
            Livewire.on('notify', (event) => {
                const toast = new bootstrap.Toast(document.getElementById('livewireToast'));
                const toastBody = document.getElementById('toastBody');
                const toastHeader = document.getElementById('toastHeader');

                toastBody.textContent = event.message;

                if (event.type === 'success') {
                    toastHeader.className = 'toast-header text-white bg-success';
                } else if (event.type === 'error') {
                    toastHeader.className = 'toast-header text-white bg-danger';
                } else if (event.type === 'warning') {
                    toastHeader.className = 'toast-header text-white bg-warning';
                } else {
                    toastHeader.className = 'toast-header text-white bg-info';
                }

                toast.show();
            });
        });
    </script>

    <!-- Toast pour notifications -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="livewireToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div id="toastHeader" class="toast-header">
                <strong class="me-auto">Notification</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastBody">
                <!-- Le message sera inséré ici -->
            </div>
        </div>
    </div>
</div>
