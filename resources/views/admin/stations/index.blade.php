@extends('layouts.admin')

@section('title', 'Gestion des Stations')
@section('page-title', 'Gestion des Stations')
@section('page-subtitle', 'Supervision de toutes vos stations')

@section('page-actions')
    <div class="btn-group">
        <a href="{{ route('admin.stations.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Nouvelle Station
        </a>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Cartes de statistiques -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Stations Total
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stations->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-gas-pump fa-2x text-gray-300"></i>
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
                                Stations Actives
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stations->where('is_active', true)->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                Ventes Aujourd'hui
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stations->sum('today_sales_count') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
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
                                Employés Total
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stations->sum('employees_count') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des stations -->
    <div class="card shadow">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-1"></i> Liste des Stations
            </h6>
        </div>
        <div class="card-body">
            @if($stations->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-gas-pump fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">Aucune station enregistrée</h4>
                    <p class="text-muted mb-4">Commencez par créer votre première station pour gérer vos opérations</p>
                    <a href="{{ route('admin.stations.create') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i> Créer votre première Station
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped" id="stationsTable">
                        <thead class="thead-light">
                            <tr>
                                <th>Nom</th>
                                <th>Adresse</th>
                                <th>Manager</th>
                                <th>Employés</th>
                                <th>Cuves</th>
                                <th>Ventes Aujourd'hui</th>
                                <th>Statut</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stations as $station)
                            <tr>
                                <td>
                                    <strong class="d-block">{{ $station->name }}</strong>
                                    @if($station->phone)
                                    <small class="text-muted">
                                        <i class="fas fa-phone me-1"></i>{{ $station->phone }}
                                    </small>
                                    @endif
                                </td>
                                <td>{{ Str::limit($station->address, 50) }}</td>
                                <td>
                                    @if($station->managers->isNotEmpty())
                                        @foreach($station->managers as $manager)
                                            <span class="badge bg-info">
                                                <i class="fas fa-user me-1"></i>{{ $manager->name }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-user-slash me-1"></i>Non assigné
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $station->employees_count }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary">{{ $station->tanks_count }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success">{{ $station->today_sales_count }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $station->is_active ? 'success' : 'danger' }}">
                                        <i class="fas fa-{{ $station->is_active ? 'check' : 'times' }}-circle me-1"></i>
                                        {{ $station->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm w-100">
                                        <a href="{{ route('admin.stations.show', $station) }}"
                                           class="btn btn-info"
                                           title="Détails de la station">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.stations.edit', $station) }}"
                                           class="btn btn-warning"
                                           title="Modifier la station">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.stations.toggle-status', $station) }}"
                                              method="POST"
                                              class="d-inline">
                                            @csrf
                                            <button type="submit"
                                                    class="btn btn-{{ $station->is_active ? 'secondary' : 'success' }}"
                                                    title="{{ $station->is_active ? 'Désactiver' : 'Activer' }} la station">
                                                <i class="fas fa-{{ $station->is_active ? 'pause' : 'play' }}"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.border-left-primary { border-left: 4px solid #4e73df; }
.border-left-success { border-left: 4px solid #1cc88a; }
.border-left-info { border-left: 4px solid #36b9cc; }
.border-left-warning { border-left: 4px solid #f6c23e; }

.station-card {
    transition: transform 0.2s;
}
.station-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.table th {
    border-top: none;
    font-weight: 600;
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
}
</style>
@endsection
