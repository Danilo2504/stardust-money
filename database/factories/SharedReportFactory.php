<?php

namespace Database\Factories;

use App\Models\SharedReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SharedReport>
 */
class SharedReportFactory extends Factory
{
    protected $model = SharedReport::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'token' => bin2hex(random_bytes(32)),
            'label' => fake()->sentence(3),
            'filters' => ['type' => 'one_time'],
            'expires_at' => fake()->optional(0.7)->dateTimeBetween('+1 day', '+30 days'),
        ];
    }
}
