<?php
// app/Livewire/SuperAdmin/SubscriptionDashboard.php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use App\Models\Company;
use App\Models\Payment;
use App\Models\SubscriptionPlan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SubscriptionDashboard extends Component
{
    public $period = 'month'; // month, quarter, year
    public $chartType = 'revenue'; // revenue, subscriptions, payments

    public function updatePeriod($newPeriod)
    {
        $this->period = $newPeriod;
    }

    public function updateChartType($newType)
    {
        $this->chartType = $newType;
    }

    public function render()
    {
        $stats = $this->getStats();
        $chartData = $this->getChartData();
        $recentPayments = $this->getRecentPayments();
        $expiringSubscriptions = $this->getExpiringSubscriptions();

        return view('livewire.super-admin.subscription-dashboard', compact(
            'stats',
            'chartData',
            'recentPayments',
            'expiringSubscriptions'
        ));
    }

    private function getStats()
    {
        $currentMonth = now()->format('Y-m');
        $previousMonth = now()->subMonth()->format('Y-m');

        return [
            'total_companies' => Company::count(),
            'active_subscriptions' => Company::where('subscription_expires_at', '>=', now())->count(),
            'expired_subscriptions' => Company::where('subscription_expires_at', '<', now())->count(),
            'monthly_revenue' => Payment::completed()
                ->where('paid_at', 'like', "{$currentMonth}%")
                ->sum('amount'),
            'previous_month_revenue' => Payment::completed()
                ->where('paid_at', 'like', "{$previousMonth}%")
                ->sum('amount'),
            'pending_payments' => Payment::pending()->count(),
            'total_revenue' => Payment::completed()->sum('amount'),
        ];
    }

    private function getChartData()
    {
        $data = [];
        $endDate = now();

        switch ($this->period) {
            case 'month':
                $startDate = now()->subMonths(11);
                $format = 'M Y';
                $interval = '1 month';
                break;
            case 'quarter':
                $startDate = now()->subQuarters(7);
                $format = 'Q Y';
                $interval = '3 months';
                break;
            case 'year':
                $startDate = now()->subYears(4);
                $format = 'Y';
                $interval = '1 year';
                break;
        }

        $current = $startDate->copy();

        while ($current <= $endDate) {
            $label = $current->format($format);

            if ($this->chartType === 'revenue') {
                $value = Payment::completed()
                    ->whereYear('paid_at', $current->year)
                    ->when($this->period === 'month', function($query) use ($current) {
                        return $query->whereMonth('paid_at', $current->month);
                    })
                    ->when($this->period === 'quarter', function($query) use ($current) {
                        $quarter = ceil($current->month / 3);
                        return $query->whereRaw('QUARTER(paid_at) = ?', [$quarter]);
                    })
                    ->sum('amount');
            } elseif ($this->chartType === 'subscriptions') {
                $value = Company::whereYear('created_at', $current->year)
                    ->when($this->period === 'month', function($query) use ($current) {
                        return $query->whereMonth('created_at', $current->month);
                    })
                    ->when($this->period === 'quarter', function($query) use ($current) {
                        $quarter = ceil($current->month / 3);
                        return $query->whereRaw('QUARTER(created_at) = ?', [$quarter]);
                    })
                    ->count();
            } else {
                $value = Payment::whereYear('created_at', $current->year)
                    ->when($this->period === 'month', function($query) use ($current) {
                        return $query->whereMonth('created_at', $current->month);
                    })
                    ->when($this->period === 'quarter', function($query) use ($current) {
                        $quarter = ceil($current->month / 3);
                        return $query->whereRaw('QUARTER(created_at) = ?', [$quarter]);
                    })
                    ->count();
            }

            $data[] = [
                'label' => $label,
                'value' => $value
            ];

            if ($this->period === 'month') {
                $current->addMonth();
            } elseif ($this->period === 'quarter') {
                $current->addMonths(3);
            } else {
                $current->addYear();
            }
        }

        return $data;
    }

    private function getRecentPayments()
    {
        return Payment::with(['company', 'subscriptionPlan'])
            ->completed()
            ->latest('paid_at')
            ->limit(5)
            ->get();
    }

    private function getExpiringSubscriptions()
    {
        return Company::with(['subscriptionPlan'])
            ->where('subscription_expires_at', '<=', now()->addDays(30))
            ->where('subscription_expires_at', '>=', now())
            ->orderBy('subscription_expires_at')
            ->limit(5)
            ->get();
    }
}
