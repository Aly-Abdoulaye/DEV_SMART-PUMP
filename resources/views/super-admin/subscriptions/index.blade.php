@extends('layouts.super-admin')

@section('title', 'Gestion des Abonnements')
@section('page-title', 'Abonnements et Paiements')

@section('page-actions')
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('super-admin.subscriptions.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-1"></i>Nouvel Abonnement
            </a>
            <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#checkOverdueModal">
                <i class="fas fa-sync me-1"></i>Vérifier Retards
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
                            Entreprises Total</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_companies'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-building fa-2x text-gray-300"></i>
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
                            Abonnements Actifs</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_subscriptions'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                            Paiements En Attente</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_payments'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Revenu Ce Mois</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['monthly_revenue'], 0, ',', ' ') }} FCFA</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alertes importantes -->
@php
    $expiredCount = $stats['expired_subscriptions'];
    $pendingCount = $stats['pending_payments'];
@endphp

@if($expiredCount > 0 || $pendingCount > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-warning shadow">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>Alertes Requiring Attention
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @if($expiredCount > 0)
                    <div class="col-md-6">
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-times-circle me-2"></i>Abonnements Expirés</h6>
                            <p class="mb-0">{{ $expiredCount }} entreprise(s) avec abonnement expiré</p>
                            <small><a href="#expired-section" class="text-danger">Voir la liste</a></small>
                        </div>
                    </div>
                    @endif
                    @if($pendingCount > 0)
                    <div class="col-md-6">
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-clock me-2"></i>Paiements En Attente</h6>
                            <p class="mb-0">{{ $pendingCount }} paiement(s) en attente de validation</p>
                            <small><a href="{{ route('super-admin.payments.index') }}?status=pending" class="text-warning">Gérer les paiements</a></small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Liste des abonnements -->
<div class="card shadow">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Liste des Abonnements</h6>
        <div class="btn-group">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-filter me-1"></i>Filtrer
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="?filter=all">Tous</a></li>
                <li><a class="dropdown-item" href="?filter=active">Actifs</a></li>
                <li><a class="dropdown-item" href="?filter=expired">Expirés</a></li>
                <li><a class="dropdown-item" href="?filter=suspended">Suspendus</a></li>
            </ul>
        </div>
    </div>
    <div class="card-body">
        @if($subscriptions->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Entreprise</th>
                            <th>Plan</th>
                            <th>Expiration</th>
                            <th>Stations</th>
                            <th>Utilisateurs</th>
                            <th>Statut</th>
                            <th>Jours restants</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subscriptions as $company)
                        @php
                            $daysLeft = now()->diffInDays($company->subscription_expires_at, false);
                            $statusColor = $daysLeft > 30 ? 'success' : ($daysLeft > 0 ? 'warning' : 'danger');
                            $isExpired = $daysLeft < 0;
                        @endphp
                        <tr class="{{ $isExpired ? 'table-danger' : '' }}">
                            <td>
                                <strong>{{ $company->name }}</strong>
                                <br>
                                <small class="text-muted">{{ $company->email }}</small>
                            </td>
                            <td>
                                <span class="badge bg-{{ $company->subscription_plan === 'enterprise' ? 'primary' : ($company->subscription_plan === 'premium' ? 'info' : 'secondary') }}">
                                    {{ ucfirst($company->subscription_plan) }}
                                </span>
                            </td>
                            <td>
                                <span class="{{ $isExpired ? 'text-danger fw-bold' : 'text-success' }}">
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
                                <span class="badge bg-{{ $statusColor }}">
                                    {{ $daysLeft > 0 ? $daysLeft . ' jours' : 'Expiré' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('super-admin.subscriptions.show', $company) }}" class="btn btn-info" title="Détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('super-admin.subscriptions.create-payment', $company) }}" class="btn btn-success" title="Nouveau Paiement">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                    @if($company->is_active)
                                        <form action="{{ route('super-admin.subscriptions.suspend', $company) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-warning" title="Suspendre" onclick="return confirm('Suspendre cette entreprise ?')">
                                                <i class="fas fa-pause"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('super-admin.subscriptions.activate', $company) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success" title="Activer" onclick="return confirm('Activer cette entreprise ?')">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-credit-card fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">Aucun abonnement</h4>
                <p class="text-muted">Commencez par créer votre premier abonnement.</p>
                <a href="{{ route('super-admin.subscriptions.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Créer un abonnement
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Section des abonnements expirés -->
@if($expiredCount > 0)
<div class="row mt-4" id="expired-section">
    <div class="col-12">
        <div class="card border-danger shadow">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>Abonnements Expirés ({{ $expiredCount }})
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Entreprise</th>
                                <th>Plan</th>
                                <th>Date d'expiration</th>
                                <th>Jours de retard</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subscriptions->where('subscription_expires_at', '<', now()) as $company)
                            <tr class="table-danger">
                                <td>{{ $company->name }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ ucfirst($company->subscription_plan) }}</span>
                                </td>
                                <td class="text-danger fw-bold">
                                    {{ $company->subscription_expires_at->format('d/m/Y') }}
                                </td>
                                <td>
                                    <span class="badge bg-danger">
                                        {{ abs(now()->diffInDays($company->subscription_expires_at)) }} jours
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('super-admin.subscriptions.show', $company) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('super-admin.subscriptions.create-payment', $company) }}" class="btn btn-success btn-sm">
                                            <i class="fas fa-credit-card"></i> Paiement
                                        </a>
                                        <form action="{{ route('super-admin.subscriptions.suspend', $company) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Suspendre cette entreprise ?')">
                                                <i class="fas fa-pause"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

<!-- Modal pour vérifier les retards -->
<div class="modal fade" id="checkOverdueModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Vérification des Retards de Paiement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Cette action va suspendre automatiquement toutes les entreprises dont l'abonnement a expiré.</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Attention:</strong> {{ $stats['expired_subscriptions'] }} entreprise(s) seront suspendues.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('super-admin.subscriptions.check-overdue') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning">Exécuter la vérification</button>
                </form>
            </div>
        </div>
    </div>
</div>