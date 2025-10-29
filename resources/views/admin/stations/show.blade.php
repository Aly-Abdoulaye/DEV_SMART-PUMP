@extends('layouts.admin')

@section('title', $station->name)
@section('page-title', $station->name)
@section('page-subtitle', 'Détails et indicateurs en temps réel')

@section('page-actions')
    <div class="btn-group">
        <a href="{{ route('admin.stations.edit', $station) }}" class="btn btn-warning">
            <i class="fas fa-edit me-1"></i> Modifier
        </a>
        <form action="{{ route('admin.stations.toggle-status', $station) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-{{ $station->is_active ? 'secondary' : 'success' }}">
                <i class="fas fa-{{ $station->is_active ? 'pause' : 'play' }} me-1"></i>
                {{ $station->is_active ? 'Désactiver' : 'Activer' }}
            </button>
        </form>
        <a href="{{ route('admin.stations.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Retour
        </a>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Cartes de statistiques en temps réel -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Ventes Aujourd'hui
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="todaySalesCount">
                                {{ $todaySales }}
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
    <div class="card border-left-success shadow h-100 py-2">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                        Pompes Actives
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="activePumpsCount">
                        {{ $activePumps }}
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
                                Employés Présents
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $station->employees_count ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-{{ $station->is_active ? 'success' : 'danger' }} shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">
                                Statut
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $station->is_active ? 'Active' : 'Inactive' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-{{ $station->is_active ? 'check' : 'times' }}-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informations de la station -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informations de la Station
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="30%"><strong>Nom:</strong></td>
                            <td>{{ $station->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Adresse:</strong></td>
                            <td>{{ $station->address }}</td>
                        </tr>
                        @if($station->phone)
                        <tr>
                            <td><strong>Téléphone:</strong></td>
                            <td>{{ $station->phone }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td><strong>Manager:</strong></td>
                            <td>
                                @if($station->managers->isNotEmpty())
                                    <span class="badge bg-info">
                                        <i class="fas fa-user me-1"></i>
                                        {{ $station->managers->first()->name }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Non assigné</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Statut:</strong></td>
                            <td>
                                <span class="badge bg-{{ $station->is_active ? 'success' : 'danger' }}">
                                    {{ $station->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Créée le:</strong></td>
                            <td>{{ $station->created_at->format('d/m/Y à H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Cuves de carburant -->
            <div class="card shadow mt-4">
                <div class="card-header bg-info text-white py-3">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-oil-can me-2"></i>Cuves de Carburant
                    </h6>
                </div>
                <div class="card-body">
                    @if($station->tanks->count() > 0)
                        @foreach($station->tanks as $tank)
                        <div class="mb-3 p-3 border rounded">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong>{{ $tank->fuel_type ?? 'Carburant' }}</strong>
                                <span class="badge bg-{{ $tank->is_active ? 'success' : 'secondary' }}">
                                    {{ $tank->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <div class="progress mb-2" style="height: 20px;">
                                @php
                                    $percentage = $tank->capacity > 0 ? ($tank->current_quantity / $tank->capacity) * 100 : 0;
                                    $progressClass = $percentage < 20 ? 'bg-danger' : ($percentage < 50 ? 'bg-warning' : 'bg-success');
                                @endphp
                                <div class="progress-bar {{ $progressClass }}" role="progressbar"
                                     style="width: {{ $percentage }}%;"
                                     aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ number_format($percentage, 1) }}%
                                </div>
                            </div>
                            <div class="d-flex justify-content-between text-sm text-muted">
                                <span>{{ number_format($tank->current_quantity) }} L</span>
                                <span>Capacité: {{ number_format($tank->capacity) }} L</span>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-oil-can fa-2x mb-2"></i>
                            <p>Aucune cuve configurée</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Activité récente et pompes -->
        <div class="col-lg-6 mb-4">
            <!-- Dernières ventes -->
            <div class="card shadow">
                <div class="card-header bg-success text-white py-3">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i>Dernières Ventes (Aujourd'hui)
                    </h6>
                </div>
                <div class="card-body">
                    @if($station->sales->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($station->sales->take(5) as $sale)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted">{{ $sale->created_at->format('H:i') }}</small>
                                    <div class="fw-bold">{{ $sale->fuel_type ?? 'Carburant' }}</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold">{{ number_format($sale->volume, 2) }} L</div>
                                    <small class="text-success">{{ number_format($sale->amount, 0) }} FCFA</small>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                            <p>Aucune vente aujourd'hui</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Pompes -->
            <!-- Pompes -->
<div class="card shadow mt-4">
    <div class="card-header bg-warning text-dark py-3">
        <h6 class="card-title mb-0">
            <i class="fas fa-gas-pump me-2"></i>Pompes
        </h6>
    </div>
    <div class="card-body">
        @if($station->pumps->count() > 0)
            <div class="row">
                @foreach($station->pumps as $pump)
                <div class="col-md-6 mb-3">
                    <div class="card border-{{ $pump->status === 'active' ? 'success' : 'danger' }}">
                        <div class="card-body text-center py-3">
                            <h5 class="card-title mb-1">Pompe #{{ $pump->pump_number }}</h5>
                            @if($pump->nozzle_number)
                                <small class="text-muted">Buse: {{ $pump->nozzle_number }}</small>
                            @endif
                            <div class="mt-2">
                                <span class="badge bg-{{ $pump->status === 'active' ? 'success' : 'danger' }}">
                                    {{ $pump->status === 'active' ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            @if($pump->current_index)
                            <div class="mt-2">
                                <small class="text-muted">Index: {{ number_format($pump->current_index) }}</small>
                            </div>
                            @endif
                            @if($pump->tank)
                            <div class="mt-1">
                                <small class="text-muted">Cuve: {{ $pump->tank->fuel_type ?? 'N/A' }}</small>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center text-muted py-3">
                <i class="fas fa-gas-pump fa-2x mb-2"></i>
                <p>Aucune pompe configurée</p>
            </div>
        @endif
    </div>
</div>

            <!-- Actions rapides -->
            <div class="card shadow mt-4">
                <div class="card-header bg-secondary text-white py-3">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-bolt me-2"></i>Actions Rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <a href="#" class="btn btn-outline-primary w-100">
                                <i class="fas fa-plus me-1"></i> Ajouter Pompe
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('admin.stations.tanks.index', $station) }}" class="btn btn-outline-success w-100">
    <i class="fas fa-oil-can me-1"></i> Gérer Cuves
</a>
                        </div>
                        <div class="col-md-6">
                            <a href="#" class="btn btn-outline-info w-100">
                                <i class="fas fa-users me-1"></i> Employés
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="#" class="btn btn-outline-warning w-100">
                                <i class="fas fa-chart-bar me-1"></i> Rapports
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Simulation de mise à jour en temps réel (à remplacer par WebSockets ou polling AJAX)
function updateRealTimeData() {
    // Mettre à jour les ventes aujourd'hui (simulation)
    const salesElement = document.getElementById('todaySalesCount');
    if (salesElement) {
        const currentSales = parseInt(salesElement.textContent);
        // Simulation d'incrémentation aléatoire
        if (Math.random() > 0.7) {
            salesElement.textContent = currentSales + 1;
            // Ajouter une animation
            salesElement.classList.add('text-success');
            setTimeout(() => salesElement.classList.remove('text-success'), 1000);
        }
    }
}

// Mettre à jour toutes les 10 secondes
setInterval(updateRealTimeData, 10000);

// Animation pour les indicateurs
document.addEventListener('DOMContentLoaded', function() {
    const counters = document.querySelectorAll('.font-weight-bold');
    counters.forEach(counter => {
        counter.style.transition = 'all 0.3s ease';
    });
});
</script>

@section('styles')
<style>
.border-left-primary { border-left: 4px solid #4e73df; }
.border-left-success { border-left: 4px solid #1cc88a; }
.border-left-info { border-left: 4px solid #36b9cc; }
.border-left-warning { border-left: 4px solid #f6c23e; }
.border-left-danger { border-left: 4px solid #e74a3b; }

.card {
    transition: transform 0.2s;
}
.card:hover {
    transform: translateY(-2px);
}

.progress {
    border-radius: 10px;
}
.progress-bar {
    border-radius: 10px;
}

.list-group-item {
    border: none;
    padding: 0.75rem 0;
}
</style>
@endsection
