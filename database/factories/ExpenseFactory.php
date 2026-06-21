<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expense>
 */
class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'code' => str_pad((string) fake()->unique()->numberBetween(0, 99999999), 8, '0', STR_PAD_LEFT),
            'description' => fake()->sentence(3),
            'amount' => fake()->randomFloat(2, 1, 500),
            'category_id' => Category::factory(),
            'notes' => null,
            'type' => 'one_time',
            'expense_date' => fake()->dateTimeBetween('-30 days', 'now'),
            'draft' => false,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['draft' => true]);
    }
}
