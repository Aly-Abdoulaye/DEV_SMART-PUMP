{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Gestion des Utilisateurs')

@section('content')
<div class="card">
    <div class="card-header bg-primary text-white">
        <h3 class="card-title">
            <i class="fas fa-users"></i> Gestion des Utilisateurs
        </h3>
        <div class="card-tools">
            <a href="{{ route('admin.users.create') }}" class="btn btn-light btn-sm">
                <i class="fas fa-plus"></i> Nouvel Utilisateur
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Filtres -->
        <div class="row mb-3">
            <div class="col-md-6">
                <form method="GET" class="form-inline">
                    <input type="text" name="search" class="form-control mr-2" placeholder="Rechercher..."
                           value="{{ request('search') }}">
                    <select name="role" class="form-control mr-2">
                        <option value="">Tous les rôles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->slug }}" {{ request('role') == $role->slug ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                </form>
            </div>
        </div>

        <!-- Tableau -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Station(s)</th>
                        <th>Statut</th>
                        <th>Dernière connexion</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle bg-{{ $user->role->color ?? 'secondary' }} mr-3">
                                    {{ $user->initials }}
                                </div>
                                {{ $user->name }}
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge badge-{{ $user->role->color ?? 'secondary' }}">
                                {{ $user->role_name }}
                            </span>
                        </td>
                        <td>
    @if($user->isStationManager())
        {{-- Afficher les stations de la même compagnie --}}
        @php
            $companyStations = \App\Models\Station::where('company_id', $user->company_id)
                ->active()
                ->get();
        @endphp
        @if($companyStations->count() > 0)
            <span class="text-success">
                <i class="fas fa-check-circle"></i> Gère {{ $companyStations->count() }} station(s)
            </span>
        @else
            <span class="text-muted">Aucune station</span>
        @endif
    @else
        <span class="text-muted">-</span>
    @endif
</td>
                        <td>
                            <span class="badge badge-{{ $user->status_color }}">
                                {{ $user->status_text }}
                            </span>
                        </td>
                        <td>{{ $user->last_login_formatted }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-{{ $user->is_active ? 'warning' : 'success' }}">
                                        <i class="fas fa-{{ $user->is_active ? 'pause' : 'play' }}"></i>
                                    </button>
                                </form>

                                <form action="{{ route('admin.users.reset-password', $user) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-info"
                                            onclick="return confirm('Réinitialiser le mot de passe?')">
                                        <i class="fas fa-key"></i>
                                    </button>
                                </form>

                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Supprimer cet utilisateur?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">Aucun utilisateur trouvé</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
}
</style>
@endpush
