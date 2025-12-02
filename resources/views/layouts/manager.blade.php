<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMART PUMP - Espace Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            background-color: #2c3e50;
            min-height: 100vh;
            padding: 0;
        }
        .sidebar .nav-link {
            color: #ecf0f1;
            padding: 15px 20px;
            border-bottom: 1px solid #34495e;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: #34495e;
            color: #3498db;
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
            border-left: 4px solid #3498db;
        }
        .alert-card {
            border-left: 4px solid #e74c3c;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
    <div class="text-center py-4">
        <h4 class="text-white">SMART PUMP</h4>
        <small class="text-muted">Espace Manager</small>
    </div>

    <nav class="nav flex-column">
        <a class="nav-link active" href="{{ route('manager.dashboard') }}">
            <i class="fas fa-tachometer-alt"></i>Tableau de Bord
            <span class="badge bg-danger float-end mt-1" id="alert-badge">2</span>
        </a>

        <!-- Carburant -->
        <a class="nav-link" data-bs-toggle="collapse" href="#collapseFuel">
            <i class="fas fa-gas-pump"></i>Gestion Carburant
            <i class="fas fa-chevron-down float-end mt-1"></i>
        </a>
        <div class="collapse show" id="collapseFuel">
            <div class="nav flex-column ps-3">
                <a class="nav-link small" href="#">Niveaux des cuves</a>
                <a class="nav-link small" href="#">Achats carburant</a>
                <a class="nav-link small" href="#">Écarts et anomalies</a>
            </div>
        </div>

        <!-- Ventes -->
        <a class="nav-link" data-bs-toggle="collapse" href="#collapseSales">
            <i class="fas fa-shopping-cart"></i>Ventes
            <i class="fas fa-chevron-down float-end mt-1"></i>
        </a>
        <div class="collapse" id="collapseSales">
            <div class="nav flex-column ps-3">
                <a class="nav-link small" href="#">Ventes du jour</a>
                <a class="nav-link small" href="#">Par employé</a>
                <a class="nav-link small" href="#">Historique</a>
            </div>
        </div>

        <!-- Dépenses -->
        <a class="nav-link" href="#">
            <i class="fas fa-money-bill-wave"></i>Dépenses
            <span class="badge bg-warning float-end mt-1">3 en attente</span>
        </a>

        <!-- Clients -->
        <a class="nav-link" href="#">
            <i class="fas fa-users"></i>Clients Partenaires
        </a>

        <!-- Communication -->
        <a class="nav-link" href="#">
            <i class="fas fa-comments"></i>Messagerie
            <span class="badge bg-info float-end mt-1">5</span>
        </a>

        <!-- Rapports -->
        <a class="nav-link" href="#">
            <i class="fas fa-file-invoice"></i>Rapports
        </a>

        <!-- Équipe -->
        <a class="nav-link" href="#">
            <i class="fas fa-user-friends"></i>Mon Équipe
        </a>

        <!-- Paramètres -->
        <a class="nav-link" href="#">
            <i class="fas fa-cog"></i>Paramètres
        </a>

        <hr class="text-white-50 mx-3 my-2">

        <a class="nav-link text-warning" href="{{ route('logout') }}"
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
<button class="btn btn-success btn-sm">
    <i class="fas fa-lock me-1"></i>Clôturer journée
</button>
                        <div class="navbar-nav ms-auto">
                            <div class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                   data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user-circle me-2"></i>
                                    {{ auth()->user()->name ?? 'Manager' }}
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profil</a></li>
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
