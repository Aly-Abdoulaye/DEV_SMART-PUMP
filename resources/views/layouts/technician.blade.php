<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART PUMP - Espace Technicien</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            background-color: #e67e22;
            min-height: 100vh;
            padding: 0;
        }
        .sidebar .nav-link {
            color: #ecf0f1;
            padding: 15px 20px;
            border-bottom: 1px solid #f39c12;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: #f39c12;
            color: #fff;
        }
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .navbar {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .stat-card {
            border-left: 4px solid #e67e22;
        }
        .alert-card {
            border-left: 4px solid #e74c3c;
        }
        .equipment-status-active { color: #27ae60; }
        .equipment-status-maintenance { color: #f39c12; }
        .equipment-status-broken { color: #e74c3c; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="text-center py-4">
                    <h4 class="text-white">SMART PUMP</h4>
                    <small class="text-light">Espace Technicien</small>
                </div>

                <nav class="nav flex-column">
                    <a class="nav-link active" href="{{ route('technician.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i>Tableau de Bord
                    </a>
                    <a class="nav-link" href="#">
                        <i class="fas fa-tools"></i>Équipements
                    </a>
                    <a class="nav-link" href="#">
                        <i class="fas fa-clipboard-list"></i>Interventions
                    </a>
                    <a class="nav-link" href="#">
                        <i class="fas fa-exclamation-triangle"></i>Alertes
                    </a>
                    <a class="nav-link" href="#">
                        <i class="fas fa-history"></i>Historique
                    </a>
                    <a class="nav-link" href="#">
                        <i class="fas fa-file-invoice"></i>Rapports
                    </a>
                    <a class="nav-link" href="#">
                        <i class="fas fa-cog"></i>Paramètres
                    </a>
                    <a class="nav-link" href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i>Déconnexion
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <!-- Top Navigation -->
                <nav class="navbar navbar-expand-lg navbar-light bg-white">
                    <div class="container-fluid">
                        <span class="navbar-brand">Tableau de Bord Technicien</span>
                        <div class="navbar-nav ms-auto">
                            <div class="badge bg-warning me-3">
                                <i class="fas fa-tools me-1"></i>
                                <span id="interventions-count">3 interventions</span>
                            </div>
                            <div class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                   data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user-circle me-2"></i>
                                    {{ auth()->user()->name ?? 'Technicien' }}
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Mon Profil</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Paramètres</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Page Content -->
                <div class="container-fluid py-4">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    @yield('scripts')
</body>
</html>
