<?php
// database/seeders/RoleSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = Role::getDefaultRoles();

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }

        $this->command->info('Rôles par défaut créés avec succès!');

        // Afficher les rôles créés
        $createdRoles = Role::all();
        $this->command->info('Rôles disponibles:');
        foreach ($createdRoles as $role) {
            $this->command->info("- {$role->name} ({$role->slug}) - Niveau: {$role->level}");
        }
    }
}
