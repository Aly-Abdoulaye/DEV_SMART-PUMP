<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Station;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $period = request('period', 'month'); // day, week, month, year
        
        $stats = [
            'total_companies' => Company::count(),
            'total_stations' => Station::count(),
            'total_users' => User::count(),
            'total_sales' => Sale::count(),
            'revenue_trend' => $this->getRevenueTrend($period),
            'company_growth' => $this->getCompanyGrowth($period),
            'top_companies' => $this->getTopCompanies(),
            'recent_activity' => $this->getRecentActivity(),
        ];

        return view('super-admin.reports.index', compact('stats', 'period'));
    }

    public function financial()
    {
        $startDate = request('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = request('end_date', now()->format('Y-m-d'));
        
        $financialData = [
            'revenue_by_plan' => $this->getRevenueByPlan(),
            'subscription_status' => $this->getSubscriptionStatus(),
            'payment_history' => $this->getPaymentHistory($startDate, $endDate),
            'upcoming_renewals' => $this->getUpcomingRenewals(),
        ];

        return view('super-admin.reports.financial', compact('financialData', 'startDate', 'endDate'));
    }

    private function getRevenueTrend($period)
    {
        // Implémentation simplifiée - à compléter avec des données réelles
        return [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            'data' => [500000, 750000, 600000, 900000, 850000, 1000000],
        ];
    }

    private function getCompanyGrowth($period)
    {
        return [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            'data' => [5, 8, 12, 15, 18, 22],
        ];
    }

    private function getTopCompanies()
    {
        return Company::withCount(['stations', 'users'])
                     ->orderBy('stations_count', 'desc')
                     ->take(5)
                     ->get();
    }

    private function getRecentActivity()
    {
        // Retourner les activités récentes (créations d'entreprises, etc.)
        return Company::with(['users'])
                     ->latest()
                     ->take(10)
                     ->get();
    }

    private function getRevenueByPlan()
    {
        $plans = ['basic', 'premium', 'enterprise'];
        $revenue = [];

        foreach ($plans as $plan) {
            $revenue[$plan] = Company::where('subscription_plan', $plan)
                                   ->where('subscription_expires_at', '>=', now())
                                   ->count();
        }

        return $revenue;
    }

    private function getSubscriptionStatus()
    {
        return [
            'active' => Company::where('subscription_expires_at', '>=', now())->count(),
            'expired' => Company::where('subscription_expires_at', '<', now())->count(),
            'suspended' => Company::where('is_active', false)->count(),
        ];
    }

    private function getPaymentHistory($startDate, $endDate)
    {
        // À implémenter avec une table payments
        return [];
    }

    private function getUpcomingRenewals()
    {
        return Company::where('subscription_expires_at', '<=', now()->addDays(30))
                     ->where('subscription_expires_at', '>=', now())
                     ->orderBy('subscription_expires_at')
                     ->get();
    }
}