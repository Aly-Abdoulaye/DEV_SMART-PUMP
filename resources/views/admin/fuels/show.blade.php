@extends('layouts.admin')

@section('title', 'Détails du Carburant - ' . $fuel->name)

@section('content')

<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-gas-pump text-primary"></i> Détails du Carburant
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.fuels.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
            <a href="{{ route('admin.fuels.edit', $fuel) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#priceModal">
                <i class="fas fa-euro-sign"></i> Modifier le prix
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Colonne gauche : Informations principales -->
        <div class="col-lg-8">
            <!-- Carte des informations de base -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle"></i> Informations générales
                    </h6>
                    <span class="badge badge-{{ $fuel->is_active ? 'success' : 'secondary' }}">
                        {{ $fuel->is_active ? 'Actif' : 'Inactif' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="font-weight-bold text-primary" width="40%">Nom :</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="color-indicator mr-2" 
                                                 style="background-color: {{ $fuel->color }}; width: 16px; height: 16px; border-radius: 50%; border: 2px solid #dee2e6;"></div>
                                            {{ $fuel->name }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-primary">Code :</td>
                                    <td><code class="text-muted">{{ $fuel->code }}</code></td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-primary">Station :</td>
                                    <td>
                                        @if($fuel->station)
                                            <a href="#" class="text-decoration-none">
                                                <i class="fas fa-gas-pump text-info"></i> {{ $fuel->station->name }}
                                            </a>
                                        @else
                                            <span class="text-muted">Non assigné</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-primary">Compagnie :</td>
                                    <td>{{ $fuel->company->name ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="font-weight-bold text-primary" width="40%">Prix actuel :</td>
                                    <td class="h5 font-weight-bold text-success">
                                        {{ number_format($fuel->current_price ?? $fuel->price, 0, ',', ' ') }} FCFA/L
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-primary">Prix précédent :</td>
                                    <td class="text-muted">
                                        {{ number_format($fuel->previous_price ?? $fuel->price, 0, ',', ' ') }} FCFA/L
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-primary">Variation :</td>
                                    <td>
                                        @php
                                            $current = $fuel->current_price ?? $fuel->price;
                                            $previous = $fuel->previous_price ?? $fuel->price;
                                            if ($previous > 0) {
                                                $change = (($current - $previous) / $previous) * 100;
                                            } else {
                                                $change = 0;
                                            }
                                        @endphp
                                        <span class="text-muted-{{ $change > 0 ? 'danger' : ($change < 0 ? 'success' : 'secondary') }}">
                                            <i class="fas fa-arrow-{{ $change > 0 ? 'up' : ($change < 0 ? 'down' : 'right') }}"></i>
                                            {{ number_format(abs($change), 2) }}%
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-primary">Date création :</td>
                                    <td>{{ $fuel->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($fuel->description)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="font-weight-bold text-primary">Description :</h6>
                            <p class="text-muted">{{ $fuel->description }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Statistiques -->
            <div class="row">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Cuves associées</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $fuel->tanks_count ?? 0 }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-oil-can fa-2x text-gray-300"></i>
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
                                        Changements de prix</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $priceHistory->count() }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
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
                                        Dernier changement</div>
                                    <div class="h6 mb-0 font-weight-bold text-gray-800">
                                        @if($priceHistory->count() > 0)
                                            {{ $priceHistory->first()->created_at->diffForHumans() }}
                                        @else
                                            Jamais
                                        @endif
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                                        Statut</div>
                                    <div class="h6 mb-0 font-weight-bold text-gray-800">
                                        @if($fuel->is_active)
                                            <span class="text-success">Actif</span>
                                        @else
                                            <span class="text-danger">Inactif</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-{{ $fuel->is_active ? 'check' : 'pause' }}-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colonne droite : Actions rapides et informations -->
        <div class="col-lg-4">
            <!-- Actions rapides -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt"></i> Actions rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary" onclick="$('#priceModal').modal('show')">
                        <i class="fas fa-euro-sign"></i> Modifier le prix
                    </button>
                        
                        <a href="{{ route('admin.fuels.edit', $fuel) }}" class="btn btn-primary btn-block">
                            <i class="fas fa-edit"></i> Modifier le carburant
                        </a>

                        <form action="{{ route('admin.fuels.toggle-status', $fuel) }}" method="POST" class="d-grid">
                            @csrf
                            <button type="submit" class="btn btn-{{ $fuel->is_active ? 'secondary' : 'success' }} btn-block"
                                    onclick="return confirm('Êtes-vous sûr de vouloir {{ $fuel->is_active ? 'désactiver' : 'activer' }} ce carburant?')">
                                <i class="fas fa-{{ $fuel->is_active ? 'pause' : 'play' }}"></i>
                                {{ $fuel->is_active ? 'Désactiver' : 'Activer' }}
                            </button>
                        </form>

                        @if(($fuel->tanks_count ?? 0) === 0)
                        <form action="{{ route('admin.fuels.destroy', $fuel) }}" method="POST" class="d-grid"
                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement ce carburant?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-block">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </form>
                        @else
                        <button type="button" class="btn btn-danger btn-block" disabled 
                                title="Impossible de supprimer - des cuves sont associées à ce carburant">
                            <i class="fas fa-trash"></i> Supprimer (bloqué)
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Informations de la station -->
            @if($fuel->station)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-gas-pump"></i> Station associée
                    </h6>
                </div>
                <div class="card-body">
                    <h6 class="font-weight-bold">{{ $fuel->station->name }}</h6>
                    @if($fuel->station->address)
                        <p class="mb-1">
                            <i class="fas fa-map-marker-alt text-danger"></i> 
                            {{ $fuel->station->address }}
                        </p>
                    @endif
                    @if($fuel->station->phone)
                        <p class="mb-1">
                            <i class="fas fa-phone text-success"></i> 
                            {{ $fuel->station->phone }}
                        </p>
                    @endif
                    @if($fuel->station->email)
                        <p class="mb-0">
                            <i class="fas fa-envelope text-info"></i> 
                            {{ $fuel->station->email }}
                        </p>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Historique des prix -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-history"></i> Historique des prix
            </h6>
           <div class="btn-group">
            <!-- Export PDF - CORRIGÉ -->
            <a href="{{ route('admin.fuels.export.pdf', $fuel) }}" 
               class="btn btn-sm btn-outline-danger" 
               target="_blank">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
            
            <!-- Export CSV/Excel - CORRIGÉ -->
            <a href="{{ route('admin.fuels.export.excel', $fuel) }}" 
               class="btn btn-sm btn-outline-success">
                <i class="fas fa-file-excel"></i> Excel
            </a>
        </div>

        </div>
        <div class="card-body">
            @if($priceHistory->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="priceHistoryTable">
                        <thead class="thead-light">
                            <tr>
                                <th>Date</th>
                                <th>Ancien prix</th>
                                <th>Nouveau prix</th>
                                <th>Variation</th>
                                <th>Raison</th>
                                <th>Modifié par</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($priceHistory as $history)
                            <tr>
                                <td>
                                    <small class="text-muted">{{ $history->created_at->format('d/m/Y') }}</small>
                                    <br>
                                    <small class="text-muted">{{ $history->created_at->format('H:i') }}</small>
                                </td>
                                <td class="text-muted">{{ number_format($history->old_price, 0, ',', ' ') }} FCFA</td>
                                <td class="font-weight-bold text-success">{{ number_format($history->new_price, 0, ',', ' ') }} FCFA</td>
                                <td>
                                    @php
                                        $change = $history->old_price > 0 ? (($history->new_price - $history->old_price) / $history->old_price) * 100 : 0;
                                    @endphp
                                    <span class="text-muted-{{ $change > 0 ? 'danger' : ($change < 0 ? 'success' : 'secondary') }}">
                                        <i class="fas fa-arrow-{{ $change > 0 ? 'up' : ($change < 0 ? 'down' : 'right') }}"></i>
                                        {{ number_format(abs($change), 2) }}%
                                    </span>
                                    <br>
                                    <small class="text-muted">
                                        {{ number_format($history->new_price - $history->old_price, 0, ',', ' ') }} FCFA
                                    </small>
                                </td>
                                <td>
                                    <span class="text-muted" title="{{ $history->change_reason }}">
                                        {{ Str::limit($history->change_reason, 40) }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $history->changedByUser->name ?? 'Système' }}</small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($priceHistory->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Affichage de {{ $priceHistory->firstItem() }} à {{ $priceHistory->lastItem() }} sur {{ $priceHistory->total() }} changements
                    </div>
                    {{ $priceHistory->links() }}
                </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                    <h5 class="text-gray-500">Aucun historique de prix</h5>
                    <p class="text-muted">Les changements de prix apparaîtront ici</p>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#priceModal">
                        <i class="fas fa-euro-sign"></i> Premier changement de prix
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
<!-- Modal de filtre pour l'export PDF -->
<div class="modal fade" id="exportFilterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Options d'export PDF</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('admin.fuels.pdf.price-history', $fuel) }}" method="GET">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Date de début</label>
                        <input type="date" name="start_date" class="form-control" 
                               value="{{ now()->subDays(30)->format('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label>Date de fin</label>
                        <input type="date" name="end_date" class="form-control" 
                               value="{{ now()->format('Y-m-d') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-download"></i> Générer PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour modifier le prix -->
<div class="modal fade" id="priceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-euro-sign text-warning"></i>
                    Modifier prix - {{ $fuel->name }}
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <!-- AJOUT DE L'ID AU FORMULAIRE -->
            <form id="priceUpdateForm" action="{{ route('admin.fuels.update-price', $fuel) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Prix actuel</label>
                        <input type="text" class="form-control bg-light" 
                               value="{{ number_format($fuel->current_price ?? $fuel->price, 0, ',', ' ') }} FCFA" 
                               disabled>
                    </div>
                    <div class="form-group">
                        <label for="new_price" class="font-weight-bold">
                            Nouveau prix (FCFA/L) *
                        </label>
                        <input type="number" step="0.01" name="new_price" 
                               id="new_price" 
                               value="{{ $fuel->current_price ?? $fuel->price }}" 
                               class="form-control" required min="0">
                    </div>
                    <div class="form-group">
                        <label for="change_reason" class="font-weight-bold">
                            Raison du changement *
                        </label>
                        <select name="change_reason" id="change_reason" 
                                class="form-control" required>
                            <option value="">Sélectionnez une raison</option>
                            <option value="Ajustement du marché">Ajustement du marché</option>
                            <option value="Changement de fournisseur">Changement de fournisseur</option>
                            <option value="Promotion commerciale">Promotion commerciale</option>
                            <option value="Augmentation des coûts">Augmentation des coûts</option>
                            <option value="Baisse des coûts">Baisse des coûts</option>
                            <option value="Concurrence">Concurrence</option>
                            <option value="Autre">Autre</option>
                        </select>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Attention:</strong> Cette modification sera enregistrée dans l'historique des prix.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


@section('scripts')
<script>
$(document).ready(function() {
    console.log('Page détails carburant chargée');

    // Auto-focus sur le champ de prix quand le modal s'ouvre
    $('#priceModal').on('shown.bs.modal', function () {
        $('#new_price').focus().select();
    });

    // Initialisation de DataTable pour l'historique des prix
    if ($('#priceHistoryTable').length) {
        $('#priceHistoryTable').DataTable({
            "pageLength": 10,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json"
            },
            "order": [[0, "desc"]],
            "responsive": true
        });
    }

    // Gestion de la soumission du formulaire de prix
    $('#priceUpdateForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        // Désactiver le bouton pendant la requête
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Mise à jour...');
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#priceModal').modal('hide');
                    // Afficher un message de succès
                    showAlert('success', response.message || 'Prix mis à jour avec succès');
                    // Recharger la page après un délai
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showAlert('danger', response.message || 'Erreur lors de la mise à jour');
                    submitBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                let errorMessage = 'Erreur lors de la mise à jour du prix';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showAlert('danger', errorMessage);
                submitBtn.prop('disabled', false).html(originalText);
                console.error('Erreur AJAX:', xhr.responseText);
            }
        });
    });

    // Fonction pour afficher les alertes
    function showAlert(type, message) {
        // Supprimer les anciennes alertes
        $('.alert-dismissible').alert('close');
        
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <strong>${type === 'success' ? '✓' : '⚠'} </strong> ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;
        $('body').append(alertHtml);
        
        // Auto-supprimer l'alerte après 5 secondes
        setTimeout(() => {
            $('.alert-dismissible').alert('close');
        }, 5000);
    }

    // Validation en temps réel du prix
    $('#new_price').on('input', function() {
        const price = parseFloat($(this).val());
        if (price < 0) {
            $(this).addClass('is-invalid');
            $('#priceError').remove();
            $(this).after('<div id="priceError" class="invalid-feedback">Le prix ne peut pas être négatif</div>');
        } else {
            $(this).removeClass('is-invalid');
            $('#priceError').remove();
        }
    });
});
</script>

<style>
.color-indicator {
    border: 2px solid #dee2e6;
    transition: all 0.3s ease;
}

.table-borderless td {
    border: none !important;
}

.card {
    border: 1px solid #e3e6f0;
    border-radius: 0.35rem;
}

.btn-group .btn {
    margin-right: 5px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

/* Animation douce pour le modal */
.modal.fade .modal-dialog {
    transform: translateY(-50px);
    transition: transform 0.3s ease-out;
}

.modal.show .modal-dialog {
    transform: translateY(0);
}

/* Style pour les indicateurs de couleur */
.color-indicator {
    display: inline-block;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid #dee2e6;
}
</style>
@endsection