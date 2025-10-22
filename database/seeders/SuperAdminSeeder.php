<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Désactiver les contraintes de clé étrangère temporairement
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Vider la table users (optionnel - pour les tests)
        // User::truncate();

        // Créer le Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@smartpump.ml',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'company_id' => null,
            'station_id' => null,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Réactiver les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('✅ Super Admin créé avec succès!');
        $this->command->info('📧 Email: superadmin@smartpump.ml');
        $this->command->info('🔑 Mot de passe: password123');
        $this->command->info('⚠️ CHANGEZ LE MOT DE PASSE EN PRODUCTION!');
    }
}