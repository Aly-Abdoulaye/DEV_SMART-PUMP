<?php
// app/Models/Role.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'level',
        'permissions',
        'is_system',
        'is_active'
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relations
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    public function scopeCustom($query)
    {
        return $query->where('is_system', false);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('level')->orderBy('name');
    }

    // Accessors
    public function getPermissionsListAttribute()
    {
        return $this->permissions ?? [];
    }

    public function getDisplayNameAttribute()
    {
        return ucfirst($this->name);
    }

    // Méthodes utilitaires
    public function hasPermission($permission)
    {
        return in_array($permission, $this->permissions_list);
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

    // Rôles par défaut
    public static function getDefaultRoles()
    {
        return [
            [
                'name' => 'Super Administrateur',
                'slug' => 'super-admin',
                'description' => 'Accès complet à toutes les fonctionnalités du système',
                'color' => 'danger',
                'level' => 100,
                'permissions' => ['*'],
                'is_system' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Administrateur d\'Entreprise',
                'slug' => 'company-admin',
                'description' => 'Gestion complète d\'une entreprise et ses stations',
                'color' => 'primary',
                'level' => 90,
                'permissions' => [
                    'companies.manage',
                    'stations.manage',
                    'users.manage',
                    'reports.view',
                    'settings.manage'
                ],
                'is_system' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Gérant de Station',
                'slug' => 'station-manager',
                'description' => 'Gestion quotidienne d\'une station-service',
                'color' => 'info',
                'level' => 80,
                'permissions' => [
                    'stations.manage',
                    'fuel.manage',
                    'sales.manage',
                    'inventory.view',
                    'reports.view'
                ],
                'is_system' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Employé',
                'slug' => 'employee',
                'description' => 'Accès limité aux fonctions opérationnelles',
                'color' => 'success',
                'level' => 50,
                'permissions' => [
                    'sales.create',
                    'inventory.view',
                    'reports.personal'
                ],
                'is_system' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Technicien',
                'slug' => 'technician',
                'description' => 'Gestion de la maintenance des équipements',
                'color' => 'warning',
                'level' => 60,
                'permissions' => [
                    'maintenance.manage',
                    'equipment.view',
                    'reports.maintenance'
                ],
                'is_system' => true,
                'is_active' => true,
            ],
        ];
    }
}
