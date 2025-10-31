{{-- resources/views/admin/users/edit.blade.php --}}
@extends('layouts.admin')

@section('title', 'Modifier l\'Utilisateur')

@section('content')
<div class="card">
    <div class="card-header bg-primary text-white">
        <h3 class="card-title">
            <i class="fas fa-edit"></i> Modifier l'Utilisateur
        </h3>
        <div class="card-tools">
            <a href="{{ route('admin.users.index') }}" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Nom complet *</label>
                        <input type="text" name="name" id="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" name="email" id="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="role_id">Rôle *</label>
                        <select name="role_id" id="role_id"
                                class="form-control @error('role_id') is-invalid @enderror" required>
                            <option value="">Sélectionnez un rôle</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}"
                                    {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="station_ids">Stations assignées</label>
                        <select name="station_ids[]" id="station_ids"
                                class="form-control select2 @error('station_ids') is-invalid @enderror"
                                multiple>
                            @foreach($stations as $station)
                                <option value="{{ $station->id }}"
                                    {{ in_array($station->id, old('station_ids', $userStations)) ? 'selected' : '' }}>
                                    {{ $station->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('station_ids')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Sélectionnez les stations pour les gérants (optionnel)
                        </small>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea name="notes" id="notes"
                          class="form-control @error('notes') is-invalid @enderror"
                          rows="3">{{ old('notes', $user->notes) }}</textarea>
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Statut</label>
                        <div>
                            <span class="badge badge-{{ $user->status_color }}">
                                {{ $user->status_text }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Dernière connexion</label>
                        <div>{{ $user->last_login_formatted }}</div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Mettre à jour
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Annuler
                </a>

                @if($user->id !== auth()->id())
                    <form action="{{ route('admin.users.reset-password', $user) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-info"
                                onclick="return confirm('Réinitialiser le mot de passe de cet utilisateur?')">
                            <i class="fas fa-key"></i> Réinitialiser MDP
                        </button>
                    </form>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2({
        placeholder: "Sélectionnez les stations",
        allowClear: true
    });
});
</script>
@endpush
