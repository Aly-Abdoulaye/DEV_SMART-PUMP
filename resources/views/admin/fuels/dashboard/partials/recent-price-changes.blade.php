{{-- Partial pour les changements de prix récents - VERSION PDF --}}
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-exchange-alt me-2"></i>Changements de Prix Récents
        </h6>
        {{-- ⭐ Lien d'export PDF --}}
        <button type="button" class="btn btn-sm btn-outline-danger" onclick="exportPriceHistoryPDF()">
            <i class="fas fa-file-pdf me-1"></i>Exporter PDF
        </button>
    </div>
    <div class="card-body">
        @if(isset($recentPriceChanges) && $recentPriceChanges->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="priceChangesTable">
                    <thead>
                        <tr>
                            <th>Carburant</th>
                            <th>Ancien Prix</th>
                            <th>Nouveau Prix</th>
                            <th>Variation</th>
                            <th>Date</th>
                            <th>Raison</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentPriceChanges as $change)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="fuel-color-badge me-2" style="background-color: {{ $change['color'] }}"></span>
                                    <strong>{{ $change['fuel_name'] }}</strong>
                                </div>
                            </td>
                            <td class="text-muted">{{ $change['old_price_formatted'] }}</td>
                            <td class="fw-bold text-dark">{{ $change['new_price_formatted'] }}</td>
                            <td>
                                <span class="badge bg-{{ $change['trend'] == 'up' ? 'danger' : ($change['trend'] == 'down' ? 'success' : 'secondary') }}">
                                    <i class="fas fa-arrow-{{ $change['trend'] == 'up' ? 'up' : ($change['trend'] == 'down' ? 'down' : 'right') }} me-1"></i>
                                    {{ $change['change_percentage_formatted'] }}
                                </span>
                                <br>
                                <small class="text-muted">{{ $change['change_amount_formatted'] }}</small>
                            </td>
                            <td>
                                <small class="text-muted">{{ $change['change_date'] }}</small>
                                <br>
                                <small><strong>Par:</strong> {{ $change['changed_by'] }}</small>
                            </td>
                            <td>
                                @if($change['reason'])
                                    <span class="text-muted" title="{{ $change['reason'] }}">
                                        <i class="fas fa-info-circle"></i>
                                        {{ Str::limit($change['reason'], 30) }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Statistiques résumées --}}
            <div class="row mt-3 text-center">
                <div class="col-md-3">
                    <div class="card border-primary">
                        <div class="card-body py-2">
                            <small class="text-muted">Total Changements</small>
                            <h6 class="mb-0">{{ $recentPriceChanges->count() }}</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-success">
                        <div class="card-body py-2">
                            <small class="text-muted">Baisses</small>
                            <h6 class="mb-0 text-success">
                                {{ $recentPriceChanges->where('trend', 'down')->count() }}
                            </h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-danger">
                        <div class="card-body py-2">
                            <small class="text-muted">Hausses</small>
                            <h6 class="mb-0 text-danger">
                                {{ $recentPriceChanges->where('trend', 'up')->count() }}
                            </h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-secondary">
                        <div class="card-body py-2">
                            <small class="text-muted">Stables</small>
                            <h6 class="mb-0 text-secondary">
                                {{ $recentPriceChanges->where('trend', 'stable')->count() }}
                            </h6>
                        </div>
                    </div>
                </div>
            </div>

        @else
            {{-- État vide --}}
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-exchange-alt fa-3x text-muted"></i>
                </div>
                <h5 class="text-muted">Aucun changement de prix récent</h5>
                <p class="text-muted mb-3">Les changements de prix apparaîtront ici</p>
                <a href="{{ route('admin.fuels.index') }}" class="btn btn-primary">
                    <i class="fas fa-gas-pump me-1"></i> Gérer les carburants
                </a>
            </div>
        @endif
    </div>
</div>

{{-- Script pour l'export PDF --}}
<script>
function exportPriceHistoryPDF() {
    // Afficher un indicateur de chargement
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Génération...';
    btn.disabled = true;

    // Récupérer les données
    const companyId = {{ auth()->user()->company_id ?? 0 }};
    
    fetch(`/admin/fuels/export/price-history-pdf?company_id=${companyId}`)
        .then(response => {
            if (!response.ok) throw new Error('Erreur réseau');
            return response.blob();
        })
        .then(blob => {
            // Créer un lien de téléchargement
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = `historique-prix-${new Date().toISOString().split('T')[0]}.pdf`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            
            // Restaurer le bouton
            btn.innerHTML = originalText;
            btn.disabled = false;
            
            // Afficher un message de succès
            showToast('PDF généré avec succès!', 'success');
        })
        .catch(error => {
            console.error('Erreur:', error);
            btn.innerHTML = originalText;
            btn.disabled = false;
            showToast('Erreur lors de la génération du PDF', 'error');
        });
}

function showToast(message, type = 'info') {
    // Créer un toast simple (vous pouvez utiliser votre système de notification existant)
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(toast);
    
    // Auto-supprimer après 5 secondes
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 5000);
}
</script>

<style>
.fuel-color-badge {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    display: inline-block;
    border: 2px solid #fff;
    box-shadow: 0 0 0 1px #dee2e6;
}

.table tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

.badge {
    font-size: 0.75em;
    padding: 0.35em 0.65em;
}
</style>