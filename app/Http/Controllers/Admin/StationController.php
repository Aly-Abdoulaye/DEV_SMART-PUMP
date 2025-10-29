<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Station;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Pour admin d'entreprise, récupérer les stations de son entreprise
        $user = auth()->user();

        if ($user->isCompanyAdmin()) {
            $stations = Station::where('company_id', $user->company_id)
                ->with(['managers', 'tanks', 'pumps'])
                ->withCount([
                    'sales as today_sales_count' => function($query) {
                        $query->whereDate('created_at', today());
                    },
                    'employees as employees_count',
                    'tanks as tanks_count'
                ])
                ->latest()
                ->get();
        } else {
            // Pour super admin ou autres rôles, adapter si nécessaire
            $stations = Station::with(['company', 'managers'])
                ->withCount(['sales', 'employees'])
                ->latest()
                ->get();
        }

        return view('admin.stations.index', compact('stations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();

        if ($user->isCompanyAdmin()) {
            $managers = User::where('company_id', $user->company_id)
                ->whereHas('role', function($query) {
                    $query->where('slug', 'station-manager');
                })
                ->active()
                ->get();
        } else {
            $managers = User::whereHas('role', function($query) {
                    $query->where('slug', 'station-manager');
                })
                ->active()
                ->get();
        }

        return view('admin.stations.create', compact('managers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'manager_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean'
        ]);

        try {
            DB::transaction(function () use ($validated, $user) {
                // Déterminer company_id selon le rôle
                $companyId = $user->isCompanyAdmin() ? $user->company_id : ($request->company_id ?? null);

                $station = Station::create([
                    'name' => $validated['name'],
                    'address' => $validated['address'],
                    'phone' => $validated['phone'],
                    'company_id' => $companyId,
                    'is_active' => $validated['is_active'] ?? true
                ]);

                // Si un manager est assigné, mettre à jour sa station
                if (!empty($validated['manager_id'])) {
                    User::where('id', $validated['manager_id'])
                        ->update(['station_id' => $station->id]);
                }
            });

            return redirect()->route('admin.stations.index')
                ->with('success', 'Station créée avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la création: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Station $station)
{
    $this->authorizeStationAccess($station);

    $station->load([
        'tanks',
        'pumps',
        'managers',
        'sales' => function($query) {
            $query->whereDate('created_at', today())->latest();
        }
    ]);

    // Utiliser le champ 'status' au lieu de 'is_active'
    $todaySales = $station->sales()->whereDate('created_at', today())->count();
    $activePumps = $station->pumps()->where('status', 'active')->count();
    $employeesCount = $station->employees()->count();

    return view('admin.stations.show', compact(
        'station',
        'todaySales',
        'activePumps',
        'employeesCount'
    ));
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Station $station)
    {
        $this->authorizeStationAccess($station);

        $user = auth()->user();

        if ($user->isCompanyAdmin()) {
            $managers = User::where('company_id', $user->company_id)
                ->whereHas('role', function($query) {
                    $query->where('slug', 'station-manager');
                })
                ->active()
                ->get();
        } else {
            $managers = User::whereHas('role', function($query) {
                    $query->where('slug', 'station-manager');
                })
                ->active()
                ->get();
        }

        return view('admin.stations.edit', compact('station', 'managers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Station $station)
    {
        $this->authorizeStationAccess($station);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'manager_id' => 'nullable|exists:users,id',
            'is_active' => 'required|boolean'
        ]);

        try {
            DB::transaction(function () use ($station, $validated) {
                // Sauvegarder l'ancien manager
                $oldManagerId = $station->managers->first()->id ?? null;

                // Mettre à jour la station
                $station->update([
                    'name' => $validated['name'],
                    'address' => $validated['address'],
                    'phone' => $validated['phone'],
                    'is_active' => $validated['is_active']
                ]);

                // Gérer le changement de manager
                $newManagerId = $validated['manager_id'] ?? null;

                if ($oldManagerId !== $newManagerId) {
                    // Retirer l'ancien manager
                    if ($oldManagerId) {
                        User::where('id', $oldManagerId)->update(['station_id' => null]);
                    }

                    // Assigner le nouveau manager
                    if ($newManagerId) {
                        User::where('id', $newManagerId)->update(['station_id' => $station->id]);
                    }
                }
            });

            return redirect()->route('admin.stations.index')
                ->with('success', 'Station modifiée avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la modification: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Station $station)
    {
        $this->authorizeStationAccess($station);

        // Vérifier s'il y a des données associées
        if ($station->sales()->exists() || $station->employees()->exists()) {
            return redirect()->back()
                ->with('error', 'Impossible de supprimer la station car elle contient des données historiques.');
        }

        $station->delete();

        return redirect()->route('admin.stations.index')
            ->with('success', 'Station supprimée avec succès.');
    }

    /**
     * Toggle station status
     */
    public function toggleStatus(Station $station)
    {
        $this->authorizeStationAccess($station);

        $station->update([
            'is_active' => !$station->is_active
        ]);

        $status = $station->is_active ? 'activée' : 'désactivée';

        return redirect()->back()
            ->with('success', "Station {$status} avec succès.");
    }

    /**
     * Authorization helper
     */
    private function authorizeStationAccess(Station $station)
    {
        $user = auth()->user();

        // Super admin a accès à tout
        if ($user->isSuperAdmin()) {
            return;
        }

        // Admin d'entreprise n'a accès qu'à ses stations
        if ($user->isCompanyAdmin() && $station->company_id !== $user->company_id) {
            abort(403, 'Accès non autorisé à cette station.');
        }

        // Autres rôles - adapter selon vos besoins
        if (!$user->isCompanyAdmin() && !$user->isSuperAdmin()) {
            abort(403, 'Accès non autorisé.');
        }
    }
}
