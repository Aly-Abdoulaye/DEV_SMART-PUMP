<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Pump;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    public function index()
    {
        $station = Auth::user()->station;
        $sales = Sale::with(['pump', 'user', 'customer'])
                    ->where('station_id', $station->id)
                    ->latest()
                    ->paginate(20);
        
        return view('manager.sales.index', compact('sales'));
    }

    public function create()
    {
        $station = Auth::user()->station;
        $pumps = Pump::where('station_id', $station->id)->where('status', 'active')->get();
        $customers = Customer::where('company_id', $station->company_id)->get();
        
        return view('manager.sales.create', compact('pumps', 'customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pump_id' => 'required|exists:pumps,id',
            'volume' => 'required|numeric|min:0.1',
            'unit_price' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,customer_account,card,mobile_money',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        $pump = Pump::findOrFail($request->pump_id);
        
        // Vérifier que la pompe appartient à la station du manager
        if ($pump->station_id !== Auth::user()->station_id) {
            abort(403, 'Non autorisé.');
        }

        $sale = Sale::create([
            'pump_id' => $validated['pump_id'],
            'station_id' => Auth::user()->station_id,
            'user_id' => Auth::id(),
            'customer_id' => $validated['customer_id'],
            'volume' => $validated['volume'],
            'unit_price' => $validated['unit_price'],
            'total_amount' => $validated['volume'] * $validated['unit_price'],
            'start_index' => $pump->current_index,
            'end_index' => $pump->current_index + $validated['volume'],
            'payment_method' => $validated['payment_method'],
            'sale_date' => now(),
        ]);

        // Mettre à jour l'index de la pompe
        $pump->update(['current_index' => $sale->end_index]);

        return redirect()->route('manager.sales.index')
            ->with('success', 'Vente enregistrée avec succès.');
    }
}