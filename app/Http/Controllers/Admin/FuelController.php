<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fuel;
use App\Models\FuelPriceHistory;
use App\Exports\FuelsExport;
use App\Exports\FuelPriceHistoryExport;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Models\Station;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PDF;


class FuelController extends Controller
{
    /**
     * Display a listing of the fuels.
     */
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        
        // Query de base
        $query = Fuel::where('company_id', $companyId)
                    ->withCount(['tanks', 'priceHistory']);

        // Filtres
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Tri
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $fuels = $query->orderBy($sortField, $sortDirection)
                      ->paginate(15)
                      ->withQueryString();

        // Statistiques pour le header
        $stats = [
            'total_fuels' => Fuel::where('company_id', $companyId)->count(),
            'active_fuels' => Fuel::where('company_id', $companyId)->where('is_active', true)->count(),
            'inactive_fuels' => Fuel::where('company_id', $companyId)->where('is_active', false)->count(),
            'total_price_changes' => FuelPriceHistory::whereHas('fuel', function($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })->count(),
        ];

        return view('admin.fuels.index', compact('fuels', 'stats', 'request'));
    }

    /**
     * Show the form for creating a new fuel.
     */
    public function create()
    {
        $companyId = auth()->user()->company_id;
        
        // Récupérer les stations de la compagnie
        $stations = Station::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.fuels.create', compact('stations'));
    }

    /**
     * Store a newly created fuel in storage.
     */
    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id;
        
        $validated = $request->validate([
            'name' => [
                'required', 
                'string', 
                'max:255',
                Rule::unique('fuels')->where(function ($query) use ($companyId) {
                    return $query->where('company_id', $companyId);
                })
            ],
            'code' => [
                'required', 
                'string', 
                'max:50',
                'alpha_dash',
                Rule::unique('fuels')->where(function ($query) use ($companyId) {
                    return $query->where('company_id', $companyId);
                })
            ],
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500'
        ], [
            'name.unique' => 'Un carburant avec ce nom existe déjà dans votre entreprise.',
            'code.unique' => 'Ce code est déjà utilisé dans votre entreprise.',
            'code.alpha_dash' => 'Le code ne peut contenir que des lettres, chiffres, tirets et underscores.',
        ]);

        // Créer le carburant
        $fuel = Fuel::create([
            'name' => $validated['name'],
            'code' => strtoupper($validated['code']),
            'price' => $validated['price'],
            'description' => $validated['description'],
            'company_id' => $companyId
        ]);

        // Enregistrer dans l'historique des prix
        FuelPriceHistory::create([
            'fuel_id' => $fuel->id,
            'old_price' => 0,
            'new_price' => $fuel->price,
            'change_reason' => 'Création du carburant',
            'changed_by' => auth()->id()
        ]);

        return redirect()->route('admin.fuels.show', $fuel)
            ->with('success', 'Carburant créé avec succès!');
    }

    /**
     * Display the specified fuel.
     */
   public function show(Fuel $fuel)
    {
        // Vérifier que le carburant appartient à la compagnie de l'utilisateur
        if ($fuel->company_id !== auth()->user()->company_id) {
            abort(403, 'Accès non autorisé.');
        }

        // ⭐ CORRECTION : Charger les relations avec with() pour éviter les requêtes N+1
        $fuel->load(['station', 'company', 'tanks']);

        // Récupérer l'historique des prix avec pagination
        $priceHistory = FuelPriceHistory::where('fuel_id', $fuel->id)
            ->with('changedByUser') // ⭐ Charger la relation changedByUser
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Compter le nombre de cuves associées
        $fuel->tanks_count = $fuel->tanks->count();

        return view('admin.fuels.show', compact('fuel', 'priceHistory'));
    }

    /**
     * Show the form for editing the specified fuel.
     */
    public function edit(Fuel $fuel)
    {
        // Vérifier que le carburant appartient à la compagnie de l'utilisateur
        if ($fuel->company_id !== auth()->user()->company_id) {
            abort(403, 'Accès non autorisé.');
        }

        $companyId = auth()->user()->company_id;
        
        // Récupérer les stations de la compagnie
        $stations = Station::where('company_id', $companyId)
            ->orderBy('name')
            ->get();

        // Charger les relations pour les compteurs
        $fuel->load(['station', 'priceHistory', 'tanks']);
        $fuel->tanks_count = $fuel->tanks->count();

        return view('admin.fuels.edit', compact('fuel', 'stations'));
    }

    /**
     * Mettre à jour le carburant
     */
    public function update(Request $request, Fuel $fuel)
    {
        // Vérifier que le carburant appartient à la compagnie de l'utilisateur
        if ($fuel->company_id !== auth()->user()->company_id) {
            abort(403, 'Accès non autorisé.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:fuels,code,' . $fuel->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'station_id' => 'required|exists:stations,id',
            'is_active' => 'boolean',
        ]);

        try {
            // Vérifier si le prix a changé
            $priceChanged = $validated['price'] != $fuel->price;
            $oldPrice = $fuel->price;

            // Mettre à jour le carburant
            $fuel->update([
                'name' => $validated['name'],
                'code' => $validated['code'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'current_price' => $validated['price'], // Mettre à jour le prix actuel
                'station_id' => $validated['station_id'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            // Si le prix a changé, créer un historique
            if ($priceChanged) {
                \App\Models\FuelPriceHistory::create([
                    'fuel_id' => $fuel->id,
                    'old_price' => $oldPrice,
                    'new_price' => $validated['price'],
                    'change_reason' => 'Modification via édition',
                    'changed_by' => auth()->id(),
                    'change_date' => now()
                ]);
            }

            return redirect()->route('admin.fuels.show', $fuel)
                ->with('success', 'Carburant mis à jour avec succès!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour du carburant: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified fuel from storage.
     */
    public function destroy(Fuel $fuel)
    {
        $this->authorizeFuelAccess($fuel);

        // Vérifier si le carburant est utilisé dans des cuves
        if ($fuel->tanks()->exists()) {
            return redirect()->route('admin.fuels.index')
                ->with('error', 'Impossible de supprimer ce carburant car il est utilisé dans ' . $fuel->tanks()->count() . ' cuve(s).');
        }

        // Vérifier s'il y a des ventes associées
        $salesCount = DB::table('sales')
            ->join('pumps', 'sales.pump_id', '=', 'pumps.id')
            ->join('tanks', 'pumps.tank_id', '=', 'tanks.id')
            ->where('tanks.fuel_type', $fuel->name)
            ->count();

        if ($salesCount > 0) {
            return redirect()->route('admin.fuels.index')
                ->with('error', 'Impossible de supprimer ce carburant car il existe ' . $salesCount . ' vente(s) associée(s).');
        }

        // Commencer une transaction pour s'assurer de l'intégrité des données
        DB::transaction(function () use ($fuel) {
            // Supprimer l'historique des prix
            $fuel->priceHistory()->delete();
            
            // Supprimer le carburant
            $fuel->delete();
        });

        return redirect()->route('admin.fuels.index')
            ->with('success', 'Carburant supprimé avec succès!');
    }

    /**
     * Update the fuel price with history tracking.
     */
    public function updatePrice(Request $request, Fuel $fuel)
    {
        $this->authorizeFuelAccess($fuel);

        $validated = $request->validate([
            'new_price' => 'required|numeric|min:0',
            'change_reason' => 'required|string|max:255'
        ]);

        $oldPrice = $fuel->price;
        $newPrice = $validated['new_price'];

        // Utiliser la méthode du modèle pour mettre à jour avec historique
        $fuel->updatePrice($newPrice, $validated['change_reason'], auth()->id());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Prix mis à jour: " . number_format($oldPrice, 0, ',', ' ') . " → " . number_format($newPrice, 0, ',', ' ') . " FCFA",
                'new_price' => $newPrice,
                'formatted_price' => number_format($newPrice, 0, ',', ' ') . ' FCFA'
            ]);
        }

        return redirect()->route('admin.fuels.show', $fuel)
            ->with('success', "Prix du {$fuel->name} mis à jour: " . 
                   number_format($oldPrice, 0, ',', ' ') . " → " . 
                   number_format($newPrice, 0, ',', ' ') . " FCFA");
    }

    /**
     * Toggle fuel active status.
     */
    public function toggleStatus(Fuel $fuel)
    {
        $this->authorizeFuelAccess($fuel);

        $newStatus = !$fuel->is_active;
        $fuel->update(['is_active' => $newStatus]);

        $statusText = $newStatus ? 'activé' : 'désactivé';

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Carburant {$statusText} avec succès",
                'is_active' => $newStatus
            ]);
        }

        return redirect()->route('admin.fuels.index')
            ->with('success', "Carburant {$statusText} avec succès!");
    }

    /**
     * Export fuels to Excel.
     */
    public function export(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $format = $request->get('format', 'xlsx');
        
        $filename = 'carburants-' . date('Y-m-d') . '.' . $format;

        return Excel::download(new FuelsExport($companyId), $filename);
    }

    /**
     * Export price history to Excel.
     */
    public function exportPriceHistoryPdf(Fuel $fuel)
{
    try {
        \Log::info('Tentative export PDF pour fuel: ' . $fuel->id);

        // Dates par défaut
        $endDate = now();
        $startDate = $endDate->copy()->subDays(30);

        // Récupérer l'historique
        $priceHistory = FuelPriceHistory::where('fuel_id', $fuel->id)
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->orderBy('created_at', 'desc')
            ->get();

        // Charger les relations
        $fuel->load(['station', 'company']);
        $priceHistory->load('changedByUser');

        // Récupérer l'entreprise
        $company = auth()->user()->company;

        // Gestion des logos
        $companyLogoBase64 = null;
        $appLogoBase64 = null;

        // Logo de l'entreprise
        if ($company && $company->logo) {
            $companyLogoPath = storage_path('app/public/' . $company->logo);
            if (file_exists($companyLogoPath)) {
                $imageInfo = getimagesize($companyLogoPath);
                $mimeType = $imageInfo['mime'] ?? 'image/png';
                $companyLogoData = file_get_contents($companyLogoPath);
                $companyLogoBase64 = 'data:' . $mimeType . ';base64,' . base64_encode($companyLogoData);
            }
        }

        // Logo de l'application
        $appLogoPath = public_path('img/Logo.png');
        if (file_exists($appLogoPath)) {
            $imageInfo = getimagesize($appLogoPath);
            $mimeType = $imageInfo['mime'] ?? 'image/png';
            $appLogoData = file_get_contents($appLogoPath);
            $appLogoBase64 = 'data:' . $mimeType . ';base64,' . base64_encode($appLogoData);
        }

        $filename = Str::slug($fuel->name . ' historique prix') . '_' . now()->format('Y-m-d') . '.pdf';

        \Log::info('Génération du PDF avec vue...');

        $pdf = Pdf::loadView('admin.fuels.pdf.price-history', [
            'fuel' => $fuel,
            'priceHistory' => $priceHistory,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'company' => $company,
            'companyLogo' => $companyLogoBase64,
            'appLogo' => $appLogoBase64,
        ]);

        // Configuration du PDF
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
            'charset' => 'UTF-8',
            'dpi' => 150
        ]);

        \Log::info('PDF généré avec succès, téléchargement...');

        // Forcer le téléchargement
        return $pdf->download($filename);

    } catch (\Exception $e) {
        \Log::error('ERREUR CRITIQUE export PDF: ' . $e->getMessage());
        \Log::error('Fichier: ' . $e->getFile() . ' Ligne: ' . $e->getLine());
        \Log::error('Stack trace: ' . $e->getTraceAsString());

        // Retourner une erreur visible
        return redirect()->back()
            ->with('error', 'Erreur lors de la génération du PDF: ' . $e->getMessage());
    }
}
    /**
     * Export all fuels' price history to PDF.
     */
    public function exportAllPriceHistoryPdf()
    {
        // Récupérer tous les carburants avec leur historique
        $fuels = Fuel::with(['priceHistory' => function($query) {
            $query->orderBy('date', 'desc');
        }])->get();

        // Utiliser le template que vous n'avez pas encore utilisé
        $pdf = PDF::loadView('fuels.exports.price-history-pdf', [
            'fuels' => $fuels,
            'exportDate' => now()->format('d/m/Y H:i'),
            'title' => 'Historique des prix - Tous les carburants'
        ]);

        return $pdf->download('historique-prix-tous-carburants-' . now()->format('d-m-Y') . '.pdf');
    }

    /**
     * Aperçu du PDF (optionnel)
     */
    public function previewPriceHistory(Fuel $fuel)
    {
        try {
            $endDate = now();
            $startDate = $endDate->copy()->subDays(30);

            $priceHistory = FuelPriceHistory::where('fuel_id', $fuel->id)
                ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->orderBy('created_at', 'desc')
                ->get();

            $fuel->load(['station', 'company']);
            $priceHistory->load('changedByUser');

            $pdf = PDF::loadView('admin.fuels.pdf.price-history', [
                'fuel' => $fuel,
                'priceHistory' => $priceHistory,
                'startDate' => $startDate,
                'endDate' => $endDate,
            ]);

            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'DejaVu Sans'
            ]);

            return $pdf->stream('preview-histoire-prix.pdf');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la prévisualisation: ' . $e->getMessage());
        }
    }
    /**
     * Get fuel statistics for the show page.
     */
    private function getFuelStats(Fuel $fuel)
    {
        $todaySales = DB::table('sales')
            ->join('pumps', 'sales.pump_id', '=', 'pumps.id')
            ->join('tanks', 'pumps.tank_id', '=', 'tanks.id')
            ->where('tanks.fuel_type', $fuel->name)
            ->whereDate('sales.sale_date', today())
            ->select(
                DB::raw('COALESCE(SUM(sales.volume), 0) as total_volume'),
                DB::raw('COALESCE(SUM(sales.total_amount), 0) as total_revenue')
            )
            ->first();

        $monthlySales = DB::table('sales')
            ->join('pumps', 'sales.pump_id', '=', 'pumps.id')
            ->join('tanks', 'pumps.tank_id', '=', 'tanks.id')
            ->where('tanks.fuel_type', $fuel->name)
            ->whereMonth('sales.sale_date', now()->month)
            ->whereYear('sales.sale_date', now()->year)
            ->select(
                DB::raw('COALESCE(SUM(sales.volume), 0) as total_volume'),
                DB::raw('COALESCE(SUM(sales.total_amount), 0) as total_revenue')
            )
            ->first();

        return [
            'tanks_count' => $fuel->tanks->count(),
            'active_tanks_count' => $fuel->tanks->where('is_active', true)->count(),
            'low_stock_tanks_count' => $fuel->tanks->where('current_volume', '<=', DB::raw('min_threshold'))->count(),
            'total_stock' => $fuel->tanks->sum('current_volume'),
            'total_capacity' => $fuel->tanks->sum('capacity'),
            'today_volume' => $todaySales->total_volume,
            'today_revenue' => $todaySales->total_revenue,
            'monthly_volume' => $monthlySales->total_volume,
            'monthly_revenue' => $monthlySales->total_revenue,
            'price_changes_count' => $fuel->priceHistory->count(),
            'last_price_change' => $fuel->priceHistory->first()?->created_at,
        ];
    }

    /**
     * Get fuel sales data for charts.
     */
    private function getFuelSalesData(Fuel $fuel)
    {
        $weeklySales = DB::table('sales')
            ->join('pumps', 'sales.pump_id', '=', 'pumps.id')
            ->join('tanks', 'pumps.tank_id', '=', 'tanks.id')
            ->where('tanks.fuel_type', $fuel->name)
            ->whereDate('sales.sale_date', '>=', now()->subDays(30))
            ->select(
                DB::raw('DATE(sales.sale_date) as date'),
                DB::raw('SUM(sales.volume) as volume'),
                DB::raw('SUM(sales.total_amount) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'weekly_sales' => $weeklySales,
            'labels' => $weeklySales->pluck('date')->map(function($date) {
                return \Carbon\Carbon::parse($date)->format('d/m');
            }),
            'volumes' => $weeklySales->pluck('volume'),
            'revenues' => $weeklySales->pluck('revenue'),
        ];
    }

    /**
     * Authorize fuel access - ensure user can only access their company's fuels.
     */
    private function authorizeFuelAccess(Fuel $fuel)
    {
        if ($fuel->company_id !== auth()->user()->company_id) {
            abort(403, 'Accès non autorisé à ce carburant.');
        }
    }
    
}