{{-- resources/views/super-admin/users/show.blade.php --}}
@extends('layouts.super-admin')

@section('title', 'Détails Utilisateur')
@section('page-title', 'Utilisateur - ' . $user->name)

@section('page-actions')
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('super-admin.users.edit', $user) }}" class="btn btn-sm btn-warning">
                <i class="fas fa-edit me-1"></i>Modifier
            </a>
            <a href="{{ route('super-admin.users.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="row">
    <!-- Informations principales -->
    <div class="col-lg-8">
        <!-- Profil utilisateur -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-user-circle me-2"></i>Profil Utilisateur
                </h6>
                <div>
                    <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }} fs-6">
                        {{ $user->is_active ? 'ACTIF' : 'INACTIF' }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Nom complet:</th>
                                <td>{{ $user->name }}</td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td>
                                    {{ $user->email }}
                                    @if($user->email_verified_at)
                                        <span class="badge bg-success ms-2">Vérifié</span>
                                    @else
                                        <span class="badge bg-warning ms-2">Non vérifié</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Téléphone:</th>
                                <td>
                                    @if($user->phone)
                                        <i class="fas fa-phone me-1 text-muted"></i>{{ $user->phone }}
                                    @else
                                        <span class="text-muted">Non renseigné</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Rôle:</th>
                                <td>
                                    <span class="badge bg-{{ $user->role->color ?? 'secondary' }} fs-6">
                                        {{ $user->role->name ?? 'N/A' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Entreprise:</th>
                                <td>
                                    @if($user->company)
                                        <strong>{{ $user->company->name }}</strong>
                                    @else
                                        <span class="text-muted">Aucune entreprise</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Créé le:</th>
                                <td>{{ $user->created_at->format('d/m/Y à H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Dernière connexion:</th>
                                <td>
                                    @if($user->last_login_at)
                                        <span class="text-success">
                                            {{ $user->last_login_at->format('d/m/Y à H:i') }}
                                        </span>
                                        <br>
                                        <small class="text-muted">{{ $user->last_login_at->diffForHumans() }}</small>
                                    @else
                                        <span class="text-muted">Jamais connecté</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Notes -->
                @if($user->notes)
                <div class="mt-4 p-3 bg-light rounded">
                    <h6 class="text-primary mb-2">
                        <i class="fas fa-sticky-note me-2"></i>Notes
                    </h6>
                    <p class="mb-0">{{ $user->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Historique des connexions -->
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-history me-2"></i>Historique des Connexions
                </h6>
            </div>
            <div class="card-body">
                @if($user->loginHistory->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Date et heure</th>
                                    <th>Adresse IP</th>
                                    <th>User Agent</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->loginHistory as $login)
                                <tr>
                                    <td>
                                        {{ $login->login_at->format('d/m/Y H:i:s') }}
                                        <br>
                                        <small class="text-muted">{{ $login->login_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <code>{{ $login->ip_address }}</code>
                                    </td>
                                    <td>
                                        <small class="text-muted" title="{{ $login->user_agent }}">
                                            {{ Str::limit($login->user_agent, 50) }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $login->success ? 'success' : 'danger' }}">
                                            {{ $login->success ? 'Succès' : 'Échec' }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-history fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Aucun historique de connexion</h5>
                        <p class="text-muted">Aucune connexion enregistrée pour cet utilisateur.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar - Actions et statistiques -->
    <div class="col-lg-4">
        <!-- Actions rapides -->
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>Actions Rapides
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('super-admin.users.edit', $user) }}" class="btn btn-warning mb-2">
                        <i class="fas fa-edit me-1"></i>Modifier le profil
                    </a>

                    <a href="{{ route('super-admin.users.edit', $user) }}#password-section" class="btn btn-danger mb-2">
                        <i class="fas fa-key me-1"></i>Changer le mot de passe
                    </a>

                    @if($user->is_active)
                        <form action="{{ route('super-admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning w-100 mb-2"
                                    onclick="return confirm('Désactiver cet utilisateur ?')">
                                <i class="fas fa-pause me-1"></i>Désactiver le compte
                            </button>
                        </form>
                    @else
                        <form action="{{ route('super-admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 mb-2"
                                    onclick="return confirm('Activer cet utilisateur ?')">
                                <i class="fas fa-play me-1"></i>Activer le compte
                            </button>
                        </form>
                    @endif

                    @if($user->company)
                        <a href="{{ route('super-admin.companies.show', $user->company) }}" class="btn btn-info w-100 mb-2">
                            <i class="fas fa-building me-1"></i>Voir l'entreprise
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistiques utilisateur -->
        <div class="card shadow mb-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>Statistiques
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="h4 text-primary">{{ $user->loginHistory->count() }}</div>
                    <small class="text-muted">Connexions totales</small>
                </div>

                <table class="table table-sm table-borderless">
                    <tr>
                        <td><i class="fas fa-sign-in-alt me-2 text-muted"></i></td>
                        <td>Connexions réussies:</td>
                        <td class="text-end">
                            {{ $user->loginHistory->where('success', true)->count() }}
                        </td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-times-circle me-2 text-muted"></i></td>
                        <td>Échecs de connexion:</td>
                        <td class="text-end">
                            {{ $user->loginHistory->where('success', false)->count() }}
                        </td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-calendar me-2 text-muted"></i></td>
                        <td>Membre depuis:</td>
                        <td class="text-end">{{ $user->created_at->diffForHumans() }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Dernières activités -->
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0">
                    <i class="fas fa-clock me-2"></i>Activité Récente
                </h6>
            </div>
            <div class="card-body">
                @if($user->last_login_at)
                    <div class="alert alert-success small">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Dernière connexion</strong>
                        <p class="mb-0 mt-1">{{ $user->last_login_at->diffForHumans() }}</p>
                    </div>
                @else
                    <div class="alert alert-warning small">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Jamais connecté</strong>
                        <p class="mb-0 mt-1">L'utilisateur ne s'est jamais connecté.</p>
                    </div>
                @endif

                @if(!$user->is_active)
                    <div class="alert alert-danger small">
                        <i class="fas fa-pause-circle me-2"></i>
                        <strong>Compte désactivé</strong>
                        <p class="mb-0 mt-1">L'utilisateur ne peut pas se connecter.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
