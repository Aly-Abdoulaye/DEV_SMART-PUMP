{{-- resources/views/admin/sales/statistics.blade.php --}}
@extends('layouts.admin')

@section('title', 'Statistiques des Ventes')

@push('styles')
<style>
    .stat-card {
        border-radius: 10px;
        transition: transform 0.3s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
    .stat-icon {
        font-size: 2.5rem;
        opacity: 0.7;
    }
    .variation-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    .chart-container {
        position: relative;
        height: 300px;
        margin-bottom: 30px;
    }
    .progress-thin {
        height: 6px;
    }
    .ranking-item {
        border-left: 4px solid transparent;
        transition: all 0.3s;
    }
    .ranking-item:hover {
        background-color: #f8f9fa;
        border-left-color: #4e73df;
    }
    .badge-rank {
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-weight: bold;
    }
    .rank-1 { background-color: #ffd700; color: #000; }
    .rank-2 { background-color: #c0c0c0; color: #000; }
    .rank-3 { background-color: #cd7f32; color: #000; }
    .rank-other { background-color: #6c757d; color: #fff; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-gray-800">
                <i class="fas fa-chart-line text-primary me-2"></i>
                Statistiques des Ventes
            </h1>
            <p class="text-muted mb-0">
                Analyse détaillée des performances commerciales
                @if($startDate && $endDate)
                <span class="badge bg-info ms-2">
                    {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}
                </span>
                @endif
            </p>
        </div>
        <div>
            <!-- Filtres rapides -->
            <div class="btn-group">
                <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-calendar-alt me-1"></i> Période
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="?period=today">Aujourd'hui</a></li>
                    <li><a class="dropdown-item" href="?period=yesterday">Hier</a></li>
                    <li><a class="dropdown-item" href="?period=week">Cette semaine</a></li>
                    <li><a class="dropdown-item" href="?period=month">Ce mois</a></li>
                    <li><a class="dropdown-item" href="?period=last_month">Mois dernier</a></li>
                    <li><a class="dropdown-item" href="?period=quarter">Ce trimestre</a></li>
                    <li><a class="dropdown-item" href="?period=year">Cette année</a></li>
                </ul>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="fas fa-filter me-1"></i> Personnaliser
            </button>
        </div>
    </div>

    <!-- Cartes de statistiques générales -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Nombre total de ventes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($generalStats['total_sales'], 0, ',', ' ') }}
                            </div>
                            <div class="mt-2">
                                <span class="text-xs">
                                    <i class="fas fa-calendar-day me-1"></i>
                                    {{ number_format($generalStats['daily_average_sales'], 1, ',', ' ') }}/jour
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300 stat-icon"></i>
                        </div>
                    </div>
                    @if(isset($comparisonStats['variations']['sales']))
                    <div class="mt-2">
                        @php $variation = $comparisonStats['variations']['sales']; @endphp
                        <span class="badge variation-badge {{ $variation >= 0 ? 'bg-success' : 'bg-danger' }}">
                            <i class="fas fa-arrow-{{ $variation >= 0 ? 'up' : 'down' }} me-1"></i>
                            {{ number_format(abs($variation), 1) }}%
                        </span>
                        <small class="text-muted ms-1">vs période précédente</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Chiffre d'Affaires
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($generalStats['total_amount'], 0, ',', ' ') }} FCFA
                            </div>
                            <div class="mt-2">
                                <span class="text-xs">
                                    <i class="fas fa-chart-line me-1"></i>
                                    {{ number_format($generalStats['average_amount'], 0, ',', ' ') }} FCFA/moy.
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300 stat-icon"></i>
                        </div>
                    </div>
                    @if(isset($comparisonStats['variations']['amount']))
                    <div class="mt-2">
                        @php $variation = $comparisonStats['variations']['amount']; @endphp
                        <span class="badge variation-badge {{ $variation >= 0 ? 'bg-success' : 'bg-danger' }}">
                            <i class="fas fa-arrow-{{ $variation >= 0 ? 'up' : 'down' }} me-1"></i>
                            {{ number_format(abs($variation), 1) }}%
                        </span>
                        <small class="text-muted ms-1">vs période précédente</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Volume Total
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                        {{ number_format($generalStats['total_volume'], 2, ',', ' ') }} L
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2">
                                <span class="text-xs">
                                    <i class="fas fa-gas-pump me-1"></i>
                                    {{ number_format($generalStats['average_volume'], 2, ',', ' ') }} L/moy.
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-gas-pump fa-2x text-gray-300 stat-icon"></i>
                        </div>
                    </div>
                    @if(isset($comparisonStats['variations']['volume']))
                    <div class="mt-2">
                        @php $variation = $comparisonStats['variations']['volume']; @endphp
                        <span class="badge variation-badge {{ $variation >= 0 ? 'bg-success' : 'bg-danger' }}">
                            <i class="fas fa-arrow-{{ $variation >= 0 ? 'up' : 'down' }} me-1"></i>
                            {{ number_format(abs($variation), 1) }}%
                        </span>
                        <small class="text-muted ms-1">vs période précédente</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Meilleur Jour
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @if($generalStats['best_day'])
                                    {{ number_format($generalStats['best_day']->total_amount, 0, ',', ' ') }} FCFA
                                @else
                                    N/A
                                @endif
                            </div>
                            <div class="mt-2">
                                <span class="text-xs">
                                    @if($generalStats['best_day'])
                                        <i class="fas fa-calendar-check me-1"></i>
                                        {{ Carbon\Carbon::parse($generalStats['best_day']->date)->format('d/m/Y') }}
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-trophy fa-2x text-gray-300 stat-icon"></i>
                        </div>
                    </div>
                    @if($generalStats['best_day'])
                    <div class="mt-2">
                        <span class="badge variation-badge bg-info">
                            {{ $generalStats['best_day']->sales_count }} ventes
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques principaux -->
    <div class="row mb-4">
        <!-- Évolution des ventes -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line me-2"></i>Évolution des ventes
                    </h6>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary active" data-metric="amount">
                            Chiffre d'Affaires
                        </button>
                        <button type="button" class="btn btn-outline-primary" data-metric="sales">
                            Nombre de ventes
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="evolutionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Répartition par mode de paiement -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>Modes de paiement
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="paymentMethodsChart"></canvas>
                    </div>
                    <div class="mt-3">
                        @foreach($statsByPaymentMethod as $payment)
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span>{{ $payment['method_label'] }}</span>
                                <span class="fw-bold">{{ number_format($payment['percentage'], 1) }}%</span>
                            </div>
                            <div class="progress progress-thin">
                                <div class="progress-bar" role="progressbar" 
                                     style="width: {{ $payment['percentage'] }}%"></div>
                            </div>
                            <small class="text-muted">
                                {{ number_format($payment['total_amount'], 0, ',', ' ') }} FCFA
                                ({{ $payment['sales_count'] }} ventes)
                            </small>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableaux de classement -->
    <div class="row mb-4">
        <!-- Top 5 des pompes -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-gas-pump me-2"></i>Top 5 des pompes
                    </h6>
                    <span class="badge bg-primary">Par chiffre d'affaires</span>
                </div>
                <div class="card-body">
                    @forelse($topPumps as $index => $pump)
                    <div class="ranking-item p-3 mb-2 border rounded">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="badge-rank rank-{{ $index + 1 }}">
                                    {{ $index + 1 }}
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    Pompe {{ $pump['pump']->numero ?? 'N/A' }}
                                    <small class="text-muted">
                                        ({{ $pump['pump']->fuel->name ?? 'N/A' }})
                                    </small>
                                </h6>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">
                                        {{ number_format($pump['sales_count'], 0, ',', ' ') }} ventes
                                    </span>
                                    <span class="fw-bold text-success">
                                        {{ number_format($pump['total_amount'], 0, ',', ' ') }} FCFA
                                    </span>
                                </div>
                                <div class="progress progress-thin mt-2">
                                    @php
                                        $maxAmount = $topPumps->max('total_amount');
                                        $percentage = $maxAmount > 0 ? ($pump['total_amount'] / $maxAmount) * 100 : 0;
                                    @endphp
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="fas fa-gas-pump fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Aucune donnée disponible</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Top 5 des employés -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-users me-2"></i>Top 5 des employés
                    </h6>
                    <span class="badge bg-primary">Par chiffre d'affaires</span>
                </div>
                <div class="card-body">
                    @forelse($topEmployees as $index => $employee)
                    <div class="ranking-item p-3 mb-2 border rounded">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="badge-rank rank-{{ $index + 1 }}">
                                    {{ $index + 1 }}
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    {{ $employee['employee']->name ?? 'Employé inconnu' }}
                                </h6>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">
                                        {{ number_format($employee['sales_count'], 0, ',', ' ') }} ventes
                                    </span>
                                    <span class="fw-bold text-success">
                                        {{ number_format($employee['total_amount'], 0, ',', ' ') }} FCFA
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <small class="text-muted">
                                        Moyenne: {{ number_format($employee['average_amount'], 0, ',', ' ') }} FCFA
                                    </small>
                                    <small class="text-muted">
                                        Volume: {{ number_format($employee['total_volume'], 2, ',', ' ') }} L
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Aucune donnée disponible</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Performance par station -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar me-2"></i>Performance par station
                    </h6>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary active" data-chart="bar">
                            Barres
                        </button>
                        <button type="button" class="btn btn-outline-primary" data-chart="pie">
                            Circulaire
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="stationsChart"></canvas>
                    </div>
                    <div class="table-responsive mt-4">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Station</th>
                                    <th class="text-center">Nombre de ventes</th>
                                    <th class="text-center">Volume (L)</th>
                                    <th class="text-center">Chiffre d'Affaires</th>
                                    <th class="text-center">Part du marché</th>
                                    <th class="text-center">Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($statsByStation as $station)
                                @php
                                    $percentage = $generalStats['total_amount'] > 0 
                                        ? ($station['total_amount'] / $generalStats['total_amount']) * 100 
                                        : 0;
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $station['station']->name }}</strong>
                                    </td>
                                    <td class="text-center">
                                        {{ number_format($station['sales_count'], 0, ',', ' ') }}
                                    </td>
                                    <td class="text-center">
                                        {{ number_format($station['total_volume'], 2, ',', ' ') }}
                                    </td>
                                    <td class="text-center fw-bold text-success">
                                        {{ number_format($station['total_amount'], 0, ',', ' ') }}
                                    </td>
                                    <td class="text-center">
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-info" role="progressbar" 
                                                 style="width: {{ $percentage }}%">
                                                {{ number_format($percentage, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $avgPerStation = $generalStats['total_amount'] / max(count($statsByStation), 1);
                                            $performance = $avgPerStation > 0 
                                                ? ($station['total_amount'] / $avgPerStation) * 100 
                                                : 0;
                                        @endphp
                                        @if($performance >= 120)
                                        <span class="badge bg-success">
                                            <i class="fas fa-arrow-up me-1"></i> Excellente
                                        </span>
                                        @elseif($performance >= 80)
                                        <span class="badge bg-primary">
                                            <i class="fas fa-check me-1"></i> Bonne
                                        </span>
                                        @else
                                        <span class="badge bg-warning">
                                            <i class="fas fa-exclamation me-1"></i> À améliorer
                                        </span>
                                        @endif
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
</div>

<!-- Modal de filtres -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filtrer les statistiques</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="GET" action="{{ route('admin.sales.statistics') }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Date de début</label>
                        <input type="date" name="start_date" class="form-control" 
                               value="{{ $startDate->format('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date de fin</label>
                        <input type="date" name="end_date" class="form-control" 
                               value="{{ $endDate->format('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Station</label>
                        <select name="station_id" class="form-select">
                            <option value="">Toutes les stations</option>
                            @foreach($stations as $station)
                            <option value="{{ $station->id }}">
                                {{ $station->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Appliquer les filtres</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphique d'évolution
    const evolutionCtx = document.getElementById('evolutionChart').getContext('2d');
    const evolutionChart = new Chart(evolutionCtx, {
        type: 'line',
        data: {
            labels: @json($chartData['evolution']['labels']),
            datasets: [{
                label: 'Chiffre d\'Affaires (FCFA)',
                data: @json($chartData['evolution']['amounts']),
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                fill: true,
                tension: 0.4
            }, {
                label: 'Nombre de ventes',
                data: @json($chartData['evolution']['sales']),
                borderColor: '#1cc88a',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                fill: true,
                tension: 0.4,
                hidden: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Graphique des modes de paiement
    const paymentCtx = document.getElementById('paymentMethodsChart').getContext('2d');
    const paymentChart = new Chart(paymentCtx, {
        type: 'doughnut',
        data: {
            labels: @json($chartData['payment_methods']['labels']),
            datasets: [{
                data: @json($chartData['payment_methods']['data']),
                backgroundColor: @json($chartData['payment_methods']['backgroundColors']),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                    }
                }
            }
        }
    });

    // Graphique des stations
    const stationsCtx = document.getElementById('stationsChart').getContext('2d');
    let stationsChart = new Chart(stationsCtx, {
        type: 'bar',
        data: {
            labels: @json($chartData['stations']['labels']),
            datasets: [{
                label: 'Chiffre d\'Affaires (FCFA)',
                data: @json($chartData['stations']['data']),
                backgroundColor: '#4e73df',
                borderColor: '#4e73df',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    },
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' FCFA';
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Gestion des boutons de changement de métrique
    document.querySelectorAll('[data-metric]').forEach(button => {
        button.addEventListener('click', function() {
            const metric = this.getAttribute('data-metric');
            
            // Mettre à jour l'état actif des boutons
            document.querySelectorAll('[data-metric]').forEach(btn => {
                btn.classList.remove('active');
            });
            this.classList.add('active');
            
            // Mettre à jour le graphique
            if (metric === 'sales') {
                evolutionChart.data.datasets[0].hidden = true;
                evolutionChart.data.datasets[1].hidden = false;
            } else {
                evolutionChart.data.datasets[0].hidden = false;
                evolutionChart.data.datasets[1].hidden = true;
            }
            evolutionChart.update();
        });
    });

    // Gestion du changement de type de graphique pour les stations
    document.querySelectorAll('[data-chart]').forEach(button => {
        button.addEventListener('click', function() {
            const chartType = this.getAttribute('data-chart');
            
            // Mettre à jour l'état actif des boutons
            document.querySelectorAll('[data-chart]').forEach(btn => {
                btn.classList.remove('active');
            });
            this.classList.add('active');
            
            // Détruire l'ancien graphique
            stationsChart.destroy();
            
            // Créer un nouveau graphique avec le type sélectionné
            stationsChart = new Chart(stationsCtx, {
                type: chartType,
                data: {
                    labels: @json($chartData['stations']['labels']),
                    datasets: [{
                        label: 'Chiffre d\'Affaires (FCFA)',
                        data: @json($chartData['stations']['data']),
                        backgroundColor: chartType === 'pie' 
                            ? ['#4e73df', '#1cc88a', '#f6c23e', '#36b9cc', '#e74a3b', '#6f42c1']
                            : '#4e73df',
                        borderColor: '#4e73df',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    },
                    scales: chartType === 'bar' ? {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false
                            },
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString() + ' FCFA';
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    } : {}
                }
            });
        });
    });

    // Exporter les graphiques
    document.getElementById('exportCharts')?.addEventListener('click', function() {
        // Implémenter l'exportation des graphiques si nécessaire
        console.log('Exportation des graphiques...');
    });
});
</script>
@endpush