@extends('layouts.admin')

@section('title', 'Modifier la Station')
@section('page-title', 'Modifier la Station')
@section('page-subtitle', $station->name)

@section('page-actions')
    <div class="btn-group">
        <a href="{{ route('admin.stations.show', $station) }}" class="btn btn-info">
            <i class="fas fa-eye me-1"></i> Voir détails
        </a>
        <a href="{{ route('admin.stations.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Retour
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
                    <form action="{{ route('admin.stations.update', $station) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Nom de la station -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label required">Nom de la station</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $station->name) }}"
                                       placeholder="Ex: Station Centre Ville" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Téléphone -->
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone" value="{{ old('phone', $station->phone) }}"
                                       placeholder="Ex: +223 20 20 20 20">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Adresse -->
                        <div class="mb-3">
                            <label for="address" class="form-label required">Adresse complète</label>
                            <textarea class="form-control @error('address') is-invalid @enderror"
                                      id="address" name="address" rows="3"
                                      placeholder="Adresse complète de la station..." required>{{ old('address', $station->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Manager -->
                        <div class="mb-3">
                            <label for="manager_id" class="form-label">Manager assigné</label>
                            <select class="form-select @error('manager_id') is-invalid @enderror"
                                    id="manager_id" name="manager_id">
                                <option value="">Sélectionner un manager...</option>
                                @foreach($managers as $manager)
                                    <option value="{{ $manager->id }}"
                                            {{ (old('manager_id', $station->managers->first()->id ?? '') == $manager->id) ? 'selected' : '' }}>
                                        {{ $manager->name }} ({{ $manager->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('manager_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Statut -->
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox"
                                       id="is_active" name="is_active" value="1"
                                       {{ old('is_active', $station->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Station active
                                </label>
                            </div>
                        </div>

                        <!-- Informations de création -->
                        <div class="alert alert-info">
                            <div class="row">
                                <div class="col-md-6">
                                    <small><strong>Créée le:</strong> {{ $station->created_at->format('d/m/Y H:i') }}</small>
                                </div>
                                <div class="col-md-6">
                                    <small><strong>Dernière modification:</strong> {{ $station->updated_at->format('d/m/Y H:i') }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.stations.show', $station) }}" class="btn btn-outline-secondary">
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
