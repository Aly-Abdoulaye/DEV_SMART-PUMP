@extends('layouts.admin')

@section('title', $tank->fuel_type . ' - ' . $station->name)
@section('page-title', 'Détails de la Cuve')
@section('page-subtitle', $station->name)

@section('page-actions')
    <div class="btn-group">
        <a href="{{ route('admin.stations.tanks.edit', [$station, $tank]) }}" class="btn btn-warning">
            <i class="fas fa-edit me-1"></i> Modifier
        </a>
        <form action="{{ route('admin.stations.tanks.toggle-status', [$station, $tank]) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-{{ $tank->is_active ? 'secondary' : 'success' }}">
                <i class="fas fa-{{ $tank->is_active ? 'pause' : 'play' }} me-1"></i>
                {{ $tank->is_active ? 'Désactiver' : 'Activer' }}
            </button>
        </form>
        <a href="{{ route('admin.stations.tanks.index', $station) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Retour
        </a>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Informations principales -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informations de la Cuve
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%"><strong>Type de carburant:</strong></td>
                            <td>
                                <span class="badge bg-info fs-6">{{ $tank->fuel_type }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Volume actuel:</strong></td>
                            <td>
                                <span class="fw-bold fs-5">{{ number_format($tank->current_volume, 0) }} L</span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Capacité totale:</strong></td>
                            <td>{{ number_format($tank->capacity, 0) }} L</td>
                        </tr>
                        <tr>
                            <td><strong>Disponible:</strong></td>
                            <td>
                                <span class="text-success fw-bold">{{ number_format($tank->available_capacity, 0) }} L</span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Seuil d'alerte:</strong></td>
                            <td>
                                <span class="badge bg-{{ $tank->isLow() ? 'warning' : 'secondary' }}">
                                    {{ number_format($tank->min_threshold, 0) }} L
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Statut:</strong></td>
                            <td>
                                <span class="badge bg-{{ $tank->is_active ? 'success' : 'danger' }}">
                                    {{ $tank->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Pompes associées:</strong></td>
                            <td>
                                <span class="badge bg-secondary">{{ $tank->pumps->count() }}</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Indicateur de niveau -->
            <div class="card shadow mt-4">
                <div class="card-header bg-{{ $tank->isLow() ? 'warning' : 'info' }} text-white py-3">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Niveau de la Cuve
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h3 class="{{ $tank->isLow() ? 'text-warning' : 'text-success' }}">
                            {{ number_format($tank->percentage, 1) }}%
                        </h3>
                        <p class="text-muted">
                            {{ number_format($tank->current_volume, 0) }} L / {{ number_format($tank->capacity, 0) }} L
                        </p>
                    </div>

                    <div class="progress" style="height: 30px;">
                        @php
                            $percentage = $tank->percentage;
                            if ($percentage < 20) {
                                $progressClass = 'bg-danger';
                            } elseif ($percentage < 50) {
                                $progressClass = 'bg-warning';
                            } else {
                                $progressClass = 'bg-success';
                            }
                        @endphp
                        <div class="progress-bar {{ $progressClass }} progress-bar-striped"
                             role="progressbar"
                             style="width: {{ $percentage }}%;"
                             aria-valuenow="{{ $percentage }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                            {{ number_format($percentage, 1) }}%
                        </div>
                    </div>

                    @if($tank->isLow())
                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Alerte Stock Bas!</strong> Le niveau est inférieur au seuil d'alerte.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions et historique -->
        <div class="col-lg-6 mb-4">
            <!-- Ajustement du volume -->
            <div class="card shadow">
                <div class="card-header bg-success text-white py-3">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-sliders-h me-2"></i>Ajuster le Volume
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.stations.tanks.adjust-volume', [$station, $tank]) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="adjustment_type" class="form-label">Type d'ajustement</label>
                            <select class="form-select" id="adjustment_type" name="adjustment_type" required>
                                <option value="add">Ajouter du carburant</option>
                                <option value="withdraw">Retirer du carburant</option>
                                <option value="set">Définir le volume</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantité (litres)</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="quantity" name="quantity"
                                       min="0.01" step="0.01" required>
                                <span class="input-group-text">L</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="reason" class="form-label">Raison</label>
                            <input type="text" class="form-control" id="reason" name="reason"
                                   placeholder="Ex: Livraison, Correction, etc." required>
                        </div>

                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-calculator me-1"></i> Appliquer l'ajustement
                        </button>
                    </form>
                </div>
            </div>

            <!-- Pompes associées -->
            <div class="card shadow mt-4">
                <div class="card-header bg-secondary text-white py-3">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-gas-pump me-2"></i>Pompes Associées
                    </h6>
                </div>
                <div class="card-body">
                    @if($tank->pumps->count() > 0)
                        <div class="list-group">
                            @foreach($tank->pumps as $pump)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">Pompe #{{ $pump->pump_number }}</h6>
                                    @if($pump->nozzle_number)
                                        <small class="text-muted">Buse: {{ $pump->nozzle_number }}</small>
                                    @endif
                                </div>
                                <span class="badge bg-{{ $pump->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ $pump->status === 'active' ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-gas-pump fa-2x mb-2"></i>
                            <p>Aucune pompe associée à cette cuve</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="card shadow mt-4">
                <div class="card-header bg-info text-white py-3">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-bolt me-2"></i>Actions Rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <a href="{{ route('admin.stations.tanks.edit', [$station, $tank]) }}"
                               class="btn btn-outline-primary w-100">
                                <i class="fas fa-edit me-1"></i> Modifier
                            </a>
                        </div>
                        <div class="col-md-6">
                            <form action="{{ route('admin.stations.tanks.toggle-status', [$station, $tank]) }}"
                                  method="POST" class="d-inline w-100">
                                @csrf
                                <button type="submit"
                                        class="btn btn-{{ $tank->is_active ? 'outline-warning' : 'outline-success' }} w-100">
                                    <i class="fas fa-{{ $tank->is_active ? 'pause' : 'play' }} me-1"></i>
                                    {{ $tank->is_active ? 'Désactiver' : 'Activer' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.progress {
    border-radius: 15px;
}
.progress-bar {
    border-radius: 15px;
    font-weight: bold;
}
</style>
@endsection
