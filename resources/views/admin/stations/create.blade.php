@extends('layouts.admin')

@section('title', 'Créer une Station')
@section('page-title', 'Créer une Nouvelle Station')
@section('page-subtitle', 'Ajouter une nouvelle station à votre réseau')

@section('page-actions')
    <a href="{{ route('admin.stations.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Retour à la liste
    </a>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-plus-circle me-2"></i>Informations de la Station
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.stations.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <!-- Nom de la station -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label required">Nom de la station</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}"
                                       placeholder="Ex: Station Centre Ville" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Téléphone -->
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone" value="{{ old('phone') }}"
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
                                      placeholder="Adresse complète de la station..." required>{{ old('address') }}</textarea>
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
                                    <option value="{{ $manager->id }}" {{ old('manager_id') == $manager->id ? 'selected' : '' }}>
                                        {{ $manager->name }} ({{ $manager->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('manager_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Optionnel - Vous pourrez assigner un manager plus tard
                            </div>
                        </div>

                        <!-- Statut -->
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox"
                                       id="is_active" name="is_active" value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Station active
                                </label>
                            </div>
                            <div class="form-text">
                                Une station inactive ne sera pas visible par les employés
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.stations.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Créer la Station
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Carte d'information -->
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
                            Une fois créée, vous pourrez ajouter des pompes et des cuves
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Vous pourrez assigner des employés à cette station
                        </li>
                        <li>
                            <i class="fas fa-check text-success me-2"></i>
                            Les données de vente seront automatiquement liées à cette station
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.required::after {
    content: " *";
    color: #dc3545;
}
</style>
@endsection
