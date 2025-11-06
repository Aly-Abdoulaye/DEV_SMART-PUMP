@extends('layouts.manager')

@section('content')
<!-- Alertes -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card alert-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                        <span class="fw-bold">Alertes :</span>
                        <span id="alert-message">
                            @if($niveau_kerosene < 20)
                                1 cuve avec niveau critique
                            @else
                                Toutes les cuves sont dans les normes
                            @endif
                        </span>
                    </div>
                    <button class="btn btn-sm btn-outline-warning">Voir détails</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cartes de Statistiques -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-muted">Ventes Aujourd'hui</h6>
                        <h3 class="text-primary">{{ number_format($ventes_jour, 0, ',', ' ') }} FCFA</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-shopping-cart fa-2x text-primary"></i>
                    </div>
                </div>
                <small class="text-success"><i class="fas fa-arrow-up"></i> 12% vs hier</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-muted">Volume Vendue</h6>
                        <h3 class="text-success">{{ number_format($volume_vendu, 0, ',', ' ') }} L</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-gas-pump fa-2x text-success"></i>
                    </div>
                </div>
                <small class="text-success"><i class="fas fa-arrow-up"></i> 8% vs hier</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-muted">Dépenses Journalières</h6>
                        <h3 class="text-warning">{{ number_format($depenses_jour, 0, ',', ' ') }} FCFA</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-money-bill-wave fa-2x text-warning"></i>
                    </div>
                </div>
                <small class="text-danger"><i class="fas fa-arrow-down"></i> 5% vs hier</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-muted">Clients Servis</h6>
                        <h3 class="text-info">{{ $clients_servis }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x text-info"></i>
                    </div>
                </div>
                <small class="text-success"><i class="fas fa-arrow-up"></i> 15% vs hier</small>
            </div>
        </div>
    </div>
</div>

<!-- Niveaux des Cuves -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-oil-can me-2"></i>Niveaux des Cuves - Station: {{ $station->name ?? 'Ma Station' }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Cuve Essence -->
                    <div class="col-md-4 mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-bold">Essence</span>
                            <span class="text-muted">{{ $niveau_essence }}%</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $niveau_essence }}%;"
                                 aria-valuenow="{{ $niveau_essence }}" aria-valuemin="0" aria-valuemax="100">
                                {{ $niveau_essence }}%
                            </div>
                        </div>
                        <small class="text-muted">Niveau normal</small>
                    </div>

                    <!-- Cuve Diesel -->
                    <div class="col-md-4 mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-bold">Diesel</span>
                            <span class="text-muted">{{ $niveau_diesel }}%</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $niveau_diesel }}%;"
                                 aria-valuenow="{{ $niveau_diesel }}" aria-valuemin="0" aria-valuemax="100">
                                {{ $niveau_diesel }}%
                            </div>
                        </div>
                        <small class="text-warning">Niveau moyen</small>
                    </div>

                    <!-- Cuve Kérosène -->
                    <div class="col-md-4 mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-bold">Kérosène</span>
                            <span class="text-muted">{{ $niveau_kerosene }}%</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $niveau_kerosene }}%;"
                                 aria-valuenow="{{ $niveau_kerosene }}" aria-valuemin="0" aria-valuemax="100">
                                {{ $niveau_kerosene }}%
                            </div>
                        </div>
                        <small class="text-danger">Niveau critique</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dernières Ventes et Actions Rapides -->
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history me-2"></i>Dernières Ventes
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Heure</th>
                                <th>Type</th>
                                <th>Volume</th>
                                <th>Montant</th>
                                <th>Employé</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>14:30</td>
                                <td>Essence</td>
                                <td>25 L</td>
                                <td>18,750 FCFA</td>
                                <td>Moussa</td>
                            </tr>
                            <tr>
                                <td>14:15</td>
                                <td>Diesel</td>
                                <td>40 L</td>
                                <td>26,000 FCFA</td>
                                <td>Fatou</td>
                            </tr>
                            <tr>
                                <td>13:45</td>
                                <td>Essence</td>
                                <td>15 L</td>
                                <td>11,250 FCFA</td>
                                <td>Moussa</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions Rapides -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>Actions Rapides
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i>Nouvelle Vente
                    </button>
                    <button class="btn btn-outline-warning">
                        <i class="fas fa-money-bill me-2"></i>Enregistrer Dépense
                    </button>
                    <button class="btn btn-outline-info">
                        <i class="fas fa-file-invoice me-2"></i>Rapport Journalier
                    </button>
                    <button class="btn btn-outline-success">
                        <i class="fas fa-gas-pump me-2"></i>Niveau Carburant
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Simulation de données en temps réel
    function updateDashboard() {
        console.log('Mise à jour du tableau de bord manager...');
        // Ici vous ajouterez les appels AJAX pour les données réelles
    }

    // Mise à jour toutes les 30 secondes
    setInterval(updateDashboard, 30000);
});
</script>
@endsection
