@extends('layouts.admin')

@section('title', 'Tableau de Bord Carburants')

@section('content')

<div class="container-fluid">
    <!-- En-tête avec actions rapides -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-gas-pump text-primary"></i> Tableau de Bord Carburants
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.fuels.analytics') }}" class="btn btn-primary">
                <i class="fas fa-chart-line"></i> Analyses Avancées
            </a>
            <a href="{{ route('admin.fuels.index') }}" class="btn btn-success">
                <i class="fas fa-cog"></i> Gérer les Carburants
            </a>
            <button class="btn btn-info" onclick="refreshDashboard()">
                <i class="fas fa-sync-alt"></i> Actualiser
            </button>
        </div>
    </div>

    <!-- Cartes de statistiques globales -->
    <div class="row">
        <!-- Total Carburants -->
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Types de Carburants</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $overview['total_fuels'] ?? 0 }}
                            </div>
                            <small class="text-success">
                                {{ $overview['active_fuels'] ?? 0 }} actifs
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-gas-pump fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ventes Aujourd'hui -->
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Ventes Aujourd'hui</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{$overview['total_sales_today'] ?? 0 }} L
                            </div>
                            <small class="text-success">
                                {{  $overview['revenue_today_formatted'] ?? ' FCFA'  }} FCFA
                            </small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prix Moyen -->
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Prix Moyen</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{$overview['average_price_formatted'] ?? ' FCFA' }}
                            </div>
                            <small class="text-muted">par litre</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-euro-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertes Stock -->
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Alertes Stock</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $overview['low_stock_alerts'] }}
                            </div>
                            <small class="text-warning">cuves en alerte</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stations Actives -->
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Stations</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ count($stationPerformance) }}
                            </div>
                            <small class="text-muted">stations actives</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-store fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance du jour -->
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                Performance</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ now()->format('d/m') }}
                            </div>
                            <small class="text-muted">mise à jour</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques et données principales -->
    <div class="row">
        <!-- Détail par carburant -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Détail par type de carburant</h6>
                    <small class="text-muted">Données en temps réel</small>
                </div>
                <div class="card-body">
                    @include('admin.fuels.dashboard.partials.fuel-summary-table')
                </div>
            </div>

            <!-- Graphique des tendances -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tendances des ventes (7 derniers jours)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="salesTrendChart" width="100%" height="30"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar avec alertes et performances -->
        <div class="col-lg-4">
            <!-- Alertes stock -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-exclamation-triangle"></i> Alertes Stock Critique
                    </h6>
                    <span class="badge badge-warning">{{ count($lowStockAlerts) }}</span>
                </div>
                <div class="card-body">
                    @include('admin.fuels.dashboard.partials.stock-alerts')
                </div>
            </div>

            <!-- Performances stations -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Performance des stations aujourd'hui</h6>
                </div>
                <div class="card-body">
                    @include('admin.fuels.dashboard.partials.station-performance')
                </div>
            </div>

            <!-- Changements de prix récents -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Changements de prix récents</h6>
                </div>
                <div class="card-body">
                    @include('admin.fuels.dashboard.partials.recent-price-changes')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Fonction pour actualiser le dashboard
function refreshDashboard() {
    window.location.reload();
}

// Graphique des tendances des ventes
// Graphique des tendances des ventes
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('salesTrendChart').getContext('2d');
    
    const salesTrendData = @json($salesTrend);
    
    // Si salesTrend est une collection groupée, nous devons la transformer
    let labels = [];
    let datasets = [];
    
    // Extraire les dates du premier type de carburant
    if (Object.keys(salesTrendData).length > 0) {
        const firstFuel = Object.keys(salesTrendData)[0];
        labels = salesTrendData[firstFuel].data.map(item => item.date_formatted);
        
        // Créer un dataset par type de carburant
        Object.keys(salesTrendData).forEach(fuelType => {
            const fuelData = salesTrendData[fuelType];
            datasets.push({
                label: fuelType,
                data: fuelData.data.map(item => item.quantity),
                borderColor: fuelData.color,
                backgroundColor: fuelData.color + '20',
                tension: 0.4
            });
        });
    }
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: datasets
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Volume des ventes par carburant (L)'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Volume (L)'
                    }
                }
            }
        }
    });
});

// Auto-refresh toutes les 2 minutes
setTimeout(refreshDashboard, 120000);
</script>
@endsection