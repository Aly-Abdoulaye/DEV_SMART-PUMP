@extends('layouts.super-admin')

@section('title', 'Gestion du Support')
@section('page-title', 'Support et Communication')

@section('page-actions')
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('super-admin.support.broadcast.form') }}" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-bullhorn me-1"></i>Notification Globale
            </a>
        </div>
    </div>
@endsection

@section('content')
<!-- Statistiques -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            En Attente</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            En Cours</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['in_progress'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-spinner fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Résolus</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['resolved'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Tickets</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Liste des tickets -->
<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Tickets de Support</h6>
    </div>
    <div class="card-body">
        @if($tickets->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th># Ticket</th>
                            <th>Sujet</th>
                            <th>Entreprise</th>
                            <th>Priorité</th>
                            <th>Statut</th>
                            <th>Assigné à</th>
                            <th>Créé le</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                        <tr>
                            <td>
                                <strong>{{ $ticket->ticket_number }}</strong>
                            </td>
                            <td>
                                <div class="fw-bold">{{ Str::limit($ticket->subject, 50) }}</div>
                                <small class="text-muted">{{ $ticket->category }}</small>
                            </td>
                            <td>{{ $ticket->company->name }}</td>
                            <td>
                                <span class="badge bg-{{ $ticket->getPriorityColorAttribute() }}">
                                    {{ $ticket->priority }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $ticket->getStatusColorAttribute() }}">
                                    {{ $ticket->status }}
                                </span>
                            </td>
                            <td>
                                {{ $ticket->assignedTo->name ?? 'Non assigné' }}
                            </td>
                            <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('super-admin.support.show', $ticket) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $tickets->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-ticket-alt fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">Aucun ticket de support</h4>
                <p class="text-muted">Aucun ticket n'a été créé pour le moment.</p>
            </div>
        @endif
    </div>
</div>
@endsection