<?php
// database/factories/LoginHistoryFactory.php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LoginHistoryFactory extends Factory
{
    protected $model = \App\Models\LoginHistory::class;

    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'login_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'success' => $this->faker->boolean(90), // 90% de succès
            'failure_reason' => function (array $attributes) {
                return $attributes['success'] ? null : $this->faker->randomElement([
                    'Mot de passe incorrect',
                    'Compte désactivé',
                    'Tentative de brute force'
                ]);
            },
        ];
    }

    public function successful()
    {
        return $this->state(function (array $attributes) {
            return [
                'success' => true,
                'failure_reason' => null,
            ];
        });
    }

    public function failed()
    {
        return $this->state(function (array $attributes) {
            return [
                'success' => false,
                'failure_reason' => $this->faker->randomElement([
                    'Mot de passe incorrect',
                    'Compte désactivé',
                    'Tentative de brute force'
                ]),
            ];
        });
    }

    public function recent()
    {
        return $this->state(function (array $attributes) {
            return [
                'login_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
            ];
        });
    }
}
