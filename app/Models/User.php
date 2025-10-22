<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role_id',
        'company_id',
        'is_active',
        'last_login_at',
        'email_verified_at',
        'notes',
        'settings'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    // Relations
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function loginHistory()
    {
        return $this->hasMany(LoginHistory::class);
    }

    public function stations()
    {
        return $this->hasMany(Station::class, 'manager_id');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'employee_id');
    }

    public function maintenanceInterventions()
    {
        return $this->hasMany(MaintenanceIntervention::class, 'technician_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeByRole($query, $roleId)
    {
        return $query->where('role_id', $roleId);
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeSuperAdmins($query)
    {
        return $query->whereHas('role', function($q) {
            $q->where('slug', 'super-admin');
        });
    }

    public function scopeCompanyAdmins($query)
    {
        return $query->whereHas('role', function($q) {
            $q->where('slug', 'company-admin');
        });
    }

    public function scopeStationManagers($query)
    {
        return $query->whereHas('role', function($q) {
            $q->where('slug', 'station-manager');
        });
    }

    public function scopeEmployees($query)
    {
        return $query->whereHas('role', function($q) {
            $q->where('slug', 'employee');
        });
    }

    public function scopeTechnicians($query)
    {
        return $query->whereHas('role', function($q) {
            $q->where('slug', 'technician');
        });
    }

    public function scopeRecentlyActive($query, $days = 7)
    {
        return $query->where('last_login_at', '>=', Carbon::now()->subDays($days));
    }

    public function scopeNeverLoggedIn($query)
    {
        return $query->whereNull('last_login_at');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    /**
     * Accesseur pour obtenir le rôle (gère les slugs et les IDs)
     */
    public function getRoleAttribute()
    {
        // Si role_id est un ID numérique, charger la relation
        if (is_numeric($this->role_id)) {
            return $this->getRelationValue('role');
        }

        // Si role_id est un slug, trouver le rôle correspondant
        if (is_string($this->role_id)) {
            return Role::where('slug', $this->role_id)->first();
        }

        return null;
    }

    // Accessors
    public function getInitialsAttribute()
    {
        return strtoupper(substr($this->name, 0, 1));
    }

    public function getStatusColorAttribute()
    {
        return $this->is_active ? 'success' : 'danger';
    }

    public function getStatusTextAttribute()
    {
        return $this->is_active ? 'Actif' : 'Inactif';
    }

    public function getLastLoginFormattedAttribute()
    {
        return $this->last_login_at
            ? $this->last_login_at->format('d/m/Y H:i')
            : 'Jamais connecté';
    }

    public function getLastLoginRelativeAttribute()
    {
        return $this->last_login_at
            ? $this->last_login_at->diffForHumans()
            : null;
    }

    public function getRoleNameAttribute()
    {
        return $this->role->name ?? 'N/A';
    }

    public function getCompanyNameAttribute()
    {
        return $this->company->name ?? 'Système';
    }

    public function getIsSuperAdminAttribute()
    {
        return $this->role && $this->role->slug === 'super-admin';
    }

    public function getIsCompanyAdminAttribute()
    {
        return $this->role && $this->role->slug === 'company-admin';
    }

    public function getIsStationManagerAttribute()
    {
        return $this->role && $this->role->slug === 'station-manager';
    }

    public function getIsEmployeeAttribute()
    {
        return $this->role && $this->role->slug === 'employee';
    }

    public function getIsTechnicianAttribute()
    {
        return $this->role && $this->role->slug === 'technician';
    }

    // Méthodes de vérification de permissions
    public function hasPermission($permission)
    {
        if (!$this->role) {
            return false;
        }

        // Super admin a tous les droits
        if ($this->is_super_admin) {
            return true;
        }

        return $this->role->hasPermission($permission);
    }

    public function canManageUsers()
    {
        return $this->hasPermission('users.manage');
    }

    public function canManageCompanies()
    {
        return $this->hasPermission('companies.manage');
    }

    public function canManageStations()
    {
        return $this->hasPermission('stations.manage');
    }

    public function canManageFuel()
    {
        return $this->hasPermission('fuel.manage');
    }

    public function canViewReports()
    {
        return $this->hasPermission('reports.view');
    }

    // Méthodes utilitaires
    public function recordLogin($ipAddress, $userAgent, $success = true, $failureReason = null)
    {
        // Enregistrer l'historique
        LoginHistory::logLoginAttempt($this->id, $ipAddress, $userAgent, $success, $failureReason);

        // Mettre à jour la dernière connexion si réussie
        if ($success) {
            $this->update([
                'last_login_at' => now(),
            ]);
        }
    }

    public function getFailedLoginAttempts($hours = 1)
    {
        return LoginHistory::getFailedAttemptsCount($this->id, $hours);
    }

    public function getLastSuccessfulLogin()
    {
        return LoginHistory::getLastSuccessfulLogin($this->id);
    }

    public function activate()
    {
        $this->update(['is_active' => true]);
        return $this;
    }

    public function deactivate()
    {
        $this->update(['is_active' => false]);
        return $this;
    }

    public function changeRole($roleId)
    {
        $this->update(['role_id' => $roleId]);
        return $this;
    }

    public function assignToCompany($companyId)
    {
        $this->update(['company_id' => $companyId]);
        return $this;
    }

    // Validation des règles métier
    public function canBeDeleted()
    {
        // Empêcher la suppression de son propre compte
        if ($this->id === auth()->id()) {
            return false;
        }

        // Vérifier s'il y a des dépendances
        if ($this->stations()->exists() ||
            $this->sales()->exists() ||
            $this->maintenanceInterventions()->exists()) {
            return false;
        }

        return true;
    }

    public function getDeletionErrors()
    {
        $errors = [];

        if ($this->id === auth()->id()) {
            $errors[] = 'Vous ne pouvez pas supprimer votre propre compte.';
        }

        if ($this->stations()->exists()) {
            $errors[] = 'Cet utilisateur est gérant de station(s).';
        }

        if ($this->sales()->exists()) {
            $errors[] = 'Cet utilisateur a des ventes associées.';
        }

        if ($this->maintenanceInterventions()->exists()) {
            $errors[] = 'Cet utilisateur a des interventions de maintenance.';
        }

        return $errors;
    }

    // Méthodes de notification
    public function sendPasswordResetNotification($token)
    {
        // Implémentation personnalisée si nécessaire
    }

    protected static function boot()
    {
        parent::boot();

        // Assigner un rôle par défaut si non spécifié
        static::creating(function ($user) {
            if (empty($user->role_id)) {
                $defaultRole = Role::where('slug', 'employee')->first();
                if ($defaultRole) {
                    $user->role_id = $defaultRole->id;
                }
            }
        });

        // Empêcher la désactivation de son propre compte
        static::updating(function ($user) {
            if ($user->isDirty('is_active') && $user->id === auth()->id()) {
                throw new \Exception('Vous ne pouvez pas désactiver votre propre compte.');
            }
        });
    }

    /**
     * Vérifie si l'utilisateur est Super Admin (version corrigée)
     */
    public function isSuperAdmin(): bool
    {
        $role = $this->role;
        if (!$role) {
            // Fallback: vérifier directement le role_id si c'est un string
            if (is_string($this->role_id)) {
                return in_array($this->role_id, ['super-admin', 'super_admin']);
            }
            return false;
        }
        return in_array($role->slug, ['super-admin', 'super_admin']);
    }

    /**
     * Vérifie si l'utilisateur est Admin d'entreprise (version corrigée)
     */
    public function isCompanyAdmin(): bool
    {
        $role = $this->role;
        if (!$role) {
            if (is_string($this->role_id)) {
                return in_array($this->role_id, ['company-admin', 'company_admin']);
            }
            return false;
        }
        return in_array($role->slug, ['company-admin', 'company_admin']);
    }

    /**
     * Vérifie si l'utilisateur est Gérant de station (version corrigée)
     */
    public function isStationManager(): bool
    {
        $role = $this->role;
        if (!$role) {
            if (is_string($this->role_id)) {
                return in_array($this->role_id, ['station-manager', 'station_manager']);
            }
            return false;
        }
        return in_array($role->slug, ['station-manager', 'station_manager']);
    }

    /**
     * Vérifie si l'utilisateur est Employé (version corrigée)
     */
    public function isEmployee(): bool
    {
        $role = $this->role;
        if (!$role) {
            if (is_string($this->role_id)) {
                return in_array($this->role_id, ['employee']); // Pas de variation
            }
            return false;
        }
        return $role->slug === 'employee';
    }

    /**
     * Vérifie si l'utilisateur est Technicien (version corrigée)
     */
    public function isTechnician(): bool
    {
        $role = $this->role;
        if (!$role) {
            if (is_string($this->role_id)) {
                return in_array($this->role_id, ['technician']); // Pas de variation
            }
            return false;
        }
        return $role->slug === 'technician';
    }

    /**
     * Vérifie si l'utilisateur a un rôle spécifique (version corrigée)
     */
    public function hasRole($roleSlug): bool
    {
        $role = $this->role;
        if (!$role) {
            if (is_string($this->role_id)) {
                // Normaliser le slug demandé et le slug stocké
                $normalizedRequested = str_replace('-', '_', $roleSlug);
                $normalizedStored = str_replace('-', '_', $this->role_id);
                return $normalizedRequested === $normalizedStored;
            }
            return false;
        }

        // Normaliser les deux slugs pour la comparaison
        $normalizedRequested = str_replace('-', '_', $roleSlug);
        $normalizedStored = str_replace('-', '_', $role->slug);
        return $normalizedRequested === $normalizedStored;
    }

    /**
     * Accesseur pour obtenir le slug normalisé du rôle
     */
    public function getNormalizedRoleSlugAttribute()
    {
        if (is_string($this->role_id)) {
            return str_replace('_', '-', $this->role_id);
        }

        if ($this->role) {
            return $this->role->slug;
        }

        return null;
    }

    /**
     * Vérifie si l'utilisateur a un des rôles spécifiés
     */
    public function hasAnyRole(array $roleSlugs): bool
    {
        return $this->role && in_array($this->role->slug, $roleSlugs);
    }

    /**
     * Vérifie les permissions (méthode alternative)
     */
    public function hasPermissionTo($permission): bool
    {
        return $this->hasPermission($permission);
    }

    /**
     * Raccourci pour vérifier plusieurs permissions
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Raccourci pour vérifier toutes les permissions
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

     /**
     * 🔥 MÉTHODES DE COMPATIBILITÉ - Pour les vues existantes
     */

    // Pour les vues qui utilisent isAdmin()
    public function isAdmin(): bool
    {
        return $this->isCompanyAdmin();
    }

    // Pour les vues qui utilisent isManager()
    public function isManager(): bool
    {
        return $this->isStationManager();
    }
}
