@extends('layouts.admin')

@section('title', 'Analytics des Carburants')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-bar text-primary"></i> Analytics des Carburants
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.fuels.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
            <!--  -->
        </div>
    </div>

    <!-- Cartes de statistiques -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Carburants
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['total_fuels'] }}
                            </div>
                            <div class="text-xs text-muted">dont {{ $stats['active_fuels'] ?? 0 }} actifs</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-gas-pump fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Activité Prix
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['total_price_changes'] ?? 0 }}
                            </div>
                            <div class="text-xs text-muted">{{ $stats['price_changes_today'] ?? 0 }} aujourd'hui</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exchange-alt fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Prix Moyen
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['avg_price'] ?? 0, 0, ',', ' ') }}
                            </div>
                            <div class="text-xs text-muted">FCFA/L</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-euro-sign fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Cuves Actives
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['total_tanks'] ?? 0 }}
                            </div>
                            <div class="text-xs text-muted">réparties</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-oil-well fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Variation Max
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['max_variation'] ?? 0, 1) }}%
                            </div>
                            <div class="text-xs text-muted">sur 30 jours</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Stabilité
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['stability_rate'] ?? 0 }}%
                            </div>
                            <div class="text-xs text-muted">moyenne</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shield-alt fa-2x text-secondary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Message si pas de données -->
    @if(($stats['total_fuels'] ?? 0) === 0)
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        Aucune donnée de carburant disponible. 
        <a href="{{ route('admin.fuels.create') }}" class="alert-link">Ajoutez votre premier carburant</a> 
        pour voir les analytics.
    </div>
    @else
    <!-- Première ligne : Graphiques principaux -->
    <div class="row mb-4">
        <!-- Évolution des prix en temps réel -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line mr-2"></i>Évolution des Prix (30 derniers jours)
                    </h6>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" 
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-filter"></i> Période
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item period-filter active" href="#" data-period="7">7 jours</a>
                            <a class="dropdown-item period-filter" href="#" data-period="30">30 jours</a>
                            <a class="dropdown-item period-filter" href="#" data-period="90">3 mois</a>
                            <a class="dropdown-item period-filter" href="#" data-period="365">1 an</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="priceEvolutionChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Distribution et statistiques -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie mr-2"></i>Répartition par Carburant
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4">
                        <canvas id="fuelDistributionChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        @foreach($currentPrices as $fuel)
                        <span class="mr-3">
                            <i class="fas fa-circle" style="color: {{ $fuel->auto_color }}"></i> 
                            {{ $fuel->name }}
                        </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Deuxième ligne : Graphiques secondaires -->
    <div class="row mb-4">
        <!-- Comparaison des prix -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-balance-scale mr-2"></i>Comparaison des Prix
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="priceComparisonChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fréquence des changements -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-wave-square mr-2"></i>Fréquence des Changements
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="changeFrequencyChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tendances du marché -->
        <div class="col-xl-4 col-md-12 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-trending-up mr-2"></i>Tendances du Marché
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        @foreach($currentPrices as $index => $fuel)
                        @php
                            $trend = $stats['trends'][$fuel->name] ?? 'stable';
                            $trendIcons = [
                                'up' => ['icon' => 'fa-arrow-up', 'color' => 'text-success'],
                                'down' => ['icon' => 'fa-arrow-down', 'color' => 'text-danger'],
                                'stable' => ['icon' => 'fa-minus', 'color' => 'text-warning']
                            ];
                        @endphp
                        <div class="d-flex justify-content-between align-items-center mb-3 p-2 border rounded">
                            <div class="d-flex align-items-center">
                                <div class="color-indicator mr-2" style="background-color: {{ $fuel->auto_color }}"></div>
                                <span>{{ $fuel->name }}</span>
                            </div>
                            <div class="{{ $trendIcons[$trend]['color'] }}">
                                <i class="fas {{ $trendIcons[$trend]['icon'] }}"></i>
                                <small class="ml-1">{{ ucfirst($trend) }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Troisième ligne : Tableaux détaillés -->
    <div class="row">
        <!-- Derniers changements de prix -->
        <div class="col-xl-8 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history mr-2"></i>Historique Récent des Prix
                    </h6>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary" onclick="exportData('excel')">
                            <i class="fas fa-file-excel"></i> Excel
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="exportData('pdf')">
                            <i class="fas fa-file-pdf"></i> PDF
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="priceHistoryTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>Carburant</th>
                                    <th>Ancien Prix</th>
                                    <th>Nouveau Prix</th>
                                    <th>Variation</th>
                                    <th>Montant Δ</th>
                                    <th>Raison</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPriceChanges as $change)
                                @php
                                    $variation = (($change->new_price - $change->old_price) / $change->old_price) * 100;
                                    $amountChange = $change->new_price - $change->old_price;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="color-indicator mr-2" 
                                                 style="background-color: {{ $change->fuel->auto_color ?? '#007bff' }}"></div>
                                            <span>{{ $change->fuel->name ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td class="text-danger font-weight-bold">
                                        {{ number_format($change->old_price, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="text-success font-weight-bold">
                                        {{ number_format($change->new_price, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $variation >= 0 ? 'success' : 'danger' }}">
                                            <i class="fas fa-arrow-{{ $variation >= 0 ? 'up' : 'down' }} mr-1"></i>
                                            {{ number_format(abs($variation), 2) }}%
                                        </span>
                                    </td>
                                    <td class="{{ $amountChange >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $amountChange >= 0 ? '+' : '' }}{{ number_format($amountChange, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td>
                                        <span class="badge badge-info" title="{{ $change->change_reason }}">
                                            {{ Str::limit($change->change_reason, 20) }}
                                        </span>
                                    </td>
                                    <td class="text-muted">
                                        <small>{{ $change->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="fas fa-history fa-2x mb-3"></i>
                                        <p>Aucun changement de prix récent</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques détaillées et prévisions -->
        <div class="col-xl-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-analytics mr-2"></i>Statistiques Détaillées
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Prix par carburant -->
                    <div class="mb-4">
                        <h6 class="font-weight-bold text-primary mb-3">
                            <i class="fas fa-gas-pump mr-2"></i>Prix Actuels
                        </h6>
                        @foreach($currentPrices as $fuel)
                        <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded bg-light">
                            <div class="d-flex align-items-center">
                                <div class="color-indicator mr-2" style="background-color: {{ $fuel->auto_color }}"></div>
                                <span>{{ $fuel->name }}</span>
                            </div>
                            <span class="font-weight-bold text-success">
                                {{ number_format($fuel->price, 0, ',', ' ') }} FCFA
                            </span>
                        </div>
                        @endforeach
                    </div>

                    <!-- Indicateurs de performance -->
                    <div class="mb-4">
                        <h6 class="font-weight-bold text-primary mb-3">
                            <i class="fas fa-tachometer-alt mr-2"></i>Indicateurs Clés
                        </h6>
                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <div class="border rounded p-2 bg-success text-white">
                                    <div class="text-sm">Prix Minimum</div>
                                    <div class="h5 font-weight-bold mb-0">
                                        {{ number_format($currentPrices->min('price'), 0, ',', ' ') }}
                                    </div>
                                    <small>FCFA</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="border rounded p-2 bg-danger text-white">
                                    <div class="text-sm">Prix Maximum</div>
                                    <div class="h5 font-weight-bold mb-0">
                                        {{ number_format($currentPrices->max('price'), 0, ',', ' ') }}
                                    </div>
                                    <small>FCFA</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-2 bg-warning text-dark">
                                    <div class="text-sm">Écart de Prix</div>
                                    <div class="h5 font-weight-bold mb-0">
                                        {{ number_format($currentPrices->max('price') - $currentPrices->min('price'), 0, ',', ' ') }}
                                    </div>
                                    <small>FCFA</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-2 bg-info text-white">
                                    <div class="text-sm">Prix Moyen</div>
                                    <div class="h5 font-weight-bold mb-0">
                                        {{ number_format($currentPrices->avg('price'), 0, ',', ' ') }}
                                    </div>
                                    <small>FCFA</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Alertes et recommandations -->
                    <div>
                        <h6 class="font-weight-bold text-primary mb-3">
                            <i class="fas fa-bell mr-2"></i>Alertes
                        </h6>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Attention:</strong> Surveillez les tendances du marché
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-lightbulb"></i>
                            <strong>Conseil:</strong> Optimisez vos stocks selon la demande
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Données PHP converties en JavaScript
    const currentPrices = @json($currentPrices ?? []);
    const fuelDistribution = @json($fuelDistribution ?? []);
    const priceEvolution = @json($priceEvolution ?? []);
    const monthlyStats = @json($monthlyStats ?? []);
    const recentPriceChanges = @json($recentPriceChanges ?? []);

    if (currentPrices.length === 0) {
        return;
    }

    // Couleurs par défaut
    const defaultColors = [
        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
        '#858796', '#5a5c69', '#6f42c1', '#e83e8c', '#fd7e14'
    ];

    // 1. Graphique d'évolution des prix
    const evolutionCtx = document.getElementById('priceEvolutionChart');
    if (evolutionCtx) {
        const fuelNames = Object.keys(priceEvolution);
        const datasets = fuelNames.map((fuelName, index) => {
            const fuelData = priceEvolution[fuelName] || [];
            const color = defaultColors[index % defaultColors.length];
            
            // Préparer les données avec les dates
            const data = fuelData.map(entry => ({
                x: new Date(entry.date),
                y: parseFloat(entry.avg_price)
            })).sort((a, b) => a.x - b.x);

            return {
                label: fuelName,
                data: data,
                borderColor: color,
                backgroundColor: color + '20',
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointHoverRadius: 6
            };
        });

        new Chart(evolutionCtx, {
            type: 'line',
            data: { datasets: datasets },
            options: {
                responsive: true,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    x: {
                        type: 'time',
                        time: { unit: 'day' },
                        title: { display: true, text: 'Date' }
                    },
                    y: {
                        beginAtZero: false,
                        title: { display: true, text: 'Prix (FCFA/L)' },
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString() + ' FCFA';
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y.toLocaleString() + ' FCFA';
                            }
                        }
                    },
                    legend: { position: 'top' }
                }
            }
        });
    }

    // 2. Graphique de distribution
    const distributionCtx = document.getElementById('fuelDistributionChart');
    if (distributionCtx) {
        new Chart(distributionCtx, {
            type: 'doughnut',
            data: {
                labels: fuelDistribution.map(fuel => fuel.name),
                datasets: [{
                    data: fuelDistribution.map(fuel => fuel.tanks_count),
                    backgroundColor: fuelDistribution.map((fuel, index) => fuel.color || defaultColors[index]),
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': ' + context.parsed + ' cuves (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }

    // 3. Graphique de comparaison des prix
    const comparisonCtx = document.getElementById('priceComparisonChart');
    if (comparisonCtx) {
        new Chart(comparisonCtx, {
            type: 'bar',
            data: {
                labels: currentPrices.map(fuel => fuel.name),
                datasets: [{
                    label: 'Prix Actuel (FCFA/L)',
                    data: currentPrices.map(fuel => fuel.price),
                    backgroundColor: currentPrices.map((fuel, index) => fuel.auto_color || defaultColors[index]),
                    borderColor: currentPrices.map((fuel, index) => fuel.auto_color || defaultColors[index]),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString() + ' FCFA';
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Prix: ' + context.parsed.y.toLocaleString() + ' FCFA/L';
                            }
                        }
                    }
                }
            }
        });
    }

    // 4. Graphique de fréquence des changements
    const frequencyCtx = document.getElementById('changeFrequencyChart');
    if (frequencyCtx && recentPriceChanges.length > 0) {
        // Compter les changements par carburant
        const changesByFuel = recentPriceChanges.reduce((acc, change) => {
            const fuelName = change.fuel ? change.fuel.name : 'Inconnu';
            acc[fuelName] = (acc[fuelName] || 0) + 1;
            return acc;
        }, {});

        new Chart(frequencyCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(changesByFuel),
                datasets: [{
                    label: 'Nombre de changements',
                    data: Object.values(changesByFuel),
                    backgroundColor: Object.keys(changesByFuel).map((fuel, index) => {
                        const fuelObj = currentPrices.find(f => f.name === fuel);
                        return fuelObj ? fuelObj.auto_color : defaultColors[index];
                    }),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // Filtres de période
    document.querySelectorAll('.period-filter').forEach(filter => {
        filter.addEventListener('click', function(e) {
            e.preventDefault();
            const period = this.getAttribute('data-period');
            
            // Mettre à jour l'interface
            document.querySelectorAll('.period-filter').forEach(f => f.classList.remove('active'));
            this.classList.add('active');
            
            // Ici vous pourriez faire une requête AJAX pour recharger les données
            alert('Filtre ' + period + ' jours appliqué - Rechargement des données...');
        });
    });
});

// Fonctions d'export
function exportData(format) {
    alert('Export ' + format + ' en cours de développement...');
}

// Auto-refresh des données toutes les 5 minutes
setTimeout(function() {
    window.location.reload();
}, 300000); // 5 minutes
</script>

<style>
.color-indicator {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid #dee2e6;
    display: inline-block;
}

.chart-area, .chart-pie, .chart-bar {
    position: relative;
}

.chart-area {
    height: 300px;
}

.chart-pie {
    height: 250px;
}

.chart-bar {
    height: 200px;
}

.card {
    border: none;
    border-radius: 0.5rem;
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}

.card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
}

.period-filter.active {
    background-color: #4e73df;
    color: white;
}

.table th {
    border-top: none;
    font-weight: 600;
}

.alert {
    border: none;
    border-left: 4px solid;
}

.badge {
    font-size: 0.75em;
}

@media print {
    .btn, .dropdown, .card-header .float-right {
        display: none !important;
    }
    
    .card {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
    }
}
</style>
@endsection