<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateFuelPriceRequest;

class FuelPriceController extends Controller
{
    public function edit(Fuel $fuel)
    {
        return view('admin.fuels.prices.edit', compact('fuel'));
    }
    
    public function update(UpdateFuelPriceRequest $request, Fuel $fuel)
    {
        $fuel->updatePrice(
            $request->validated('new_price'),
            $request->validated('reason'),
            auth()->id()
        );
        
        return response()->json([
            'success' => true,
            'new_price' => $fuel->price,
            'message' => 'Prix mis à jour avec succès'
        ]);
    }
    
    public function history(Fuel $fuel)
    {
        $history = $fuel->priceHistories()
                       ->with('changer')
                       ->latest()
                       ->paginate(20);
                       
        return view('admin.fuels.prices.history', compact('fuel', 'history'));
    }
    
}