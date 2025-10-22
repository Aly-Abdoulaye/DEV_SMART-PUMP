<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Tank;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $station = Auth::user()->station;
        
        $stats = [
            'today_sales' => Sale::where('station_id', $station->id)
                                ->whereDate('created_at', today())
                                ->sum('total_amount'),
            'today_volume' => Sale::where('station_id', $station->id)
                                 ->whereDate('created_at', today())
                                 ->sum('volume'),
            'low_stocks' => Tank::where('station_id', $station->id)
                               ->whereColumn('current_volume', '<=', 'min_threshold')
                               ->count(),
            'month_expenses' => Expense::where('station_id', $station->id)
                                     ->whereMonth('created_at', now()->month)
                                     ->sum('amount'),
            'recent_sales' => Sale::with(['pump', 'user'])
                                 ->where('station_id', $station->id)
                                 ->latest()
                                 ->take(5)
                                 ->get(),
        ];

        return view('manager.dashboard', compact('stats', 'station'));
    }
}