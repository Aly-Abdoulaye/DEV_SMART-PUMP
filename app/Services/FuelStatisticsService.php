<?php

namespace App\Services;

use App\Models\Fuel;
use App\Models\Station;
use App\Models\Tank;
use App\Models\Sale;
use App\Models\FuelPriceHistory;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FuelStatisticsService
{
    /**
     * Vue d'ensemble du tableau de bord
     */
    public function getOverview($companyId)
    {
        try {
            
            $today = now();
            $startOfDay = $today->copy()->startOfDay();
            $endOfDay = $today->copy()->endOfDay();
            $startOfWeek = $today->copy()->startOfWeek();
            $endOfWeek = $today->copy()->endOfWeek();
            $startOfMonth = $today->copy()->startOfMonth();
            $endOfMonth = $today->copy()->endOfMonth();

            // Compteurs de base
            $totalStations = Station::where('company_id', $companyId)->count();
            $totalFuels = Fuel::where('company_id', $companyId)->count();
            $activeFuels = Fuel::where('company_id', $companyId)->where('is_active', true)->count();

            // Ventes du jour
            $todaySales = Sale::whereHas('station', function($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })->whereBetween('created_at', [$startOfDay, $endOfDay]);

            $totalSalesToday = $todaySales->sum('quantity');
            $revenueToday = $todaySales->sum('amount');

            // Ventes de la semaine
            $weekSales = Sale::whereHas('station', function($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })->whereBetween('created_at', [$startOfWeek, $endOfWeek]);

            $totalSalesWeek = $weekSales->sum('quantity');
            $revenueWeek = $weekSales->sum('amount');

            // Ventes du mois
            $monthSales = Sale::whereHas('station', function($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })->whereBetween('created_at', [$startOfMonth, $endOfMonth]);

            $monthlyVolume = $monthSales->sum('quantity');
            $monthlyRevenue = $monthSales->sum('amount');

            // Alertes
            $lowStockAlerts = $this->getLowStockAlertsCount($companyId);
            $outOfStockAlerts = $this->getOutOfStockAlertsCount($companyId);

            // Données de prix
            $averagePrice = $this->getOverallAveragePrice($companyId);
            $priceTrend = $this->getAveragePriceTrend($companyId, 30);

            // Calculs dérivés
            $averageDailySales = $today->day > 1 ? $monthlyVolume / $today->day : $monthlyVolume;
            $utilizationRate = $this->calculateOverallUtilizationRate($companyId);

            return [
                // Stations et carburants
                'total_stations' => $totalStations,
                'total_fuels' => $totalFuels,
                'active_fuels' => $activeFuels,
                
                // Ventes - Aujourd'hui
                'total_sales_today' => $totalSalesToday,
                'revenue_today' => $revenueToday,
                'transactions_today' => $todaySales->count(),
                
                // Ventes - Semaine
                'total_sales_week' => $totalSalesWeek,
                'revenue_week' => $revenueWeek,
                
                // Ventes - Mois
                'monthly_volume' => $monthlyVolume,
                'monthly_revenue' => $monthlyRevenue,
                
                // Alertes
                'low_stock_alerts' => $lowStockAlerts,
                'out_of_stock_alerts' => $outOfStockAlerts,
                'total_alerts' => $lowStockAlerts + $outOfStockAlerts,
                
                // Données de prix
                'average_price' => $averagePrice,
                'average_price_formatted' => number_format($averagePrice, 0, ',', ' ') . ' FCFA',
                'price_trend' => $priceTrend,
                
                // Métriques calculées
                'average_daily_sales' => round($averageDailySales, 2),
                'utilization_rate' => $utilizationRate,
                'performance_score' => $this->calculatePerformanceScore($companyId),
                
                // Formats pour l'affichage
                'revenue_today_formatted' => number_format($revenueToday, 0, ',', ' ') . ' FCFA',
                'monthly_revenue_formatted' => number_format($monthlyRevenue, 0, ',', ' ') . ' FCFA',
                'total_sales_today_formatted' => number_format($totalSalesToday, 0, ',', ' ') . ' L',
                'total_sales_week_formatted' => number_format($totalSalesWeek, 0, ',', ' ') . ' L',
            ];

        } catch (\Exception $e) {
            return $this->getDefaultOverview();
        }
    }

    /**
     * Résumé par type de carburant
     */
    public function getFuelSummary($companyId)
{
    try {
        return Fuel::where('company_id', $companyId)
            ->with(['station', 'tanks'])
            ->get()
            ->map(function($fuel) use ($companyId) {
                $totalCapacity = $fuel->tanks->sum('capacity');
                $currentVolume = $fuel->tanks->sum('current_volume');
                
                // CORRECTION : Utiliser 'price' au lieu de 'current_price'
                $todaySalesVolume = $this->calculateTodaySalesVolume($fuel->id, $companyId);
                $todaySalesRevenue = $todaySalesVolume * $fuel->price; // ← Correction ici
                
                return [
                    'id' => $fuel->id,
                    'name' => $fuel->name,
                    'code' => $fuel->code,
                    // CORRECTION : Utiliser 'price' au lieu de 'current_price'
                    'current_price' => $fuel->price, // ← Correction ici
                    'current_price_formatted' => number_format($fuel->price, 0, ',', ' ') . ' FCFA', // ← Correction ici
                    'station_name' => $fuel->station->name ?? 'N/A',
                    'total_capacity' => $totalCapacity,
                    'current_volume' => $currentVolume,
                    'utilization_rate' => $totalCapacity > 0 ? round(($currentVolume / $totalCapacity) * 100, 2) : 0,
                    'color' => $fuel->color,
                    'is_active' => $fuel->is_active,
                    'status_badge' => $fuel->is_active ? 
                        '<span class="badge bg-success">Actif</span>' : 
                        '<span class="badge bg-danger">Inactif</span>',
                    // Ajouter les champs manquants
                    'today_sales_volume' => $todaySalesVolume,
                    'today_sales_revenue' => $todaySalesRevenue,
                ];
            });
    } catch (\Exception $e) {
        return collect([]);
    }
}
        /**
     * Calculer le volume des ventes du jour pour un carburant
     */
    private function calculateTodaySalesVolume($fuelId, $companyId)
    {
        try {
            $today = now()->format('Y-m-d');
            
            return Sale::whereHas('station', function($query) use ($companyId) {
                    $query->where('company_id', $companyId);
                })
                ->where('fuel_id', $fuelId)
                ->whereDate('created_at', $today)
                ->sum('quantity');
                
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Alertes de stock faible
     */
    public function getLowStockAlerts($companyId)
    {
        try {
            return Tank::where('current_volume', '<=', DB::raw('capacity * 0.2'))
                ->where('current_volume', '>', 0)
                ->whereHas('station', function($query) use ($companyId) {
                    $query->where('company_id', $companyId);
                })
                ->with(['station', 'fuel'])
                ->get()
                ->map(function($tank) {
                    $percentage = $tank->capacity > 0 ? round(($tank->current_volume / $tank->capacity) * 100, 2) : 0;
                    
                    return [
                        'type' => 'low_stock',
                        'message' => "Stock faible pour {$tank->fuel_type} à {$tank->station->name}",
                        'station' => $tank->station->name,
                        'fuel_type' => $tank->fuel_type,
                        'current_volume' => $tank->current_volume,
                        'capacity' => $tank->capacity,
                        'percentage' => $percentage,
                        'color' => app(Fuel::class)->getColorByName($tank->fuel_type),
                        'alert_level' => $percentage <= 10 ? 'danger' : 'warning',
                        'station_id' => $tank->station_id,
                        'tank_id' => $tank->id
                    ];
                });
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    /**
     * Performance des stations
     */
    public function getStationPerformance($companyId)
    {
        try {
            return Station::where('company_id', $companyId)
                ->with(['tanks', 'sales' => function($query) {
                    $query->select('station_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(amount) as total_amount'))
                          ->groupBy('station_id');
                }])
                ->get()
                ->map(function($station) {
                    $totalCapacity = $station->tanks->sum('capacity');
                    $currentVolume = $station->tanks->sum('current_volume');
                    $sales = $station->sales->first();
                    
                    $utilizationRate = $totalCapacity > 0 ? round(($currentVolume / $totalCapacity) * 100, 2) : 0;
                    
                    return [
                        'id' => $station->id,
                        'name' => $station->name,
                        'total_capacity' => $totalCapacity,
                        'current_volume' => $currentVolume,
                        'utilization_rate' => $utilizationRate,
                        'total_sales_volume' => $sales->total_quantity ?? 0,
                        'total_revenue' => $sales->total_amount ?? 0,
                        'status' => $station->status,
                        'status_badge' => $station->status === 'active' ? 
                            '<span class="badge bg-success">Active</span>' : 
                            '<span class="badge bg-danger">Inactive</span>',
                        'performance_level' => $this->getPerformanceLevel($utilizationRate),
                        'total_sales_volume_formatted' => number_format($sales->total_quantity ?? 0, 0, ',', ' ') . ' L',
                        'total_revenue_formatted' => number_format($sales->total_amount ?? 0, 0, ',', ' ') . ' FCFA',
                    ];
                });
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    /**
     * Niveau de performance basé sur le taux d'utilisation
     */
    private function getPerformanceLevel($utilizationRate)
    {
        if ($utilizationRate >= 80) return 'excellent';
        if ($utilizationRate >= 60) return 'good';
        if ($utilizationRate >= 40) return 'average';
        if ($utilizationRate >= 20) return 'poor';
        return 'critical';
    }

    /**
     * Tendances des ventes
     */
    public function getSalesTrends($companyId, $days = 7)
    {
        try {
            $startDate = Carbon::now()->subDays($days);
            $endDate = Carbon::now();

            $salesData = Sale::whereHas('station', function($query) use ($companyId) {
                    $query->where('company_id', $companyId);
                })
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('SUM(quantity) as total_quantity'),
                    DB::raw('SUM(amount) as total_amount'),
                    'fuel_type'
                )
                ->groupBy('date', 'fuel_type')
                ->orderBy('date')
                ->get();

            return $salesData->groupBy('fuel_type')
                ->map(function($sales, $fuelType) {
                    return [
                        'fuel_type' => $fuelType,
                        'data' => $sales->map(function($sale) {
                            return [
                                'date' => $sale->date,
                                'quantity' => $sale->total_quantity,
                                'amount' => $sale->total_amount,
                                'date_formatted' => Carbon::parse($sale->date)->format('d/m'),
                            ];
                        }),
                        'total_quantity' => $sales->sum('total_quantity'),
                        'total_amount' => $sales->sum('total_amount'),
                        'color' => app(Fuel::class)->getColorByName($fuelType)
                    ];
                });
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    /**
     * Changements de prix récents
     */
    public function getRecentPriceChanges($companyId, $limit = 5)
    {
        try {
            return FuelPriceHistory::whereHas('fuel', function($query) use ($companyId) {
                    $query->where('company_id', $companyId);
                })
                ->with(['fuel', 'changedByUser'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function($change) {
                    $percentageChange = $change->old_price > 0 ? 
                        (($change->new_price - $change->old_price) / $change->old_price) * 100 : 0;

                    return [
                        'fuel_name' => $change->fuel->name,
                        'old_price' => $change->old_price,
                        'new_price' => $change->new_price,
                        'change_amount' => $change->new_price - $change->old_price,
                        'change_percentage' => round($percentageChange, 2),
                        'change_date' => $change->created_at->format('d/m/Y H:i'),
                        'changed_by' => $change->changedByUser->name ?? 'Système',
                        'reason' => $change->change_reason,
                        'trend' => $percentageChange >= 0 ? 'up' : 'down',
                        'color' => app(Fuel::class)->getColorByName($change->fuel->name),
                        'old_price_formatted' => number_format($change->old_price, 0, ',', ' ') . ' FCFA',
                        'new_price_formatted' => number_format($change->new_price, 0, ',', ' ') . ' FCFA',
                        'change_amount_formatted' => ($change->new_price - $change->old_price >= 0 ? '+' : '') . 
                            number_format($change->new_price - $change->old_price, 0, ',', ' ') . ' FCFA',
                        'change_percentage_formatted' => ($percentageChange >= 0 ? '+' : '') . round($percentageChange, 2) . '%'
                    ];
                });
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    /**
     * Analyses avancées
     */
    public function getAdvancedAnalytics($companyId)
    {
        try {
            // Top 5 des carburants les plus vendus
            $topSellingFuels = Sale::whereHas('station', function($query) use ($companyId) {
                    $query->where('company_id', $companyId);
                })
                ->select('fuel_type', DB::raw('SUM(quantity) as total_quantity'))
                ->groupBy('fuel_type')
                ->orderBy('total_quantity', 'desc')
                ->limit(5)
                ->get()
                ->map(function($sale) {
                    return [
                        'fuel_type' => $sale->fuel_type,
                        'total_quantity' => $sale->total_quantity,
                        'color' => app(Fuel::class)->getColorByName($sale->fuel_type)
                    ];
                });

            // Performance mensuelle
            $monthlyPerformance = Sale::whereHas('station', function($query) use ($companyId) {
                    $query->where('company_id', $companyId);
                })
                ->select(
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(quantity) as total_quantity'),
                    DB::raw('SUM(amount) as total_amount')
                )
                ->whereYear('created_at', now()->year)
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get()
                ->map(function($data) {
                    return [
                        'period' => $data->year . '-' . str_pad($data->month, 2, '0', STR_PAD_LEFT),
                        'total_quantity' => $data->total_quantity,
                        'total_amount' => $data->total_amount,
                        'month_name' => Carbon::create()->month($data->month)->format('F')
                    ];
                });

            return [
                'top_selling_fuels' => $topSellingFuels,
                'monthly_performance' => $monthlyPerformance,
                'average_price_by_fuel' => $this->getAveragePriceByFuel($companyId),
                'inventory_turnover' => $this->calculateInventoryTurnover($companyId),
                'best_performing_station' => $this->getBestPerformingStation($companyId),
                'revenue_growth' => $this->calculateRevenueGrowth($companyId),
            ];
        } catch (\Exception $e) {
            return [
                'top_selling_fuels' => collect([]),
                'monthly_performance' => collect([]),
                'average_price_by_fuel' => collect([]),
                'inventory_turnover' => 0,
                'best_performing_station' => null,
                'revenue_growth' => 0,
            ];
        }
    }

    /**
     * Prix moyen par carburant
     */
    private function getAveragePriceByFuel($companyId)
    {
        try {
            return Fuel::where('company_id', $companyId)
                ->where('is_active', true)
                ->select('name', 'price') // ← CHANGER 'current_price' en 'price'
                ->get()
                ->mapWithKeys(function($fuel) {
                    return [$fuel->name => $fuel->price]; // ← CHANGER 'current_price' en 'price'
                });
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    /**
     * Calcul du taux de rotation des stocks
     */
    private function calculateInventoryTurnover($companyId)
    {
        try {
            $totalSales = Sale::whereHas('station', function($query) use ($companyId) {
                    $query->where('company_id', $companyId);
                })
                ->whereMonth('created_at', now()->month)
                ->sum('quantity');

            $averageInventory = Tank::whereHas('station', function($query) use ($companyId) {
                    $query->where('company_id', $companyId);
                })
                ->avg('current_volume');

            return $averageInventory > 0 ? round($totalSales / $averageInventory, 2) : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Station la plus performante
     */
    private function getBestPerformingStation($companyId)
    {
        try {
            return Station::where('company_id', $companyId)
                ->with(['sales' => function($query) {
                    $query->select('station_id', DB::raw('SUM(amount) as total_revenue'))
                          ->groupBy('station_id');
                }])
                ->get()
                ->map(function($station) {
                    $revenue = $station->sales->first()->total_revenue ?? 0;
                    return [
                        'id' => $station->id,
                        'name' => $station->name,
                        'revenue' => $revenue,
                        'revenue_formatted' => number_format($revenue, 0, ',', ' ') . ' FCFA'
                    ];
                })
                ->sortByDesc('revenue')
                ->first();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Calcul de la croissance du revenue
     */
    private function calculateRevenueGrowth($companyId)
    {
        try {
            $currentMonthRevenue = Sale::whereHas('station', function($query) use ($companyId) {
                    $query->where('company_id', $companyId);
                })
                ->whereMonth('created_at', now()->month)
                ->sum('amount');

            $previousMonthRevenue = Sale::whereHas('station', function($query) use ($companyId) {
                    $query->where('company_id', $companyId);
                })
                ->whereMonth('created_at', now()->subMonth()->month)
                ->sum('amount');

            return $previousMonthRevenue > 0 ? 
                round((($currentMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100, 2) : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Prix moyens par type de carburant
     */
    public function getAveragePrices($companyId)
    {
        try {
            return Fuel::where('company_id', $companyId)
                ->where('is_active', true)
                ->select('name', 'price', 'previous_price') // ← CHANGER 'current_price' en 'price'
                ->get()
                ->map(function($fuel) {
                    $change = $fuel->previous_price ? 
                        (($fuel->price - $fuel->previous_price) / $fuel->previous_price) * 100 : 0; // ← CHANGER 'current_price' en 'price'
                    
                    return [
                        'name' => $fuel->name,
                        'current_price' => $fuel->price, // ← CHANGER 'current_price' en 'price'
                        'previous_price' => $fuel->previous_price,
                        'change' => round($change, 2),
                        'trend' => $change >= 0 ? 'up' : 'down',
                        'color' => app(Fuel::class)->getColorByName($fuel->name),
                        'current_price_formatted' => number_format($fuel->price, 0, ',', ' ') . ' FCFA', // ← CHANGER 'current_price' en 'price'
                        'change_formatted' => ($change >= 0 ? '+' : '') . round($change, 2) . '%'
                    ];
                });
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    /**
     * Prix moyen global de tous les carburants
     */
    public function getOverallAveragePrice($companyId)
    {
        try {
            $average = Fuel::where('company_id', $companyId)
                ->where('is_active', true)
                ->avg('price'); // ← CHANGER 'current_price' en 'price'
            
            return $average ?: 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Évolution du prix moyen sur la période
     */
    public function getAveragePriceTrend($companyId, $days = 30)
    {
        try {
            $currentAverage = $this->getOverallAveragePrice($companyId);
            
            // Prix moyen il y a $days jours (calcul simplifié)
            $previousAverage = Fuel::where('company_id', $companyId)
                ->where('is_active', true)
                ->where('updated_at', '<=', now()->subDays($days))
                ->avg('price'); // ← CHANGER 'current_price' en 'price'
            
            $previousAverage = $previousAverage ?: $currentAverage;
            
            $change = $previousAverage > 0 ? 
                (($currentAverage - $previousAverage) / $previousAverage) * 100 : 0;
            
            return [
                'current' => $currentAverage,
                'previous' => $previousAverage,
                'change' => round($change, 2),
                'trend' => $change >= 0 ? 'up' : 'down',
                'current_formatted' => number_format($currentAverage, 0, ',', ' ') . ' FCFA',
                'change_formatted' => ($change >= 0 ? '+' : '') . round($change, 2) . '%'
            ];
        } catch (\Exception $e) {
            return [
                'current' => 0,
                'previous' => 0,
                'change' => 0,
                'trend' => 'stable',
                'current_formatted' => '0 FCFA',
                'change_formatted' => '0%'
            ];
        }
    }

    /**
     * Compteur d'alertes de stock faible
     */
    private function getLowStockAlertsCount($companyId)
    {
        try {
            return Tank::where('current_volume', '<=', DB::raw('capacity * 0.2'))
                ->where('current_volume', '>', 0)
                ->whereHas('station', function($query) use ($companyId) {
                    $query->where('company_id', $companyId);
                })
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Compteur d'alertes de rupture de stock
     */
    private function getOutOfStockAlertsCount($companyId)
    {
        try {
            return Tank::where('current_volume', '<=', 0)
                ->whereHas('station', function($query) use ($companyId) {
                    $query->where('company_id', $companyId);
                })
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Taux d'utilisation global
     */
    private function calculateOverallUtilizationRate($companyId)
    {
        try {
            $totalCapacity = Tank::whereHas('station', function($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })->sum('capacity');

            $currentVolume = Tank::whereHas('station', function($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })->sum('current_volume');

            return $totalCapacity > 0 ? round(($currentVolume / $totalCapacity) * 100, 2) : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Score de performance global
     */
    private function calculatePerformanceScore($companyId)
    {
        try {
            $utilizationRate = $this->calculateOverallUtilizationRate($companyId);
            $alertCount = $this->getLowStockAlertsCount($companyId) + $this->getOutOfStockAlertsCount($companyId);
            
            // Score basé sur le taux d'utilisation et le nombre d'alertes
            $score = $utilizationRate;
            if ($alertCount > 0) {
                $score -= ($alertCount * 5); // Pénalité pour les alertes
            }
            
            return max(0, min(100, $score)); // Garder entre 0 et 100
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Valeurs par défaut en cas d'erreur
     */
    private function getDefaultOverview()
    {
        return [
            'total_stations' => 0,
            'total_fuels' => 0,
            'active_fuels' => 0,
            'total_sales_today' => 0,
            'revenue_today' => 0,
            'transactions_today' => 0,
            'total_sales_week' => 0,
            'revenue_week' => 0,
            'monthly_volume' => 0,
            'monthly_revenue' => 0,
            'low_stock_alerts' => 0,
            'out_of_stock_alerts' => 0,
            'total_alerts' => 0,
            'average_price' => 0,
            'average_price_formatted' => '0 FCFA',
            'price_trend' => [
                'current' => 0,
                'previous' => 0,
                'change' => 0,
                'trend' => 'stable',
                'current_formatted' => '0 FCFA',
                'change_formatted' => '0%'
            ],
            'average_daily_sales' => 0,
            'utilization_rate' => 0,
            'performance_score' => 0,
            'revenue_today_formatted' => '0 FCFA',
            'monthly_revenue_formatted' => '0 FCFA',
            'total_sales_today_formatted' => '0 L',
            'total_sales_week_formatted' => '0 L',
        ];
    }

    /**
     * Données pour les graphiques de performance
     */
    public function getPerformanceChartData($companyId, $type = 'sales', $period = '30days')
    {
        try {
            $endDate = Carbon::now();
            
            switch ($period) {
                case '7days':
                    $startDate = $endDate->copy()->subDays(7);
                    break;
                case '30days':
                    $startDate = $endDate->copy()->subDays(30);
                    break;
                case '90days':
                    $startDate = $endDate->copy()->subDays(90);
                    break;
                default:
                    $startDate = $endDate->copy()->subDays(30);
            }

            if ($type === 'sales') {
                return $this->getSalesChartData($companyId, $startDate, $endDate);
            } elseif ($type === 'inventory') {
                return $this->getInventoryChartData($companyId, $startDate, $endDate);
            }

            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Données pour le graphique des ventes
     */
    private function getSalesChartData($companyId, $startDate, $endDate)
    {
        try {
            return Sale::whereHas('station', function($query) use ($companyId) {
                    $query->where('company_id', $companyId);
                })
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('SUM(quantity) as total_quantity'),
                    DB::raw('SUM(amount) as total_amount')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->map(function($sale) {
                    return [
                        'date' => $sale->date,
                        'quantity' => $sale->total_quantity,
                        'amount' => $sale->total_amount,
                        'date_formatted' => Carbon::parse($sale->date)->format('d/m')
                    ];
                });
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    /**
     * Données pour le graphique d'inventaire
     */
    private function getInventoryChartData($companyId, $startDate, $endDate)
    {
        try {
            return Tank::whereHas('station', function($query) use ($companyId) {
                    $query->where('company_id', $companyId);
                })
                ->select('fuel_type', DB::raw('SUM(current_volume) as total_volume'))
                ->groupBy('fuel_type')
                ->get()
                ->map(function($tank) {
                    return [
                        'fuel_type' => $tank->fuel_type,
                        'total_volume' => $tank->total_volume,
                        'color' => app(Fuel::class)->getColorByName($tank->fuel_type)
                    ];
                });
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    /**
     * Données pour les KPIs rapides (widgets)
     */
    public function getQuickKPIs($companyId)
    {
        try {
            $overview = $this->getOverview($companyId);
            
            return [
                'today_sales' => [
                    'value' => $overview['total_sales_today'],
                    'formatted' => $overview['total_sales_today_formatted'],
                    'trend' => 'up', // À implémenter avec comparaison hier/aujourd'hui
                    'icon' => 'fas fa-gas-pump'
                ],
                'today_revenue' => [
                    'value' => $overview['revenue_today'],
                    'formatted' => $overview['revenue_today_formatted'],
                    'trend' => 'up',
                    'icon' => 'fas fa-dollar-sign'
                ],
                'active_alerts' => [
                    'value' => $overview['total_alerts'],
                    'formatted' => $overview['total_alerts'],
                    'trend' => $overview['total_alerts'] > 0 ? 'up' : 'down',
                    'icon' => 'fas fa-exclamation-triangle'
                ],
                'utilization_rate' => [
                    'value' => $overview['utilization_rate'],
                    'formatted' => $overview['utilization_rate'] . '%',
                    'trend' => $overview['utilization_rate'] > 50 ? 'up' : 'down',
                    'icon' => 'fas fa-chart-pie'
                ]
            ];
        } catch (\Exception $e) {
            return [
                'today_sales' => ['value' => 0, 'formatted' => '0 L', 'trend' => 'stable', 'icon' => 'fas fa-gas-pump'],
                'today_revenue' => ['value' => 0, 'formatted' => '0 FCFA', 'trend' => 'stable', 'icon' => 'fas fa-dollar-sign'],
                'active_alerts' => ['value' => 0, 'formatted' => '0', 'trend' => 'stable', 'icon' => 'fas fa-exclamation-triangle'],
                'utilization_rate' => ['value' => 0, 'formatted' => '0%', 'trend' => 'stable', 'icon' => 'fas fa-chart-pie']
            ];
        }
    }
}