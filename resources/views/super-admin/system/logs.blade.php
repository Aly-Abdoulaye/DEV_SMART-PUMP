@extends('layouts.super-admin')

@section('title', 'Logs Système')
@section('page-title', 'Logs du Système')

@section('page-actions')
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <form action="{{ route('super-admin.system.clear-logs') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Vider tous les logs ? Cette action est irréversible.')">
                    <i class="fas fa-trash me-1"></i>Vider les Logs
                </button>
            </form>
            <a href="{{ route('super-admin.system.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            Dernières 100 lignes - laravel.log
            <small class="text-muted">(Les plus récentes en premier)</small>
        </h6>
    </div>
    <div class="card-body">
        @if(!empty($logs))
            <div class="log-container" style="max-height: 600px; overflow-y: auto; background: #1a1a1a; color: #f8f9fa; padding: 15px; border-radius: 5px; font-family: 'Courier New', monospace; font-size: 12px;">
                @foreach($logs as $log)
                    @php
                        // Colorisation basique des logs
                        $color = '#f8f9fa'; // Blanc par défaut
                        if (str_contains($log, 'ERROR')) {
                            $color = '#e74a3b'; // Rouge pour les erreurs
                        } elseif (str_contains($log, 'WARNING')) {
                            $color = '#f6c23e'; // Jaune pour les warnings
                        } elseif (str_contains($log, 'INFO')) {
                            $color = '#36b9cc'; // Cyan pour les infos
                        } elseif (str_contains($log, 'DEBUG')) {
                            $color = '#858796'; // Gris pour les debug
                        }
                    @endphp
                    <div style="color: {{ $color }}; margin-bottom: 2px; white-space: pre-wrap;">
                        {{ $log }}
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-file-alt fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">Aucun log disponible</h4>
                <p class="text-muted">Le fichier de logs est vide ou n'existe pas.</p>
            </div>
        @endif
    </div>
</div>

<style>
.log-container::-webkit-scrollbar {
    width: 8px;
}

.log-container::-webkit-scrollbar-track {
    background: #2d3748;
}

.log-container::-webkit-scrollbar-thumb {
    background: #4a5568;
    border-radius: 4px;
}

.log-container::-webkit-scrollbar-thumb:hover {
    background: #718096;
}
</style>
@endsection