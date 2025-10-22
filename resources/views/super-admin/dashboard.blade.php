@extends('layouts.super-admin')

@section('title', 'Tableau de bord Super Admin')
@section('page-title', 'Tableau de bord Super Admin')

@section('page-actions')
    <div class="btn-group me-2">
        <a href="{{ route('super-admin.reports.index') }}" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-download me-1"></i>Exporter Rapports
        </a>
        <a href="{{ route('super-admin.system.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-cog me-1"></i>Système
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <!-- Supervision globale -->
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
                            Stations Total</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_stations'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-gas-pump fa-2x text-gray-300"></i>
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
                            Utilisateurs Total</div>
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
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Volume Vendu (L)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_volume'] ?? 0, 0, ',', ' ') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-oil-can fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alertes et supervision globale -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>Supervision Globale - Alertes
                </h6>
                <span class="badge bg-danger">{{ $stats['pending_tickets'] + $stats['suspended_companies'] + $stats['expiring_subscriptions'] }} Alertes</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-times-circle me-2"></i>Entreprises suspendues</h6>
                            <p class="mb-0">{{ $stats['suspended_companies'] ?? 0 }} entreprise(s)</p>
                            <small><a href="{{ route('super-admin.companies.index') }}?status=suspended">Voir la liste</a></small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-clock me-2"></i>Abonnements expirant</h6>
                            <p class="mb-0">{{ $stats['expiring_subscriptions'] ?? 0 }} abonnement(s)</p>
                            <small><a href="{{ route('super-admin.subscriptions.index') }}">Gérer les abonnements</a></small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-ticket-alt me-2"></i>Tickets support en attente</h6>
                            <p class="mb-0">{{ $stats['pending_tickets'] ?? 0 }} ticket(s)</p>
                            <small><a href="{{ route('super-admin.support.index') }}">Traiter les tickets</a></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques de performances -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-line me-2"></i>Performances Globales
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 mb-3">
                        <div class="card border-left-success">
                            <div class="card-body">
                                <div class="text-success">Entreprises Actives</div>
                                <div class="h5">{{ $stats['active_companies'] ?? 0 }}/{{ $stats['total_companies'] }}</div>
                                <small class="text-muted">{{ number_format(($stats['active_companies']/$stats['total_companies'])*100, 1) }}%</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border-left-primary">
                            <div class="card-body">
                                <div class="text-primary">Ventes Total</div>
                                <div class="h5">{{ number_format($stats['total_sales'] ?? 0, 0, ',', ' ') }}</div>
                                <small class="text-muted">Transactions</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border-left-info">
                            <div class="card-body">
                                <div class="text-info">Chiffre d'Affaires</div>
                                <div class="h5">{{ number_format($stats['total_revenue'] ?? 0, 0, ',', ' ') }} FCFA</div>
                                <small class="text-muted">Cumulé</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border-left-warning">
                            <div class="card-body">
                                <div class="text-warning">Activités Suspectes</div>
                                <div class="h5">{{ $stats['suspicious_activities'] ?? 0 }}</div>
                                <small class="text-muted"><a href="{{ route('super-admin.audit.suspicious') }}">Voir l'audit</a></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-bolt me-2"></i>Actions Rapides
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('super-admin.companies.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>Nouvelle Entreprise
                    </a>
                    <a href="{{ route('super-admin.support.broadcast.form') }}" class="btn btn-info btn-sm">
                        <i class="fas fa-bullhorn me-1"></i>Notification Globale
                    </a>
                    <a href="{{ route('super-admin.backup.index') }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-database me-1"></i>Sauvegarde
                    </a>
                    <a href="{{ route('super-admin.audit.index') }}" class="btn btn-dark btn-sm">
                        <i class="fas fa-shield-alt me-1"></i>Audit Sécurité
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Activité récente -->
<div class="row">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-building me-2"></i>Entreprises Récentes
                </h6>
                <a href="{{ route('super-admin.companies.index') }}" class="btn btn-sm btn-primary">Voir tout</a>
            </div>
            <div class="card-body">
                @if($stats['recent_companies']->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($stats['recent_companies'] as $company)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">{{ $company->name }}</h6>
                                <small class="text-muted">{{ $company->email }}</small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-{{ $company->is_active ? 'success' : 'danger' }} mb-1">
                                    {{ $company->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                                <br>
                                <small class="text-muted">{{ $company->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-muted">Aucune entreprise récente</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-ticket-alt me-2"></i>Tickets Support Récents
                </h6>
                <a href="{{ route('super-admin.support.index') }}" class="btn btn-sm btn-primary">Voir tout</a>
            </div>
            <div class="card-body">
                @if($stats['recent_tickets']->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($stats['recent_tickets'] as $ticket)
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ Str::limit($ticket->subject, 40) }}</h6>
                                <span class="badge bg-{{ $ticket->getStatusColorAttribute() }}">
                                    {{ $ticket->status }}
                                </span>
                            </div>
                            <p class="mb-1 small">{{ $ticket->company->name }}</p>
                            <small class="text-muted">{{ $ticket->created_at->diffForHumans() }}</small>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-muted">Aucun ticket récent</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection