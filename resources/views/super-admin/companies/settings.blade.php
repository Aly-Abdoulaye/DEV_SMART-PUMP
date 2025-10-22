@extends('layouts.super-admin')

@section('title', 'Paramètres Entreprise')
@section('page-title', 'Paramètres - ' . $company->name)

@section('page-actions')
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <form action="{{ route('super-admin.companies.settings.reset', $company) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-warning" onclick="return confirm('Réinitialiser tous les paramètres aux valeurs par défaut ?')">
                    <i class="fas fa-undo me-1"></i>Réinitialiser
                </button>
            </form>
            <a href="{{ route('super-admin.companies.show', $company) }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour
            </a>
        </div>
    </div>
@endsection

@section('content')
<form action="{{ route('super-admin.companies.settings.update', $company) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row">
        <!-- Colonne gauche - Apparence -->
        <div class="col-lg-6">
            <!-- Logo et identité visuelle -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-palette me-2"></i>Identité Visuelle
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Logo -->
                    <div class="mb-4 text-center">
                        <label class="form-label fw-bold">Logo de l'entreprise</label>
                        <div class="mb-3">
                            @if($company->logo)
                                <img src="{{ Storage::disk('public')->url($company->logo) }}" 
                                     alt="Logo {{ $company->name }}" 
                                     class="img-thumbnail mb-2" 
                                     style="max-height: 150px;">
                                <br>
                                <form action="{{ route('super-admin.companies.settings.remove-logo', $company) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer le logo ?')">
                                        <i class="fas fa-trash me-1"></i>Supprimer
                                    </button>
                                </form>
                            @else
                                <div class="border rounded p-4 mb-3 bg-light">
                                    <i class="fas fa-building fa-3x text-muted mb-2"></i>
                                    <p class="text-muted mb-0">Aucun logo</p>
                                </div>
                            @endif
                        </div>
                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                        <div class="form-text">Format: JPEG, PNG, GIF. Max: 2MB</div>
                    </div>

                    <!-- Couleurs -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="primary_color" class="form-label">Couleur principale</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" id="primary_color" name="primary_color" value="{{ $company->primary_color ?? '#4e73df' }}">
                                <input type="text" class="form-control" value="{{ $company->primary_color ?? '#4e73df' }}" readonly style="max-width: 100px;">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="secondary_color" class="form-label">Couleur secondaire</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" id="secondary_color" name="secondary_color" value="{{ $company->secondary_color ?? '#858796' }}">
                                <input type="text" class="form-control" value="{{ $company->secondary_color ?? '#858796' }}" readonly style="max-width: 100px;">
                            </div>
                        </div>
                    </div>

                    <!-- Aperçu -->
                    <div class="mt-3 p-3 border rounded bg-light">
                        <h6 class="text-center">Aperçu de l'interface</h6>
                        <div class="d-flex justify-content-center gap-2">
                            <span class="badge" style="background-color: {{ $company->primary_color ?? '#4e73df' }}; color: white;">
                                Bouton Primaire
                            </span>
                            <span class="badge" style="background-color: {{ $company->secondary_color ?? '#858796' }}; color: white;">
                                Bouton Secondaire
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seuils d'alerte -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bell me-2"></i>Seuils d'Alerte
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="alert_threshold" class="form-label">Seuil d'alerte général (FCFA)</label>
                        <input type="number" class="form-control" id="alert_threshold" name="alert_threshold" 
                               value="{{ $company->alert_threshold ?? 100 }}" step="0.01" min="0" required>
                        <div class="form-text">Seuil pour les alertes financières et opérationnelles</div>
                    </div>

                    <div class="mb-3">
                        <label for="low_stock_alert" class="form-label">Alerte stock bas (Litres)</label>
                        <input type="number" class="form-control" id="low_stock_alert" name="low_stock_alert" 
                               value="{{ $company->low_stock_alert ?? 50 }}" step="0.01" min="0" required>
                        <div class="form-text">Seuil pour les alertes de niveau de carburant bas</div>
                    </div>

                    <div class="mb-3">
                        <label for="maintenance_alert_days" class="form-label">Alerte maintenance (Jours)</label>
                        <input type="number" class="form-control" id="maintenance_alert_days" name="maintenance_alert_days" 
                               value="{{ $company->maintenance_alert_days ?? 7 }}" min="1" max="30" required>
                        <div class="form-text">Nombre de jours d'avance pour les alertes de maintenance</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colonne droite - Règles métier -->
        <div class="col-lg-6">
            <!-- Règles métier -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-gavel me-2"></i>Règles Métier
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="business_rules" class="form-label">Règles de gestion spécifiques</label>
                        <textarea class="form-control" id="business_rules" name="business_rules" rows="8" 
                                  placeholder="Définissez les règles métier spécifiques à cette entreprise...">{{ $company->business_rules ?? '' }}</textarea>
                        <div class="form-text">
                            Exemples: 
                            <ul class="small">
                                <li>Seuil maximum de vente par client</li>
                                <li>Périodes de maintenance obligatoire</li>
                                <li>Règles de facturation spécifiques</li>
                                <li>Contraintes opérationnelles</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paramètres de sécurité -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-shield-alt me-2"></i>Sécurité et Conformité
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="session_timeout" class="form-label">Timeout de session (minutes)</label>
                        <input type="number" class="form-control" id="session_timeout" name="session_timeout" 
                               value="{{ $company->session_timeout ?? 120 }}" min="15" max="480">
                        <div class="form-text">Durée d'inactivité avant déconnexion automatique</div>
                    </div>

                    <div class="mb-3">
                        <label for="password_policy" class="form-label">Politique de mot de passe</label>
                        <select class="form-select" id="password_policy" name="password_policy">
                            <option value="weak" {{ ($company->password_policy ?? 'medium') == 'weak' ? 'selected' : '' }}>Faible - 6 caractères minimum</option>
                            <option value="medium" {{ ($company->password_policy ?? 'medium') == 'medium' ? 'selected' : '' }}>Moyen - 8 caractères avec chiffres</option>
                            <option value="strong" {{ ($company->password_policy ?? 'medium') == 'strong' ? 'selected' : '' }}>Forte - 10 caractères avec chiffres et symboles</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Statut de conformité -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-check-circle me-2"></i>Statut de Conformité
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Informations de conformité</h6>
                        <ul class="mb-0 small">
                            <li><strong>Abonnement:</strong> {{ $company->subscription_plan }} - Expire le {{ $company->subscription_expires_at->format('d/m/Y') }}</li>
                            <li><strong>Statut:</strong> 
                                <span class="badge bg-{{ $company->is_active ? 'success' : 'danger' }}">
                                    {{ $company->is_active ? 'Conforme' : 'Non conforme' }}
                                </span>
                            </li>
                            <li><strong>Dernière vérification:</strong> {{ $company->updated_at->format('d/m/Y H:i') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Boutons de soumission -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body text-center">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i>Enregistrer les paramètres
                    </button>
                    <a href="{{ route('super-admin.companies.show', $company) }}" class="btn btn-secondary btn-lg ms-2">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
// Mise à jour en temps réel de l'aperçu des couleurs
document.getElementById('primary_color').addEventListener('input', function() {
    document.querySelector('[style*="background-color"]').style.backgroundColor = this.value;
});

document.getElementById('secondary_color').addEventListener('input', function() {
    document.querySelectorAll('[style*="background-color"]')[1].style.backgroundColor = this.value;
});
</script>
@endpush