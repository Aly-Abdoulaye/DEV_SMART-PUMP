@extends('layouts.super-admin')

@section('title', 'Rapports et Statistiques')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-chart-bar me-2"></i>Rapports et Statistiques
    </h1>
    <div class="btn-group">
        <a href="{{ route('super-admin.reports.financial') }}" class="btn btn-outline-primary">
            <i class="fas fa-money-bill-wave me-1"></i>Rapport Financier
        </a>
    </div>
</div>

<!-- Filtres -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Filtres</h6>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="period" class="form-label">Période</label>
                <select class="form-select" id="period" name="period">
                    <option value="day" {{ $period == 'day' ? 'selected' : '' }}>Aujourd'hui</option>
                    <option value="week" {{ $period == 'week' ? 'selected' : '' }}>Cette semaine</option>
                    <option value="month" {{ $period == 'month' ? 'selected' : '' }}>Ce mois</option>
                    <option value="year" {{ $period == 'year' ? 'selected' : '' }}>Cette année</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i>Filtrer
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Cartes de statistiques globales -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Entreprises</div>
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
                            Stations</div>
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
                            Utilisateurs</div>
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
                            Ventes Total</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_sales'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Graphiques -->
<div class="row">
    <!-- Évolution du chiffre d'affaires -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Évolution du Chiffre d'Affaires</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="revenueChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Croissance des entreprises -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Croissance des Entreprises</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="growthChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top entreprises -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Top 5 des Entreprises</h6>
            </div>
            <div class="card-body">
                @if($stats['top_companies']->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Entreprise</th>
                                    <th>Stations</th>
                                    <th>Utilisateurs</th>
                                    <th>Plan</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['top_companies'] as $company)
                                <tr>
                                    <td>
                                        <strong>{{ $company->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $company->email }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $company->stations_count }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $company->users_count }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst($company->subscription_plan) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $company->is_active ? 'bg-success' : 'bg-danger' }}">
                                            {{ $company->is_active ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center text-muted">Aucune donnée disponible.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Activité récente -->
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Activité Récente</h6>
            </div>
            <div class="card-body">
                @if($stats['recent_activity']->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Entreprise</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Détails</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['recent_activity'] as $activity)
                                <tr>
                                    <td>{{ $activity->name }}</td>
                                    <td>
                                        <span class="badge bg-primary">Nouvelle entreprise</span>
                                    </td>
                                    <td>{{ $activity->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $activity->users->count() }} utilisateur(s) créé(s)
                                        </small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center text-muted">Aucune activité récente.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Graphique de revenus
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: @json($stats['revenue_trend']['labels']),
        datasets: [{
            label: 'Chiffre d\'affaires (FCFA)',
            data: @json($stats['revenue_trend']['data']),
            borderColor: '#4e73df',
            backgroundColor: 'rgba(78, 115, 223, 0.1)',
            borderWidth: 2,
            fill: true
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: true
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString('fr-FR') + ' FCFA';
                    }
                }
            }
        }
    }
});

// Graphique de croissance
const growthCtx = document.getElementById('growthChart').getContext('2d');
const growthChart = new Chart(growthCtx, {
    type: 'line',
    data: {
        labels: @json($stats['company_growth']['labels']),
        datasets: [{
            label: 'Nombre d\'entreprises',
            data: @json($stats['company_growth']['data']),
            borderColor: '#1cc88a',
            backgroundColor: 'rgba(28, 200, 138, 0.1)',
            borderWidth: 2,
            fill: true
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: true
            }
        }
    }
});
</script>
@endpush