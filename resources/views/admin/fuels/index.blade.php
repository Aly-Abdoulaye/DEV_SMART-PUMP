@extends('layouts.admin')

@section('title', 'Gestion des Carburants')

@section('content')
<div class="container-fluid">
    <!-- En-tête avec statistiques -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-gas-pump text-primary"></i> Gestion des Carburants
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.fuels.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouveau Carburant
            </a>
            <a href="{{ route('admin.fuels.dashboard') }}" class="btn btn-success">
                <i class="fas fa-chart-bar"></i> Tableau de Bord
            </a>
            <div class="dropdown">
    <button class="btn btn-info dropdown-toggle" type="button" id="exportDropdown" 
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-download"></i> Exporter
    </button>
    <div class="dropdown-menu" aria-labelledby="exportDropdown">
        {{-- Export Excel --}}
        <a class="dropdown-item export-link" href="{{ route('admin.admin.fuels.export') }}" 
           data-format="excel" onclick="handleExport(event, this)">
            <i class="fas fa-file-excel text-success me-2"></i> Format Excel
            </a>
            
            {{-- Export Historique des Prix --}}
            <a class="dropdown-item export-link" href="#" 
            data-format="history" onclick="handleExport(event, this)">
                <i class="fas fa-history text-info me-2"></i> Historique des Prix
            </a>
            
            <div class="dropdown-divider"></div>
            
            {{-- Export PDF --}}
            <a class="dropdown-item export-link" href="#" 
            data-format="pdf" onclick="handleExport(event, this)">
                <i class="fas fa-file-pdf text-danger me-2"></i> Format PDF
            </a>
            </div>
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
                                Total Carburants</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_fuels'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-gas-pump fa-2x text-gray-300"></i>
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
                                Carburants Actifs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_fuels'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                Carburants Inactifs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['inactive_fuels'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-pause-circle fa-2x text-gray-300"></i>
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
                                Changements de Prix</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_price_changes'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtres et Recherche</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.fuels.index') }}" class="form-inline">
                <div class="form-group mr-3 mb-2">
                    <label for="status" class="sr-only">Statut</label>
                    <select name="status" id="status" class="form-control">
                        <option value="all" {{ $request->status == 'all' ? 'selected' : '' }}>Tous les statuts</option>
                        <option value="active" {{ $request->status == 'active' ? 'selected' : '' }}>Actifs</option>
                        <option value="inactive" {{ $request->status == 'inactive' ? 'selected' : '' }}>Inactifs</option>
                    </select>
                </div>
                
                <div class="form-group mr-3 mb-2">
                    <label for="search" class="sr-only">Recherche</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           placeholder="Nom, code ou description..." value="{{ $request->search }}">
                </div>
                
                <button type="submit" class="btn btn-primary mb-2">
                    <i class="fas fa-search"></i> Appliquer
                </button>
                
                @if($request->has('status') || $request->has('search'))
                    <a href="{{ route('admin.fuels.index') }}" class="btn btn-secondary mb-2 ml-2">
                        <i class="fas fa-times"></i> Effacer
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Tableau des carburants -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                Liste des Carburants ({{ $fuels->total() }})
            </h6>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                        data-toggle="dropdown">
                    <i class="fas fa-cog"></i> Actions
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('admin.admin.fuels.export') }}">
                        <i class="fas fa-file-excel text-success"></i> Exporter en Excel
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('admin.fuels.index', array_merge($request->all(), ['sort' => 'name', 'direction' => 'asc'])) }}">
                        Trier par nom (A-Z)
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.fuels.index', array_merge($request->all(), ['sort' => 'price', 'direction' => 'desc'])) }}">
                        Trier par prix (↓)
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($fuels->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-gas-pump fa-4x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-500">Aucun carburant trouvé</h5>
                    <p class="text-muted mb-4">
                        @if($request->has('search') || $request->has('status'))
                            Aucun carburant ne correspond à vos critères de recherche.
                        @else
                            Commencez par ajouter votre premier carburant.
                        @endif
                    </p>
                    <a href="{{ route('admin.fuels.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Ajouter un Carburant
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="fuelsTable">
                        <thead class="thead-light">
                            <tr>
                                <th>Nom</th>
                                <th>Code</th>
                                <th>Prix (FCFA/L)</th>
                                <th>Description</th>
                                <th>Cuves</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fuels as $fuel)
                            <tr class="{{ $fuel->is_active ? '' : 'table-secondary' }}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="color-indicator mr-2" 
                                             style="background-color: {{ $fuel->auto_color }}; width: 16px; height: 16px; border-radius: 50%; border: 2px solid #dee2e6;"></div>
                                        <div>
                                            <strong>{{ $fuel->name }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-history"></i> 
                                                {{ $fuel->priceHistories_count }} modif. prix
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <code class="text-muted">{{ $fuel->code }}</code>
                                </td>
                                <td class="font-weight-bold text-success">
                                    {{ number_format($fuel->price, 0, ',', ' ') }} FCFA
                                </td>
                                <td>
                                    @if($fuel->description)
                                        <span class="text-truncate d-inline-block" style="max-width: 200px;" 
                                              title="{{ $fuel->description }}">
                                            {{ Str::limit($fuel->description, 50) }}
                                        </span>
                                    @else
                                        <span class="text-muted">---</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="text-muted-info">{{ $fuel->tanks_count }}</span>
                                    <small class="text-muted d-block">cuve(s)</small>
                                </td>
                                <td class="text-center">
                                    @if($fuel->is_active)
                                        <span class="text-muted">
                                            <i class="fas fa-check"></i> Actif
                                        </span>
                                    @else
                                        <span class="text-muted">
                                            <i class="fas fa-pause"></i> Inactif
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <!-- Modifier le prix -->
                                        <button type="button" class="btn btn-warning btn-sm" 
                                                data-toggle="modal" data-target="#priceModal{{ $fuel->id }}"
                                                title="Modifier le prix">
                                            <i class="fas fa-euro-sign"></i>
                                        </button>
                                        
                                        <!-- Voir détails -->
                                        <a href="{{ route('admin.fuels.show', $fuel) }}" class="btn btn-info btn-sm"
                                           title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <!-- Modifier -->
                                        <a href="{{ route('admin.fuels.edit', $fuel) }}" class="btn btn-primary btn-sm"
                                           title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <!-- Activer/Désactiver -->
                                        <form action="{{ route('admin.fuels.toggle-status', $fuel) }}" method="POST" 
                                              class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-{{ $fuel->is_active ? 'secondary' : 'success' }} btn-sm"
                                                    title="{{ $fuel->is_active ? 'Désactiver' : 'Activer' }}"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir {{ $fuel->is_active ? 'désactiver' : 'activer' }} ce carburant?')">
                                                <i class="fas fa-{{ $fuel->is_active ? 'pause' : 'play' }}"></i>
                                            </button>
                                        </form>
                                        
                                        <!-- Supprimer -->
                                        <form action="{{ route('admin.fuels.destroy', $fuel) }}" method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement ce carburant?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Supprimer"
                                                    {{ $fuel->tanks_count > 0 ? 'disabled' : '' }}>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Modal pour modifier le prix -->
                                    <div class="modal fade" id="priceModal{{ $fuel->id }}" tabindex="-1">
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
                                                <form action="{{ route('admin.fuels.update-price', $fuel) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">Prix actuel</label>
                                                            <input type="text" class="form-control bg-light" 
                                                                   value="{{ number_format($fuel->price, 0, ',', ' ') }} FCFA" 
                                                                   disabled>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="new_price{{ $fuel->id }}" class="font-weight-bold">
                                                                Nouveau prix (FCFA/L) *
                                                            </label>
                                                            <input type="number" step="0.01" name="new_price" 
                                                                   id="new_price{{ $fuel->id }}" 
                                                                   value="{{ $fuel->price }}" 
                                                                   class="form-control" required min="0">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="change_reason{{ $fuel->id }}" class="font-weight-bold">
                                                                Raison du changement *
                                                            </label>
                                                            <select name="change_reason" id="change_reason{{ $fuel->id }}" 
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
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Affichage de {{ $fuels->firstItem() }} à {{ $fuels->lastItem() }} sur {{ $fuels->total() }} carburants
                    </div>
                    {{ $fuels->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Auto-focus sur le champ de recherche
document.addEventListener('DOMContentLoaded', function() {
    @if($request->has('search'))
        document.getElementById('search').focus();
    @endif

    // Auto-focus sur le champ de prix dans les modals
    $('.modal').on('shown.bs.modal', function() {
        $(this).find('input[type="number"]').focus();
    });
});
function handleExport(event, element) {
    event.preventDefault();
    
    console.log('Export clicked:', element.href);
    
    // Afficher un indicateur de chargement
    const originalHtml = element.innerHTML;
    element.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Génération...';
    
    // Désactiver tous les liens d'export temporairement
    document.querySelectorAll('.export-link').forEach(link => {
        link.style.pointerEvents = 'none';
        link.classList.add('disabled');
    });
    
    // Faire la requête
    fetch(element.href, {
        method: 'GET',
        headers: {
            'Accept': 'application/json, text/plain, */*',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (response.ok) {
            return response.blob();
        }
        throw new Error('Network response was not ok.');
    })
    .then(blob => {
        // Créer un lien de téléchargement
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        
        // Nom du fichier basé sur l'URL
        const filename = element.getAttribute('data-format') + '-export-' + new Date().toISOString().split('T')[0] + '.csv';
        a.download = filename;
        
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        
        // Afficher un message de succès
        showAlert('Export réussi! Le téléchargement a commencé.', 'success');
    })
    .catch(error => {
        console.error('Export error:', error);
        
        // Si fetch échoue, essayer d'ouvrir le lien normalement
        window.open(element.href, '_blank');
        
        showAlert('Export en cours... Si le téléchargement ne démarre pas, vérifiez votre bloqueur de publicités.', 'info');
    })
    .finally(() => {
        // Restaurer l'état original après 2 secondes
        setTimeout(() => {
            element.innerHTML = originalHtml;
            document.querySelectorAll('.export-link').forEach(link => {
                link.style.pointerEvents = 'auto';
                link.classList.remove('disabled');
            });
        }, 2000);
    });
}

function showAlert(message, type) {
    // Créer une alerte Bootstrap
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alert.innerHTML = `
        ${message}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    `;
    
    document.body.appendChild(alert);
    
    // Auto-supprimer après 5 secondes
    setTimeout(() => {
        if (alert.parentNode) {
            alert.parentNode.removeChild(alert);
        }
    }, 5000);
}
</script>
<style>
.export-link.disabled {
    opacity: 0.6;
    pointer-events: none;
}
</style>
@endsection