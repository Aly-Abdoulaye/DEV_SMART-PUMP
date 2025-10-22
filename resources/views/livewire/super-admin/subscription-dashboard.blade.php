{{-- resources/views/livewire/super-admin/subscription-dashboard.blade.php --}}

<div>
    <!-- En-tête avec sélecteurs -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h4 class="mb-0 text-primary">
                                <i class="fas fa-chart-line me-2"></i>Tableau de Bord des Abonnements
                            </h4>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="btn-group" role="group">
                                <button type="button"
                                        class="btn btn-sm btn-outline-primary {{ $period === 'month' ? 'active' : '' }}"
                                        wire:click="updatePeriod('month')">
                                    Mensuel
                                </button>
                                <button type="button"
                                        class="btn btn-sm btn-outline-primary {{ $period === 'quarter' ? 'active' : '' }}"
                                        wire:click="updatePeriod('quarter')">
                                    Trimestriel
                                </button>
                                <button type="button"
                                        class="btn btn-sm btn-outline-primary {{ $period === 'year' ? 'active' : '' }}"
                                        wire:click="updatePeriod('year')">
                                    Annuel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cartes de statistiques -->
    <div class="row mb-4">
        <!-- Revenu du mois -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Revenu Ce Mois</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['monthly_revenue'], 0, ',', ' ') }} FCFA
                            </div>
                            @if($stats['previous_month_revenue'] > 0)
                                @php
                                    $growth = (($stats['monthly_revenue'] - $stats['previous_month_revenue']) / $stats['previous_month_revenue']) * 100;
                                @endphp
                                <div class="mt-2 mb-0 text-sm">
                                    <span class="text-{{ $growth >= 0 ? 'success' : 'danger' }}">
                                        <i class="fas fa-{{ $growth >= 0 ? 'arrow-up' : 'arrow-down' }} me-1"></i>
                                        {{ number_format(abs($growth), 1) }}%
                                    </span>
                                    <span class="text-muted">vs mois dernier</span>
                                </div>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Entreprises totales -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Entreprises Total</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_companies'] }}</div>
                            <div class="mt-2 mb-0 text-sm">
                                <span class="text-success">
                                    <i class="fas fa-check-circle me-1"></i>
                                    {{ $stats['active_subscriptions'] }} actives
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Abonnements actifs -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Abonnements Actifs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_subscriptions'] }}</div>
                            <div class="mt-2 mb-0 text-sm">
                                <span class="text-{{ $stats['expired_subscriptions'] > 0 ? 'danger' : 'success' }}">
                                    <i class="fas fa-{{ $stats['expired_subscriptions'] > 0 ? 'exclamation-triangle' : 'check' }} me-1"></i>
                                    {{ $stats['expired_subscriptions'] }} expirés
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenu total -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Revenu Total</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['total_revenue'], 0, ',', ' ') }} FCFA
                            </div>
                            <div class="mt-2 mb-0 text-sm">
                                <span class="text-{{ $stats['pending_payments'] > 0 ? 'warning' : 'success' }}">
                                    <i class="fas fa-{{ $stats['pending_payments'] > 0 ? 'clock' : 'check' }} me-1"></i>
                                    {{ $stats['pending_payments'] }} paiements en attente
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-bar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique et données -->
    <div class="row mb-4">
        <!-- Graphique principal -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-area me-2"></i>
                        Évolution
                        <span class="text-lowercase">
                            @if($chartType === 'revenue') du Revenu
                            @elseif($chartType === 'subscriptions') des Abonnements
                            @else des Paiements
                            @endif
                        </span>
                    </h6>
                    <div class="btn-group btn-group-sm">
                        <button type="button"
                                class="btn btn-sm btn-outline-primary {{ $chartType === 'revenue' ? 'active' : '' }}"
                                wire:click="updateChartType('revenue')">
                            Revenu
                        </button>
                        <button type="button"
                                class="btn btn-sm btn-outline-primary {{ $chartType === 'subscriptions' ? 'active' : '' }}"
                                wire:click="updateChartType('subscriptions')">
                            Abonnements
                        </button>
                        <button type="button"
                                class="btn btn-sm btn-outline-primary {{ $chartType === 'payments' ? 'active' : '' }}"
                                wire:click="updateChartType('payments')">
                            Paiements
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="subscriptionChart" width="100%" height="40"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Légende et résumé -->
        <div class="col-lg-4">
            <!-- Résumé du graphique -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Résumé de la Période</h6>
                </div>
                <div class="card-body">
                    @php
                        $total = array_sum(array_column($chartData, 'value'));
                        $average = count($chartData) > 0 ? $total / count($chartData) : 0;
                        $max = count($chartData) > 0 ? max(array_column($chartData, 'value')) : 0;
                        $min = count($chartData) > 0 ? min(array_column($chartData, 'value')) : 0;
                    @endphp
                    <div class="mb-3">
                        <div class="small text-muted">Total sur la période</div>
                        <div class="h5 text-primary">
                            @if($chartType === 'revenue')
                                {{ number_format($total, 0, ',', ' ') }} FCFA
                            @else
                                {{ number_format($total) }}
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="small text-muted">Moyenne</div>
                        <div class="h6">
                            @if($chartType === 'revenue')
                                {{ number_format($average, 0, ',', ' ') }} FCFA
                            @else
                                {{ number_format($average, 1) }}
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="small text-muted">Maximum</div>
                            <div class="h6 text-success">
                                @if($chartType === 'revenue')
                                    {{ number_format($max, 0, ',', ' ') }} FCFA
                                @else
                                    {{ number_format($max) }}
                                @endif
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="small text-muted">Minimum</div>
                            <div class="h6 text-warning">
                                @if($chartType === 'revenue')
                                    {{ number_format($min, 0, ',', ' ') }} FCFA
                                @else
                                    {{ number_format($min) }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Types de graphique -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Types de Données</h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action {{ $chartType === 'revenue' ? 'active' : '' }}"
                           wire:click.prevent="updateChartType('revenue')">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Revenu</h6>
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <small>Chiffre d'affaires généré</small>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action {{ $chartType === 'subscriptions' ? 'active' : '' }}"
                           wire:click.prevent="updateChartType('subscriptions')">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Abonnements</h6>
                                <i class="fas fa-users"></i>
                            </div>
                            <small>Nouveaux abonnements</small>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action {{ $chartType === 'payments' ? 'active' : '' }}"
                           wire:click.prevent="updateChartType('payments')">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Paiements</h6>
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <small>Transactions effectuées</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Derniers paiements et abonnements expirant -->
    <div class="row">
        <!-- Derniers paiements -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history me-2"></i>Derniers Paiements
                    </h6>
                    <a href="{{ route('super-admin.subscriptions.index') }}" class="btn btn-sm btn-primary">
                        Voir tout
                    </a>
                </div>
                <div class="card-body">
                    @if($recentPayments->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentPayments as $payment)
                            <div class="list-group-item px-0">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="avatar-sm">
                                            <div class="avatar-title bg-success rounded-circle text-white">
                                                <i class="fas fa-check"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-0">{{ $payment->company->name ?? 'Entreprise inconnue' }}</h6>
                                                <small class="text-muted">
                                                    {{ $payment->paid_at->format('d/m/Y') }} •
                                                    {{ $payment->payment_method }}
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <strong class="text-success">
                                                    {{ number_format($payment->amount, 0, ',', ' ') }} FCFA
                                                </strong>
                                                <br>
                                                <small class="badge bg-{{ $payment->subscriptionPlan->color ?? 'secondary' }}">
                                                    {{ $payment->subscriptionPlan->display_name ?? 'N/A' }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">Aucun paiement récent</h6>
                            <p class="text-muted small">Aucun paiement n'a été enregistré récemment.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Abonnements expirant bientôt -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-clock me-2"></i>Abonnements Expirant Bientôt
                    </h6>
                    <span class="badge bg-warning">{{ $expiringSubscriptions->count() }}</span>
                </div>
                <div class="card-body">
                    @if($expiringSubscriptions->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($expiringSubscriptions as $company)
                            @php
                                $daysLeft = now()->diffInDays($company->subscription_expires_at, false);
                                $isUrgent = $daysLeft <= 7;
                            @endphp
                            <div class="list-group-item px-0 {{ $isUrgent ? 'border-warning' : '' }}">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="avatar-sm">
                                            <div class="avatar-title bg-{{ $isUrgent ? 'warning' : 'info' }} rounded-circle text-white">
                                                <i class="fas fa-{{ $isUrgent ? 'exclamation' : 'clock' }}"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-0">{{ $company->name }}</h6>
                                                <small class="text-muted">
                                                    Expire le {{ $company->subscription_expires_at->format('d/m/Y') }}
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-{{ $isUrgent ? 'danger' : 'warning' }}">
                                                    {{ $daysLeft }} jour(s)
                                                </span>
                                                <br>
                                                <small class="text-muted">
                                                    {{ $company->subscription_plan }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h6 class="text-success">Aucun abonnement en attente</h6>
                            <p class="text-muted small">Tous les abonnements sont à jour.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts pour le graphique Chart.js -->
    @script
    <script>
        document.addEventListener('livewire:initialized', () => {
            let chart = null;

            // Fonction pour initialiser/mettre à jour le graphique
            function updateChart() {
                const ctx = document.getElementById('subscriptionChart').getContext('2d');

                // Détruire le graphique existant
                if (chart) {
                    chart.destroy();
                }

                // Données du composant Livewire
                const chartData = $wire.chartData;
                const chartType = $wire.chartType;
                const period = $wire.period;

                // Préparer les données
                const labels = chartData.map(item => item.label);
                const data = chartData.map(item => item.value);

                // Configuration selon le type de données
                let label, backgroundColor, borderColor, yAxisLabel;

                switch(chartType) {
                    case 'revenue':
                        label = 'Revenu (FCFA)';
                        backgroundColor = 'rgba(78, 115, 223, 0.1)';
                        borderColor = 'rgba(78, 115, 223, 1)';
                        yAxisLabel = 'FCFA';
                        break;
                    case 'subscriptions':
                        label = 'Nouveaux Abonnements';
                        backgroundColor = 'rgba(28, 200, 138, 0.1)';
                        borderColor = 'rgba(28, 200, 138, 1)';
                        yAxisLabel = 'Nombre';
                        break;
                    case 'payments':
                        label = 'Paiements';
                        backgroundColor = 'rgba(246, 194, 62, 0.1)';
                        borderColor = 'rgba(246, 194, 62, 1)';
                        yAxisLabel = 'Nombre';
                        break;
                }

                // Créer le graphique
                chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: label,
                            data: data,
                            backgroundColor: backgroundColor,
                            borderColor: borderColor,
                            borderWidth: 2,
                            pointBackgroundColor: borderColor,
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            fill: true,
                            tension: 0.3
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        layout: {
                            padding: {
                                left: 10,
                                right: 25,
                                top: 25,
                                bottom: 0
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false,
                                    drawBorder: false
                                },
                                ticks: {
                                    maxTicksLimit: 7
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: "rgb(234, 236, 244)",
                                    zeroLineColor: "rgb(234, 236, 244)",
                                    drawBorder: false,
                                    borderDash: [2],
                                    zeroLineBorderDash: [2]
                                },
                                ticks: {
                                    callback: function(value) {
                                        if (chartType === 'revenue') {
                                            return new Intl.NumberFormat('fr-FR').format(value) + ' F';
                                        }
                                        return new Intl.NumberFormat('fr-FR').format(value);
                                    }
                                },
                                title: {
                                    display: true,
                                    text: yAxisLabel
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: "rgb(255,255,255)",
                                bodyFontColor: "#858796",
                                titleMarginBottom: 10,
                                titleFontColor: '#6e707e',
                                titleFontSize: 14,
                                borderColor: '#dddfeb',
                                borderWidth: 1,
                                xPadding: 15,
                                yPadding: 15,
                                displayColors: false,
                                intersect: false,
                                mode: 'index',
                                caretPadding: 10,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (chartType === 'revenue') {
                                            label += new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' FCFA';
                                        } else {
                                            label += new Intl.NumberFormat('fr-FR').format(context.parsed.y);
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Mettre à jour le graphique lorsque les données changent
            $wire.on('chart-updated', () => {
                updateChart();
            });

            // Initialiser le graphique au chargement
            updateChart();
        });
    </script>
    @endscript

    <!-- Styles personnalisés -->
    <style>
        .chart-area {
            position: relative;
            height: 300px;
            width: 100%;
        }

        .avatar-sm {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .avatar-title {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .list-group-item {
            border: none;
            border-bottom: 1px solid #e3e6f0;
        }

        .list-group-item:last-child {
            border-bottom: none;
        }

        .btn-group .btn.active {
            background-color: #4e73df;
            border-color: #4e73df;
            color: white;
        }
    </style>
</div>
