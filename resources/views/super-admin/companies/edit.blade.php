@extends('layouts.super-admin')

@section('title', 'Modifier Entreprise')
@section('page-title', 'Modifier - ' . $company->name)

@section('page-actions')
    <a href="{{ route('super-admin.companies.show', $company) }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>Retour
    </a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>Modifier l'Entreprise
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('super-admin.companies.update', $company) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Nom de l'entreprise *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $company->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $company->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Téléphone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone', $company->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
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
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="subscription_expires_at" class="form-label">Date d'expiration *</label>
                        <input type="date" class="form-control @error('subscription_expires_at') is-invalid @enderror" 
                               id="subscription_expires_at" name="subscription_expires_at" 
                               value="{{ old('subscription_expires_at', $company->subscription_expires_at->format('Y-m-d')) }}" required>
                        @error('subscription_expires_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Adresse</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  id="address" name="address" rows="3">{{ old('address', $company->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3 form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" 
                               {{ old('is_active', $company->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Entreprise active</label>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('super-admin.companies.show', $company) }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Annuler
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-1"></i>Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection