{{-- resources/views/layouts/employee.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SMART PUMP - Espace Employé')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @stack('styles')
    <style>
        .sidebar {
            background-color: #2c3e50;
            min-height: 100vh;
            padding: 0;
            transition: all 0.3s;
        }
        .sidebar .nav-link {
            color: #bdc3c7;
            padding: 15px 20px;
            border-bottom: 1px solid #34495e;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: #3498db;
            color: white;
            padding-left: 25px;
        }
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        .main-content {
            background-color: #ecf0f1;
            min-height: 100vh;
        }
        .navbar {
            background-color: white;
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
        .stat-card {
        transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .sale-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .border-start-primary {
            border-left: 4px solid #4e73df !important;
        }
        .border-start-success {
            border-left: 4px solid #1cc88a !important;
        }
        .border-start-info {
            border-left: 4px solid #36b9cc !important;
        }
        .border-start-warning {
            border-left: 4px solid #f6c23e !important;
        }
        .quick-action-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transition: transform 0.3s;
        }
        .quick-action-card:hover {
            transform: translateY(-5px);
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            background-color: #3498db;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar d-none d-md-block">
                <div class="text-center py-4">
                    <div class="mb-3">
                        <div class="user-avatar mx-auto">
                            {{ substr(Auth::user()->name, 0, 2) }}
                        </div>
                    </div>
                    <h5 class="text-white mb-1">{{ Auth::user()->name }}</h5>
                    <small class="text-light">
                        <i class="fas fa-gas-pump me-1"></i>
                        {{ Auth::user()->station->name ?? 'Station' }}
                    </small>
                </div>

                <nav class="nav flex-column">
                    <a class="nav-link {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}" 
                       href="{{ route('employee.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i>Tableau de Bord
                    </a>
                    <a class="nav-link {{ request()->routeIs('employee.sales.create') ? 'active' : '' }}" 
                       href="{{ route('employee.sales.create') }}">
                        <i class="fas fa-cash-register"></i>Nouvelle Vente
                    </a>
                    <a class="nav-link {{ request()->routeIs('employee.sales.*') && !request()->routeIs('employee.sales.create') ? 'active' : '' }}" 
                       href="{{ route('employee.sales.index') }}">
                        <i class="fas fa-history"></i>Mes Ventes
                    </a>
                    <a class="nav-link {{ request()->routeIs('employee.statistics') ? 'active' : '' }}" 
                       href="{{ route('employee.statistics') }}">
                        <i class="fas fa-chart-bar"></i>Mes Statistiques
                    </a>
                    <a class="nav-link {{ request()->routeIs('employee.schedule') ? 'active' : '' }}" 
                       href="{{ route('employee.schedule') }}">
                        <i class="fas fa-user-clock"></i>Horaires
                    </a>
                    <a class="nav-link {{ request()->routeIs('employee.profile.*') ? 'active' : '' }}" 
                       href="{{ route('employee.profile.edit') }}">
                        <i class="fas fa-user-cog"></i>Mon Profil
                    </a>
                    <div class="mt-5 px-3">
                        <a class="btn btn-outline-light w-100" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                        </a>
                    </div>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <!-- Top Navigation -->
                <nav class="navbar navbar-expand-lg navbar-light bg-white">
                    <div class="container-fluid">
                        <button class="navbar-toggler d-md-none" type="button" data-bs-toggle="collapse" 
                                data-bs-target="#mobileSidebar">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        
                        <span class="navbar-brand d-none d-md-block">
                            <i class="fas fa-gas-pump text-primary me-2"></i>
                            SMART PUMP - Espace Employé
                        </span>
                        
                        <div class="d-md-none">
                            <div class="user-avatar">
                                {{ substr(Auth::user()->name, 0, 2) }}
                            </div>
                        </div>

                        <div class="navbar-nav ms-auto">
                            <div class="nav-item me-3">
                                <div class="badge bg-primary">
                                    <i class="fas fa-clock me-1"></i>
                                    <span id="current-time">{{ now()->format('H:i') }}</span>
                                </div>
                            </div>
                            <div class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                   data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user-circle me-2"></i>
                                    {{ Auth::user()->name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('employee.profile.edit') }}">
                                            <i class="fas fa-user me-2"></i>Mon Profil
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="fas fa-cog me-2"></i>Paramètres
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Mobile Sidebar -->
                <div class="collapse d-md-none" id="mobileSidebar">
                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <nav class="nav flex-column">
                                <a class="nav-link" href="{{ route('employee.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i>Tableau de Bord
                                </a>
                                <a class="nav-link" href="{{ route('employee.sales.create') }}">
                                    <i class="fas fa-cash-register me-2"></i>Nouvelle Vente
                                </a>
                                <a class="nav-link" href="{{ route('employee.sales.index') }}">
                                    <i class="fas fa-history me-2"></i>Mes Ventes
                                </a>
                                <a class="nav-link" href="{{ route('employee.statistics') }}">
                                    <i class="fas fa-chart-bar me-2"></i>Mes Statistiques
                                </a>
                                <a class="nav-link" href="{{ route('employee.schedule') }}">
                                    <i class="fas fa-user-clock me-2"></i>Horaires
                                </a>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Page Content -->
                <div class="container-fluid py-4">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

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
    
    <script>
        // Horloge en temps réel
        function updateTime() {
            const now = new Date();
            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');
            document.getElementById('current-time').textContent = `${hours}:${minutes}`;
        }
        setInterval(updateTime, 60000);
    </script>

    @stack('scripts')
</body>
</html>