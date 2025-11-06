@extends('layouts.technician')

@section('content')
<!-- Alertes Urgentes -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card alert-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                        <span class="fw-bold">Alertes Actives :</span>
                        <span id="alert-message">
                            {{ count($alertes_actives) }} équipement(s) nécessitent une attention immédiate
                        </span>
                    </div>
                    <button class="btn btn-sm btn-outline-danger">Voir toutes les alertes</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques Technicien -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-muted">Interventions en Cours</h6>
                        <h3 class="text-warning">{{ $interventions_en_cours }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-tools fa-2x text-warning"></i>
                    </div>
                </div>
                <small class="text-warning">À traiter rapidement</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-muted">Interventions Terminées</h6>
                        <h3 class="text-success">{{ $interventions_terminees }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                </div>
                <small class="text-success">Ce mois</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-muted">Équipements Surveillés</h6>
                        <h3 class="text-primary">{{ $equipements_surveilles }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-gas-pump fa-2x text-primary"></i>
                    </div>
                </div>
                <small class="text-primary">Sous maintenance</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-muted">Taux de Réussite</h6>
                        <h3 class="text-info">{{ $taux_reussite }}%</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-chart-line fa-2x text-info"></i>
                    </div>
                </div>
                <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar bg-info" role="progressbar" style="width: {{ $taux_reussite }}%;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alertes Actives et Interventions Récentes -->
<div class="row mb-4">
    <!-- Alertes Actives -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>Alertes Actives
                </h5>
                <span class="badge bg-danger">{{ count($alertes_actives) }}</span>
            </div>
            <div class="card-body">
                @forelse($alertes_actives as $alerte)
                <div class="alert alert-warning alert-dismissible fade show mb-2" role="alert">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="alert-heading mb-1">{{ $alerte['equipement'] }} - {{ $alerte['type'] }}</h6>
                            <small class="mb-0">{{ $alerte['station'] }}</small>
                            <br>
                            <small class="text-muted">{{ $alerte['date']->diffForHumans() }}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-{{ $alerte['niveau'] == 'critique' ? 'danger' : 'warning' }} mb-2">
                                {{ ucfirst($alerte['niveau']) }}
                            </span>
                            <br>
                            <button class="btn btn-sm btn-outline-primary mt-1">Intervenir</button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-3">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <p>Aucune alerte active</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Interventions Récentes -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-clipboard-list me-2"></i>Interventions Récentes
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Équipement</th>
                                <th>Type</th>
                                <th>Statut</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($interventions_recentes as $intervention)
                            <tr>
                                <td>
                                    <strong>{{ $intervention['equipement'] }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $intervention['station'] }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $intervention['type'] }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $intervention['status'] == 'terminée' ? 'success' : ($intervention['status'] == 'en cours' ? 'warning' : 'info') }}">
                                        {{ ucfirst($intervention['status']) }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ $intervention['date']->format('d/m H:i') }}</small>
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

<!-- État des Équipements et Actions Rapides -->
<div class="row">
    <!-- État des Équipements -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>État des Équipements
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Type d'Équipement</th>
                                <th>Total</th>
                                <th>Actifs</th>
                                <th>En Maintenance</th>
                                <th>En Panne</th>
                                <th>Taux de Disponibilité</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($etat_equipements as $equipement)
                            @php
                                $taux_disponibilite = ($equipement['actifs'] / $equipement['total']) * 100;
                            @endphp
                            <tr>
                                <td><strong>{{ $equipement['type'] }}</strong></td>
                                <td>{{ $equipement['total'] }}</td>
                                <td>
                                    <span class="equipment-status-active">
                                        <i class="fas fa-circle me-1"></i>{{ $equipement['actifs'] }}
                                    </span>
                                </td>
                                <td>
                                    <span class="equipment-status-maintenance">
                                        <i class="fas fa-circle me-1"></i>{{ $equipement['maintenance'] }}
                                    </span>
                                </td>
                                <td>
                                    <span class="equipment-status-broken">
                                        <i class="fas fa-circle me-1"></i>{{ $equipement['panne'] }}
                                    </span>
                                </td>
                                <td>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-{{ $taux_disponibilite > 90 ? 'success' : ($taux_disponibilite > 80 ? 'warning' : 'danger') }}"
                                             style="width: {{ $taux_disponibilite }}%"></div>
                                    </div>
                                    <small>{{ number_format($taux_disponibilite, 1) }}%</small>
                                </td>
                            </tr>
                            @endforeach
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
                    <button class="btn btn-warning btn-lg">
                        <i class="fas fa-plus me-2"></i>Nouvelle Intervention
                    </button>
                    <button class="btn btn-outline-primary">
                        <i class="fas fa-clipboard-check me-2"></i>Rapport d'Intervention
                    </button>
                    <button class="btn btn-outline-success">
                        <i class="fas fa-gas-pump me-2"></i>État des Équipements
                    </button>
                    <button class="btn btn-outline-info">
                        <i class="fas fa-calendar me-2"></i>Planning Maintenance
                    </button>
                    <button class="btn btn-outline-secondary">
                        <i class="fas fa-warehouse me-2"></i>Stock Pièces
                    </button>
                </div>

                <!-- Statistiques Rapides -->
                <div class="mt-4 p-3 bg-light rounded">
                    <h6 class="text-center mb-3">Indicateurs Clés</h6>
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <small class="text-muted d-block">MTBF</small>
                            <strong class="text-success">156h</strong>
                        </div>
                        <div class="col-6 mb-3">
                            <small class="text-muted d-block">MTTR</small>
                            <strong class="text-info">2.3h</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Disponibilité</small>
                            <strong class="text-primary">98.5%</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Coût Moyen</small>
                            <strong class="text-warning">45K FCFA</strong>
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
$(document).ready(function() {
    // Mise à jour dynamique des compteurs
    function updateCounters() {
        // Simulation de mise à jour des interventions
        const currentCount = parseInt($('#interventions-count').text());
        const newCount = Math.max(1, Math.floor(Math.random() * 5));
        $('#interventions-count').text(newCount + ' interventions');

        console.log('Mise à jour des indicateurs technicien...');
    }

    // Mise à jour toutes les 45 secondes
    setInterval(updateCounters, 45000);

    // Gestion des alertes
    $('.alert .btn-close').on('click', function() {
        const alertElement = $(this).closest('.alert');
        alertElement.fadeOut(300, function() {
            // Ici, vous enverriez une requête au serveur pour marquer l'alerte comme traitée
            console.log('Alerte traitée:', alertElement.data('alerte-id'));
        });
    });

    // Actions rapides
    $('.btn').on('click', function() {
        const action = $(this).text().trim();
        console.log('Action technicien:', action);
        // Ici, vous redirigeriez vers la page appropriée
    });
});
</script>
@endsection
