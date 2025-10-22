@extends('layouts.super-admin')

@section('title', 'Rapport Financier')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-money-bill-wave me-2"></i>Rapport Financier
    </h1>
    <a href="{{ route('super-admin.reports.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>Retour aux rapports
    </a>
</div>

<!-- Filtres -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Filtres</h6>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="start_date" class="form-label">Date de début</label>
                <input type="date" class="form-control" id="start_date" name="start_date" 
                       value="{{ $startDate }}">
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label">Date de fin</label>
                <input type="date" class="form-control" id="end_date" name="end_date" 
                       value="{{ $endDate }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i>Appliquer
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Statistiques financières -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Revenu Basic</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format(($financialData['revenue_by_plan']['basic'] ?? 0) * 50000, 0, ',', ' ') }} FCFA
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill fa-2x text-gray-300"></i>
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
                            Revenu Premium</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format(($financialData['revenue_by_plan']['premium'] ?? 0) * 100000, 0, ',', ' ') }} FCFA
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
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
                            Revenu Enterprise</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format(($financialData['revenue_by_plan']['enterprise'] ?? 0) * 200000, 0, ',', ' ') }} FCFA
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-building fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Revenu Total Mensuel</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            @php
                                $totalRevenue = (($financialData['revenue_by_plan']['basic'] ?? 0) * 50000) +
                                              (($financialData['revenue_by_plan']['premium'] ?? 0) * 100000) +
                                              (($financialData['revenue_by_plan']['enterprise'] ?? 0) * 200000);
                            @endphp
                            {{ number_format($totalRevenue, 0, ',', ' ') }} FCFA
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Graphiques financiers -->
<div class="row">
    <!-- Répartition par plan -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Répartition par Plan d'Abonnement</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="planDistributionChart" height="250"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    <span class="mr-2">
                        <i class="fas fa-circle text-primary"></i> Basic
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-success"></i> Premium
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-info"></i> Enterprise
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statut des abonnements -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Statut des Abonnements</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="subscriptionStatusChart" height="250"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    <span class="mr-2">
                        <i class="fas fa-circle text-success"></i> Actifs
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-danger"></i> Expirés
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-warning"></i> Suspendus
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Renouvellements à venir -->
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Renouvellements à Venir (30 jours)</h6>
            </div>
            <div class="card-body">
                @if($financialData['upcoming_renewals']->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Entreprise</th>
                                    <th>Plan</th>
                                    <th>Date d'expiration</th>
                                    <th>Jours restants</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($financialData['upcoming_renewals'] as $company)
                                @php
                                    $daysLeft = now()->diffInDays($company->subscription_expires_at, false);
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $company->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $company->email }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $company->subscription_plan === 'enterprise' ? 'primary' : ($company->subscription_plan === 'premium' ? 'info' : 'secondary') }}">
                                            {{ ucfirst($company->subscription_plan) }}
                                        </span>
                                    </td>
                                    <td>{{ $company->subscription_expires_at->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $daysLeft > 7 ? 'warning' : 'danger' }}">
                                            {{ $daysLeft }} jours
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('super-admin.subscriptions.show', $company) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-sync-alt me-1"></i>Renouveler
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center text-muted">Aucun renouvellement prévu dans les 30 prochains jours.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Graphique de répartition par plan
const planCtx = document.getElementById('planDistributionChart').getContext('2d');
const planChart = new Chart(planCtx, {
    type: 'doughnut',
    data: {
        labels: ['Basic', 'Premium', 'Enterprise'],
        datasets: [{
            data: [
                {{ $financialData['revenue_by_plan']['basic'] ?? 0 }},
                {{ $financialData['revenue_by_plan']['premium'] ?? 0 }},
                {{ $financialData['revenue_by_plan']['enterprise'] ?? 0 }}
            ],
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
            hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
    },
    options: {
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        cutout: '70%',
    },
});

// Graphique de statut des abonnements
const statusCtx = document.getElementById('subscriptionStatusChart').getContext('2d');
const statusChart = new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Actifs', 'Expirés', 'Suspendus'],
        datasets: [{
            data: [
                {{ $financialData['subscription_status']['active'] ?? 0 }},
                {{ $financialData['subscription_status']['expired'] ?? 0 }},
                {{ $financialData['subscription_status']['suspended'] ?? 0 }}
            ],
            backgroundColor: ['#1cc88a', '#e74a3b', '#f6c23e'],
            hoverBackgroundColor: ['#17a673', '#d52a1e', '#dda20a'],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
    },
    options: {
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        cutout: '70%',
    },
});
</script>
@endpush