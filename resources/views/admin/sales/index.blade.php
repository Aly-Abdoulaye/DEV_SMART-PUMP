{{-- resources/views/admin/sales/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Gestion des Ventes')

@section('content')
<div class="container-fluid px-4">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-gray-800">
                <i class="fas fa-cash-register text-primary me-2"></i>
                Ventes et Transactions
            </h1>
            <p class="text-muted mb-0">Consultez et gérez toutes les transactions de vos stations</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.sales.statistics') }}" class="btn btn-outline-primary">
                <i class="fas fa-chart-bar me-1"></i> Statistiques
            </a>
            <div class="btn-group">
                <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-download me-1"></i> Exporter
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.sales.export.excel', request()->query()) }}">
                            <i class="fas fa-file-excel text-success me-2"></i> Excel
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.sales.export.pdf', request()->query()) }}">
                            <i class="fas fa-file-pdf text-danger me-2"></i> PDF
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Cartes de statistiques -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Ventes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['total_ventes'], 0, ',', ' ') }}
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
                                Volume Total
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['total_volume'], 2, ',', ' ') }} L
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
                                Chiffre d'Affaires
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                        {{ number_format($stats['total_montant'], 0, ',', ' ') }} FCFA
                                    </div>
                                </div>
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
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Ventes Aujourd'hui
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['ventes_aujourdhui'], 0, ',', ' ') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Carte principale avec filtres -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-2"></i>Filtres de recherche
            </h6>
            <span class="badge bg-info">{{ $sales->total() }} ventes trouvées</span>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.sales.index') }}" id="filterForm">
                <div class="row g-3">
                    <!-- Station -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Station</label>
                        <select name="station_id" class="form-select">
                            <option value="">Sélectionnez une station</option>
                                @foreach($stations as $station)
                                    <option value="{{ $station->id }}" 
                                        {{ old('station_id') == $station->id ? 'selected' : '' }}>
                                        {{ $station->name }} 
                                        @if($station->address)
                                            - {{ $station->address }}
                                        @endif
                                    </option>
                                @endforeach
                        </select>
                    </div>

                    <!-- Pompe -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Pompe</label>
                        <select name="pump_id" class="form-select">
                            <option value="">Toutes les pompes</option>
                            @foreach($pumps as $pump)
                            <option value="{{ $pump->id }}" 
                                {{ request('pump_id') == $pump->id ? 'selected' : '' }}>
                                Pompe {{ $pump->numero }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Période rapide -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Période rapide</label>
                        <select name="periode" class="form-select">
                            <option value="">Sélectionner</option>
                            <option value="today" {{ request('periode') == 'today' ? 'selected' : '' }}>Aujourd'hui</option>
                            <option value="yesterday" {{ request('periode') == 'yesterday' ? 'selected' : '' }}>Hier</option>
                            <option value="week" {{ request('periode') == 'week' ? 'selected' : '' }}>Cette semaine</option>
                            <option value="month" {{ request('periode') == 'month' ? 'selected' : '' }}>Ce mois</option>
                            <option value="last_month" {{ request('periode') == 'last_month' ? 'selected' : '' }}>Mois dernier</option>
                        </select>
                    </div>

                    <!-- Mode de paiement -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Mode de paiement</label>
                        <select name="payment_method" class="form-select">
                            <option value="">Tous les modes</option>
                            <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Espèces</option>
                            <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>Carte bancaire</option>
                            <option value="mobile_money" {{ request('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                            <option value="customer_account" {{ request('payment_method') == 'customer_account' ? 'selected' : '' }}>Compte client</option>
                        </select>
                    </div>

                    <!-- Dates personnalisées -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Date début</label>
                        <input type="date" name="date_debut" class="form-control" 
                               value="{{ request('date_debut') }}" id="date_debut">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Date fin</label>
                        <input type="date" name="date_fin" class="form-control" 
                               value="{{ request('date_fin') }}" id="date_fin">
                    </div>

                    <!-- Statut -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Statut</label>
                        <select name="status" class="form-select">
                            <option value="">Tous les statuts</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Complète</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                        </select>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i> Rechercher
                                </button>
                                <a href="{{ route('admin.sales.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-redo me-1"></i> Réinitialiser
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des ventes -->
    <div class="card shadow">
        <div class="card-body">
            @if($sales->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="salesTable">
                    <thead class="table-dark">
                        <tr>
                            <th>N° Ticket</th>
                            <th>Date & Heure</th>
                            <th>Station</th>
                            <th>Pompe</th>
                            <th>Volume (L)</th>
                            <th>Prix Unitaire</th>
                            <th>Montant Total</th>
                            <th>Mode Paiement</th>
                            <th>Employé</th>
                            <th>Statut</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales as $sale)
                        <tr class="{{ $sale->status == 'cancelled' ? 'table-danger' : ($sale->status == 'pending' ? 'table-warning' : '') }}">
                            <td>
                                <strong class="text-primary">{{ $sale->numero_ticket }}</strong>
                            </td>
                            <td>
                                <small>{{ $sale->sale_date->format('d/m/Y H:i') }}</small>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $sale->station->nom }}</span>
                            </td>
                            <td>
                                <small>Pompe {{ $sale->pump->numero }}</small>
                            </td>
                            <td class="text-end">
                                <span class="badge bg-primary">
                                    {{ number_format($sale->volume, 2, ',', ' ') }} L
                                </span>
                            </td>
                            <td class="text-end">
                                {{ number_format($sale->unit_price, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="text-end fw-bold text-success">
                                {{ number_format($sale->total_amount, 0, ',', ' ') }} FCFA
                            </td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ $sale->payment_method_label }}
                                </span>
                            </td>
                            <td>
                                <small>{{ $sale->employee->name ?? 'N/A' }}</small>
                            </td>
                            <td>
                                @if($sale->status == 'completed')
                                <span class="badge bg-success">Complète</span>
                                @elseif($sale->status == 'cancelled')
                                <span class="badge bg-danger">Annulée</span>
                                @else
                                <span class="badge bg-warning">En attente</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.sales.show', $sale) }}" 
                                       class="btn btn-info" title="Détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.sales.ticket', $sale) }}" 
                                       target="_blank" class="btn btn-secondary" title="Ticket">
                                        <i class="fas fa-receipt"></i>
                                    </a>
                                    @if($sale->status == 'completed')
                                    <button type="button" class="btn btn-warning" 
                                            title="Annuler" data-bs-toggle="modal" 
                                            data-bs-target="#cancelSaleModal{{ $sale->id }}">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                    @endif
                                </div>
                                
                                <!-- Modal d'annulation -->
                                @if($sale->status == 'completed')
                                <div class="modal fade" id="cancelSaleModal{{ $sale->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Annuler la vente</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('admin.sales.cancel', $sale) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <p>
                                                        Vente <strong>{{ $sale->numero_ticket }}</strong><br>
                                                        Montant: <strong>{{ number_format($sale->total_amount, 0, ',', ' ') }} FCFA</strong><br>
                                                        Volume: <strong>{{ number_format($sale->volume, 2, ',', ' ') }} L</strong>
                                                    </p>
                                                    <div class="mb-3">
                                                        <label class="form-label">Raison de l'annulation *</label>
                                                        <textarea name="reason" class="form-control" 
                                                                  rows="3" required></textarea>
                                                    </div>
                                                    <div class="alert alert-warning">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                        Le stock sera restauré automatiquement.
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" 
                                                            data-bs-dismiss="modal">Annuler</button>
                                                    <button type="submit" class="btn btn-danger">
                                                        Confirmer l'annulation
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Affichage de {{ $sales->firstItem() }} à {{ $sales->lastItem() }} 
                    sur {{ $sales->total() }} ventes
                </div>
                {{ $sales->links() }}
            </div>
            
            @else
            <div class="text-center py-5">
                <i class="fas fa-cash-register fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">Aucune vente trouvée</h4>
                <p class="text-muted">Modifiez vos critères de recherche ou attendez de nouvelles ventes.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Synchroniser les dates
    const dateDebut = document.getElementById('date_debut');
    const dateFin = document.getElementById('date_fin');
    
    if (dateDebut && dateFin) {
        dateDebut.addEventListener('change', function() {
            if (!dateFin.value || new Date(dateFin.value) < new Date(this.value)) {
                dateFin.value = this.value;
            }
        });
    }
});
</script>
@endpush