@extends('layouts.admin')

@section('title', 'Modifier la Pompe - ' . $station->name)
@section('page-title', 'Modifier la Pompe')
@section('page-subtitle', 'Pompe #' . $pump->pump_number . ' - ' . $station->name)

@section('page-actions')
    <div class="btn-group">
        <a href="{{ route('admin.stations.pumps.show', [$station, $pump]) }}" class="btn btn-info">
            <i class="fas fa-eye me-1"></i> Voir détails
        </a>
        <a href="{{ route('admin.stations.pumps.index', $station) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Retour aux pompes
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
                    <form action="{{ route('admin.stations.pumps.update', [$station, $pump]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Numéro de pompe -->
                            <div class="col-md-6 mb-3">
                                <label for="pump_number" class="form-label required">Numéro de pompe</label>
                                <input type="text" class="form-control @error('pump_number') is-invalid @enderror"
                                       id="pump_number" name="pump_number"
                                       value="{{ old('pump_number', $pump->pump_number) }}"
                                       placeholder="Ex: P001, Pompe 1" required>
                                @error('pump_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Numéro de buse -->
                            <div class="col-md-6 mb-3">
                                <label for="nozzle_number" class="form-label">Numéro de buse</label>
                                <input type="text" class="form-control @error('nozzle_number') is-invalid @enderror"
                                       id="nozzle_number" name="nozzle_number"
                                       value="{{ old('nozzle_number', $pump->nozzle_number) }}"
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
                                        <option value="{{ $tank->id }}"
                                                {{ old('tank_id', $pump->tank_id) == $tank->id ? 'selected' : '' }}>
                                            {{ $tank->fuel_type }} ({{ number_format($tank->current_volume) }} L)
                                        </option>
                                    @endforeach
                                </select>
                                @error('tank_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Statut -->
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label required">Statut</label>
                                <select class="form-select @error('status') is-invalid @enderror"
                                        id="status" name="status" required>
                                    @foreach($statusOptions as $value => $label)
                                        <option value="{{ $value }}"
                                                {{ old('status', $pump->status) == $value ? 'selected' : '' }}>
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
                                       id="initial_index" name="initial_index"
                                       value="{{ old('initial_index', $pump->initial_index) }}"
                                       min="0" step="0.01" required>
                                @error('initial_index')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror>
                            </div>

                            <!-- Index actuel -->
                            <div class="col-md-6 mb-3">
                                <label for="current_index" class="form-label required">Index actuel</label>
                                <input type="number" class="form-control @error('current_index') is-invalid @enderror"
                                       id="current_index" name="current_index"
                                       value="{{ old('current_index', $pump->current_index) }}"
                                       min="0" step="0.01" required>
                                @error('current_index')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Informations de la pompe -->
                        <div class="alert alert-info">
                            <div class="row">
                                <div class="col-md-6">
                                    <small><strong>Créée le:</strong> {{ $pump->created_at->format('d/m/Y H:i') }}</small>
                                </div>
                                <div class="col-md-6">
                                    <small><strong>Dernière modification:</strong> {{ $pump->updated_at->format('d/m/Y H:i') }}</small>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <small><strong>Total ventes:</strong>
                                        <span class="badge bg-success">{{ number_format($pump->total_sales, 0) }}</span>
                                    </small>
                                </div>
                                <div class="col-md-6">
                                    <small><strong>Carburant:</strong>
                                        <span class="badge bg-info">{{ $pump->fuel_type }}</span>
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.stations.pumps.show', [$station, $pump]) }}" class="btn btn-outline-secondary">
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
    const initialIndexInput = document.getElementById('initial_index');
    const currentIndexInput = document.getElementById('current_index');

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
