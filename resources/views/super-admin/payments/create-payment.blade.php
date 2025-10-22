@extends('layouts.super-admin')

@section('title', 'Nouveau Paiement')
@section('page-title', 'Nouveau Paiement - ' . $company->name)

@section('page-actions')
    <a href="{{ route('super-admin.subscriptions.show', $company) }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>Retour
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-credit-card me-2"></i>Nouveau Paiement
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('super-admin.subscriptions.store-payment', $company) }}" method="POST">
                    @csrf
                    
                    <!-- Informations entreprise -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-2">Entreprise</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Nom:</strong> {{ $company->name }}
                            </div>
                            <div class="col-md-6">
                                <strong>Plan actuel:</strong> 
                                <span class="badge bg-{{ $company->subscription_plan === 'enterprise' ? 'primary' : ($company->subscription_plan === 'premium' ? 'info' : 'secondary') }}">
                                    {{ ucfirst($company->subscription_plan) }}
                                </span>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <strong>Expiration:</strong> {{ $company->subscription_expires_at->format('d/m/Y') }}
                            </div>
                            <div class="col-md-6">
                                <strong>Statut:</strong> 
                                <span class="badge bg-{{ $company->is_active ? 'success' : 'danger' }}">
                                    {{ $company->is_active ? 'Actif' : 'Suspendu' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Montant -->
                        <div class="col-md-6 mb-3">
                            <label for="amount" class="form-label">Montant (FCFA) *</label>
                            <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                   id="amount" name="amount" value="{{ old('amount') }}" 
                                   min="0" step="1000" required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Montant effectivement payé par l'entreprise.
                            </div>
                        </div>
                        
                        <!-- Méthode de paiement -->
                        <div class="col-md-6 mb-3">
                            <label for="payment_method" class="form-label">Méthode de paiement *</label>
                            <select class="form-select @error('payment_method') is-invalid @enderror" 
                                    id="payment_method" name="payment_method" required>
                                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Virement bancaire</option>
                                <option value="mobile_money" {{ old('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                                <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Carte bancaire</option>
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Espèces</option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Statut du paiement -->
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Statut du paiement *</label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Complété</option>
                                <option value="failed" {{ old('status') == 'failed' ? 'selected' : '' }}>Échoué</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Date de début -->
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Date de début *</label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                   id="start_date" name="start_date" 
                                   value="{{ old('start_date', date('Y-m-d')) }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Début de la période couverte par le paiement.
                            </div>
                        </div>
                        
                        <!-- Date de fin -->
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">Date de fin *</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                   id="end_date" name="end_date" 
                                   value="{{ old('end_date', date('Y-m-d', strtotime('+1 year'))) }}" required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Fin de la période couverte par le paiement.
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="mb-4">
                        <label for="notes" class="form-label">Notes (optionnel)</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="4" 
                                  placeholder="Informations supplémentaires sur le paiement (référence, détails, etc.)...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('super-admin.subscriptions.show', $company) }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Annuler
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i>Enregistrer le paiement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar - Informations et recommandations -->
    <div class="col-lg-4">
        <!-- Informations sur le plan -->
        <div class="card shadow mb-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Informations Plan Actuel
                </h6>
            </div>
            <div class="card-body">
                @php
                    $currentPlan = $plans->firstWhere('name', $company->subscription_plan);
                @endphp
                @if($currentPlan)
                    <h6 class="text-primary">{{ $currentPlan->display_name }}</h6>
                    <ul class="list-unstyled small">
                        <li><i class="fas fa-money-bill-wave me-2 text-muted"></i> 
                            Mensuel: <strong>{{ number_format($currentPlan->monthly_price, 0, ',', ' ') }} FCFA</strong>
                        </li>
                        <li><i class="fas fa-calendar-alt me-2 text-muted"></i> 
                            Annuel: <strong>{{ number_format($currentPlan->annual_price, 0, ',', ' ') }} FCFA</strong>
                        </li>
                        <li><i class="fas fa-gas-pump me-2 text-muted"></i> 
                            Stations: <strong>{{ $currentPlan->max_stations }}</strong> max
                        </li>
                        <li><i class="fas fa-users me-2 text-muted"></i> 
                            Utilisateurs: <strong>{{ $currentPlan->max_users }}</strong> max
                        </li>
                        @if($currentPlan->has_advanced_reports)
                        <li><i class="fas fa-chart-bar me-2 text-success"></i> Rapports avancés</li>
                        @endif
                        @if($currentPlan->has_premium_support)
                        <li><i class="fas fa-headset me-2 text-success"></i> Support premium</li>
                        @endif
                    </ul>
                @else
                    <p class="text-muted">Aucune information sur le plan actuel.</p>
                @endif
            </div>
        </div>

        <!-- Recommandations -->
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0">
                    <i class="fas fa-lightbulb me-2"></i>Recommandations
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info small">
                    <h6><i class="fas fa-check-circle me-2"></i>Bonnes pratiques</h6>
                    <ul class="mb-0 ps-3">
                        <li>Vérifiez le montant avec le client</li>
                        <li>Notez la référence de transaction</li>
                        <li>Mettez à jour la date d'expiration</li>
                        <li>Activez l'entreprise si suspendue</li>
                    </ul>
                </div>

                <!-- Calcul automatique -->
                <div class="mt-3">
                    <h6>Calcul de référence</h6>
                    <div class="small">
                        <div class="d-flex justify-content-between">
                            <span>Basic (1 an):</span>
                            <strong>540 000 FCFA</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Premium (1 an):</span>
                            <strong>1 080 000 FCFA</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Enterprise (1 an):</span>
                            <strong>2 160 000 FCFA</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dernier paiement -->
@php
    $lastPayment = $company->payments()->latest()->first();
@endphp

@if($lastPayment)
<div class="card shadow mt-4">
    <div class="card-header bg-secondary text-white">
        <h6 class="mb-0">
            <i class="fas fa-history me-2"></i>Dernier Paiement
        </h6>
    </div>
    <div class="card-body">
        <div class="small">
            <div class="d-flex justify-content-between">
                <span>Montant:</span>
                <strong>{{ number_format($lastPayment->amount, 0, ',', ' ') }} FCFA</strong>
            </div>
            <div class="d-flex justify-content-between">
                <span>Méthode:</span>
                <span>{{ $lastPayment->payment_method }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Statut:</span>
                <span class="badge bg-{{ $lastPayment->getStatusColorAttribute() }}">
                    {{ $lastPayment->status }}
                </span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Date:</span>
                <span>{{ $lastPayment->created_at->format('d/m/Y') }}</span>
            </div>
        </div>
    </div>
</div>
@endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// Calcul automatique basé sur le plan et la période
function calculateAmount() {
    const startDate = new Date(document.getElementById('start_date').value);
    const endDate = new Date(document.getElementById('end_date').value);
    const amountInput = document.getElementById('amount');
    
    // Calculer la durée en mois
    const months = (endDate.getFullYear() - startDate.getFullYear()) * 12 + (endDate.getMonth() - startDate.getMonth());
    
    // Prix mensuel selon le plan
    const monthlyPrices = {
        'basic': 50000,
        'premium': 100000,
        'enterprise': 200000
    };
    
    const currentPlan = '{{ $company->subscription_plan }}';
    
    if (monthlyPrices[currentPlan] && months > 0) {
        const calculatedAmount = monthlyPrices[currentPlan] * months;
        amountInput.value = calculatedAmount;
    }
}

// Écouter les changements de dates
document.getElementById('start_date').addEventListener('change', calculateAmount);
document.getElementById('end_date').addEventListener('change', calculateAmount);

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    calculateAmount();
});
</script>
@endpush