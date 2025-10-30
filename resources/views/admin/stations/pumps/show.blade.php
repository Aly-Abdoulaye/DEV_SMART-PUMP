@extends('layouts.admin')

@section('title', 'Pompe #' . $pump->pump_number . ' - ' . $station->name)
@section('page-title', 'Détails de la Pompe')
@section('page-subtitle', 'Pompe #' . $pump->pump_number . ' - ' . $station->name)

@section('page-actions')
    <div class="btn-group">
        <a href="{{ route('admin.stations.pumps.edit', [$station, $pump]) }}" class="btn btn-warning">
            <i class="fas fa-edit me-1"></i> Modifier
        </a>
        <a href="{{ route('admin.stations.pumps.index', $station) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Retour aux pompes
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
                        <i class="fas fa-info-circle me-2"></i>Informations de la Pompe
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%"><strong>Numéro Pompe:</strong></td>
                            <td>
                                <span class="badge bg-primary fs-6">#{{ $pump->pump_number }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Buse:</strong></td>
                            <td>
                                @if($pump->nozzle_number)
                                    <span class="badge bg-secondary">{{ $pump->nozzle_number }}</span>
                                @else
                                    <span class="text-muted">Non définie</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Cuve associée:</strong></td>
                            <td>
                                @if($pump->tank)
                                    <span class="badge bg-info">
                                        <i class="fas fa-oil-can me-1"></i>
                                        {{ $pump->tank->fuel_type }}
                                    </span>
                                @else
                                    <span class="badge bg-warning">
                                        <i class="fas fa-unlink me-1"></i>
                                        Non associée
                                    </span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Index initial:</strong></td>
                            <td>{{ number_format($pump->initial_index, 0) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Index actuel:</strong></td>
                            <td>
                                <span class="fw-bold fs-5">{{ number_format($pump->current_index, 0) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Total ventes:</strong></td>
                            <td>
                                <span class="badge bg-success fs-6">
                                    {{ number_format($pump->total_sales, 0) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Statut:</strong></td>
                            <td>
                                <span class="badge bg-{{ $pump->status_color }} fs-6">
                                    {{ $pump->status_text }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Opérationnelle:</strong></td>
                            <td>
                                @if($pump->canBeUsed())
                                    <span class="badge bg-success">Oui</span>
                                @else
                                    <span class="badge bg-danger">Non</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Mise à jour de l'index -->
            <div class="card shadow mt-4">
                <div class="card-header bg-success text-white py-3">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-sliders-h me-2"></i>Mise à jour de l'Index
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.stations.pumps.update-index', [$station, $pump]) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="new_index" class="form-label">Nouvel index</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="new_index" name="new_index"
                                       value="{{ $pump->current_index }}" min="{{ $pump->current_index }}" step="0.01" required>
                            </div>
                            <div class="form-text">
                                Index actuel: <strong>{{ number_format($pump->current_index, 0) }}</strong>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="reason" class="form-label">Raison</label>
                            <input type="text" class="form-control" id="reason" name="reason"
                                   placeholder="Ex: Relevé manuel, Correction, etc." required>
                        </div>

                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-sync-alt me-1"></i> Mettre à jour l'index
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Actions et statistiques -->
        <div class="col-lg-6 mb-4">
            <!-- Statistiques -->
            <div class="card shadow">
                <div class="card-header bg-info text-white py-3">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Statistiques
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-6 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h3 class="text-primary">{{ $todaySales }}</h3>
                                    <small class="text-muted">Ventes Aujourd'hui</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h3 class="text-success">{{ number_format($totalVolume, 0) }}</h3>
                                    <small class="text-muted">Volume Total (L)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Changement de statut -->
            <div class="card shadow mt-4">
                <div class="card-header bg-warning text-dark py-3">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-cogs me-2"></i>Changement de Statut
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.stations.pumps.update-status', [$station, $pump]) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="status" class="form-label">Nouveau statut</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="active" {{ $pump->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $pump->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="maintenance" {{ $pump->status == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="broken" {{ $pump->status == 'broken' ? 'selected' : '' }}>En Panne</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-warning w-100">
                            <i class="fas fa-exchange-alt me-1"></i> Changer le statut
                        </button>
                    </form>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="card shadow mt-4">
                <div class="card-header bg-secondary text-white py-3">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-bolt me-2"></i>Actions Rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <a href="{{ route('admin.stations.pumps.edit', [$station, $pump]) }}"
                               class="btn btn-outline-primary w-100">
                                <i class="fas fa-edit me-1"></i> Modifier
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('admin.stations.tanks.show', [$station, $pump->tank]) }}"
                               class="btn btn-outline-info w-100"
                               {{ !$pump->tank ? 'disabled' : '' }}>
                                <i class="fas fa-oil-can me-1"></i> Voir Cuve
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
