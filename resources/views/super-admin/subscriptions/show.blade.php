@extends('layouts.super-admin')

@section('title', 'Détails Abonnement')
@section('page-title', 'Abonnement - ' . $company->name)

@section('page-actions')
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('super-admin.subscriptions.create-payment', $company) }}" class="btn btn-sm btn-success">
                <i class="fas fa-plus me-1"></i>Nouveau Paiement
            </a>
            <a href="{{ route('super-admin.subscriptions.edit', $company) }}" class="btn btn-sm btn-warning">
                <i class="fas fa-edit me-1"></i>Modifier
            </a>
            <form action="{{ route('super-admin.subscriptions.renew', $company) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-primary" onclick="return confirm('Renouveler l\\'abonnement pour 1 an ?')">
                    <i class="fas fa-sync me-1"></i>Renouveler
                </button>
            </form>
        </div>
    </div>
@endsection

@section('content')
<div class="row">
    <!-- Informations de l'abonnement -->
    <div class="col-lg-8">
        <!-- Statut de l'abonnement -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle me-2"></i>Statut de l'Abonnement
                </h6>
                <div>
                    @if($company->is_active)
                        <span class="badge bg-success fs-6">ACTIF</span>
                    @else
                        <span class="badge bg-danger fs-6">SUSPENDU</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Entreprise:</th>
                                <td>{{ $company->name }}</td>
                            </tr>
                            <tr>
                                <th>Plan actuel:</th>
                                <td>
                                    <span class="badge bg-{{ $company->subscription_plan === 'enterprise' ? 'primary' : ($company->subscription_plan === 'premium' ? 'info' : 'secondary') }} fs-6">
                                        {{ strtoupper($company->subscription_plan) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Date d'expiration:</th>
                                <td>
                                    @if($company->subscription_expires_at)
                                        <span class="{{ $company->subscription_expires_at->isPast() ? 'text-danger fw-bold' : 'text-success' }}">
                                            {{ $company->subscription_expires_at->format('d/m/Y') }}
                                            <small class="text-muted">({{ $company->subscription_expires_at->diffForHumans() }})</small>
                                        </span>
                                    @else
                                        <span class="text-danger fw-bold">Non définie</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Jours restants:</th>
                                <td>
                                    @php
                                        $statusColor = $daysLeft > 30 ? 'success' : ($daysLeft > 0 ? 'warning' : 'danger');
                                    @endphp
                                    <span class="badge bg-{{ $statusColor }} fs-6">
                                        @if($company->subscription_expires_at)
                                            {{ $daysLeft > 0 ? $daysLeft . ' jours' : 'EXPIRÉ' }}
                                        @else
                                            NON DÉFINI
                                        @endif
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Stations:</th>
                                <td>{{ $company->stations_count ?? 0 }}</td>
                            </tr>
                            <tr>
                                <th>Utilisateurs:</th>
                                <td>{{ $company->users_count ?? 0 }}</td>
                            </tr>
                            <tr>
                                <th>Paiements:</th>
                                <td>{{ $company->payments_count ?? 0 }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Barre de progression -->
                @if($company->subscription_expires_at && !$company->subscription_expires_at->isPast() && $currentPayment)
                <div class="mt-3">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Début: {{ $currentPayment->start_date->format('d/m/Y') }}</span>
                        <span>Fin: {{ $company->subscription_expires_at->format('d/m/Y') }}</span>
                    </div>
                    @php
                        $totalDays = $currentPayment->start_date->diffInDays($company->subscription_expires_at);
                        $daysPassed = $currentPayment->start_date->diffInDays(now());
                        $percentage = min(100, max(0, ($daysPassed / $totalDays) * 100));
                    @endphp
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-{{ $percentage > 80 ? 'warning' : 'success' }}" 
                             role="progressbar" 
                             style="width: {{ $percentage }}%"
                             aria-valuenow="{{ $percentage }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Historique des paiements -->
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-history me-2"></i>Historique des Paiements
                </h6>
            </div>
            <div class="card-body">
                @if($company->payments_count > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th># Paiement</th>
                                    <th>Période</th>
                                    <th>Montant</th>
                                    <th>Méthode</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($company->payments as $payment)
                                <tr>
                                    <td>
                                        <strong>{{ $payment->payment_number }}</strong>
                                        @if($payment->invoice_number)
                                            <br><small class="text-muted">Facture: {{ $payment->invoice_number }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($payment->start_date && $payment->end_date)
                                            {{ $payment->start_date->format('d/m/Y') }} -<br>
                                            {{ $payment->end_date->format('d/m/Y') }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold text-success">
                                        {{ number_format($payment->amount, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td>
                                        <i class="{{ $payment->getPaymentMethodIconAttribute() }} me-1"></i>
                                        {{ $payment->payment_method }}
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $payment->getStatusColorAttribute() }}">
                                            {{ $payment->status }}
                                        </span>
                                        @if($payment->isOverdue())
                                            <br><small class="text-danger">En retard</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $payment->created_at->format('d/m/Y') }}
                                        @if($payment->paid_at)
                                            <br><small class="text-muted">Payé: {{ $payment->paid_at->format('d/m/Y') }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Aucun paiement</h5>
                        <p class="text-muted">Aucun paiement n'a été enregistré pour cette entreprise.</p>
                        <a href="{{ route('super-admin.subscriptions.create-payment', $company) }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Enregistrer un paiement
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar - Actions et informations -->
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
                    <a href="{{ route('super-admin.subscriptions.create-payment', $company) }}" class="btn btn-success mb-2">
                        <i class="fas fa-credit-card me-1"></i>Nouveau Paiement
                    </a>
                    
                    <form action="{{ route('super-admin.subscriptions.renew', $company) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100 mb-2" onclick="return confirm('Renouveler l\\'abonnement pour 1 an ?')">
                            <i class="fas fa-sync me-1"></i>Renouveler Abonnement
                        </button>
                    </form>

                    @if($company->is_active)
                        <form action="{{ route('super-admin.subscriptions.suspend', $company) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning w-100 mb-2" onclick="return confirm('Suspendre cette entreprise ?')">
                                <i class="fas fa-pause me-1"></i>Suspendre
                            </button>
                        </form>
                    @else
                        <form action="{{ route('super-admin.subscriptions.activate', $company) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 mb-2" onclick="return confirm('Activer cette entreprise ?')">
                                <i class="fas fa-play me-1"></i>Activer
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('super-admin.companies.show', $company) }}" class="btn btn-info w-100 mb-2">
                        <i class="fas fa-building me-1"></i>Voir l'Entreprise
                    </a>
                </div>
            </div>
        </div>

        <!-- Dernier paiement -->
        @if($currentPayment)
        <div class="card shadow mb-4">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">
                    <i class="fas fa-receipt me-2"></i>Dernier Paiement
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="h4 text-success">{{ number_format($currentPayment->amount, 0, ',', ' ') }} FCFA</div>
                    <small class="text-muted">Montant payé</small>
                </div>
                <table class="table table-sm table-borderless">
                    <tr>
                        <td><i class="fas fa-calendar me-2 text-muted"></i></td>
                        <td>Période:</td>
                        <td class="text-end">
                            @if($currentPayment->start_date && $currentPayment->end_date)
                                {{ $currentPayment->start_date->format('d/m/Y') }} -<br>
                                {{ $currentPayment->end_date->format('d/m/Y') }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-wallet me-2 text-muted"></i></td>
                        <td>Méthode:</td>
                        <td class="text-end">{{ $currentPayment->payment_method }}</td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-check-circle me-2 text-muted"></i></td>
                        <td>Statut:</td>
                        <td class="text-end">
                            <span class="badge bg-{{ $currentPayment->getStatusColorAttribute() }}">
                                {{ $currentPayment->status }}
                            </span>
                        </td>
                    </tr>
                    @if($currentPayment->paid_at)
                    <tr>
                        <td><i class="fas fa-clock me-2 text-muted"></i></td>
                        <td>Payé le:</td>
                        <td class="text-end">{{ $currentPayment->paid_at->format('d/m/Y') }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
        @else
        <div class="card shadow mb-4">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>Aucun Paiement
                </h6>
            </div>
            <div class="card-body text-center">
                <i class="fas fa-credit-card fa-2x text-muted mb-3"></i>
                <p class="text-muted mb-3">Aucun paiement enregistré.</p>
                <a href="{{ route('super-admin.subscriptions.create-payment', $company) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>Premier Paiement
                </a>
            </div>
        </div>
        @endif

        <!-- Prochain renouvellement -->
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0">
                    <i class="fas fa-clock me-2"></i>Prochain Renouvellement
                </h6>
            </div>
            <div class="card-body">
                @if($company->subscription_expires_at && !$company->subscription_expires_at->isPast())
                    <div class="text-center mb-3">
                        <div class="h5 text-warning">{{ $company->subscription_expires_at->format('d/m/Y') }}</div>
                        <small class="text-muted">Date d'expiration</small>
                    </div>
                    
                    @if($daysLeft <= 30)
                        <div class="alert alert-warning small">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Renouvellement dans {{ $daysLeft }} jours</strong>
                            <p class="mb-0 mt-1">Pensez à contacter le client pour le renouvellement.</p>
                        </div>
                    @else
                        <div class="alert alert-success small">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Abonnement en cours</strong>
                            <p class="mb-0 mt-1">Prochain renouvellement dans {{ $daysLeft }} jours.</p>
                        </div>
                    @endif
                @else
                    <div class="alert alert-danger text-center">
                        <i class="fas fa-times-circle me-2"></i>
                        <strong>Abonnement {{ $company->subscription_expires_at ? 'expiré' : 'non configuré' }}</strong>
                        <p class="mb-0 mt-1">
                            @if($company->subscription_expires_at)
                                L'abonnement a expiré depuis {{ abs($daysLeft) }} jours.
                            @else
                                Aucune date d'expiration définie.
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection