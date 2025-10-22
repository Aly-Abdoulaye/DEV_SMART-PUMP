<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::withCount(['stations', 'users'])->latest()->get();
        return view('super-admin.companies.index', compact('companies'));
    }

    public function create()
    {
        return view('super-admin.companies.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'subscription_plan' => 'required|in:basic,premium,enterprise',
            'subscription_expires_at' => 'required|date',
        ]);

        Company::create($validated);

        return redirect()->route('super-admin.companies.index')
            ->with('success', 'Entreprise créée avec succès.');
    }

    public function show(Company $company)
    {
        $company->load(['stations', 'users', 'customers']);
        return view('super-admin.companies.show', compact('company'));
    }

    public function edit(Company $company)
    {
        return view('super-admin.companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email,' . $company->id,
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'subscription_plan' => 'required|in:basic,premium,enterprise',
            'subscription_expires_at' => 'required|date',
            'is_active' => 'boolean',
        ]);

        $company->update($validated);

        return redirect()->route('super-admin.companies.index')
            ->with('success', 'Entreprise mise à jour avec succès.');
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return redirect()->route('super-admin.companies.index')
            ->with('success', 'Entreprise supprimée avec succès.');
    }

    public function suspend(Company $company)
{
    $company->update(['is_active' => false]);
    
    return redirect()->route('super-admin.companies.index')
        ->with('warning', "L'entreprise {$company->name} a été suspendue.");
}

public function activate(Company $company)
{
    $company->update(['is_active' => true]);
    
    return redirect()->route('super-admin.companies.index')
        ->with('success', "L'entreprise {$company->name} a été activée.");
}

public function impersonate(Company $company)
{
    // Permet au Super Admin de se connecter comme l'admin de l'entreprise
    Auth::login($company->users()->where('role', 'admin')->first());
    
    return redirect()->route('admin.dashboard')
        ->with('info', "Vous êtes maintenant connecté en tant qu'admin de {$company->name}");
}
}