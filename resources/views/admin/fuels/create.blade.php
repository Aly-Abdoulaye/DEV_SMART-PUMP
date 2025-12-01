@extends('layouts.admin')

@section('title', 'Ajouter un Carburant')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-gas-pump text-primary"></i> Ajouter un Carburant
        </h1>
        <div>
            <a href="{{ route('admin.fuels.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </div>

    <!-- Carte du formulaire -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-plus-circle"></i> Informations du nouveau carburant
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.fuels.store') }}" method="POST" id="fuelForm">
                @csrf

                <div class="row">
                    <!-- Colonne gauche -->
                    <div class="col-md-6">
                        <!-- Nom du carburant -->
                        <div class="form-group">
                            <label for="name" class="font-weight-bold">
                                Nom du carburant <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" 
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
                                   id="code" name="code" value="{{ old('code') }}" 
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
                                        {{ old('station_id') == $station->id ? 'selected' : '' }}>
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
                                       id="price" name="price" value="{{ old('price') }}" 
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
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">
                                    <span id="statusText">Actif</span>
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Un carburant inactif ne sera pas disponible pour la vente
                            </small>
                        </div>

                        <!-- Couleur personnalisée (optionnel) -->
                        <div class="form-group">
                            <label for="color" class="font-weight-bold">
                                Couleur d'identification
                            </label>
                            <div class="input-group">
                                <input type="color" class="form-control" 
                                       id="color" name="color" value="{{ old('color', '#FF6B6B') }}"
                                       style="height: 38px;">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" 
                                            onclick="resetColor()">
                                        <i class="fas fa-sync-alt"></i> Réinitialiser
                                    </button>
                                </div>
                            </div>
                            <small class="form-text text-muted">
                                Couleur pour identifier visuellement ce carburant (optionnel)
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label for="description" class="font-weight-bold">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="3" 
                              placeholder="Description optionnelle du carburant...">{{ old('description') }}</textarea>
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
                            <i class="fas fa-eye"></i> Aperçu du carburant
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="color-preview mr-3" 
                                         style="width: 20px; height: 20px; border-radius: 50%; background-color: {{ old('color', '#FF6B6B') }};"></div>
                                    <div>
                                        <h5 class="mb-0" id="previewName">
                                            {{ old('name', 'Nom du carburant') }}
                                        </h5>
                                        <code class="text-muted" id="previewCode">
                                            {{ old('code', 'CODE') }}
                                        </code>
                                    </div>
                                </div>
                                <p class="mb-1" id="previewDescription">
                                    {{ old('description', 'Description du carburant...') }}
                                </p>
                                <div class="mt-2">
                                    <span class="badge badge-success" id="previewStatus">Actif</span>
                                    <span class="badge badge-info ml-2" id="previewPrice">
                                        {{ number_format(old('price', 0), 0, ',', ' ') }} FCFA/L
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4 text-right">
                                <div id="previewStation" class="text-muted">
                                    @if(old('station_id') && $stations->find(old('station_id')))
                                        {{ $stations->find(old('station_id'))->name }}
                                    @else
                                        Station non sélectionnée
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="form-group mt-4">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.fuels.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                        <div>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-save"></i> Créer le carburant
                            </button>
                            <button type="button" class="btn btn-outline-primary" onclick="prefillTestData()">
                                <i class="fas fa-vial"></i> Données de test
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Mise à jour en temps réel de l'aperçu
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const codeInput = document.getElementById('code');
    const priceInput = document.getElementById('price');
    const descriptionInput = document.getElementById('description');
    const colorInput = document.getElementById('color');
    const statusSwitch = document.getElementById('is_active');
    const stationSelect = document.getElementById('station_id');

    // Fonction de mise à jour de l'aperçu
    function updatePreview() {
        // Nom
        document.getElementById('previewName').textContent = nameInput.value || 'Nom du carburant';
        
        // Code
        document.getElementById('previewCode').textContent = codeInput.value || 'CODE';
        
        // Prix
        const price = parseFloat(priceInput.value) || 0;
        document.getElementById('previewPrice').textContent = 
            price.toLocaleString('fr-FR') + ' FCFA/L';
        
        // Description
        document.getElementById('previewDescription').textContent = 
            descriptionInput.value || 'Description du carburant...';
        
        // Couleur
        document.querySelector('.color-preview').style.backgroundColor = colorInput.value;
        
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
    [nameInput, codeInput, priceInput, descriptionInput, colorInput, stationSelect].forEach(input => {
        input.addEventListener('input', updatePreview);
        input.addEventListener('change', updatePreview);
    });

    statusSwitch.addEventListener('change', updatePreview);

    // Génération automatique du code si vide
    nameInput.addEventListener('blur', function() {
        if (!codeInput.value && nameInput.value) {
            // Générer un code à partir du nom
            const code = nameInput.value
                .toUpperCase()
                .replace(/[^A-Z]/g, '')
                .substring(0, 3);
            
            if (code.length >= 2) {
                codeInput.value = code;
                updatePreview();
            }
        }
    });

    // Formatage du prix
    priceInput.addEventListener('blur', function() {
        if (this.value) {
            this.value = parseFloat(this.value).toFixed(2);
        }
    });

    // Initialisation de l'aperçu
    updatePreview();
});

// Réinitialiser la couleur
function resetColor() {
    const defaultColors = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7', '#DDA0DD'];
    const randomColor = defaultColors[Math.floor(Math.random() * defaultColors.length)];
    document.getElementById('color').value = randomColor;
    document.querySelector('.color-preview').style.backgroundColor = randomColor;
}

// Pré-remplir avec des données de test (pour le développement)
function prefillTestData() {
    if (!confirm('Voulez-vous pré-remplir le formulaire avec des données de test ?')) {
        return;
    }

    const testFuels = [
        { name: 'Essence Super', code: 'ESS', price: 850, color: '#FF6B6B' },
        { name: 'Gazole', code: 'GAZ', price: 780, color: '#4ECDC4' },
        { name: 'SP95', code: 'SP95', price: 870, color: '#45B7D1' },
        { name: 'SP98', code: 'SP98', price: 890, color: '#96CEB4' },
        { name: 'GPL', code: 'GPL', price: 450, color: '#FFEAA7' },
        { name: 'E85', code: 'E85', price: 650, color: '#DDA0DD' }
    ];

    const randomFuel = testFuels[Math.floor(Math.random() * testFuels.length)];
    
    document.getElementById('name').value = randomFuel.name;
    document.getElementById('code').value = randomFuel.code;
    document.getElementById('price').value = randomFuel.price;
    document.getElementById('color').value = randomFuel.color;
    document.getElementById('description').value = `Carburant ${randomFuel.name} de haute qualité.`;

    // Mettre à jour l'aperçu
    const updateEvent = new Event('input');
    document.getElementById('name').dispatchEvent(updateEvent);
    document.getElementById('code').dispatchEvent(updateEvent);
    document.getElementById('price').dispatchEvent(updateEvent);
    document.getElementById('color').dispatchEvent(updateEvent);
    document.getElementById('description').dispatchEvent(updateEvent);

    // Afficher un message
    alert(`Données de test pour "${randomFuel.name}" chargées !`);
}

// Validation du formulaire
document.getElementById('fuelForm').addEventListener('submit', function(e) {
    const name = document.getElementById('name').value.trim();
    const code = document.getElementById('code').value.trim();
    const price = document.getElementById('price').value;
    const station = document.getElementById('station_id').value;

    if (!name || !code || !price || !station) {
        e.preventDefault();
        alert('Veuillez remplir tous les champs obligatoires (*)');
        return;
    }

    // Afficher un indicateur de chargement
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Création en cours...';
    submitBtn.disabled = true;
});
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
</style>
@endsection