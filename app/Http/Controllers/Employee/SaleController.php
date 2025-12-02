<?php
// app/Http/Controllers/Employee/SaleController.php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Pump;
use App\Models\Customer;
use App\Models\Fuel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class SaleController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:employe');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Sale::where('user_id', $user->id)
                     ->with(['pump.fuel', 'customer', 'station']);
        
        // Filtres
        if ($request->filled('date')) {
            $query->whereDate('sale_date', $request->date);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $sales = $query->orderBy('sale_date', 'desc')->paginate(20);
        
        // Statistiques
        $stats = [
            'total_sales' => Sale::where('user_id', $user->id)->count(),
            'total_amount' => Sale::where('user_id', $user->id)->sum('total_amount'),
            'today_sales' => Sale::where('user_id', $user->id)
                                ->whereDate('sale_date', Carbon::today())
                                ->count(),
        ];
        
        return view('employee.sales.index', compact('sales', 'stats'));
    }

    public function create()
    {
        $user = Auth::user();
        
        // Récupérer les pompes de la station de l'employé
        $pumps = Pump::where('station_id', $user->station_id)
                    ->where('is_active', true)
                    ->with('fuel')
                    ->get();
        
        // Clients de l'entreprise
        $customers = Customer::where('company_id', $user->company_id)
                            ->where('is_active', true)
                            ->get();
        
        return view('employee.sales.create', compact('pumps', 'customers'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'pump_id' => 'required|exists:pumps,id',
            'volume' => 'required|numeric|min:0.1',
            'payment_method' => 'required|in:cash,card,mobile_money,customer_account',
            'customer_id' => 'nullable|exists:customers,id',
            'plaque_vehicule' => 'nullable|string|max:20',
        ]);
        
        // Vérifier que la pompe appartient à la station de l'employé
        $pump = Pump::findOrFail($request->pump_id);
        if ($pump->station_id != $user->station_id) {
            return back()->withErrors(['pump_id' => 'Pompe non autorisée.']);
        }
        
        DB::beginTransaction();
        try {
            // Récupérer le carburant
            $fuel = $pump->fuel;
            
            // Vérifier le stock
            if ($fuel->quantity_available < $request->volume) {
                return back()->withErrors([
                    'volume' => 'Stock insuffisant. Disponible: ' . $fuel->quantity_available . 'L'
                ]);
            }
            
            // Calculer les indices
            $startIndex = $pump->current_index;
            $endIndex = $startIndex + $request->volume;
            
            // Si paiement compte client
            if ($request->payment_method == 'customer_account' && $request->customer_id) {
                $customer = Customer::find($request->customer_id);
                $amount = $request->volume * $fuel->price;
                
                if ($customer->solde < $amount) {
                    return back()->withErrors([
                        'payment_method' => 'Solde insuffisant. Solde: ' . $customer->solde . ' FCFA'
                    ]);
                }
                
                // Débiter le compte
                $customer->decrement('solde', $amount);
            }
            
            // Créer la vente
            $sale = Sale::create([
                'pump_id' => $request->pump_id,
                'station_id' => $user->station_id,
                'user_id' => $user->id,
                'customer_id' => $request->customer_id,
                'volume' => $request->volume,
                'unit_price' => $fuel->price,
                'total_amount' => $request->volume * $fuel->price,
                'start_index' => $startIndex,
                'end_index' => $endIndex,
                'payment_method' => $request->payment_method,
                'status' => 'completed',
                'sale_date' => now(),
                'plaque_vehicule' => $request->plaque_vehicule,
            ]);
            
            // Mettre à jour le stock
            $fuel->decrement('quantity_available', $request->volume);
            
            // Mettre à jour l'indice de la pompe
            $pump->update([
                'current_index' => $endIndex,
                'total_volume' => $pump->total_volume + $request->volume,
            ]);
            
            DB::commit();
            
            // Rediriger vers le ticket
            return redirect()->route('employee.sales.ticket', $sale)
                           ->with('success', 'Vente enregistrée avec succès !');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    public function show(Sale $sale)
    {
        // Vérifier que la vente appartient à l'employé
        if ($sale->user_id != Auth::id()) {
            abort(403);
        }
        
        return view('employee.sales.show', compact('sale'));
    }

    public function ticket(Sale $sale)
    {
        if ($sale->user_id != Auth::id()) {
            abort(403);
        }
        
        return view('employee.sales.ticket', compact('sale'));
    }

    public function printTicket(Sale $sale)
    {
        if ($sale->user_id != Auth::id()) {
            abort(403);
        }
        
        $pdf = PDF::loadView('employee.sales.pdf-ticket', compact('sale'));
        return $pdf->stream('ticket-' . $sale->id . '.pdf');
    }
}