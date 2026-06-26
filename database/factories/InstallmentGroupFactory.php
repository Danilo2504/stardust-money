<?php

namespace Database\Factories;

use App\Models\InstallmentGroup;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InstallmentGroup>
 */
class InstallmentGroupFactory extends Factory
{
    protected $model = InstallmentGroup::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'description' => fake()->sentence(3),
            'total_amount' => fake()->randomFloat(2, 100, 2000),
            'total_installments' => fake()->numberBetween(3, 24),
        ];
    }
}
