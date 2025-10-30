<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tank;
use App\Models\Station;
use Illuminate\Http\Request;

class TankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Station $station)
    {
        $this->authorizeStationAccess($station);

        $tanks = Tank::where('station_id', $station->id)
            ->with('pumps')
            ->latest()
            ->get();

        // Statistiques pour le header
        $totalCapacity = $tanks->sum('capacity');
        $totalVolume = $tanks->sum('current_volume');
        $lowStockTanks = $tanks->filter(function($tank) {
            return $tank->isLow();
        })->count();

        return view('admin.stations.tanks.index', compact('station', 'tanks', 'totalCapacity', 'totalVolume', 'lowStockTanks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Station $station)
    {
        $this->authorizeStationAccess($station);

        $fuelTypes = ['Gazole', 'SP95', 'SP98', 'GPL', 'Super'];

        return view('admin.stations.tanks.create', compact('station', 'fuelTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Station $station)
    {
        $this->authorizeStationAccess($station);

        $validated = $request->validate([
            'fuel_type' => 'required|string|max:50',
            'capacity' => 'required|numeric|min:1',
            'current_volume' => 'required|numeric|min:0',
            'min_threshold' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        Tank::create([
            'station_id' => $station->id,
            'fuel_type' => $validated['fuel_type'],
            'capacity' => $validated['capacity'],
            'current_volume' => $validated['current_volume'],
            'min_threshold' => $validated['min_threshold'],
            'is_active' => $validated['is_active'] ?? true
        ]);

        return redirect()->route('admin.stations.tanks.index', $station)
            ->with('success', 'Cuve créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Station $station, Tank $tank)
    {
        $this->authorizeTankAccess($station, $tank);

        $tank->load('pumps', 'station');

        // Historique des mouvements (à implémenter plus tard)
        $recentMovements = [];

        return view('admin.stations.tanks.show', compact('station', 'tank', 'recentMovements'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Station $station, Tank $tank)
    {
        $this->authorizeTankAccess($station, $tank);

        $fuelTypes = ['Gazole', 'SP95', 'SP98', 'GPL', 'Super'];

        return view('admin.stations.tanks.edit', compact('station', 'tank', 'fuelTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Station $station, Tank $tank)
    {
        $this->authorizeTankAccess($station, $tank);

        $validated = $request->validate([
            'fuel_type' => 'required|string|max:50',
            'capacity' => 'required|numeric|min:1',
            'current_volume' => 'required|numeric|min:0',
            'min_threshold' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        $tank->update($validated);

        return redirect()->route('admin.stations.tanks.index', $station)
            ->with('success', 'Cuve modifiée avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Station $station, Tank $tank)
    {
        $this->authorizeTankAccess($station, $tank);

        // Vérifier s'il y a des pompes associées
        if ($tank->pumps()->exists()) {
            return redirect()->back()
                ->with('error', 'Impossible de supprimer la cuve car elle est associée à des pompes.');
        }

        $tank->delete();

        return redirect()->route('admin.stations.tanks.index', $station)
            ->with('success', 'Cuve supprimée avec succès.');
    }

    /**
     * Ajuster le volume de la cuve
     */
    public function adjustVolume(Request $request, Station $station, Tank $tank)
    {
        $this->authorizeTankAccess($station, $tank);

        $validated = $request->validate([
            'adjustment_type' => 'required|in:add,withdraw,set',
            'quantity' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:255'
        ]);

        try {
            switch ($validated['adjustment_type']) {
                case 'add':
                    if (($tank->current_volume + $validated['quantity']) > $tank->capacity) {
                        return redirect()->back()
                            ->with('error', 'Volume trop important. Capacité disponible: ' . $tank->available_capacity . ' L');
                    }
                    $tank->update(['current_volume' => $tank->current_volume + $validated['quantity']]);
                    break;

                case 'withdraw':
                    if ($tank->current_volume < $validated['quantity']) {
                        return redirect()->back()
                            ->with('error', 'Stock insuffisant. Volume actuel: ' . $tank->current_volume . ' L');
                    }
                    $tank->update(['current_volume' => $tank->current_volume - $validated['quantity']]);
                    break;

                case 'set':
                    if ($validated['quantity'] > $tank->capacity) {
                        return redirect()->back()
                            ->with('error', 'Le volume ne peut pas dépasser la capacité de ' . $tank->capacity . ' L');
                    }
                    $tank->update(['current_volume' => $validated['quantity']]);
                    break;
            }

            return redirect()->back()
                ->with('success', 'Volume ajusté avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'ajustement: ' . $e->getMessage());
        }
    }

    /**
     * Toggle tank status
     */
    public function toggleStatus(Station $station, Tank $tank)
    {
        $this->authorizeTankAccess($station, $tank);

        $tank->update([
            'is_active' => !$tank->is_active
        ]);

        $status = $tank->is_active ? 'activée' : 'désactivée';

        return redirect()->back()
            ->with('success', "Cuve {$status} avec succès.");
    }

    /**
     * Authorization helpers
     */
 private function authorizeStationAccess(Station $station)
{
    $user = auth()->user();

    $stationCompanyId = \DB::table('stations')
        ->where('id', $station->id)
        ->value('company_id');

    $userCompanyId = $user->company_id;

    \Log::info('ROLE DEBUG:', [
        'user_role' => $user->role->slug ?? 'N/A',
        'user_role_id' => $user->role_id,
        'is_company_admin' => $user->isCompanyAdmin(),
        'is_super_admin' => $user->isSuperAdmin(),
        'methods_work' => [
            'isCompanyAdmin' => $user->isCompanyAdmin(),
            'isSuperAdmin' => $user->isSuperAdmin()
        ]
    ]);

    // TEST: Autoriser temporairement tout admin d'entreprise
    if ($user->isCompanyAdmin() || $user->isSuperAdmin()) {
        \Log::info('TEMPORARY ACCESS GRANTED FOR TESTING');
        return;
    }

    abort(403, 'Accès non autorisé.');
}

    private function authorizeTankAccess(Station $station, Tank $tank)
    {
        $this->authorizeStationAccess($station);

        if ($tank->station_id !== $station->id) {
            abort(403, 'Cette cuve n\'appartient pas à la station sélectionnée.');
        }
    }
}
