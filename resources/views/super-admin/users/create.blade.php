{{-- resources/views/super-admin/users/create.blade.php --}}
@extends('layouts.super-admin')

@section('title', 'Nouvel Utilisateur')
@section('page-title', 'Créer un Utilisateur')

@section('page-actions')
    <a href="{{ route('super-admin.users.index') }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>Retour
    </a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-user-plus me-2"></i>Nouvel Utilisateur
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('super-admin.users.store') }}" method="POST">
                    @csrf

                    <!-- Informations personnelles -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-user me-2"></i>Informations Personnelles
                        </h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nom complet *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Téléphone</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone" value="{{ old('phone') }}"
                                       placeholder="+223 XX XX XX XX">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Rôle et entreprise -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-briefcase me-2"></i>Affectation
                        </h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="role_id" class="form-label">Rôle *</label>
                                <select class="form-select @error('role_id') is-invalid @enderror"
                                        id="role_id" name="role_id" required>
                                    <option value="">Sélectionnez un rôle</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="company_id" class="form-label">Entreprise</label>
                                <select class="form-select @error('company_id') is-invalid @enderror"
                                        id="company_id" name="company_id">
                                    <option value="">Aucune entreprise (Super Admin)</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('company_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Optionnel. Laissez vide pour un utilisateur système.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sécurité -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-lock me-2"></i>Sécurité
                        </h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Mot de passe *</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Minimum 8 caractères avec chiffres et lettres.
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirmation *</label>
                                <input type="password" class="form-control"
                                       id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>
                    </div>

                    <!-- Paramètres -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-cog me-2"></i>Paramètres
                        </h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                                           {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="is_active">Compte actif</label>
                                </div>
                                <div class="form-text">
                                    Le compte pourra se connecter immédiatement.
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes (optionnel)</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes" name="notes" rows="3"
                                      placeholder="Informations supplémentaires sur cet utilisateur...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('super-admin.users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Créer l'utilisateur
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar - Aide -->
    <div class="col-lg-4">
        <div class="card shadow">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Guide de Création
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="fas fa-lightbulb me-2"></i>Bonnes pratiques</h6>
                    <ul class="mb-0 small">
                        <li>Utilisez une adresse email professionnelle</li>
                        <li>Attribuez le rôle approprié aux permissions</li>
                        <li>Générez un mot de passe fort</li>
                        <li>Notez les informations importantes</li>
                    </ul>
                </div>

                <!-- Rôles disponibles -->
                <h6 class="mt-4">Rôles Disponibles</h6>
                <div class="small">
                    @foreach($roles as $role)
                    <div class="card mb-2">
                        <div class="card-body py-2">
                            <strong class="text-{{ $role->color ?? 'primary' }}">{{ $role->name }}</strong>
                            <br>
                            <small class="text-muted">{{ $role->description ?? 'Aucune description' }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
