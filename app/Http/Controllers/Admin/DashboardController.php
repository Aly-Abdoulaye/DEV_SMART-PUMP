<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Station;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $company = Auth::user()->company;
        
        $stats = [
            'total_stations' => $company->stations()->count(),
            'active_stations' => $company->stations()->where('is_active', true)->count(),
            'total_employees' => $company->users()->count(),
            'today_sales' => Sale::whereIn('station_id', $company->stations->pluck('id'))
                                ->whereDate('created_at', today())
                                ->sum('total_amount'),
            'recent_stations' => $company->stations()->latest()->take(5)->get(),
        ];

        return view('admin.dashboard', compact('stats', 'company'));
    }
}