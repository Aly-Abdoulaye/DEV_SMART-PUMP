{{-- resources/views/super-admin/users/edit.blade.php --}}
@extends('layouts.super-admin')

@section('title', 'Modifier Utilisateur')
@section('page-title', 'Modifier Utilisateur - ' . $user->name)

@section('page-actions')
    <a href="{{ route('super-admin.users.show', $user) }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>Retour
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Formulaire d'édition -->
        <div class="card shadow mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>Modifier l'Utilisateur
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('super-admin.users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Informations personnelles -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-user me-2"></i>Informations Personnelles
                        </h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nom complet *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Téléphone</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
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
                                        <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
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
                                        <option value="{{ $company->id }}" {{ old('company_id', $user->company_id) == $company->id ? 'selected' : '' }}>
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('company_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                                           {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="is_active">Compte actif</label>
                                </div>
                                <div class="form-text">
                                    Décocher pour bloquer l'accès au compte.
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes (optionnel)</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes" name="notes" rows="3"
                                      placeholder="Informations supplémentaires sur cet utilisateur...">{{ old('notes', $user->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('super-admin.users.show', $user) }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Annuler
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-1"></i>Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Changement de mot de passe -->
        <div class="card shadow">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">
                    <i class="fas fa-key me-2"></i>Changer le Mot de Passe
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('super-admin.users.update-password', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="new_password" class="form-label">Nouveau mot de passe *</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="new_password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="new_password_confirmation" class="form-label">Confirmation *</label>
                            <input type="password" class="form-control"
                                   id="new_password_confirmation" name="password_confirmation" required>
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Attention:</strong> L'utilisateur devra utiliser ce nouveau mot de passe pour se connecter.
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-danger"
                                onclick="return confirm('Êtes-vous sûr de vouloir changer le mot de passe ?')">
                            <i class="fas fa-key me-1"></i>Changer le mot de passe
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar - Informations -->
    <div class="col-lg-4">
        <!-- Statut actuel -->
        <div class="card shadow mb-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Statut Actuel
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="avatar-lg mx-auto mb-3">
                        <div class="avatar-title bg-{{ $user->is_active ? 'primary' : 'secondary' }} rounded-circle text-white fs-2">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    </div>
                    <h5>{{ $user->name }}</h5>
                    <p class="text-muted">{{ $user->email }}</p>
                </div>

                <table class="table table-sm table-borderless">
                    <tr>
                        <td><i class="fas fa-shield-alt me-2 text-muted"></i></td>
                        <td>Rôle:</td>
                        <td class="text-end">
                            <span class="badge bg-{{ $user->role->color ?? 'secondary' }}">
                                {{ $user->role->name ?? 'N/A' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-building me-2 text-muted"></i></td>
                        <td>Entreprise:</td>
                        <td class="text-end">
                            {{ $user->company->name ?? 'Aucune' }}
                        </td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-check-circle me-2 text-muted"></i></td>
                        <td>Statut:</td>
                        <td class="text-end">
                            <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">
                                {{ $user->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-calendar me-2 text-muted"></i></td>
                        <td>Créé le:</td>
                        <td class="text-end">{{ $user->created_at->format('d/m/Y') }}</td>
                    </tr>
                    @if($user->last_login_at)
                    <tr>
                        <td><i class="fas fa-sign-in-alt me-2 text-muted"></i></td>
                        <td>Dernière connexion:</td>
                        <td class="text-end">{{ $user->last_login_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>Actions Rapides
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($user->is_active)
                        <form action="{{ route('super-admin.users.toggle-status', $user) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-warning w-100 mb-2"
                                    onclick="return confirm('Désactiver cet utilisateur ?')">
                                <i class="fas fa-pause me-1"></i>Désactiver le compte
                            </button>
                        </form>
                    @else
                        <form action="{{ route('super-admin.users.toggle-status', $user) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 mb-2"
                                    onclick="return confirm('Activer cet utilisateur ?')">
                                <i class="fas fa-play me-1"></i>Activer le compte
                            </button>
                        </form>
                    @endif

                    @if($user->id !== auth()->id())
                        <form action="{{ route('super-admin.users.destroy', $user) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100"
                                    onclick="return confirm('Supprimer définitivement cet utilisateur ?')">
                                <i class="fas fa-trash me-1"></i>Supprimer le compte
                            </button>
                        </form>
                    @else
                        <button class="btn btn-danger w-100" disabled title="Vous ne pouvez pas supprimer votre propre compte">
                            <i class="fas fa-trash me-1"></i>Supprimer le compte
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
