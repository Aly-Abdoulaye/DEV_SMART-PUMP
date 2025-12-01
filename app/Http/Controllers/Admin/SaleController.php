<?php
// app/Http\Controllers/Admin/SaleController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Station;
use App\Models\Pump;
use App\Models\User;
use App\Models\Customer;
use App\Models\Fuel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesExport;

class SaleController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    /**
     * Afficher la liste des ventes
     */
    public function index(Request $request)
    {
        // Récupérer l'entreprise de l'admin
        $companyId = Auth::user()->company_id;
        
        // Récupérer toutes les stations de cette entreprise
        $stations = Station::where('company_id', $companyId)->get();
        $stationIds = $stations->pluck('id')->toArray();
        
        // Construire la requête
        $query = Sale::with(['station', 'pump.fuel', 'employee', 'customer'])
                     ->whereIn('station_id', $stationIds);
        
        // Appliquer les filtres
        $this->applyFilters($query, $request);
        
        // Tri et pagination
        $sales = $query->orderBy('sale_date', 'desc')->paginate(50);
        
        // Calculer les statistiques
        $statsQuery = Sale::whereIn('station_id', $stationIds);
        $this->applyFilters($statsQuery, $request);
        
        $stats = [
            'total_ventes' => $sales->total(),
            'total_volume' => $statsQuery->sum('volume'),
            'total_montant' => $statsQuery->sum('total_amount'),
            'ventes_aujourdhui' => Sale::whereIn('station_id', $stationIds)
                                       ->whereDate('sale_date', Carbon::today())
                                       ->count(),
        ];
        
        // Récupérer les pompes pour les filtres
        $pumps = Pump::whereIn('station_id', $stationIds)->get();
        
        return view('admin.sales.index', compact('sales', 'stations', 'pumps', 'stats'));
    }

    /**
     * Afficher les détails d'une vente
     */
    public function show(Sale $sale)
    {
        // Vérifier que la vente appartient à une station de l'entreprise
        $this->authorize('view', $sale);
        
        return view('admin.sales.show', compact('sale'));
    }

    /**
     * Annuler une vente
     */
    public function cancel(Request $request, Sale $sale)
    {
        $this->authorize('cancel', $sale);
        
        $request->validate([
            'reason' => 'required|string|max:255'
        ]);
        
        DB::beginTransaction();
        try {
            // Restaurer le stock du carburant
            $pump = $sale->pump;
            if ($pump && $pump->fuel) {
                $pump->fuel->increment('quantite_disponible', $sale->volume);
                
                // Mettre à jour le niveau de la cuve associée
                $tank = $pump->fuel->tank;
                if ($tank) {
                    $tank->increment('niveau_actuel', $sale->volume);
                }
            }
            
            // Si paiement par compte client, rembourser
            if ($sale->payment_method == 'customer_account' && $sale->customer) {
                $sale->customer->increment('solde', $sale->total_amount);
            }
            
            // Mettre à jour le statut de la vente
            $sale->update([
                'status' => 'cancelled',
                'cancelled_reason' => $request->reason,
                'cancelled_by' => Auth::id(),
                'cancelled_at' => now()
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.sales.show', $sale)
                           ->with('success', 'Vente annulée avec succès.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de l\'annulation: ' . $e->getMessage());
        }
    }

    /**
     * Générer un ticket PDF
     */
    public function ticket(Sale $sale)
    {
        $this->authorize('view', $sale);
        
        $pdf = PDF::loadView('admin.sales.ticket', compact('sale'));
        return $pdf->stream('ticket-' . $sale->numero_ticket . '.pdf');
    }

    /**
     * Exporter en Excel
     */
    public function exportExcel(Request $request)
    {
        // CORRECTION : Utiliser company_id au lieu de entreprise_id
        $companyId = Auth::user()->company_id;
        $stations = Station::where('company_id', $companyId)->pluck('id');
        
        // Passer les paramètres de filtrage à l'export
        $export = new SalesExport($request, $stations);
        
        return Excel::download($export, 'ventes_' . date('Y-m-d_H-i') . '.xlsx');
    }

    /**
     * Exporter en PDF
     */
    public function exportPdf(Request $request)
    {
        // CORRECTION : Utiliser company_id
        $companyId = Auth::user()->company_id;
        $stations = Station::where('company_id', $companyId)->get();
        
        // Construire la requête avec les filtres
        $query = Sale::with(['station', 'pump.fuel', 'employee'])
                     ->whereIn('station_id', $stations->pluck('id'));
        
        // Appliquer les mêmes filtres que l'index
        $this->applyFilters($query, $request);
        
        $sales = $query->orderBy('sale_date', 'desc')->limit(500)->get();
        
        // Calculer les statistiques pour le PDF
        $statsQuery = Sale::whereIn('station_id', $stations->pluck('id'));
        $this->applyFilters($statsQuery, $request);
        
        $stats = [
            'total_ventes' => $statsQuery->count(),
            'total_volume' => $statsQuery->sum('volume'),
            'total_montant' => $statsQuery->sum('total_amount'),
            'debut_periode' => $request->date_debut ?? Carbon::now()->startOfMonth(),
            'fin_periode' => $request->date_fin ?? Carbon::now()->endOfMonth(),
        ];
        
        // Générer le PDF
        $pdf = PDF::loadView('admin.sales.export-pdf', compact('sales', 'stats', 'stations'));
        
        // Taille du papier et orientation
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download('rapport_ventes_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Afficher les statistiques
     */
    public function statistics()
    {
        $companyId = Auth::user()->company_id;
        $stations = Station::where('company_id', $companyId)->pluck('id');
        
        // Statistiques par mois
        $monthlyStats = Sale::select(
                DB::raw('MONTH(sale_date) as mois'),
                DB::raw('YEAR(sale_date) as annee'),
                DB::raw('COUNT(*) as total_ventes'),
                DB::raw('SUM(volume) as total_volume'),
                DB::raw('SUM(total_amount) as total_montant')
            )
            ->whereIn('station_id', $stations)
            ->where('sale_date', '>=', Carbon::now()->subMonths(12))
            ->groupBy('annee', 'mois')
            ->orderBy('annee', 'desc')
            ->orderBy('mois', 'desc')
            ->get();
        
        // Top produits
        $topProducts = Sale::select(
                'pump_id',
                DB::raw('COUNT(*) as nombre_ventes'),
                DB::raw('SUM(volume) as volume_total'),
                DB::raw('SUM(total_amount) as chiffre_affaires')
            )
            ->whereIn('station_id', $stations)
            ->where('sale_date', '>=', Carbon::now()->subMonth())
            ->with('pump.fuel')
            ->groupBy('pump_id')
            ->orderBy('chiffre_affaires', 'desc')
            ->limit(10)
            ->get();
        
        // Ventes par station
        $salesByStation = Station::whereIn('id', $stations)
            ->withCount(['sales as total_ventes' => function($query) {
                $query->where('sale_date', '>=', Carbon::now()->subMonth());
            }])
            ->withSum(['sales as volume_total' => function($query) {
                $query->where('sale_date', '>=', Carbon::now()->subMonth());
            }], 'volume')
            ->withSum(['sales as chiffre_affaires' => function($query) {
                $query->where('sale_date', '>=', Carbon::now()->subMonth());
            }], 'total_amount')
            ->get();
        
        return view('admin.sales.statistics', compact(
            'monthlyStats', 'topProducts', 'salesByStation'
        ));
    }

    /**
     * Appliquer les filtres à la requête
     */
    private function applyFilters($query, Request $request)
    {
        if ($request->filled('station_id')) {
            $query->where('station_id', $request->station_id);
        }
        
        if ($request->filled('pump_id')) {
            $query->where('pump_id', $request->pump_id);
        }
        
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('sale_date', [
                $request->date_debut,
                Carbon::parse($request->date_fin)->endOfDay()
            ]);
        } elseif ($request->filled('periode')) {
            $periode = $this->getDateRange($request->periode);
            $query->whereBetween('sale_date', $periode);
        }
    }

    /**
     * Obtenir la plage de dates selon la période
     */
    private function getDateRange($periode)
    {
        switch ($periode) {
            case 'today':
                return [Carbon::today(), Carbon::today()->endOfDay()];
            case 'yesterday':
                return [Carbon::yesterday(), Carbon::yesterday()->endOfDay()];
            case 'week':
                return [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()];
            case 'month':
                return [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()];
            case 'last_month':
                return [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()];
            default:
                return [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()];
        }
    }
    private function getSalesByDay($sales)
{
    return $sales->groupBy(function($sale) {
        return $sale->sale_date->format('Y-m-d');
    })->map(function($daySales) {
        return [
            'count' => $daySales->count(),
            'volume' => $daySales->sum('volume'),
            'amount' => $daySales->sum('total_amount'),
        ];
    });
}

private function getTopPumps($sales)
{
    return $sales->groupBy('pump_id')
                 ->map(function($pumpSales) {
                     return [
                         'pump' => $pumpSales->first()->pump ?? null,
                         'count' => $pumpSales->count(),
                         'volume' => $pumpSales->sum('volume'),
                         'amount' => $pumpSales->sum('total_amount'),
                     ];
                 })
                 ->sortByDesc('amount')
                 ->take(10);
}

private function getPaymentMethodsStats($sales)
{
    return $sales->groupBy('payment_method')
                 ->map(function($methodSales) {
                     return [
                         'count' => $methodSales->count(),
                         'amount' => $methodSales->sum('total_amount'),
                         'percentage' => ($methodSales->count() / $sales->count()) * 100,
                     ];
                 });
}
}