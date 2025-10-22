<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Station;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StationController extends Controller
{
    public function index()
    {
        $company = Auth::user()->company;
        $stations = $company->stations()->withCount(['users', 'tanks', 'pumps'])->get();
        
        return view('admin.stations.index', compact('stations'));
    }

    public function create()
    {
        $managers = User::where('company_id', Auth::user()->company_id)
                       ->where('role', 'manager')
                       ->whereNull('station_id')
                       ->get();
        
        return view('admin.stations.create', compact('managers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'nullable|string',
            'manager_id' => 'nullable|exists:users,id',
        ]);

        $station = Station::create([
            'name' => $validated['name'],
            'address' => $validated['address'],
            'phone' => $validated['phone'],
            'company_id' => Auth::user()->company_id,
        ]);

        // Assigner le manager si spécifié
        if ($request->manager_id) {
            User::where('id', $request->manager_id)->update(['station_id' => $station->id]);
        }

        return redirect()->route('admin.stations.index')
            ->with('success', 'Station créée avec succès.');
    }

    public function show(Station $station)
    {
        $this->checkAuthorization($station);
        
        $station->load(['tanks', 'pumps', 'users', 'sales' => function($query) {
            $query->latest()->take(10);
        }]);
        
        return view('admin.stations.show', compact('station'));
    }

    public function edit(Station $station)
    {
        $this->checkAuthorization($station);
        
        $managers = User::where('company_id', Auth::user()->company_id)
                       ->where('role', 'manager')
                       ->get();
        
        return view('admin.stations.edit', compact('station', 'managers'));
    }

    public function update(Request $request, Station $station)
    {
        $this->checkAuthorization($station);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'nullable|string',
            'manager_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);

        $station->update($validated);

        return redirect()->route('admin.stations.index')
            ->with('success', 'Station mise à jour avec succès.');
    }

    public function destroy(Station $station)
    {
        $this->checkAuthorization($station);
        
        $station->delete();
        
        return redirect()->route('admin.stations.index')
            ->with('success', 'Station supprimée avec succès.');
    }

    private function checkAuthorization(Station $station)
    {
        if ($station->company_id !== Auth::user()->company_id) {
            abort(403, 'Non autorisé.');
        }
    }
}