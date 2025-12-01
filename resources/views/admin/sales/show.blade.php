{{-- resources/views/admin/sales/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'Détails de la vente')

@section('content')
<div class="container-fluid">
    <!-- En-tête avec boutons -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sales.index') }}">Ventes</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $vente->numero_ticket }}</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-receipt text-primary me-2"></i>
                Détails de la vente
            </h1>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.sales.ticket', $vente) }}" target="_blank" class="btn btn-secondary">
                <i class="fas fa-print me-1"></i> Imprimer
            </a>
            <a href="{{ route('admin.sales.index') }}" class="btn btn-light">
                <i class="fas fa-arrow-left me-1"></i> Retour
            </a>
        </div>
    </div>

    <!-- Carte principale -->
    <div class="row">
        <!-- Informations générales -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informations de la transaction
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">N° Ticket:</th>
                                    <td><code class="fs-5">{{ $vente->numero_ticket }}</code></td>
                                </tr>
                                <tr>
                                    <th>Date & Heure:</th>
                                    <td>
                                        <i class="far fa-calendar me-1"></i>{{ $vente->created_at->format('d/m/Y') }}
                                        <i class="far fa-clock ms-3 me-1"></i>{{ $vente->created_at->format('H:i:s') }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Station:</th>
                                    <td>
                                        <span class="badge bg-info">
                                            <i class="fas fa-gas-pump me-1"></i>{{ $vente->station->nom }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Employé:</th>
                                    <td>
                                        <i class="fas fa-user me-1"></i>{{ $vente->employe->nom ?? 'N/A' }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Statut:</th>
                                    <td>
                                        @if($vente->statut == 'complete')
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Complète
                                        </span>
                                        @elseif($vente->statut == 'annulee')
                                        <span class="badge bg-danger">
                                            <i class="fas fa-ban me-1"></i>Annulée
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Mode paiement:</th>
                                    <td>
                                        @php
                                            $modes = [
                                                'especes' => ['primary', '💵 Espèces'],
                                                'carte' => ['success', '💳 Carte'],
                                                'mobile_money' => ['warning', '📱 Mobile Money'],
                                                'compte_client' => ['info', '👤 Compte Client']
                                            ];
                                            $mode = $modes[$vente->mode_paiement] ?? ['secondary', '💰 Autre'];
                                        @endphp
                                        <span class="badge bg-{{ $mode[0] }}">{{ $mode[1] }}</span>
                                    </td>
                                </tr>
                                @if($vente->plaque_vehicule)
                                <tr>
                                    <th>Plaque véhicule:</th>
                                    <td>
                                        <i class="fas fa-car me-1"></i>{{ $vente->plaque_vehicule }}
                                    </td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Détails du produit -->
            <div class="card shadow mt-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-gas-pump me-2"></i>
                        Détails du produit
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="table-light">
                                <tr>
                                    <th>Produit</th>
                                    <th class="text-end">Volume</th>
                                    <th class="text-end">Prix unitaire</th>
                                    <th class="text-end">Montant</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="color-box me-3" 
                                                 style="background-color: {{ $vente->produit->couleur ?? '#4e73df' }}"></div>
                                            <div>
                                                <strong>{{ $vente->produit->nom }}</strong><br>
                                                <small class="text-muted">{{ $vente->produit->code }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end align-middle">
                                        <span class="badge bg-primary fs-6">
                                            {{ number_format($vente->volume, 2, ',', ' ') }} L
                                        </span>
                                    </td>
                                    <td class="text-end align-middle">
                                        {{ number_format($vente->prix_unitaire, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="text-end align-middle">
                                        <h5 class="text-success mb-0">
                                            {{ number_format($vente->montant_total, 0, ',', ' ') }} FCFA
                                        </h5>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="table-secondary">
                                <tr>
                                    <th colspan="3" class="text-end">Total TTC</th>
                                    <th class="text-end">
                                        <h4 class="text-success mb-0">
                                            {{ number_format($vente->montant_total, 0, ',', ' ') }} FCFA
                                        </h4>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions et informations complémentaires -->
        <div class="col-lg-4">
            <!-- Carte récapitulative -->
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Récapitulatif
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="display-4 text-primary">
                            {{ number_format($vente->montant_total, 0, ',', ' ') }}
                        </div>
                        <div class="text-muted">FCFA</div>
                    </div>
                    
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between">
                            <span>Volume:</span>
                            <strong>{{ number_format($vente->volume, 2, ',', ' ') }} L</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between">
                            <span>Prix unitaire:</span>
                            <strong>{{ number_format($vente->prix_unitaire, 0, ',', ' ') }} FCFA</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between">
                            <span>Station:</span>
                            <strong>{{ $vente->station->nom }}</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between">
                            <span>Pompe:</span>
                            <strong>{{ $vente->pompe->numero ?? 'N/A' }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            @if($vente->statut == 'complete')
            <div class="card shadow border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Actions
                    </h5>
                </div>
                <div class="card-body">
                    <button type="button" 
                            class="btn btn-danger w-100" 
                            data-bs-toggle="modal" 
                            data-bs-target="#cancelModal">
                        <i class="fas fa-ban me-2"></i>Annuler cette vente
                    </button>
                    <div class="mt-2 text-center">
                        <small class="text-muted">Cette action est irréversible</small>
                    </div>
                </div>
            </div>
            @endif

            <!-- Informations complémentaires -->
            <div class="card shadow mt-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>
                        Historique
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="timeline">
                        <li>
                            <strong>Création:</strong>
                            <span class="float-end text-muted">{{ $vente->created_at->format('d/m/Y H:i') }}</span>
                        </li>
                        @if($vente->updated_at != $vente->created_at)
                        <li>
                            <strong>Dernière modification:</strong>
                            <span class="float-end text-muted">{{ $vente->updated_at->format('d/m/Y H:i') }}</span>
                        </li>
                        @endif
                        @if($vente->annule_le)
                        <li>
                            <strong>Annulation:</strong>
                            <span class="float-end text-muted">{{ $vente->annule_le->format('d/m/Y H:i') }}</span>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'annulation -->
@if($vente->statut == 'complete')
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmer l'annulation
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.sales.cancel', $vente) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Attention!</strong> Vous êtes sur le point d'annuler cette vente.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Raison de l'annulation <span class="text-danger">*</span></label>
                        <textarea name="raison" class="form-control" rows="4" required 
                                  placeholder="Veuillez indiquer la raison de l'annulation..."></textarea>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Le stock de {{ $vente->produit->nom }} sera automatiquement restauré de {{ $vente->volume }}L.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-ban me-1"></i> Confirmer l'annulation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<style>
.color-box {
    width: 30px;
    height: 30px;
    border-radius: 5px;
    border: 1px solid #dee2e6;
}
.timeline {
    list-style: none;
    padding: 0;
}
.timeline li {
    padding: 5px 0;
    border-bottom: 1px solid #eee;
}
.timeline li:last-child {
    border-bottom: none;
}
</style>
@endsection