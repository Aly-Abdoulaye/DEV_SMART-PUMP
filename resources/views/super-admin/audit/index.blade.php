@extends('layouts.super-admin')

@section('title', 'Audit de Sécurité')
@section('page-title', 'Audit de Sécurité et Conformité')

@section('page-actions')
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('super-admin.audit.suspicious') }}" class="btn btn-sm btn-outline-danger">
                <i class="fas fa-exclamation-triangle me-1"></i>Activités Suspectes
            </a>
            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#exportModal">
                <i class="fas fa-download me-1"></i>Exporter
            </button>
        </div>
    </div>
@endsection

@section('content')
<!-- Statistiques de sécurité -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Activités Aujourd'hui</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['today'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-history fa-2x text-gray-300"></i>
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
                            Activités Suspectes</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['suspicious'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shield-alt fa-2x text-gray-300"></i>
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
                            Utilisateurs Surveillés</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['users'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
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
                            Entreprises Auditées</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['companies'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-building fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Activités suspectes récentes -->
@if($suspiciousActivities->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-danger shadow">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>Activités Suspectes Récentes
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Utilisateur</th>
                                <th>Entreprise</th>
                                <th>Action</th>
                                <th>Raison</th>
                                <th>Niveau de risque</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($suspiciousActivities as $activity)
                            <tr class="table-danger">
                                <td>{{ $activity->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('super-admin.audit.user', $activity->user) }}">
                                        {{ $activity->user->name }}
                                    </a>
                                </td>
                                <td>
                                    @if($activity->company)
                                    <a href="{{ route('super-admin.audit.company', $activity->company) }}">
                                        {{ $activity->company->name }}
                                    </a>
                                    @else
                                    <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>{{ $activity->action }}</td>
                                <td>
                                    <small>{{ $activity->suspicious_reason }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $activity->getRiskColorAttribute() }}">
                                        {{ $activity->risk_level }}
                                    </span>
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

<!-- Journal d'audit complet -->
<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Journal d'Audit Complet</h6>
    </div>
    <div class="card-body">
        @if($logs->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Date & Heure</th>
                            <th>Utilisateur</th>
                            <th>Entreprise</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>IP</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                        <tr class="{{ $log->suspicious ? 'table-warning' : '' }}">
                            <td>
                                <small>{{ $log->created_at->format('d/m/Y H:i:s') }}</small>
                            </td>
                            <td>
                                <a href="{{ route('super-admin.audit.user', $log->user) }}" class="text-decoration-none">
                                    {{ $log->user->name }}
                                </a>
                            </td>
                            <td>
                                @if($log->company)
                                <a href="{{ route('super-admin.audit.company', $log->company) }}" class="text-decoration-none">
                                    {{ $log->company->name }}
                                </a>
                                @else
                                <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $log->getActionTypeAttribute() }}</span>
                            </td>
                            <td>
                                <small>{{ Str::limit($log->description, 80) }}</small>
                            </td>
                            <td>
                                <code>{{ $log->ip_address }}</code>
                            </td>
                            <td>
                                @if($log->suspicious)
                                <span class="badge bg-danger">Suspect</span>
                                @else
                                <span class="badge bg-success">Normal</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $logs->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-shield-alt fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">Aucune activité enregistrée</h4>
                <p class="text-muted">Le journal d'audit est vide pour le moment.</p>
            </div>
        @endif
    </div>
</div>

<!-- Modal d'export -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Exporter le Journal d'Audit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('super-admin.audit.export') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Date de début</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="end_date" class="form-label">Date de fin</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="type" class="form-label">Type d'activités</label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="all">Toutes les activités</option>
                            <option value="suspicious">Activités suspectes seulement</option>
                            <option value="logins">Connexions seulement</option>
                            <option value="changes">Modifications seulement</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Exporter en CSV</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection