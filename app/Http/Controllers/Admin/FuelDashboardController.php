<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FuelStatisticsService;
use Illuminate\Http\Request;
use App\Models\Fuel;
use App\Models\PriceHistory;
use Illuminate\Support\Facades\Log;

class FuelDashboardController extends Controller
{
    /**
     * Service des statistiques carburants
     */
    protected $fuelStatsService;

    /**
     * Constructeur avec injection de dépendance
     */
    public function __construct(FuelStatisticsService $fuelStatsService)
    {
        $this->fuelStatsService = $fuelStatsService;
    }

    /**
     * Tableau de bord principal - Vue d'ensemble
     */
    public function index()
    {
        try {

            
            
            $companyId = auth()->user()->company_id;
            $totalFuels = \App\Models\Fuel::where('company_id', $companyId)->count();
            $activeFuels = \App\Models\Fuel::where('company_id', $companyId)->where('is_active', true)->count();
            logger("Direct DB Check - Total: $totalFuels, Active: $activeFuels");

            // Récupérer toutes les données via le service avec fallbacks
            $lowStockAlerts = $this->fuelStatsService->getLowStockAlerts($companyId);
            $data = [
                'overview' => $this->fuelStatsService->getOverview($companyId),
                'fuelSummary' => $this->fuelStatsService->getFuelSummary($companyId),
                'lowStockAlerts' => $lowStockAlerts,
                'low_stock_tanks' => $lowStockAlerts, // Alias
                'stationPerformance' => $this->fuelStatsService->getStationPerformance($companyId),
                'salesTrend' => $this->fuelStatsService->getSalesTrends($companyId, 7),
                'recentPriceChanges' => $this->fuelStatsService->getRecentPriceChanges($companyId, 5),
            ];

            return view('admin.fuels.dashboard.index', $data);

        } catch (\Exception $e) {
            Log::error('FuelDashboard Error: ' . $e->getMessage());
            
            // Données par défaut en cas d'erreur
                       $lowStockAlerts = collect([]);
           return view('admin.fuels.dashboard.index', [
               'overview' => $this->getDefaultOverview(),
               'fuelSummary' => collect([]),
               'lowStockAlerts' => $lowStockAlerts,
               'low_stock_tanks' => $lowStockAlerts, // Alias
               'stationPerformance' => collect([]),
               'salesTrend' => collect([]),
               'recentPriceChanges' => collect([]),
               'error' => 'Erreur lors du chargement des données'
           ]);
        }
    }

    /**
     * Analyses avancées et rapports détaillés
     */
    public function analytics()
    {
        try {
            $currentPrices = Fuel::where('is_active', true)
                ->select('name', 'price', 'auto_color')
                ->orderBy('price', 'desc')
                ->get();

            // Statistiques étendues
            $stats = [
                'total_fuels' => Fuel::count(),
                'active_fuels' => $currentPrices->count(),
                'total_price_changes' => PriceHistory::count(),
                'price_changes_today' => PriceHistory::whereDate('created_at', today())->count(),
                'avg_price' => $currentPrices->avg('price') ?? 0,
                'total_tanks' => Tank::count(),
                'max_variation' => 12.5, // À calculer selon vos données
                'stability_rate' => 85, // À calculer selon vos données
                'trends' => [
                    'Essence' => 'up',
                    'Diesel' => 'stable', 
                    'GPL' => 'down'
                ]
            ];

            // Distribution des carburants
            $fuelDistribution = Fuel::withCount('tanks')
                ->where('is_active', true)
                ->get()
                ->map(function($fuel) {
                    return [
                        'name' => $fuel->name,
                        'tanks_count' => $fuel->tanks_count,
                        'color' => $fuel->auto_color
                    ];
                });

            // Évolution des prix (30 derniers jours)
            $priceEvolution = PriceHistory::selectRaw('
                    fuel_id,
                    DATE(created_at) as date,
                    AVG(new_price) as avg_price
                ')
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('fuel_id', 'date')
                ->with('fuel')
                ->get()
                ->groupBy('fuel.name');

            // Derniers changements de prix
            $recentPriceChanges = PriceHistory::with('fuel')
                ->latest()
                ->take(15)
                ->get();

            // Statistiques mensuelles
            $monthlyStats = PriceHistory::selectRaw('
                    MONTH(created_at) as month,
                    YEAR(created_at) as year,
                    COUNT(*) as changes_count
                ')
                ->where('created_at', '>=', now()->subMonths(6))
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get();

            return view('admin.fuels.analytics', compact(
                'stats',
                'fuelDistribution',
                'priceEvolution',
                'recentPriceChanges',
                'currentPrices',
                'monthlyStats'
            ));

        } catch (\Exception $e) {
            \Log::error('Error in fuel analytics: ' . $e->getMessage());
            
            return view('admin.fuels.dashboard.analytics', [
                'stats' => [
                    'total_fuels' => 0,
                    'active_fuels' => 0,
                    'total_price_changes' => 0,
                    'price_changes_today' => 0,
                    'avg_price' => 0,
                    'total_tanks' => 0,
                    'max_variation' => 0,
                    'stability_rate' => 0,
                    'trends' => []
                ],
                'fuelDistribution' => collect([]),
                'priceEvolution' => collect([]),
                'recentPriceChanges' => collect([]),
                'currentPrices' => collect([]),
                'monthlyStats' => collect([]),
            ]);
        }
    }

    /**
     * Données pour les graphiques (API)
     */
    public function chartData(Request $request)
    {
        try {
            $companyId = auth()->user()->company_id;
            
            $chartType = $request->get('type', 'sales_trend');
            $days = $request->get('days', 30);

            $data = [];
            
            switch ($chartType) {
                case 'sales_trend':
                    $data = $this->fuelStatsService->getSalesTrends($companyId, $days);
                    break;
                    
                case 'fuel_performance':
                    $data = $this->fuelStatsService->getFuelSummary($companyId);
                    break;
                    
                case 'station_comparison':
                    $data = $this->fuelStatsService->getStationPerformance($companyId);
                    break;
                    
                default:
                    $data = [];
            }

            return response()->json([
                'success' => true,
                'data' => $data,
                'labels' => $this->generateChartLabels($chartType, $data)
            ]);

        } catch (\Exception $e) {
            Log::error('FuelChartData Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'data' => [],
                'labels' => [],
                'error' => 'Erreur lors du chargement des données'
            ]);
        }
    }

    /**
     * Alertes en temps réel (pour widgets)
     */
    public function alerts()
    {
        try {
            $companyId = auth()->user()->company_id;

            return response()->json([
                'success' => true,
                'alerts' => [
                    'low_stock' => $this->fuelStatsService->getLowStockAlerts($companyId),
                    'recent_changes' => $this->fuelStatsService->getRecentPriceChanges($companyId, 10),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('FuelAlerts Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'alerts' => [
                    'low_stock' => [],
                    'recent_changes' => [],
                ]
            ]);
        }
    }

    /**
     * Export du tableau de bord en PDF
     */
    public function exportPdf()
    {
        try {
            $companyId = auth()->user()->company_id;
            
            $data = [
                'overview' => $this->fuelStatsService->getOverview($companyId),
                'fuelSummary' => $this->fuelStatsService->getFuelSummary($companyId),
                'stationPerformance' => $this->fuelStatsService->getStationPerformance($companyId),
                'exportDate' => now()->format('d/m/Y H:i'),
                'company' => \App\Models\Company::find($companyId),
            ];

            // Pour l'instant, retournez une vue (vous pourrez ajouter PDF plus tard)
            return view('admin.fuels.dashboard.export-pdf', $data);

        } catch (\Exception $e) {
            Log::error('FuelExportPDF Error: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'Erreur lors de l\'export PDF');
        }
    }

    /**
     * Génération des labels pour les graphiques
     */
    private function generateChartLabels($chartType, $data)
    {
        try {
            switch ($chartType) {
                case 'sales_trend':
                    return $data->pluck('date')->map(function ($date) {
                        return \Carbon\Carbon::parse($date)->format('d/m');
                    })->toArray();
                    
                case 'fuel_performance':
                    return $data->pluck('name')->toArray();
                    
                case 'station_comparison':
                    return $data->pluck('name')->toArray();
                    
                default:
                    return [];
            }
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Données par défaut en cas d'erreur
     */
    private function getDefaultOverview()
    {
        return [
            'total_stations' => 0,
            'total_fuels' => 0,
            'active_fuels' => 0,
            'monthly_revenue' => 0,
            'monthly_volume' => 0,
            'low_stock_alerts' => 0,
            'out_of_stock_alerts' => 0,
        ];
    }
}