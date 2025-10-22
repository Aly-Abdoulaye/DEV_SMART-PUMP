@extends('layouts.super-admin')

@section('title', 'Nouvel Abonnement')
@section('page-title', 'Nouvel Abonnement')

@section('page-actions')
    <a href="{{ route('super-admin.subscriptions.index') }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>Retour
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-plus me-2"></i>Nouvel Abonnement et Paiement
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('super-admin.subscriptions.store') }}" method="POST">
                    @csrf
                    
                    <!-- Sélection de l'entreprise -->
                    <div class="mb-4">
                        <label for="company_id" class="form-label fw-bold">Entreprise *</label>
                        <select class="form-select @error('company_id') is-invalid @enderror" 
                                id="company_id" name="company_id" required>
                            <option value="">Sélectionnez une entreprise</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }} - {{ $company->email }}
                                    @if($company->subscription_expires_at)
                                        (Expire le {{ $company->subscription_expires_at->format('d/m/Y') }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('company_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <!-- Plan d'abonnement -->
                        <div class="col-md-6 mb-3">
                            <label for="subscription_plan" class="form-label">Plan d'abonnement *</label>
                            <select class="form-select @error('subscription_plan') is-invalid @enderror" 
                                    id="subscription_plan" name="subscription_plan" required>
                                <option value="">Sélectionnez un plan</option>
                                <option value="basic" {{ old('subscription_plan') == 'basic' ? 'selected' : '' }}>Basic</option>
                                <option value="premium" {{ old('subscription_plan') == 'premium' ? 'selected' : '' }}>Premium</option>
                                <option value="enterprise" {{ old('subscription_plan') == 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                            </select>
                            @error('subscription_plan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Période de facturation -->
                        <div class="col-md-6 mb-3">
                            <label for="billing_period" class="form-label">Période de facturation *</label>
                            <select class="form-select @error('billing_period') is-invalid @enderror" 
                                    id="billing_period" name="billing_period" required>
                                <option value="monthly" {{ old('billing_period') == 'monthly' ? 'selected' : '' }}>Mensuel</option>
                                <option value="annual" {{ old('billing_period') == 'annual' ? 'selected' : '' }}>Annuel (10% de réduction)</option>
                            </select>
                            @error('billing_period')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Montant -->
                        <div class="col-md-6 mb-3">
                            <label for="amount" class="form-label">Montant payé (FCFA) *</label>
                            <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                   id="amount" name="amount" value="{{ old('amount') }}" 
                                   min="0" step="1000" required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                        <!-- Date de début -->
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Date de début *</label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                   id="start_date" name="start_date" 
                                   value="{{ old('start_date', date('Y-m-d')) }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (optionnel)</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="3" placeholder="Informations supplémentaires sur le paiement...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('super-admin.subscriptions.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Créer l'abonnement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar - Informations sur les plans -->
    <div class="col-lg-4">
        <div class="card shadow">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Tarifs des Plans
                </h6>
            </div>
            <div class="card-body">
                @foreach($plans as $plan)
                <div class="card mb-3 border-{{ $plan->name === 'enterprise' ? 'primary' : ($plan->name === 'premium' ? 'info' : 'secondary') }}">
                    <div class="card-body">
                        <h6 class="card-title">{{ $plan->display_name }}</h6>
                        <div class="mb-2">
                            <span class="h5 text-primary">{{ number_format($plan->monthly_price, 0, ',', ' ') }} FCFA</span>
                            <small class="text-muted">/mois</small>
                        </div>
                        <div class="mb-2">
                            <span class="h6 text-success">{{ number_format($plan->annual_price, 0, ',', ' ') }} FCFA</span>
                            <small class="text-muted">/an</small>
                            <br>
                            <small class="text-success">
                                <i class="fas fa-percentage me-1"></i>Économisez {{ $plan->yearly_savings_percentage }}%
                            </small>
                        </div>
                        <ul class="small list-unstyled">
                            <li><i class="fas fa-gas-pump me-1 text-muted"></i> {{ $plan->max_stations }} station(s) max</li>
                            <li><i class="fas fa-users me-1 text-muted"></i> {{ $plan->max_users }} utilisateur(s) max</li>
                            @if($plan->has_advanced_reports)
                            <li><i class="fas fa-chart-bar me-1 text-success"></i> Rapports avancés</li>
                            @endif
                            @if($plan->has_premium_support)
                            <li><i class="fas fa-headset me-1 text-success"></i> Support premium</li>
                            @endif
                        </ul>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Ajustement automatique du montant selon le plan et la période
document.getElementById('subscription_plan').addEventListener('change', updateAmount);
document.getElementById('billing_period').addEventListener('change', updateAmount);

function updateAmount() {
    const plan = document.getElementById('subscription_plan').value;
    const period = document.getElementById('billing_period').value;
    const amountInput = document.getElementById('amount');
    
    const prices = {
        'basic': { monthly: 50000, annual: 540000 },
        'premium': { monthly: 100000, annual: 1080000 },
        'enterprise': { monthly: 200000, annual: 2160000 }
    };
    
    if (prices[plan] && prices[plan][period]) {
        amountInput.value = prices[plan][period];
    }
}

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', updateAmount);
</script>
@endpush