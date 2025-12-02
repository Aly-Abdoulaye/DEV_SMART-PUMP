<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Station;
use App\Models\Pump;
use App\Models\Fuel;
use App\Models\MaintenanceIntervention;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EmployeeDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:employee');
    }

    public function index()
    {
        $user = Auth::user();
        
        // Récupérer la station de l'utilisateur
        $station = $user->station;
        
        // Si l'employé n'a pas de station
        if (!$station) {
            return view('employee.dashboard', [
                'user' => $user,
                'station' => null,
                'todayStats' => $this->getEmptyTodayStats(),
                'weekStats' => $this->getEmptyWeekStats(),
                'recentSales' => collect(),
                'pumps' => collect(),
                'goals' => $this->getEmployeeGoals($user),
            ])->with('warning', 'Aucune station assignée. Contactez votre administrateur.');
        }
        
        // Calculer les statistiques
        $todayStats = $this->getTodayStats($user);
        $weekStats = $this->getWeekStats($user);
        
        // Récupérer les autres données
        $recentSales = Sale::where('user_id', $user->id)
            ->with(['pump.fuel', 'customer'])
            ->latest()
            ->limit(10)
            ->get();
        
        $pumps = $station->pumps()
            ->where('status', 'active')
            ->with('fuel')
            ->get();
        
        $goals = $this->getEmployeeGoals($user);
        
        return view('employee.dashboard', [
            'user' => $user,
            'station' => $station,
            'todayStats' => $todayStats,
            'weekStats' => $weekStats,
            'recentSales' => $recentSales,
            'pumps' => $pumps,
            'goals' => $goals,
        ]);
    }

    private function getTodayStats($user)
    {
        $today = Carbon::today();
        
        $todaySales = Sale::where('user_id', $user->id)
                         ->whereDate('sale_date', $today)
                         ->where('status', 'completed');
        
        $count = $todaySales->count();
        $totalAmount = $todaySales->sum('total_amount');
        $totalVolume = $todaySales->sum('volume');
        
        return [
            'sales_count' => $count,
            'total_volume' => $totalVolume,
            'total_amount' => $totalAmount,
            'average_amount' => $count > 0 ? $totalAmount / $count : 0,
            'best_sale' => $todaySales->orderBy('total_amount', 'desc')->first(),
        ];
    }

    private function getWeekStats($user)
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        
        $weekSales = Sale::where('user_id', $user->id)
                        ->whereBetween('sale_date', [$startOfWeek, $endOfWeek])
                        ->where('status', 'completed');
        
        // Ventilation par jour
        $dailyStatsQuery = Sale::selectRaw('DATE(sale_date) as date, COUNT(*) as count, SUM(total_amount) as amount')
                         ->where('user_id', $user->id)
                         ->whereBetween('sale_date', [$startOfWeek, $endOfWeek])
                         ->where('status', 'completed')
                         ->groupBy('date')
                         ->orderBy('date')
                         ->get();
        
        // Formater les données pour le graphique
        $dailyStats = [];
        foreach ($dailyStatsQuery as $item) {
            $dayName = Carbon::parse($item->date)->locale('fr')->shortDayName;
            $dailyStats[$dayName] = [
                'count' => $item->count,
                'amount' => $item->amount
            ];
        }
        
        // S'assurer que tous les jours de la semaine sont présents
        $daysOfWeek = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
        foreach ($daysOfWeek as $day) {
            if (!isset($dailyStats[$day])) {
                $dailyStats[$day] = [
                    'count' => 0,
                    'amount' => 0
                ];
            }
        }
        
        // Trier par ordre des jours de la semaine
        $sortedDailyStats = [];
        foreach ($daysOfWeek as $day) {
            $sortedDailyStats[$day] = $dailyStats[$day];
        }
        
        return [
            'sales_count' => $weekSales->count(),
            'total_amount' => $weekSales->sum('total_amount'),
            'daily_stats' => $sortedDailyStats,
        ];
    }

    private function getEmployeeGoals($user)
    {
        // Objectifs par défaut - vous pouvez les stocker en base de données
        return [
            'daily_sales' => 30, // Nombre de ventes quotidiennes cible
            'daily_amount' => 500000, // Montant quotidien cible (FCFA)
            'weekly_sales' => 150, // Nombre de ventes hebdomadaires cible
            'customer_satisfaction' => 95, // Pourcentage de satisfaction cible
        ];
    }

    private function getEmptyTodayStats()
    {
        return [
            'sales_count' => 0,
            'total_volume' => 0,
            'total_amount' => 0,
            'average_amount' => 0,
            'best_sale' => null,
        ];
    }

    private function getEmptyWeekStats()
    {
        $daysOfWeek = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
        $dailyStats = [];
        foreach ($daysOfWeek as $day) {
            $dailyStats[$day] = [
                'count' => 0,
                'amount' => 0
            ];
        }
        
        return [
            'sales_count' => 0,
            'total_amount' => 0,
            'daily_stats' => $dailyStats,
        ];
    }
}