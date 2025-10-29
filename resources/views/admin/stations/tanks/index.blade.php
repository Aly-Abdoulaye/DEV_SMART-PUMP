@extends('layouts.admin')

@section('title', 'Gestion des Cuves - ' . $station->name)
@section('page-title', 'Gestion des Cuves')
@section('page-subtitle', $station->name)

@section('page-actions')
    <div class="btn-group">
        <a href="{{ route('admin.stations.show', $station) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Retour à la station
        </a>
        <a href="{{ route('admin.stations.tanks.create', $station) }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Nouvelle Cuve
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
                                Cuves Total
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $tanks->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-oil-can fa-2x text-gray-300"></i>
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
                                Volume Total
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($totalVolume) }} L
                            </div>
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
                                Capacité Totale
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($totalCapacity) }} L
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-pie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-{{ $lowStockTanks > 0 ? 'warning' : 'success' }} shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">
                                Alertes Stock
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $lowStockTanks }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des cuves -->
    <div class="card shadow">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-1"></i> Liste des Cuves
            </h6>
        </div>
        <div class="card-body">
            @if($tanks->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-oil-can fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">Aucune cuve enregistrée</h4>
                    <p class="text-muted mb-4">Commencez par créer votre première cuve</p>
                    <a href="{{ route('admin.stations.tanks.create', $station) }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i> Créer une Cuve
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th>Type Carburant</th>
                                <th>Volume Actuel</th>
                                <th>Capacité</th>
                                <th>Niveau</th>
                                <th>Pompes</th>
                                <th>Statut</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tanks as $tank)
                            <tr>
                                <td>
                                    <strong>{{ $tank->fuel_type }}</strong>
                                </td>
                                <td>
                                    <strong>{{ number_format($tank->current_volume, 0) }} L</strong>
                                </td>
                                <td>{{ number_format($tank->capacity, 0) }} L</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 20px;">
                                            @php
                                                $percentage = $tank->percentage;
                                                $progressClass = $percentage < 20 ? 'bg-danger' : ($percentage < 50 ? 'bg-warning' : 'bg-success');
                                            @endphp
                                            <div class="progress-bar {{ $progressClass }}" role="progressbar"
                                                 style="width: {{ $percentage }}%;"
                                                 aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                                {{ number_format($percentage, 1) }}%
                                            </div>
                                        </div>
                                        @if($tank->isLow())
                                            <i class="fas fa-exclamation-triangle text-warning" title="Stock bas"></i>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $tank->pumps->count() }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $tank->is_active ? 'success' : 'danger' }}">
                                        {{ $tank->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm w-100">
                                        <a href="{{ route('admin.stations.tanks.show', [$station, $tank]) }}"
                                           class="btn btn-info" title="Détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.stations.tanks.edit', [$station, $tank]) }}"
                                           class="btn btn-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.stations.tanks.toggle-status', [$station, $tank]) }}"
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit"
                                                    class="btn btn-{{ $tank->is_active ? 'secondary' : 'success' }}"
                                                    title="{{ $tank->is_active ? 'Désactiver' : 'Activer' }}">
                                                <i class="fas fa-{{ $tank->is_active ? 'pause' : 'play' }}"></i>
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

.progress {
    border-radius: 10px;
}
.progress-bar {
    border-radius: 10px;
    font-size: 0.75rem;
}
</style>
@endsection
