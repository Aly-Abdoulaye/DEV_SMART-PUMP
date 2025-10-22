@extends('layouts.super-admin')

@section('title', 'Détails Entreprise')
@section('page-title', 'Détails - ' . $company->name)

@section('page-actions')
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('super-admin.companies.edit', $company) }}" class="btn btn-sm btn-warning">
                <i class="fas fa-edit me-1"></i>Modifier
            </a>
            <a href="{{ route('super-admin.companies.settings.edit', $company) }}" class="btn btn-sm btn-info">
                <i class="fas fa-cog me-1"></i>Paramètres
            </a>
            @if($company->is_active)
                <form action="{{ route('super-admin.companies.suspend', $company) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Suspendre cette entreprise ?')">
                        <i class="fas fa-pause me-1"></i>Suspendre
                    </button>
                </form>
            @else
                <form action="{{ route('super-admin.companies.activate', $company) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Activer cette entreprise ?')">
                        <i class="fas fa-play me-1"></i>Activer
                    </button>
                </form>
            @endif
        </div>
    </div>
@endsection

@section('content')
<div class="row">
    <!-- Informations générales -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle me-2"></i>Informations Générales
                </h6>
                <span class="badge bg-{{ $company->is_active ? 'success' : 'danger' }} fs-6">
                    {{ $company->is_active ? 'ACTIVE' : 'SUSPENDUE' }}
                </span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Nom:</th>
                                <td>{{ $company->name }}</td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td>{{ $company->email }}</td>
                            </tr>
                            <tr>
                                <th>Téléphone:</th>
                                <td>{{ $company->phone ?? 'Non renseigné' }}</td>
                            </tr>
                            <tr>
                                <th>Adresse:</th>
                                <td>{{ $company->address ?? 'Non renseignée' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Plan:</th>
                                <td>
                                    <span class="badge bg-{{ $company->subscription_plan === 'enterprise' ? 'primary' : ($company->subscription_plan === 'premium' ? 'info' : 'secondary') }}">
                                        {{ strtoupper($company->subscription_plan) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Expiration:</th>
                                <td>
                                    <span class="{{ $company->subscription_expires_at->isPast() ? 'text-danger' : 'text-success' }}">
                                        {{ $company->subscription_expires_at->format('d/m/Y') }}
                                        @if($company->subscription_expires_at->isPast())
                                            <small class="text-danger">(Expiré)</small>
                                        @else
                                            <small class="text-muted">({{ $company->subscription_expires_at->diffForHumans() }})</small>
                                        @endif
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Créée le:</th>
                                <td>{{ $company->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Modifiée le:</th>
                                <td>{{ $company->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-bar me-2"></i>Statistiques
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 mb-3">
                        <div class="card border-left-primary">
                            <div class="card-body">
                                <div class="text-primary">Stations</div>
                                <div class="h5">{{ $company->stations_count ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border-left-success">
                            <div class="card-body">
                                <div class="text-success">Utilisateurs</div>
                                <div class="h5">{{ $company->users_count ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border-left-info">
                            <div class="card-body">
                                <div class="text-info">Clients</div>
                                <div class="h5">{{ $company->customers_count ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border-left-warning">
                            <div class="card-body">
                                <div class="text-warning">Ventes</div>
                                <div class="h5">{{ $company->sales_count ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar - Actions rapides -->
    <div class="col-lg-4">
        <!-- Logo et identité -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-image me-2"></i>Identité Visuelle
                </h6>
            </div>
            <div class="card-body text-center">
                @if($company->logo)
                    <img src="{{ Storage::disk('public')->url($company->logo) }}" 
                         alt="Logo {{ $company->name }}" 
                         class="img-fluid rounded mb-3" 
                         style="max-height: 150px;">
                @else
                    <div class="bg-light rounded p-4 mb-3">
                        <i class="fas fa-building fa-3x text-muted"></i>
                        <p class="text-muted mt-2 mb-0">Aucun logo</p>
                    </div>
                @endif
                
                <div class="d-grid gap-2">
                    <a href="{{ route('super-admin.companies.settings.edit', $company) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-palette me-1"></i>Personnaliser l'apparence
                    </a>
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-bolt me-2"></i>Actions Rapides
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <form action="{{ route('super-admin.companies.impersonate', $company) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-info w-100 mb-2" onclick="return confirm('Vous connecter en tant qu\\'admin de cette entreprise ?')">
                            <i class="fas fa-user-secret me-1"></i>Se connecter comme
                        </button>
                    </form>
                    
                    <a href="{{ route('super-admin.subscriptions.show', $company) }}" class="btn btn-warning w-100 mb-2">
                        <i class="fas fa-credit-card me-1"></i>Gérer l'abonnement
                    </a>
                    
                    <a href="{{ route('super-admin.audit.company', $company) }}" class="btn btn-secondary w-100 mb-2">
                        <i class="fas fa-shield-alt me-1"></i>Voir l'audit
                    </a>

                    <form action="{{ route('super-admin.companies.destroy', $company) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Supprimer définitivement cette entreprise ? Cette action est irréversible.')">
                            <i class="fas fa-trash me-1"></i>Supprimer l'entreprise
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Statut de conformité -->
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-check-circle me-2"></i>Conformité
                </h6>
            </div>
            <div class="card-body">
                @php
                    $isCompliant = $company->is_active && !$company->subscription_expires_at->isPast();
                @endphp
                <div class="alert alert-{{ $isCompliant ? 'success' : 'danger' }}">
                    <h6>
                        <i class="fas fa-{{ $isCompliant ? 'check' : 'exclamation-triangle' }} me-2"></i>
                        {{ $isCompliant ? 'Conforme' : 'Non conforme' }}
                    </h6>
                    <ul class="mb-0 small">
                        <li>Abonnement: {{ $company->subscription_expires_at->isPast() ? 'EXPIRÉ' : 'ACTIF' }}</li>
                        <li>Statut: {{ $company->is_active ? 'ACTIF' : 'SUSPENDU' }}</li>
                        <li>Dernière vérification: {{ now()->format('d/m/Y H:i') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stations de l'entreprise -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-gas-pump me-2"></i>Stations ({{ $company->stations->count() }})
                </h6>
                <a href="#" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i>Ajouter une station
                </a>
            </div>
            <div class="card-body">
                @if($company->stations->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nom</th>
                                    <th>Adresse</th>
                                    <th>Téléphone</th>
                                    <th>Statut</th>
                                    <th>Créée le</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($company->stations as $station)
                                <tr>
                                    <td>{{ $station->name }}</td>
                                    <td>{{ $station->address }}</td>
                                    <td>{{ $station->phone ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $station->is_active ? 'success' : 'danger' }}">
                                            {{ $station->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>{{ $station->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="#" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-gas-pump fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Aucune station</h5>
                        <p class="text-muted">Cette entreprise n'a pas encore de stations.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection