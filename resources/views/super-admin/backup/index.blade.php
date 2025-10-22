@extends('layouts.super-admin')

@section('title', 'Gestion des Sauvegardes')
@section('page-title', 'Sauvegardes et Restauration')

@section('page-actions')
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <form action="{{ route('super-admin.backup.create') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-success">
                    <i class="fas fa-plus me-1"></i>Nouvelle Sauvegarde
                </button>
            </form>
            <form action="{{ route('super-admin.backup.cleanup') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Supprimer les sauvegardes de plus de 30 jours ?')">
                    <i class="fas fa-broom me-1"></i>Nettoyer
                </button>
            </form>
        </div>
    </div>
@endsection

@section('content')
<!-- Informations stockage -->
<div class="row mb-4">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Espace Utilisé</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $storageInfo['total_size'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-hdd fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Nombre de Sauvegardes</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $storageInfo['file_count'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-database fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Dernière Sauvegarde</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $storageInfo['last_backup'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Liste des sauvegardes -->
<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Sauvegardes Disponibles</h6>
    </div>
    <div class="card-body">
        @if(count($backups) > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Nom du fichier</th>
                            <th>Taille</th>
                            <th>Date de création</th>
                            <th>Âge</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($backups as $backup)
                        @php
                            $backupDate = \Carbon\Carbon::createFromTimestamp(filemtime($backup['path']));
                            $ageInDays = $backupDate->diffInDays(now());
                            $ageColor = $ageInDays > 30 ? 'danger' : ($ageInDays > 7 ? 'warning' : 'success');
                        @endphp
                        <tr>
                            <td>
                                <code>{{ $backup['name'] }}</code>
                            </td>
                            <td>{{ $backup['size'] }}</td>
                            <td>{{ $backup['date'] }}</td>
                            <td>
                                <span class="badge bg-{{ $ageColor }}">
                                    {{ $ageInDays }} jour(s)
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('super-admin.backup.download', $backup['name']) }}" 
                                       class="btn btn-primary" title="Télécharger">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <form action="{{ route('super-admin.backup.destroy', $backup['name']) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" 
                                                onclick="return confirm('Supprimer cette sauvegarde ?')"
                                                title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-database fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">Aucune sauvegarde</h4>
                <p class="text-muted">Créez votre première sauvegarde du système.</p>
                <form action="{{ route('super-admin.backup.create') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Créer une sauvegarde
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>

<!-- Informations importantes -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-info">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Informations Importantes
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-shield-alt me-2 text-success"></i>Bonnes Pratiques</h6>
                        <ul class="small">
                            <li>Effectuez des sauvegardes régulières</li>
                            <li>Conservez les sauvegardes hors site</li>
                            <li>Testez régulièrement la restauration</li>
                            <li>Chiffrez les sauvegardes sensibles</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-exclamation-triangle me-2 text-warning"></i>Recommandations</h6>
                        <ul class="small">
                            <li>Conservez au moins 7 jours de sauvegardes</li>
                            <li>Surveillez l'espace disque</li>
                            <li>Automatisez les sauvegardes nocturnes</li>
                            <li>Documentez les procédures de restauration</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection