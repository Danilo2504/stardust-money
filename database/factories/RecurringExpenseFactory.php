<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\RecurringExpense;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RecurringExpense>
 */
class RecurringExpenseFactory extends Factory
{
    protected $model = RecurringExpense::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'description' => fake()->sentence(3),
            'amount' => fake()->randomFloat(2, 1, 500),
            'category_id' => Category::factory(),
            'custom_interval_value' => fake()->numberBetween(1, 3),
            'custom_interval_unit' => fake()->randomElement(['weeks', 'months']),
            'next_due_date' => fake()->dateTimeBetween('now', '+30 days'),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
