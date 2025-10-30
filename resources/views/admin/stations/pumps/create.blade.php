@extends('layouts.admin')

@section('title', 'Créer une Pompe - ' . $station->name)
@section('page-title', 'Créer une Nouvelle Pompe')
@section('page-subtitle', $station->name)

@section('page-actions')
    <a href="{{ route('admin.stations.pumps.index', $station) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Retour aux pompes
    </a>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-plus-circle me-2"></i>Informations de la Pompe
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.stations.pumps.store', $station) }}" method="POST">
                        @csrf

                        <div class="row">
                            <!-- Numéro de pompe -->
                            <div class="col-md-6 mb-3">
                                <label for="pump_number" class="form-label required">Numéro de pompe</label>
                                <input type="text" class="form-control @error('pump_number') is-invalid @enderror"
                                       id="pump_number" name="pump_number" value="{{ old('pump_number') }}"
                                       placeholder="Ex: P001, Pompe 1" required>
                                @error('pump_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Numéro de buse -->
                            <div class="col-md-6 mb-3">
                                <label for="nozzle_number" class="form-label">Numéro de buse</label>
                                <input type="text" class="form-control @error('nozzle_number') is-invalid @enderror"
                                       id="nozzle_number" name="nozzle_number" value="{{ old('nozzle_number') }}"
                                       placeholder="Ex: B01, Buse 1">
                                @error('nozzle_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Cuve associée -->
                            <div class="col-md-6 mb-3">
                                <label for="tank_id" class="form-label">Cuve associée</label>
                                <select class="form-select @error('tank_id') is-invalid @enderror"
                                        id="tank_id" name="tank_id">
                                    <option value="">Sélectionner une cuve...</option>
                                    @foreach($tanks as $tank)
                                        <option value="{{ $tank->id }}" {{ old('tank_id') == $tank->id ? 'selected' : '' }}>
                                            {{ $tank->fuel_type }} ({{ number_format($tank->current_volume) }} L)
                                        </option>
                                    @endforeach
                                </select>
                                @error('tank_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Optionnel - Vous pourrez associer une cuve plus tard
                                </div>
                            </div>

                            <!-- Statut -->
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label required">Statut</label>
                                <select class="form-select @error('status') is-invalid @enderror"
                                        id="status" name="status" required>
                                    @foreach($statusOptions as $value => $label)
                                        <option value="{{ $value }}" {{ old('status', 'active') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Index initial -->
                            <div class="col-md-6 mb-3">
                                <label for="initial_index" class="form-label required">Index initial</label>
                                <input type="number" class="form-control @error('initial_index') is-invalid @enderror"
                                       id="initial_index" name="initial_index" value="{{ old('initial_index', 0) }}"
                                       min="0" step="0.01" required>
                                @error('initial_index')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror>
                                <div class="form-text">
                                    Index au moment de l'installation
                                </div>
                            </div>

                            <!-- Index actuel -->
                            <div class="col-md-6 mb-3">
                                <label for="current_index" class="form-label required">Index actuel</label>
                                <input type="number" class="form-control @error('current_index') is-invalid @enderror"
                                       id="current_index" name="current_index" value="{{ old('current_index', 0) }}"
                                       min="0" step="0.01" required>
                                @error('current_index')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Index actuel de la pompe
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.stations.pumps.index', $station) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Créer la Pompe
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Informations importantes -->
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informations importantes
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Numéro de pompe:</strong> Doit être unique pour cette station
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Association cuve:</strong> Permet de suivre le carburant distribué
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Index:</strong> L'index actuel doit être ≥ à l'index initial
                        </li>
                        <li>
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Statut:</strong> Détermine si la pompe est opérationnelle
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
    const initialIndexInput = document.getElementById('initial_index');
    const currentIndexInput = document.getElementById('current_index');

    // Validation: index actuel ne peut pas être inférieur à l'index initial
    function validateIndexes() {
        const initialIndex = parseFloat(initialIndexInput.value);
        const currentIndex = parseFloat(currentIndexInput.value);

        if (currentIndex < initialIndex) {
            currentIndexInput.value = initialIndex;
            alert('L\'index actuel ne peut pas être inférieur à l\'index initial.');
        }
    }

    initialIndexInput.addEventListener('change', validateIndexes);
    currentIndexInput.addEventListener('change', validateIndexes);
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
