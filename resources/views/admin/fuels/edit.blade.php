@extends('layouts.admin')

@section('title', 'Modifier le Carburant - ' . $fuel->name)

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit text-primary"></i> Modifier le Carburant
        </h1>
        <div>
            <a href="{{ route('admin.fuels.show', $fuel) }}" class="btn btn-info">
                <i class="fas fa-eye"></i> Voir détails
            </a>
            <a href="{{ route('admin.fuels.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </div>

    <!-- Carte du formulaire -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-edit"></i> Modification du carburant "{{ $fuel->name }}"
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.fuels.update', $fuel) }}" method="POST" id="fuelForm">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Colonne gauche -->
                    <div class="col-md-6">
                        <!-- Nom du carburant -->
                        <div class="form-group">
                            <label for="name" class="font-weight-bold">
                                Nom du carburant <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $fuel->name) }}" 
                                   placeholder="Ex: Essence Super, Gazole, SP95..." required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Nom complet du type de carburant
                            </small>
                        </div>

                        <!-- Code du carburant -->
                        <div class="form-group">
                            <label for="code" class="font-weight-bold">
                                Code unique <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                   id="code" name="code" value="{{ old('code', $fuel->code) }}" 
                                   placeholder="Ex: ESS, GAZ, SP95, E85..." required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Code court et unique pour identifier le carburant
                            </small>
                        </div>

                        <!-- Station -->
                        <div class="form-group">
                            <label for="station_id" class="font-weight-bold">
                                Station associée <span class="text-danger">*</span>
                            </label>
                            <select class="form-control @error('station_id') is-invalid @enderror" 
                                    id="station_id" name="station_id" required>
                                <option value="">Sélectionnez une station</option>
                                @foreach($stations as $station)
                                    <option value="{{ $station->id }}" 
                                        {{ old('station_id', $fuel->station_id) == $station->id ? 'selected' : '' }}>
                                        {{ $station->name }} 
                                        @if($station->address)
                                            - {{ $station->address }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('station_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Station où ce carburant sera disponible
                            </small>
                        </div>
                    </div>

                    <!-- Colonne droite -->
                    <div class="col-md-6">
                        <!-- Prix -->
                        <div class="form-group">
                            <label for="price" class="font-weight-bold">
                                Prix (FCFA/L) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" 
                                       class="form-control @error('price') is-invalid @enderror" 
                                       id="price" name="price" value="{{ old('price', $fuel->price) }}" 
                                       placeholder="0.00" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">FCFA/L</span>
                                </div>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">
                                Prix actuel du carburant par litre
                            </small>
                        </div>

                        <!-- Statut -->
                        <div class="form-group">
                            <label class="font-weight-bold">Statut</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" 
                                       id="is_active" name="is_active" value="1" 
                                       {{ old('is_active', $fuel->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">
                                    <span id="statusText">
                                        {{ $fuel->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Un carburant inactif ne sera pas disponible pour la vente
                            </small>
                        </div>

                        <!-- Informations de suivi -->
                        <div class="card border-left-info shadow">
                            <div class="card-body py-3">
                                <h6 class="font-weight-bold text-info">
                                    <i class="fas fa-info-circle"></i> Informations de suivi
                                </h6>
                                <div class="small">
                                    <div class="mb-1">
                                        <strong>Créé le :</strong> 
                                        {{ $fuel->created_at->format('d/m/Y à H:i') }}
                                    </div>
                                    <div class="mb-1">
                                        <strong>Modifié le :</strong> 
                                        {{ $fuel->updated_at->format('d/m/Y à H:i') }}
                                    </div>
                                    @if($fuel->priceHistory->count() > 0)
                                    <div>
                                        <strong>Dernier changement de prix :</strong> 
                                        {{ $fuel->priceHistory->first()->created_at->format('d/m/Y') }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label for="description" class="font-weight-bold">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="3" 
                              placeholder="Description optionnelle du carburant...">{{ old('description', $fuel->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        Caractéristiques spécifiques ou informations supplémentaires
                    </small>
                </div>

                <!-- Aperçu en temps réel -->
                <div class="card border-left-primary shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-eye"></i> Aperçu des modifications
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="color-preview mr-3" 
                                         style="width: 20px; height: 20px; border-radius: 50%; background-color: {{ $fuel->color }};"></div>
                                    <div>
                                        <h5 class="mb-0" id="previewName">
                                            {{ $fuel->name }}
                                        </h5>
                                        <code class="text-muted" id="previewCode">
                                            {{ $fuel->code }}
                                        </code>
                                    </div>
                                </div>
                                <p class="mb-1" id="previewDescription">
                                    {{ $fuel->description ?: 'Description du carburant...' }}
                                </p>
                                <div class="mt-2">
                                    <span class="badge badge-{{ $fuel->is_active ? 'success' : 'secondary' }}" id="previewStatus">
                                        {{ $fuel->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                    <span class="badge badge-info ml-2" id="previewPrice">
                                        {{ number_format($fuel->price, 0, ',', ' ') }} FCFA/L
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4 text-right">
                                <div id="previewStation" class="text-muted">
                                    {{ $fuel->station->name ?? 'Station non sélectionnée' }}
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-history"></i>
                                        {{ $fuel->priceHistory->count() }} changements de prix
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="form-group mt-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('admin.fuels.show', $fuel) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                            <button type="button" class="btn btn-outline-danger" data-toggle="modal" data-target="#deleteModal">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-save"></i> Mettre à jour
                            </button>
                            <button type="button" class="btn btn-outline-primary" onclick="resetForm()">
                                <i class="fas fa-undo"></i> Réinitialiser
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-exclamation-triangle"></i> Confirmation de suppression
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer le carburant <strong>"{{ $fuel->name }}"</strong> ?</p>
                
                @if($fuel->tanks_count > 0)
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Attention :</strong> Ce carburant est associé à {{ $fuel->tanks_count }} cuve(s). 
                    La suppression n'est possible que si aucune cuve n'est associée.
                </div>
                @else
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    Cette action est irréversible. Toutes les données associées à ce carburant seront perdues.
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Annuler
                </button>
                
                @if($fuel->tanks_count === 0)
                <form action="{{ route('admin.fuels.destroy', $fuel) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Confirmer la suppression
                    </button>
                </form>
                @else
                <button type="button" class="btn btn-danger" disabled>
                    <i class="fas fa-ban"></i> Suppression impossible
                </button>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Données originales pour la réinitialisation
const originalData = {
    name: "{{ $fuel->name }}",
    code: "{{ $fuel->code }}",
    price: "{{ $fuel->price }}",
    description: "{{ $fuel->description }}",
    station_id: "{{ $fuel->station_id }}",
    is_active: {{ $fuel->is_active ? 'true' : 'false' }}
};

// Mise à jour en temps réel de l'aperçu
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const codeInput = document.getElementById('code');
    const priceInput = document.getElementById('price');
    const descriptionInput = document.getElementById('description');
    const statusSwitch = document.getElementById('is_active');
    const stationSelect = document.getElementById('station_id');

    // Fonction de mise à jour de l'aperçu
    function updatePreview() {
        // Nom
        document.getElementById('previewName').textContent = nameInput.value || originalData.name;
        
        // Code
        document.getElementById('previewCode').textContent = codeInput.value || originalData.code;
        
        // Prix
        const price = parseFloat(priceInput.value) || parseFloat(originalData.price);
        document.getElementById('previewPrice').textContent = 
            price.toLocaleString('fr-FR') + ' FCFA/L';
        
        // Description
        document.getElementById('previewDescription').textContent = 
            descriptionInput.value || originalData.description || 'Description du carburant...';
        
        // Statut
        const statusText = statusSwitch.checked ? 'Actif' : 'Inactif';
        document.getElementById('previewStatus').textContent = statusText;
        document.getElementById('previewStatus').className = 
            statusSwitch.checked ? 'badge badge-success' : 'badge badge-secondary';
        document.getElementById('statusText').textContent = statusText;
        
        // Station
        const selectedStation = stationSelect.options[stationSelect.selectedIndex];
        document.getElementById('previewStation').textContent = 
            selectedStation.text || 'Station non sélectionnée';
    }

    // Écouteurs d'événements
    [nameInput, codeInput, priceInput, descriptionInput, stationSelect].forEach(input => {
        input.addEventListener('input', updatePreview);
        input.addEventListener('change', updatePreview);
    });

    statusSwitch.addEventListener('change', updatePreview);

    // Formatage du prix
    priceInput.addEventListener('blur', function() {
        if (this.value) {
            this.value = parseFloat(this.value).toFixed(2);
        }
    });

    // Initialisation de l'aperçu
    updatePreview();
});

// Réinitialiser le formulaire
function resetForm() {
    if (!confirm('Voulez-vous réinitialiser le formulaire ? Toutes les modifications non sauvegardées seront perdues.')) {
        return;
    }

    document.getElementById('name').value = originalData.name;
    document.getElementById('code').value = originalData.code;
    document.getElementById('price').value = originalData.price;
    document.getElementById('description').value = originalData.description;
    document.getElementById('station_id').value = originalData.station_id;
    document.getElementById('is_active').checked = originalData.is_active;

    // Mettre à jour l'aperçu
    const updateEvent = new Event('input');
    document.getElementById('name').dispatchEvent(updateEvent);
    document.getElementById('code').dispatchEvent(updateEvent);
    document.getElementById('price').dispatchEvent(updateEvent);
    document.getElementById('description').dispatchEvent(updateEvent);
    document.getElementById('station_id').dispatchEvent(updateEvent);
    document.getElementById('is_active').dispatchEvent(new Event('change'));

    // Afficher un message
    showAlert('Formulaire réinitialisé aux valeurs originales', 'info');
}

// Validation du formulaire
document.getElementById('fuelForm').addEventListener('submit', function(e) {
    const name = document.getElementById('name').value.trim();
    const code = document.getElementById('code').value.trim();
    const price = document.getElementById('price').value;
    const station = document.getElementById('station_id').value;

    if (!name || !code || !price || !station) {
        e.preventDefault();
        showAlert('Veuillez remplir tous les champs obligatoires (*)', 'error');
        return;
    }

    // Vérifier si des modifications ont été faites
    const hasChanges = 
        name !== originalData.name ||
        code !== originalData.code ||
        parseFloat(price) !== parseFloat(originalData.price) ||
        descriptionInput.value !== originalData.description ||
        station !== originalData.station_id ||
        document.getElementById('is_active').checked !== originalData.is_active;

    if (!hasChanges) {
        e.preventDefault();
        showAlert('Aucune modification détectée.', 'warning');
        return;
    }

    // Afficher un indicateur de chargement
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mise à jour...';
    submitBtn.disabled = true;
});

// Fonction d'affichage des alertes
function showAlert(message, type) {
    // Supprimer les alertes existantes
    document.querySelectorAll('.custom-alert').forEach(alert => alert.remove());

    const alertClass = {
        'success': 'alert-success',
        'error': 'alert-danger',
        'warning': 'alert-warning',
        'info': 'alert-info'
    }[type] || 'alert-info';

    const icon = {
        'success': 'fa-check-circle',
        'error': 'fa-exclamation-circle',
        'warning': 'fa-exclamation-triangle',
        'info': 'fa-info-circle'
    }[type] || 'fa-info-circle';

    const alert = document.createElement('div');
    alert.className = `alert ${alertClass} alert-dismissible fade show custom-alert position-fixed`;
    alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alert.innerHTML = `
        <i class="fas ${icon} me-2"></i>${message}
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
.color-preview {
    border: 2px solid #dee2e6;
    transition: background-color 0.3s ease;
}

.form-control:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.custom-switch .custom-control-label::before {
    border-color: #6c757d;
}

.custom-switch .custom-control-input:checked ~ .custom-control-label::before {
    border-color: #4e73df;
    background-color: #4e73df;
}

.card {
    border: 1px solid #e3e6f0;
    border-radius: 0.35rem;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
</style>
@endsection