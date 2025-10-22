<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Models\Station;
use App\Models\Sale;
use App\Models\SupportTicket;
use App\Models\AuditLog;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $stats = [
            // Supervision globale
            'total_companies' => Company::count(),
            'total_stations' => Station::count(),
            'total_users' => User::count(),
            'total_volume' => Sale::sum('volume'),
            'total_sales' => Sale::count(),
            'total_revenue' => Sale::sum('total_amount'),
            
            // Alertes et anomalies
            'suspended_companies' => Company::where('is_active', false)->count(),
            'expiring_subscriptions' => Company::where('subscription_expires_at', '<=', Carbon::now()->addDays(7))
                                                ->where('subscription_expires_at', '>', Carbon::now())
                                                ->count(),
            'pending_tickets' => SupportTicket::where('status', 'pending')->count(),
            'suspicious_activities' => AuditLog::where('suspicious', true)->count(),
            
            // Performances
            'active_companies' => Company::where('is_active', true)->count(),
            
            // Activité récente
            'recent_companies' => Company::latest()->take(5)->get(),
            'recent_tickets' => SupportTicket::with('company')->latest()->take(5)->get(),
        ];

        return view('super-admin.dashboard', compact('stats'));
    }
}