@extends('layouts.admin')

@section('title', 'Modifier la Cuve - ' . $station->name)
@section('page-title', 'Modifier la Cuve')
@section('page-subtitle', $station->name)

@section('page-actions')
    <div class="btn-group">
        <a href="{{ route('admin.stations.tanks.show', [$station, $tank]) }}" class="btn btn-info">
            <i class="fas fa-eye me-1"></i> Voir détails
        </a>
        <a href="{{ route('admin.stations.tanks.index', $station) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Retour aux cuves
        </a>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>Modifier les Informations
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.stations.tanks.update', [$station, $tank]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Type de carburant -->
                            <div class="col-md-6 mb-3">
                                <label for="fuel_type" class="form-label required">Type de carburant</label>
                                <select class="form-select @error('fuel_type') is-invalid @enderror"
                                        id="fuel_type" name="fuel_type" required>
                                    <option value="">Sélectionner un type...</option>
                                    @foreach($fuelTypes as $fuel)
                                        <option value="{{ $fuel }}"
                                                {{ old('fuel_type', $tank->fuel_type) == $fuel ? 'selected' : '' }}>
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
                                           id="capacity" name="capacity"
                                           value="{{ old('capacity', $tank->capacity) }}"
                                           min="1" step="0.01" required>
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
                                           id="current_volume" name="current_volume"
                                           value="{{ old('current_volume', $tank->current_volume) }}"
                                           min="0" step="0.01" required>
                                    <span class="input-group-text">L</span>
                                </div>
                                @error('current_volume')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror>
                            </div>

                            <!-- Seuil d'alerte -->
                            <div class="col-md-6 mb-3">
                                <label for="min_threshold" class="form-label required">Seuil d'alerte (litres)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('min_threshold') is-invalid @enderror"
                                           id="min_threshold" name="min_threshold"
                                           value="{{ old('min_threshold', $tank->min_threshold) }}"
                                           min="0" step="0.01" required>
                                    <span class="input-group-text">L</span>
                                </div>
                                @error('min_threshold')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Statut -->
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox"
                                       id="is_active" name="is_active" value="1"
                                       {{ old('is_active', $tank->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Cuve active
                                </label>
                            </div>
                        </div>

                        <!-- Informations de la cuve -->
                        <div class="alert alert-info">
                            <div class="row">
                                <div class="col-md-6">
                                    <small><strong>Créée le:</strong> {{ $tank->created_at->format('d/m/Y H:i') }}</small>
                                </div>
                                <div class="col-md-6">
                                    <small><strong>Dernière modification:</strong> {{ $tank->updated_at->format('d/m/Y H:i') }}</small>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-12">
                                    <small><strong>Niveau actuel:</strong>
                                        <span class="badge bg-{{ $tank->isLow() ? 'warning' : 'success' }}">
                                            {{ number_format($tank->percentage, 1) }}%
                                        </span>
                                        ({{ number_format($tank->current_volume) }} L / {{ number_format($tank->capacity) }} L)
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.stations.tanks.show', [$station, $tank]) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Annuler
                            </a>
                            <div class="btn-group">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save me-1"></i> Mettre à jour
                                </button>
                            </div>
                        </div>
                    </form>
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
    function validateVolume() {
        const capacity = parseFloat(capacityInput.value);
        const currentVolume = parseFloat(currentVolumeInput.value);

        if (currentVolume > capacity) {
            currentVolumeInput.value = capacity;
            alert('Le volume actuel ne peut pas dépasser la capacité de la cuve.');
        }
    }

    capacityInput.addEventListener('change', validateVolume);
    currentVolumeInput.addEventListener('change', validateVolume);
});
</script>
@endsection
