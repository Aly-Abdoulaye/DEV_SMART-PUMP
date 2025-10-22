@extends('layouts.app')

@section('title', 'Tableau de bord Admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-tachometer-alt me-2"></i>Tableau de bord
            </h1>
            <span class="badge bg-primary fs-6">{{ $company->name }}</span>
        </div>
    </div>
</div>

<div class="row">
    <!-- Cartes de statistiques -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
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
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Ventes Aujourd'hui</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['today_sales'], 0, ',', ' ') }} FCFA</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
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
                            Employés</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_employees'] }}</div>
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
                            Stations Actives</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_stations'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stations récentes -->
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Stations Récentes</h6>
                <a href="{{ route('admin.stations.index') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-list me-1"></i>Gérer les stations
                </a>
            </div>
            <div class="card-body">
                @if($stats['recent_stations']->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Adresse</th>
                                    <th>Téléphone</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['recent_stations'] as $station)
                                <tr>
                                    <td>{{ $station->name }}</td>
                                    <td>{{ $station->address }}</td>
                                    <td>{{ $station->phone ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge {{ $station->is_active ? 'bg-success' : 'bg-danger' }}">
                                            {{ $station->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.stations.show', $station) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center text-muted">Aucune station créée pour le moment.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection