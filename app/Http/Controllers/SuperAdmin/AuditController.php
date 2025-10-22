<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AuditController extends Controller
{
    public function index()
    {
        $logs = AuditLog::with(['user', 'company'])
                       ->latest()
                       ->paginate(50);
        
        $stats = [
            'today' => AuditLog::whereDate('created_at', today())->count(),
            'suspicious' => AuditLog::where('suspicious', true)->count(),
            'users' => User::count(),
            'companies' => Company::count(),
        ];

        $suspiciousActivities = AuditLog::where('suspicious', true)
                                       ->latest()
                                       ->take(10)
                                       ->get();

        return view('super-admin.audit.index', compact('logs', 'stats', 'suspiciousActivities'));
    }

    public function suspicious()
    {
        $activities = AuditLog::where('suspicious', true)
                             ->with(['user', 'company'])
                             ->latest()
                             ->paginate(50);
        
        return view('super-admin.audit.suspicious', compact('activities'));
    }

    public function userActivity(User $user)
    {
        $activities = AuditLog::where('user_id', $user->id)
                             ->with('company')
                             ->latest()
                             ->paginate(50);
        
        return view('super-admin.audit.user', compact('activities', 'user'));
    }

    public function companyActivity(Company $company)
    {
        $activities = AuditLog::where('company_id', $company->id)
                             ->with('user')
                             ->latest()
                             ->paginate(50);
        
        return view('super-admin.audit.company', compact('activities', 'company'));
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'type' => 'required|in:all,suspicious,logins,changes',
        ]);

        $query = AuditLog::with(['user', 'company'])
                        ->whereBetween('created_at', [$validated['start_date'], $validated['end_date']]);

        if ($validated['type'] !== 'all') {
            switch ($validated['type']) {
                case 'suspicious':
                    $query->where('suspicious', true);
                    break;
                case 'logins':
                    $query->where('action', 'LIKE', '%login%');
                    break;
                case 'changes':
                    $query->where('action', 'LIKE', '%update%')
                          ->orWhere('action', 'LIKE', '%create%')
                          ->orWhere('action', 'LIKE', '%delete%');
                    break;
            }
        }

        $logs = $query->get();

        // Générer un CSV
        $fileName = 'audit-logs-' . date('Y-m-d') . '.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Utilisateur', 'Entreprise', 'Action', 'IP', 'Suspicieux']);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->user->name ?? 'N/A',
                    $log->company->name ?? 'N/A',
                    $log->action,
                    $log->ip_address,
                    $log->suspicious ? 'Oui' : 'Non'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}