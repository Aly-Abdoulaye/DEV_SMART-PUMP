<?php
// database/factories/RoleFactory.php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = \App\Models\Role::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'slug' => $this->faker->slug(),
            'description' => $this->faker->sentence(),
            'color' => $this->faker->randomElement(['primary', 'success', 'warning', 'danger', 'info']),
            'level' => $this->faker->numberBetween(1, 100),
            'permissions' => ['users.view', 'reports.view'],
            'is_system' => false,
            'is_active' => true,
        ];
    }

    public function system()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_system' => true,
            ];
        });
    }

    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }
}
