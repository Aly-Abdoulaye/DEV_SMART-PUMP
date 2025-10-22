<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanySettingController extends Controller
{
    public function edit(Company $company)
    {
        return view('super-admin.companies.settings', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            // Logo et apparence
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'primary_color' => 'required|string|max:7',
            'secondary_color' => 'required|string|max:7',
            
            // Seuils d'alerte
            'alert_threshold' => 'required|numeric|min:0',
            'low_stock_alert' => 'required|numeric|min:0',
            'maintenance_alert_days' => 'required|integer|min:1|max:30',
            
            // Règles métier
            'business_rules' => 'nullable|string|max:1000',
            
            // Paramètres de sécurité
            'session_timeout' => 'nullable|integer|min:15|max:480',
            'password_policy' => 'nullable|string|in:weak,medium,strong',
        ]);

        // Gestion du logo
        if ($request->hasFile('logo')) {
            // Supprimer l'ancien logo si existe
            if ($company->logo && Storage::disk('public')->exists($company->logo)) {
                Storage::disk('public')->delete($company->logo);
            }
            
            $logoPath = $request->file('logo')->store('company-logos', 'public');
            $validated['logo'] = $logoPath;
        } else {
            unset($validated['logo']);
        }

        // Mettre à jour l'entreprise
        $company->update($validated);

        return redirect()->route('super-admin.companies.show', $company)
            ->with('success', 'Paramètres de l\'entreprise mis à jour avec succès.');
    }

    public function resetSettings(Company $company)
    {
        // Réinitialiser aux valeurs par défaut
        $company->update([
            'primary_color' => '#4e73df',
            'secondary_color' => '#858796',
            'alert_threshold' => 100.00,
            'low_stock_alert' => 50.00,
            'maintenance_alert_days' => 7,
            'business_rules' => null,
        ]);

        return redirect()->route('super-admin.companies.settings.edit', $company)
            ->with('info', 'Paramètres réinitialisés aux valeurs par défaut.');
    }

    public function uploadLogo(Request $request, Company $company)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('logo')) {
            // Supprimer l'ancien logo
            if ($company->logo && Storage::disk('public')->exists($company->logo)) {
                Storage::disk('public')->delete($company->logo);
            }
            
            $logoPath = $request->file('logo')->store('company-logos', 'public');
            $company->update(['logo' => $logoPath]);

            return response()->json([
                'success' => true,
                'logo_url' => Storage::disk('public')->url($logoPath)
            ]);
        }

        return response()->json(['success' => false], 400);
    }

    public function removeLogo(Company $company)
    {
        if ($company->logo && Storage::disk('public')->exists($company->logo)) {
            Storage::disk('public')->delete($company->logo);
        }
        
        $company->update(['logo' => null]);

        return redirect()->route('super-admin.companies.settings.edit', $company)
            ->with('info', 'Logo supprimé avec succès.');
    }
}