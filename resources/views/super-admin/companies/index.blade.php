@extends('layouts.super-admin')

@section('title', 'Gestion des Entreprises')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-building me-2"></i>Gestion des Entreprises
    </h1>
    <a href="{{ route('super-admin.companies.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>Nouvelle Entreprise
    </a>
</div>

<div class="card shadow">
    <div class="card-body">
        @if($companies->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Nom</th>
                            <th>Contact</th>
                            <th>Plan</th>
                            <th>Expiration</th>
                            <th>Stations</th>
                            <th>Utilisateurs</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($companies as $company)
                        <tr>
                            <td>
                                <strong>{{ $company->name }}</strong>
                            </td>
                            <td>
                                <div>{{ $company->email }}</div>
                                <small class="text-muted">{{ $company->phone }}</small>
                            </td>
                            <td>
                                <span class="badge bg-{{ $company->subscription_plan === 'enterprise' ? 'primary' : ($company->subscription_plan === 'premium' ? 'info' : 'secondary') }}">
                                    {{ ucfirst($company->subscription_plan) }}
                                </span>
                            </td>
                            <td>
                                <span class="{{ $company->subscription_expires_at->isPast() ? 'text-danger' : 'text-success' }}">
                                    {{ $company->subscription_expires_at->format('d/m/Y') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-dark">{{ $company->stations_count }}</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $company->users_count }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $company->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $company->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('super-admin.companies.show', $company) }}" class="btn btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('super-admin.companies.edit', $company) }}" class="btn btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('super-admin.companies.destroy', $company) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette entreprise ?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-building fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">Aucune entreprise</h4>
                <p class="text-muted">Commencez par créer votre première entreprise.</p>
                <a href="{{ route('super-admin.companies.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Créer une entreprise
                </a>
            </div>
        @endif
    </div>
</div>
@endsection