<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run()
    {
        $plans = [
            [
                'name' => 'basic',
                'display_name' => 'Basic',
                'description' => 'Parfait pour les petites stations avec des besoins essentiels',
                'monthly_price' => 50000,
                'annual_price' => 540000, // 10% de réduction
                'setup_fee' => 0,
                'max_stations' => 1,
                'max_users' => 3,
                'max_customers' => 50,
                'has_advanced_reports' => false,
                'has_api_access' => false,
                'has_premium_support' => false,
                'sort_order' => 1,
            ],
            [
                'name' => 'premium',
                'display_name' => 'Premium',
                'description' => 'Idéal pour les stations multiples avec rapports avancés',
                'monthly_price' => 100000,
                'annual_price' => 1080000, // 10% de réduction
                'setup_fee' => 50000,
                'max_stations' => 5,
                'max_users' => 10,
                'max_customers' => 500,
                'has_advanced_reports' => true,
                'has_api_access' => false,
                'has_premium_support' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'enterprise',
                'display_name' => 'Enterprise',
                'description' => 'Solution complète pour les grands groupes avec API et support prioritaire',
                'monthly_price' => 200000,
                'annual_price' => 2160000, // 10% de réduction
                'setup_fee' => 100000,
                'max_stations' => 999,
                'max_users' => 999,
                'max_customers' => 9999,
                'has_advanced_reports' => true,
                'has_api_access' => true,
                'has_premium_support' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['name' => $plan['name']],
                $plan
            );
        }

        $this->command->info('Plans d\'abonnement créés avec succès!');
    }
}