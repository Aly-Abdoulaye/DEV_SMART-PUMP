@extends('layouts.super-admin')

@section('title', 'Modifier Abonnement')
@section('page-title', 'Modifier Abonnement - ' . $company->name)

@section('page-actions')
    <a href="{{ route('super-admin.subscriptions.show', $company) }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>Retour
    </a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>Modifier l'Abonnement
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('super-admin.subscriptions.update', $company) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- Informations entreprise -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-2">Informations de l'entreprise</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Nom:</strong> {{ $company->name }}
                            </div>
                            <div class="col-md-6">
                                <strong>Email:</strong> {{ $company->email }}
                            </div>
                        </div>
                    </div>

                    <!-- Plan d'abonnement -->
                    <div class="mb-3">
                        <label for="subscription_plan" class="form-label">Plan d'abonnement *</label>
                        <select class="form-select @error('subscription_plan') is-invalid @enderror" 
                                id="subscription_plan" name="subscription_plan" required>
                            <option value="basic" {{ old('subscription_plan', $company->subscription_plan) == 'basic' ? 'selected' : '' }}>Basic</option>
                            <option value="premium" {{ old('subscription_plan', $company->subscription_plan) == 'premium' ? 'selected' : '' }}>Premium</option>
                            <option value="enterprise" {{ old('subscription_plan', $company->subscription_plan) == 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                        </select>
                        @error('subscription_plan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Changer le plan peut affecter les limites de stations et d'utilisateurs.
                        </div>
                    </div>
                    
                    <!-- Date d'expiration -->
                    <div class="mb-3">
                        <label for="subscription_expires_at" class="form-label">Date d'expiration *</label>
                        <input type="date" class="form-control @error('subscription_expires_at') is-invalid @enderror" 
                               id="subscription_expires_at" name="subscription_expires_at" 
                               value="{{ old('subscription_expires_at', $company->subscription_expires_at->format('Y-m-d')) }}" required>
                        @error('subscription_expires_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Date à laquelle l'abonnement expire automatiquement.
                        </div>
                    </div>
                    
                    <!-- Statut de l'entreprise -->
                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" 
                                   {{ old('is_active', $company->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="is_active">Entreprise active</label>
                        </div>
                        <div class="form-text">
                            Décocher pour suspendre l'accès à la plateforme.
                        </div>
                    </div>

                    <!-- Informations sur le statut actuel -->
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Statut actuel</h6>
                        <ul class="mb-0">
                            <li><strong>Plan:</strong> {{ ucfirst($company->subscription_plan) }}</li>
                            <li><strong>Expiration:</strong> {{ $company->subscription_expires_at->format('d/m/Y') }}</li>
                            <li><strong>Statut:</strong> 
                                <span class="badge bg-{{ $company->is_active ? 'success' : 'danger' }}">
                                    {{ $company->is_active ? 'Actif' : 'Suspendu' }}
                                </span>
                            </li>
                            <li><strong>Jours restants:</strong> 
                                <span class="badge bg-{{ now()->diffInDays($company->subscription_expires_at, false) > 0 ? 'success' : 'danger' }}">
                                    {{ now()->diffInDays($company->subscription_expires_at, false) }} jours
                                </span>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('super-admin.subscriptions.show', $company) }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Annuler
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-1"></i>Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Actions dangereuses -->
        <div class="card border-danger mt-4">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>Actions de Maintenance
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <form action="{{ route('super-admin.subscriptions.renew', $company) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary w-100 mb-2" 
                                    onclick="return confirm('Renouveler l\\'abonnement pour 1 an à partir de la date d\\'expiration actuelle ?')">
                                <i class="fas fa-sync me-1"></i>Renouveler 1 an
                            </button>
                        </form>
                    </div>
                    <div class="col-md-6">
                        @if($company->is_active)
                            <form action="{{ route('super-admin.subscriptions.suspend', $company) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-warning w-100 mb-2" 
                                        onclick="return confirm('Suspendre l\\'accès de cette entreprise ?')">
                                    <i class="fas fa-pause me-1"></i>Suspendre
                                </button>
                            </form>
                        @else
                            <form action="{{ route('super-admin.subscriptions.activate', $company) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-success w-100 mb-2" 
                                        onclick="return confirm('Activer l\\'accès de cette entreprise ?')">
                                    <i class="fas fa-play me-1"></i>Activer
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection