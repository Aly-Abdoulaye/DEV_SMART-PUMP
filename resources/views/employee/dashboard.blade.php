@extends('layouts.employee')

@section('content')
<!-- Message de Bienvenue -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card sale-card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h4 class="text-white">Bonjour, {{ $user->name }} ! 👋</h4>
                        <p class="text-light mb-0">Bonne journée de travail. Vous avez effectué {{ $nombre_ventes }} ventes aujourd'hui.</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="bg-white bg-opacity-25 p-3 rounded">
                            <h5 class="text-white mb-0">Poste: {{ $user->poste ?? 'Pompiste' }}</h5>
                            <small class="text-light">Station: {{ $user->station->name ?? 'Principale' }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques Employé -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-muted">Chiffre d'Affaires</h6>
                        <h3 class="text-success">{{ number_format($ventes_jour, 0, ',', ' ') }} FCFA</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-money-bill-wave fa-2x text-success"></i>
                    </div>
                </div>
                <small class="text-success"><i class="fas fa-arrow-up"></i> Aujourd'hui</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-muted">Volume Distribué</h6>
                        <h3 class="text-primary">{{ number_format($volume_vendu, 0, ',', ' ') }} L</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-gas-pump fa-2x text-primary"></i>
                    </div>
                </div>
                <small class="text-primary">{{ $nombre_ventes }} transactions</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-muted">Commission</h6>
                        <h3 class="text-warning">{{ number_format($commission_jour, 0, ',', ' ') }} FCFA</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-coins fa-2x text-warning"></i>
                    </div>
                </div>
                <small class="text-warning">Estimation du jour</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-muted">Objectif Quotidien</h6>
                        <h3 class="text-info">{{ $objectif_atteint }}%</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-bullseye fa-2x text-info"></i>
                    </div>
                </div>
                <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar bg-info" role="progressbar" style="width: {{ $objectif_atteint }}%;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Interface de Vente Rapide -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>Vente Rapide
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <form id="quick-sale-form">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Type de Carburant</label>
                                    <select class="form-select" name="fuel_type" required>
                                        <option value="">Sélectionner...</option>
                                        <option value="essence">Essence</option>
                                        <option value="diesel">Diesel</option>
                                        <option value="kerosene">Kérosène</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Volume (Litres)</label>
                                    <input type="number" class="form-control" name="volume" placeholder="0" min="1" step="0.5" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Montant (FCFA)</label>
                                    <input type="number" class="form-control" name="amount" placeholder="0" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Mode de Paiement</label>
                                    <select class="form-select" name="payment_method" required>
                                        <option value="cash">Espèces</option>
                                        <option value="card">Carte Bancaire</option>
                                        <option value="mobile">Mobile Money</option>
                                        <option value="voucher">Bon d'achat</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Pompe</label>
                                    <select class="form-select" name="pump_id" required>
                                        <option value="P1">Pompe 1 (Essence)</option>
                                        <option value="P2">Pompe 2 (Essence)</option>
                                        <option value="P3">Pompe 3 (Diesel)</option>
                                        <option value="P4">Pompe 4 (Kérosène)</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-check me-2"></i>Enregistrer la Vente
                            </button>
                        </form>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-light p-3 rounded">
                            <h6 class="text-center">Calcul Automatique</h6>
                            <hr>
                            <div class="text-center">
                                <small class="text-muted">Prix/Litre</small>
                                <h5 id="price-per-liter">0 FCFA</h5>
                            </div>
                            <div class="text-center mt-3">
                                <small class="text-muted">Total</small>
                                <h3 id="total-amount" class="text-success">0 FCFA</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dernières Ventes et Produit Populaire -->
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history me-2"></i>Mes Dernières Ventes
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
                                <th>Pompe</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dernieres_ventes as $vente)
                            <tr>
                                <td>{{ $vente['heure'] }}</td>
                                <td>
                                    <span class="badge
                                        @if($vente['type'] == 'Essence') bg-primary
                                        @elseif($vente['type'] == 'Diesel') bg-warning
                                        @else bg-secondary @endif">
                                        {{ $vente['type'] }}
                                    </span>
                                </td>
                                <td>{{ $vente['volume'] }} L</td>
                                <td>{{ number_format($vente['montant'], 0, ',', ' ') }} FCFA</td>
                                <td>{{ $vente['pompe'] }}</td>
                                <td><span class="badge bg-success">Terminé</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques Personnelles -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-pie me-2"></i>Mes Stats
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <h6>Produit le Plus Vendu</h6>
                    <div class="bg-primary text-white p-3 rounded">
                        <h4>{{ $produit_plus_vendu }}</h4>
                        <small>42% des ventes</small>
                    </div>
                </div>

                <div class="mb-3">
                    <small class="text-muted">Performance du Mois</small>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-success" style="width: 85%"></div>
                    </div>
                    <small>85% - Excellent</small>
                </div>

                <div class="mb-3">
                    <small class="text-muted">Précision des transactions</small>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-info" style="width: 92%"></div>
                    </div>
                    <small>92% - Très bon</small>
                </div>

                <div class="text-center mt-4">
                    <button class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-download me-1"></i>Mes Rapports
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
    // Mise à jour de l'heure en temps réel
    function updateTime() {
        const now = new Date();
        $('#current-time').text(now.toLocaleTimeString('fr-FR', {
            hour: '2-digit',
            minute: '2-digit'
        }));
    }
    setInterval(updateTime, 60000);

    // Prix par type de carburant
    const fuelPrices = {
        'essence': 750,
        'diesel': 650,
        'kerosene': 650
    };

    // Calcul automatique du montant
    $('select[name="fuel_type"], input[name="volume"]').on('change input', function() {
        const fuelType = $('select[name="fuel_type"]').val();
        const volume = parseFloat($('input[name="volume"]').val()) || 0;

        if (fuelType && volume > 0) {
            const price = fuelPrices[fuelType];
            const total = price * volume;

            $('#price-per-liter').text(price + ' FCFA');
            $('input[name="amount"]').val(total);
            $('#total-amount').text(total.toLocaleString('fr-FR') + ' FCFA');
        }
    });

    // Soumission du formulaire de vente
    $('#quick-sale-form').on('submit', function(e) {
        e.preventDefault();

        // Simulation d'enregistrement
        const formData = $(this).serialize();
        console.log('Vente enregistrée:', formData);

        // Afficher message de succès
        alert('Vente enregistrée avec succès !');
        $(this)[0].reset();
        $('#total-amount').text('0 FCFA');
        $('#price-per-liter').text('0 FCFA');
    });
});
</script>
@endsection
