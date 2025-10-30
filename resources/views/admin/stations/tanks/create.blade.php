@extends('layouts.admin')

@section('title', 'Créer une Cuve - ' . $station->name)
@section('page-title', 'Créer une Nouvelle Cuve')
@section('page-subtitle', $station->name)

@section('page-actions')
    <a href="{{ route('admin.stations.tanks.index', $station) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Retour aux cuves
    </a>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-plus-circle me-2"></i>Informations de la Cuve
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.stations.tanks.store', $station) }}" method="POST">
                        @csrf

                        <div class="row">
                            <!-- Type de carburant -->
                            <div class="col-md-6 mb-3">
                                <label for="fuel_type" class="form-label required">Type de carburant</label>
                                <select class="form-select @error('fuel_type') is-invalid @enderror"
                                        id="fuel_type" name="fuel_type" required>
                                    <option value="">Sélectionner un type...</option>
                                    @foreach($fuelTypes as $fuel)
                                        <option value="{{ $fuel }}" {{ old('fuel_type') == $fuel ? 'selected' : '' }}>
                                            {{ $fuel }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('fuel_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Capacité -->
                            <div class="col-md-6 mb-3">
                                <label for="capacity" class="form-label required">Capacité totale (litres)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('capacity') is-invalid @enderror"
                                           id="capacity" name="capacity" value="{{ old('capacity') }}"
                                           min="1" step="0.01" placeholder="Ex: 30000" required>
                                    <span class="input-group-text">L</span>
                                </div>
                                @error('capacity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Volume actuel -->
                            <div class="col-md-6 mb-3">
                                <label for="current_volume" class="form-label required">Volume actuel (litres)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('current_volume') is-invalid @enderror"
                                           id="current_volume" name="current_volume" value="{{ old('current_volume', 0) }}"
                                           min="0" step="0.01" placeholder="Ex: 15000" required>
                                    <span class="input-group-text">L</span>
                                </div>
                                @error('current_volume')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Seuil d'alerte -->
                            <div class="col-md-6 mb-3">
                                <label for="min_threshold" class="form-label required">Seuil d'alerte (litres)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('min_threshold') is-invalid @enderror"
                                           id="min_threshold" name="min_threshold" value="{{ old('min_threshold', 1000) }}"
                                           min="0" step="0.01" placeholder="Ex: 5000" required>
                                    <span class="input-group-text">L</span>
                                </div>
                                @error('min_threshold')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Alerte lorsque le volume descend en dessous de cette valeur
                                </div>
                            </div>
                        </div>

                        <!-- Statut -->
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox"
                                       id="is_active" name="is_active" value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Cuve active
                                </label>
                            </div>
                            <div class="form-text">
                                Une cuve inactive ne sera pas utilisable pour les ventes
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.stations.tanks.index', $station) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Créer la Cuve
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Informations importantes -->
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Recommandations
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Capacité:</strong> Définir la capacité totale de la cuve
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Volume actuel:</strong> Saisir le volume actuellement dans la cuve
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Seuil d'alerte:</strong> Définir le niveau minimum avant alerte
                        </li>
                        <li>
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Type carburant:</strong> Choisir le type de carburant correspondant
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const capacityInput = document.getElementById('capacity');
    const currentVolumeInput = document.getElementById('current_volume');

    // Validation: volume actuel ne peut pas dépasser la capacité
    capacityInput.addEventListener('change', function() {
        const capacity = parseFloat(this.value);
        const currentVolume = parseFloat(currentVolumeInput.value);

        if (currentVolume > capacity) {
            currentVolumeInput.value = capacity;
            alert('Le volume actuel ne peut pas dépasser la capacité de la cuve.');
        }
    });

    currentVolumeInput.addEventListener('change', function() {
        const capacity = parseFloat(capacityInput.value);
        const currentVolume = parseFloat(this.value);

        if (currentVolume > capacity) {
            this.value = capacity;
            alert('Le volume actuel ne peut pas dépasser la capacité de la cuve.');
        }
    });
});
</script>
@endsection

@section('styles')
<style>
.required::after {
    content: " *";
    color: #dc3545;
}
</style>
@endsection
