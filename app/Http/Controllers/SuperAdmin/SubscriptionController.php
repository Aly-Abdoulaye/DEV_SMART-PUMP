<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Payment;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Company::with(['stations', 'users'])
                               ->withCount(['stations', 'users'])
                               ->orderBy('subscription_expires_at')
                               ->get();

        $stats = [
            'total_companies' => Company::count(),
            'active_subscriptions' => Company::where('subscription_expires_at', '>=', now())->count(),
            'expired_subscriptions' => Company::where('subscription_expires_at', '<', now())->count(),
            'pending_payments' => Payment::pending()->count(),
            'monthly_revenue' => $this->calculateMonthlyRevenue(),
        ];

        return view('super-admin.subscriptions.index', compact('subscriptions', 'stats'));
    }

    public function create()
    {
        $companies = Company::where('is_active', true)->get();
        $plans = SubscriptionPlan::active()->ordered()->get();

        return view('super-admin.subscriptions.create', compact('companies', 'plans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'subscription_plan' => 'required|in:basic,premium,enterprise',
            'billing_period' => 'required|in:monthly,annual',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:bank_transfer,mobile_money,card,cash',
            'start_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $company = Company::findOrFail($validated['company_id']);
        $plan = SubscriptionPlan::where('name', $validated['subscription_plan'])->first();

        // Calculer la date de fin
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = $validated['billing_period'] === 'annual'
            ? $startDate->copy()->addYear()
            : $startDate->copy()->addMonth();

        // Créer le paiement
        $payment = Payment::create([
            'payment_number' => Payment::generatePaymentNumber(),
            'amount' => $validated['amount'],
            'currency' => 'XOF',
            'status' => 'completed',
            'payment_method' => $validated['payment_method'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'company_id' => $company->id,
            'subscription_plan_id' => $plan->id,
            'invoice_number' => Payment::generateInvoiceNumber(),
            'invoice_date' => now(),
            'due_date' => $startDate,
            'notes' => $validated['notes'],
            'paid_at' => now(),
        ]);

        // Mettre à jour l'entreprise
        $company->update([
            'subscription_plan' => $validated['subscription_plan'],
            'subscription_expires_at' => $endDate,
            'is_active' => true,
        ]);

        return redirect()->route('super-admin.subscriptions.show', $company)
            ->with('success', 'Abonnement créé et paiement enregistré avec succès.');
    }

public function show(Company $company)
{
    // CORRECTION : loadCount doit être appelé séparément de load
    $company->load(['payments' => function($query) {
        $query->latest();
    }, 'stations', 'users']);

    // Charger les counts séparément
    $company->loadCount(['stations', 'users', 'payments']);

    $currentPayment = $company->payments()->latest()->first();
    $upcomingRenewals = $this->getUpcomingRenewals();
    $plans = SubscriptionPlan::active()->ordered()->get();

    // Calculer les jours restants en toute sécurité
    $daysLeft = 0;
    $isExpired = true;

    if ($company->subscription_expires_at) {
        $daysLeft = now()->diffInDays($company->subscription_expires_at, false);
        $isExpired = $daysLeft < 0;
    }

    return view('super-admin.subscriptions.show', compact(
        'company',
        'currentPayment',
        'upcomingRenewals',
        'plans',
        'daysLeft',
        'isExpired'
    ));
}    public function edit(Company $company)
    {
        $plans = SubscriptionPlan::active()->ordered()->get();
        return view('super-admin.subscriptions.edit', compact('company', 'plans'));
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'subscription_plan' => 'required|in:basic,premium,enterprise',
            'subscription_expires_at' => 'required|date',
            'is_active' => 'boolean',
        ]);

        $company->update($validated);

        return redirect()->route('super-admin.subscriptions.show', $company)
            ->with('success', 'Abonnement mis à jour avec succès.');
    }

    public function renew(Company $company)
    {
        // Renouveler l'abonnement pour 1 an
        $newExpiryDate = $company->subscription_expires_at->greaterThan(now())
            ? $company->subscription_expires_at->addYear()
            : now()->addYear();

        $company->update([
            'subscription_expires_at' => $newExpiryDate,
        ]);

        // Créer un nouveau paiement pour le renouvellement
        $plan = SubscriptionPlan::where('name', $company->subscription_plan)->first();
        $amount = $plan ? $plan->annual_price : 0;

        Payment::create([
            'payment_number' => Payment::generatePaymentNumber(),
            'amount' => $amount,
            'currency' => 'XOF',
            'status' => 'pending',
            'payment_method' => 'bank_transfer',
            'start_date' => $company->subscription_expires_at,
            'end_date' => $newExpiryDate,
            'company_id' => $company->id,
            'subscription_plan_id' => $plan->id ?? null,
            'invoice_number' => Payment::generateInvoiceNumber(),
            'invoice_date' => now(),
            'due_date' => $company->subscription_expires_at,
            'notes' => 'Renouvellement automatique d\'abonnement',
        ]);

        return redirect()->route('super-admin.subscriptions.show', $company)
            ->with('success', 'Abonnement renouvelé avec succès pour 1 an. Paiement en attente.');
    }

    public function suspend(Company $company)
    {
        $company->update(['is_active' => false]);

        return redirect()->route('super-admin.subscriptions.show', $company)
            ->with('warning', 'Entreprise suspendue. L\'accès à la plateforme est bloqué.');
    }

    public function activate(Company $company)
    {
        $company->update(['is_active' => true]);

        return redirect()->route('super-admin.subscriptions.show', $company)
            ->with('success', 'Entreprise activée avec succès.');
    }

    // Gestion des paiements
    public function createPayment(Company $company)
    {
        $plans = SubscriptionPlan::active()->ordered()->get();
        return view('super-admin.subscriptions.create-payment', compact('company', 'plans'));
    }

    public function storePayment(Request $request, Company $company)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:bank_transfer,mobile_money,card,cash',
            'status' => 'required|in:pending,completed,failed',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'notes' => 'nullable|string',
        ]);

        $payment = Payment::create([
            'payment_number' => Payment::generatePaymentNumber(),
            'amount' => $validated['amount'],
            'currency' => 'XOF',
            'status' => $validated['status'],
            'payment_method' => $validated['payment_method'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'company_id' => $company->id,
            'subscription_plan_id' => SubscriptionPlan::where('name', $company->subscription_plan)->first()->id,
            'invoice_number' => Payment::generateInvoiceNumber(),
            'invoice_date' => now(),
            'due_date' => $validated['start_date'],
            'notes' => $validated['notes'],
            'paid_at' => $validated['status'] === 'completed' ? now() : null,
        ]);

        // Si le paiement est complété, mettre à jour la date d'expiration
        if ($validated['status'] === 'completed') {
            $company->update([
                'subscription_expires_at' => $validated['end_date'],
                'is_active' => true,
            ]);
        }

        return redirect()->route('super-admin.subscriptions.show', $company)
            ->with('success', 'Paiement enregistré avec succès.');
    }

    // Suspension automatique des entreprises en retard de paiement
    public function checkOverdueSubscriptions()
    {
        $overdueCompanies = Company::where('subscription_expires_at', '<', now())
                                  ->where('is_active', true)
                                  ->get();

        $suspendedCount = 0;
        foreach ($overdueCompanies as $company) {
            $company->update(['is_active' => false]);
            $suspendedCount++;
        }

        return $suspendedCount;
    }

    private function calculateMonthlyRevenue()
    {
        $currentMonth = now()->format('Y-m');
        $revenue = Payment::where('status', 'completed')
                         ->where('paid_at', 'like', "{$currentMonth}%")
                         ->sum('amount');

        return $revenue;
    }

    private function getUpcomingRenewals()
    {
        return Company::where('subscription_expires_at', '<=', now()->addDays(30))
                     ->where('subscription_expires_at', '>=', now())
                     ->orderBy('subscription_expires_at')
                     ->get();
    }
}
