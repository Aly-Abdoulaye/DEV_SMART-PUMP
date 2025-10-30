<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pump;
use App\Models\Station;
use App\Models\Tank;
use Illuminate\Http\Request;

class PumpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Station $station)
    {
        $this->authorizeStationAccess($station);

        $pumps = Pump::where('station_id', $station->id)
            ->with(['tank'])
            ->latest()
            ->get();

        $activePumps = $pumps->where('status', 'active')->count();
        $pumpsWithTank = $pumps->where('tank_id', '!=', null)->count();

        return view('admin.stations.pumps.index', compact('station', 'pumps', 'activePumps', 'pumpsWithTank'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Station $station)
    {
        $this->authorizeStationAccess($station);

        $tanks = Tank::where('station_id', $station->id)
            ->active()
            ->get();

        $statusOptions = [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'maintenance' => 'Maintenance',
            'broken' => 'En Panne'
        ];

        return view('admin.stations.pumps.create', compact('station', 'tanks', 'statusOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Station $station)
    {
        $this->authorizeStationAccess($station);

        $validated = $request->validate([
            'pump_number' => 'required|string|max:50',
            'nozzle_number' => 'nullable|string|max:50',
            'tank_id' => 'nullable|exists:tanks,id',
            'status' => 'required|in:active,inactive,maintenance,broken',
            'initial_index' => 'required|numeric|min:0',
            'current_index' => 'required|numeric|min:0'
        ]);

        // Vérifier que le numéro de pompe est unique pour cette station
        $existingPump = Pump::where('station_id', $station->id)
            ->where('pump_number', $validated['pump_number'])
            ->first();

        if ($existingPump) {
            return redirect()->back()
                ->with('error', 'Une pompe avec ce numéro existe déjà dans cette station.')
                ->withInput();
        }

        Pump::create([
            'station_id' => $station->id,
            'pump_number' => $validated['pump_number'],
            'nozzle_number' => $validated['nozzle_number'],
            'tank_id' => $validated['tank_id'],
            'status' => $validated['status'],
            'initial_index' => $validated['initial_index'],
            'current_index' => $validated['current_index']
        ]);

        return redirect()->route('admin.stations.pumps.index', $station)
            ->with('success', 'Pompe créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Station $station, Pump $pump)
    {
        $this->authorizePumpAccess($station, $pump);

        $pump->load(['tank', 'sales' => function($query) {
            $query->latest()->take(10);
        }, 'maintenances' => function($query) {
            $query->latest()->take(5);
        }]);

        $todaySales = $pump->sales()->whereDate('created_at', today())->count();
        $totalVolume = $pump->sales()->sum('volume');

        return view('admin.stations.pumps.show', compact('station', 'pump', 'todaySales', 'totalVolume'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Station $station, Pump $pump)
    {
        $this->authorizePumpAccess($station, $pump);

        $tanks = Tank::where('station_id', $station->id)
            ->active()
            ->get();

        $statusOptions = [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'maintenance' => 'Maintenance',
            'broken' => 'En Panne'
        ];

        return view('admin.stations.pumps.edit', compact('station', 'pump', 'tanks', 'statusOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Station $station, Pump $pump)
    {
        $this->authorizePumpAccess($station, $pump);

        $validated = $request->validate([
            'pump_number' => 'required|string|max:50',
            'nozzle_number' => 'nullable|string|max:50',
            'tank_id' => 'nullable|exists:tanks,id',
            'status' => 'required|in:active,inactive,maintenance,broken',
            'initial_index' => 'required|numeric|min:0',
            'current_index' => 'required|numeric|min:0'
        ]);

        // Vérifier l'unicité du numéro de pompe (sauf pour cette pompe)
        $existingPump = Pump::where('station_id', $station->id)
            ->where('pump_number', $validated['pump_number'])
            ->where('id', '!=', $pump->id)
            ->first();

        if ($existingPump) {
            return redirect()->back()
                ->with('error', 'Une pompe avec ce numéro existe déjà dans cette station.')
                ->withInput();
        }

        $pump->update($validated);

        return redirect()->route('admin.stations.pumps.index', $station)
            ->with('success', 'Pompe modifiée avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Station $station, Pump $pump)
    {
        $this->authorizePumpAccess($station, $pump);

        // Vérifier s'il y a des ventes associées
        if ($pump->sales()->exists()) {
            return redirect()->back()
                ->with('error', 'Impossible de supprimer la pompe car elle a des ventes associées.');
        }

        $pump->delete();

        return redirect()->route('admin.stations.pumps.index', $station)
            ->with('success', 'Pompe supprimée avec succès.');
    }

    /**
     * Mettre à jour l'index de la pompe
     */
    public function updateIndex(Request $request, Station $station, Pump $pump)
    {
        $this->authorizePumpAccess($station, $pump);

        $validated = $request->validate([
            'new_index' => 'required|numeric|min:0',
            'reason' => 'required|string|max:255'
        ]);

        if ($validated['new_index'] < $pump->current_index) {
            return redirect()->back()
                ->with('error', 'Le nouvel index ne peut pas être inférieur à l\'index actuel.');
        }

        $oldIndex = $pump->current_index;
        $pump->update(['current_index' => $validated['new_index']]);

        return redirect()->back()
            ->with('success', "Index mis à jour: {$oldIndex} → {$validated['new_index']}");
    }

    /**
     * Changer le statut de la pompe
     */
    public function updateStatus(Request $request, Station $station, Pump $pump)
    {
        $this->authorizePumpAccess($station, $pump);

        $validated = $request->validate([
            'status' => 'required|in:active,inactive,maintenance,broken'
        ]);

        $pump->update(['status' => $validated['status']]);

        $statusText = $pump->status_text;

        return redirect()->back()
            ->with('success', "Statut de la pompe changé à: {$statusText}");
    }

    /**
     * Authorization helpers
     */
    private function authorizeStationAccess(Station $station)
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) return;

        if ($user->isCompanyAdmin() && $station->company_id !== $user->company_id) {
            abort(403, 'Accès non autorisé à cette station.');
        }
    }

    private function authorizePumpAccess(Station $station, Pump $pump)
    {
        $this->authorizeStationAccess($station);

        if ($pump->station_id !== $station->id) {
            abort(403, 'Cette pompe n\'appartient pas à la station sélectionnée.');
        }
    }
}
