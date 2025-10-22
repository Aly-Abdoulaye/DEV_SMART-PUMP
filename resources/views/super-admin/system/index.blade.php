@extends('layouts.super-admin')

@section('title', 'Paramètres Système')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-cog me-2"></i>Paramètres Système
    </h1>
</div>

<!-- Actions système -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Actions Système</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <form action="{{ route('super-admin.system.backup') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-database me-2"></i>Sauvegarder
                            </button>
                        </form>
                    </div>
                    <div class="col-md-3 mb-3">
                        <form action="{{ route('super-admin.system.clear-cache') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="fas fa-broom me-2"></i>Vider le Cache
                            </button>
                        </form>
                    </div>
                    <div class="col-md-3 mb-3">
                        <form action="{{ route('super-admin.system.migrate') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-info w-100" onclick="return confirm('Exécuter les migrations ?')">
                                <i class="fas fa-sync me-2"></i>Exécuter Migrations
                            </button>
                        </form>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('super-admin.system.logs') }}" class="btn btn-secondary w-100">
                            <i class="fas fa-file-alt me-2"></i>Voir les Logs
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Informations système -->
<div class="row">
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informations Serveur</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td><strong>PHP Version</strong></td>
                                <td>{{ $systemInfo['php_version'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Laravel Version</strong></td>
                                <td>{{ $systemInfo['laravel_version'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Logiciel Serveur</strong></td>
                                <td>{{ $systemInfo['server_software'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Pilote Base de Données</strong></td>
                                <td>{{ $systemInfo['database_driver'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Fuseau Horaire</strong></td>
                                <td>{{ $systemInfo['timezone'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Configuration Application</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td><strong>Environnement</strong></td>
                                <td>
                                    <span class="badge bg-{{ $systemInfo['environment'] == 'production' ? 'success' : 'warning' }}">
                                        {{ $systemInfo['environment'] }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Mode Debug</strong></td>
                                <td>
                                    <span class="badge bg-{{ $systemInfo['debug_mode'] == 'Activé' ? 'danger' : 'success' }}">
                                        {{ $systemInfo['debug_mode'] }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Taille Stockage</strong></td>
                                <td>{{ $systemInfo['storage_size'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Nombre de Sauvegardes</strong></td>
                                <td>
                                    <span class="badge bg-primary">{{ $systemInfo['backup_count'] }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>URL Application</strong></td>
                                <td>
                                    <small class="text-muted">{{ config('app.url') }}</small>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques d'utilisation -->
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Statistiques d'Utilisation</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 mb-3">
                        <div class="card border-left-primary">
                            <div class="card-body">
                                <div class="text-primary">Entreprises</div>
                                <div class="h5">{{ \App\Models\Company::count() }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border-left-success">
                            <div class="card-body">
                                <div class="text-success">Stations</div>
                                <div class="h5">{{ \App\Models\Station::count() }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border-left-info">
                            <div class="card-body">
                                <div class="text-info">Utilisateurs</div>
                                <div class="h5">{{ \App\Models\User::count() }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card border-left-warning">
                            <div class="card-body">
                                <div class="text-warning">Ventes</div>
                                <div class="h5">{{ \App\Models\Sale::count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection