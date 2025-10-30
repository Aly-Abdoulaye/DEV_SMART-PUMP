@extends('layouts.admin')

@section('title', 'Gestion des Pompes - ' . $station->name)
@section('page-title', 'Gestion des Pompes')
@section('page-subtitle', $station->name)

@section('page-actions')
    <div class="btn-group">
        <a href="{{ route('admin.stations.show', $station) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Retour à la station
        </a>
        <a href="{{ route('admin.stations.pumps.create', $station) }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Nouvelle Pompe
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
                                Pompes Total
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pumps->count() }}</div>
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
                                Pompes Actives
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $activePumps }}
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
                                Pompes Associées
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $pumpsWithTank }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-link fa-2x text-gray-300"></i>
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
                                En Maintenance
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $pumps->where('status', 'maintenance')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tools fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des pompes -->
    <div class="card shadow">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-1"></i> Liste des Pompes
            </h6>
        </div>
        <div class="card-body">
            @if($pumps->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-gas-pump fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">Aucune pompe enregistrée</h4>
                    <p class="text-muted mb-4">Commencez par créer votre première pompe</p>
                    <a href="{{ route('admin.stations.pumps.create', $station) }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i> Créer une Pompe
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th>Numéro Pompe</th>
                                <th>Buse</th>
                                <th>Cuve Associée</th>
                                <th>Index Actuel</th>
                                <th>Total Ventes</th>
                                <th>Statut</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pumps as $pump)
                            <tr>
                                <td>
                                    <strong>Pompe #{{ $pump->pump_number }}</strong>
                                </td>
                                <td>
                                    @if($pump->nozzle_number)
                                        <span class="badge bg-secondary">{{ $pump->nozzle_number }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($pump->tank)
                                        <span class="badge bg-info">
                                            <i class="fas fa-oil-can me-1"></i>
                                            {{ $pump->tank->fuel_type }}
                                        </span>
                                    @else
                                        <span class="badge bg-warning">
                                            <i class="fas fa-unlink me-1"></i>
                                            Non associée
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ number_format($pump->current_index, 0) }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-success">
                                        {{ number_format($pump->total_sales, 0) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $pump->status_color }}">
                                        {{ $pump->status_text }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm w-100">
                                        <a href="{{ route('admin.stations.pumps.show', [$station, $pump]) }}"
                                           class="btn btn-info" title="Détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.stations.pumps.edit', [$station, $pump]) }}"
                                           class="btn btn-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.stations.pumps.update-status', [$station, $pump]) }}"
                                              method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="status" value="{{ $pump->status === 'active' ? 'inactive' : 'active' }}">
                                            <button type="submit"
                                                    class="btn btn-{{ $pump->status === 'active' ? 'secondary' : 'success' }}"
                                                    title="{{ $pump->status === 'active' ? 'Désactiver' : 'Activer' }}">
                                                <i class="fas fa-{{ $pump->status === 'active' ? 'pause' : 'play' }}"></i>
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
</style>
@endsection
