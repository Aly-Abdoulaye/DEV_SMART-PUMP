<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART PUMP - Admin - @yield('title', 'Tableau de bord')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    @stack('styles')
    @livewireStyles
</head>
<body>
    <!-- Navigation Top -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-gas-pump me-2"></i>
                <strong>SMART PUMP</strong>

            </a>

            <div class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i>{{ auth()->user()->name }}
                        {{-- <small class="ms-1 opacity-75">({{ auth()->user()->company->name ?? 'Entreprise' }})</small> --}}
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Paramètres</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </div>
        </div>
    </nav>

    <!-- Sidebar + Content -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                               href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Tableau de bord
                            </a>
                        </li>

                        <!-- Gestion des Stations -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.stations.*') ? 'active' : '' }}"
                               href="{{ route('admin.stations.index') }}">
                                <i class="fas fa-gas-pump me-2"></i>
                                Gestion des Stations
                            </a>
                        </li>

                        <!-- Gestion des Utilisateurs -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                               href="#">
                                <i class="fas fa-users me-2"></i>
                                Gestion des Utilisateurs
                            </a>
                        </li>

                        <!-- Gestion du Carburant -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.fuel.*') ? 'active' : '' }}"
                               href="#">
                                <i class="fas fa-oil-can me-2"></i>
                                Gestion du Carburant
                            </a>
                        </li>

                        <!-- Ventes et Transactions -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.sales.*') ? 'active' : '' }}"
                               href="#">
                                <i class="fas fa-cash-register me-2"></i>
                                Ventes et Transactions
                            </a>
                        </li>

                        <!-- Rapports et Analytics -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"
                               href="#">
                                <i class="fas fa-chart-bar me-2"></i>
                                Rapports et Analytics
                            </a>
                        </li>

                        <!-- Gestion Financière -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.finance.*') ? 'active' : '' }}"
                               href="#">
                                <i class="fas fa-money-bill-wave me-2"></i>
                                Gestion Financière
                            </a>
                        </li>

                        <!-- Maintenance -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.maintenance.*') ? 'active' : '' }}"
                               href="#">
                                <i class="fas fa-tools me-2"></i>
                                Maintenance
                                <span class="badge bg-warning ms-2" id="maintenance-badge">0</span>
                            </a>
                        </li>

                        <!-- Support -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.support.*') ? 'active' : '' }}"
                               href="#">
                                <i class="fas fa-headset me-2"></i>
                                Support
                            </a>
                        </li>
                    </ul>

                    <!-- Section Paramètres -->
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <span>Paramètres</span>
                    </h6>
                    <ul class="nav flex-column mb-2">
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-building me-2"></i>
                                Mon Entreprise
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-bell me-2"></i>
                                Alertes et Notifications
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-file-export me-2"></i>
                                Export de données
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <!-- En-tête de page avec actions -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">

                    <div class="btn-toolbar mb-2 mb-md-0">
                        @yield('page-actions')
                    </div>
                </div>

                <!-- Messages flash -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Veuillez corriger les erreurs suivantes :</strong>
                        <ul class="mb-0 mt-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Contenu principal -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // Auto-dismiss alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });

        // Sidebar active state management
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('.nav-link');

            navLinks.forEach(link => {
                if (link.href === window.location.href) {
                    link.classList.add('active');
                }
            });
        });
    </script>

    @stack('scripts')
    @livewireScripts
</body>
</html>
