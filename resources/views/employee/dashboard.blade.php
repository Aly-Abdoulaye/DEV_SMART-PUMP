{{-- resources/views/employee/dashboard.blade.php --}}
@extends('layouts.employee')

@section('title', 'Tableau de Bord - Employé')

@section('content')
<div class="container-fluid py-4">
    <!-- En-tête avec infos station -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <div class="bg-primary text-white rounded-circle p-3">
                        <i class="fas fa-gas-pump fa-2x"></i>
                    </div>
                </div>
                <div>
                    <h1 class="h4 mb-1">Bonjour, {{ $user->name  }} !</h1>
                    
                    <p class="text-muted mb-0">
                        <i class="fas fa-map-marker-alt me-1"></i>
                        
                        @if(isset($station) && $station)
                            Station : <strong>{{ $station->name }}</strong>
                        @else
                            Station : <strong class="text-warning">Non assignée</strong>
                        @endif
                        
                        | Poste : {{ Auth::user()->roleName }}
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Heure actuelle</h6>
                            <div class="display-6 fw-bold" id="live-clock">{{ now()->format('H:i') }}</div>
                        </div>
                        <div class="text-end">
                            <small>{{ now()->format('d/m/Y') }}</small><br>
                            <div id="attendance-status" class="badge bg-success">
                                <i class="fas fa-check-circle me-1"></i> En service
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cartes de statistiques du jour -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-start-primary h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                Ventes aujourd'hui
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                {{ $todayStats['sales_count'] }}
                            </div>
                            <div class="mt-2">
                                @php
                                    $goalPercentage = $goals['daily_sales'] > 0 
                                        ? ($todayStats['sales_count'] / $goals['daily_sales']) * 100 
                                        : 0;
                                @endphp
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-primary" role="progressbar" 
                                         style="width: {{ min($goalPercentage, 100) }}%"></div>
                                </div>
                                <small class="text-muted">
                                    Objectif : {{ $goals['daily_sales'] }} ventes
                                    ({{ number_format($goalPercentage, 1) }}%)
                                </small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-start-success h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">
                                CA du jour
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                {{ number_format($todayStats['total_amount'], 0, ',', ' ') }} FCFA
                            </div>
                            <div class="mt-2">
                                @php
                                    $amountGoalPercentage = $goals['daily_amount'] > 0 
                                        ? ($todayStats['total_amount'] / $goals['daily_amount']) * 100 
                                        : 0;
                                @endphp
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ min($amountGoalPercentage, 100) }}%"></div>
                                </div>
                                <small class="text-muted">
                                    Objectif : {{ number_format($goals['daily_amount'], 0, ',', ' ') }} FCFA
                                    ({{ number_format($amountGoalPercentage, 1) }}%)
                                </small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-start-info h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">
                                Volume total
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                {{ number_format($todayStats['total_volume'], 2, ',', ' ') }} L
                            </div>
                            <div class="mt-2">
                                <small class="text-muted">
                                    Moyenne par vente : 
                                    {{ number_format($todayStats['average_amount'], 0, ',', ' ') }} FCFA
                                </small>
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
            <div class="card sale-card h-100">
                <div class="card-body">
                    <div class="text-center">
                        <h6 class="text-white mb-3">Nouvelle Vente</h6>
                        <a href="{{ route('employee.sales.create') }}" class="btn btn-light btn-lg w-100">
                            <i class="fas fa-plus-circle me-2"></i>
                            Démarrer une vente
                        </a>
                        <div class="mt-3 text-white-50">
                            <small><i class="fas fa-bolt me-1"></i> Rapide et simple</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pompes disponibles et dernières ventes -->
    <div class="row">
        <!-- Pompes de la station -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-gas-pump me-2"></i>Pompes disponibles
                    </h6>
                    <span class="badge bg-primary">{{ $pumps->count() }} pompes</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($pumps as $pump)
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <div class="d-flex align-items-center">
                                    <div class="p-2 bg-light rounded me-3">
                                        <i class="fas fa-gas-pump text-primary"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Pompe {{ $pump->pump_number }}</h6>
                                        <small class="text-muted">
                                            {{ $pump->fuel->name ?? 'N/A' }}
                                        </small>
                                        <div class="mt-2">
                                            <span class="badge bg-success">
                                                <i class="fas fa-bolt me-1"></i> Disponible
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Dernières ventes -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-history me-2"></i>Dernières ventes
                    </h6>
                    <a href="{{ route('employee.sales.index') }}" class="btn btn-sm btn-outline-primary">
                        Voir tout
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                       @forelse($recentSales as $sale)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">
                                        <i class="fas fa-gas-pump text-primary me-2"></i>
                                        Pompe {{ $sale->pump->pump_number ?? 'N/A' }}
                                    </h6>
                                    <small class="text-muted">
                                        {{ $sale->sale_date->format('H:i') ?? now()->format('H:i') }} | 
                                        {{ number_format($sale->volume ?? 0, 2, ',', ' ') }}L
                                    </small>
                                </div>
                                <div class="text-end">
                                    <strong class="text-success">
                                        {{ number_format($sale->total_amount ?? 0, 0, ',', ' ') }} FCFA
                                    </strong>
                                    <div>
                                        <small class="badge bg-secondary">
                                            {{ $sale->payment_method == 'cash' ? '💵 Espèces' : 
                                            ($sale->payment_method == 'card' ? '💳 Carte' : 
                                            ($sale->payment_method == 'mobile_money' ? '📱 Mobile Money' : '👤 Compte')) }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="list-group-item text-center py-5">
                            <i class="fas fa-cash-register fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aucune vente aujourd'hui</p>
                            <a href="{{ route('employee.sales.create') }}" class="btn btn-primary">
                                Faire une première vente
                            </a>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques de la semaine -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-chart-line me-2"></i>Performance de la semaine
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <canvas id="weekChart" height="200"></canvas>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <h6 class="fw-bold">Résumé hebdomadaire</h6>
                                <div class="mt-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Total ventes :</span>
                                        <strong>{{ $weekStats['sales_count'] ?? 0 }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Chiffre d'affaires :</span>
                                        <strong class="text-success">
                                            {{ number_format($weekStats['total_amount'] ?? 0, 0, ',', ' ') }} FCFA
                                        </strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Objectif hebdo :</span>
                                        <strong>{{ $goals['weekly_sales'] ?? 0 }} ventes</strong>
                                    </div>
                                    @php
                                        $weekGoalPercentage = ($goals['weekly_sales'] ?? 0) > 0 
                                            ? (($weekStats['sales_count'] ?? 0) / ($goals['weekly_sales'] ?? 1)) * 100 
                                            : 0;
                                    @endphp
                                    <div class="progress mt-3" style="height: 10px;">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: {{ min($weekGoalPercentage, 100) }}%">
                                            {{ number_format($weekGoalPercentage, 1) }}%
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-bolt me-2"></i>Actions rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('employee.sales.create') }}" 
                               class="btn btn-primary w-100 py-3">
                                <i class="fas fa-cash-register fa-2x mb-2"></i><br>
                                Nouvelle vente
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('employee.sales.index') }}" 
                               class="btn btn-info w-100 py-3">
                                <i class="fas fa-history fa-2x mb-2"></i><br>
                                Mes ventes
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('employee.statistics') }}" 
                               class="btn btn-success w-100 py-3">
                                <i class="fas fa-chart-bar fa-2x mb-2"></i><br>
                                Statistiques
                            </a>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-warning w-100 py-3" id="quickReportBtn">
                                <i class="fas fa-clipboard-check fa-2x mb-2"></i><br>
                                Rapport journalier
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal rapport rapide -->
<div class="modal fade" id="quickReportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rapport journalier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Ce rapport sera envoyé à votre responsable.
                </div>
                <div class="mb-3">
                    <label class="form-label">Commentaires (optionnel)</label>
                    <textarea class="form-control" rows="3" 
                              placeholder="Anomalies, incidents, suggestions..."></textarea>
                </div>
                <div class="alert alert-light">
                    <h6>Récapitulatif du jour :</h6>
                    <ul class="mb-0">
                        <li>{{ $todayStats['sales_count'] ?? 0 }} ventes effectuées</li>
                        <li>Chiffre d'affaires : {{ number_format($todayStats['total_amount'] ?? 0, 0, ',', ' ') }} FCFA</li>
                        <li>Volume total : {{ number_format($todayStats['total_volume'] ?? 0, 2, ',', ' ') }} L</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary">Envoyer le rapport</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Horloge en temps réel
    function updateClock() {
        const now = new Date();
        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');
        const seconds = now.getSeconds().toString().padStart(2, '0');
        
        document.getElementById('live-clock').textContent = `${hours}:${minutes}:${seconds}`;
    }
    
    setInterval(updateClock, 1000);
    updateClock();

    // Graphique de la semaine
    const weekCtx = document.getElementById('weekChart').getContext('2d');
    const days = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
    
    // Préparer les données
    const dailyStats = @json($weekStats['daily_stats'] ?? []);
    const days = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
        
    // Préparer les données pour le graphique
    const dailyData = days.map(day => {
        return dailyStats[day]?.amount || 0;
    });

    const dailyCount = days.map(day => {
        return dailyStats[day]?.count || 0;
    });
    
    new Chart(weekCtx, {
        type: 'bar',
        data: {
            labels: days,
            datasets: [{
                label: 'Chiffre d\'affaires (FCFA)',
                data: dailyData,
                backgroundColor: '#4e73df',
                borderColor: '#4e73df',
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
                        afterLabel: function(context) {
                            const index = context.dataIndex;
                            return `Nombre de ventes : ${dailyCount[index]}`;
                        }
                    }
                }
            }
        }
    });

    // Bouton rapport rapide
    document.getElementById('quickReportBtn')?.addEventListener('click', function() {
        const modal = new bootstrap.Modal(document.getElementById('quickReportModal'));
        modal.show();
    });

    // Vérification des pompes (simulation)
    function checkPumpsStatus() {
        // Cette fonction pourrait appeler une API pour vérifier l'état des pompes
        console.log('Vérification des pompes...');
    }

    // Vérifier toutes les 5 minutes
    setInterval(checkPumpsStatus, 300000);
});
</script>
@endpush