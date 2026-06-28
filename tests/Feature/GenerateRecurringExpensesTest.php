<?php

namespace Tests\Feature;

use App\Console\Commands\Recurring\GenerateRecurringExpenses;
use App\Models\Category;
use App\Models\Expense;
use App\Models\RecurringExpense;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class GenerateRecurringExpensesTest extends TestCase
{
    public function test_it_generates_draft_expense_for_active_recurring(): void
    {
        Carbon::setTestNow('2026-06-28 00:00:00');

        try {
            $user = $this->track(User::factory()->create());
            $category = $this->track(Category::factory()->for($user)->create());
            $recurring = $this->track(RecurringExpense::factory()->for($user)->create([
                'category_id' => $category->id,
                'custom_interval_value' => 1,
                'custom_interval_unit' => 'months',
                'next_due_date' => '2026-06-01 00:00:00',
                'is_active' => true,
            ]));

            $this->artisan(GenerateRecurringExpenses::class)
                ->assertSuccessful();

            $this->assertDatabaseHas('expenses', [
                'user_id' => $user->id,
                'recurring_expense_id' => $recurring->id,
                'description' => $recurring->description,
                'amount' => $recurring->amount,
                'category_id' => $category->id,
                'type' => 'recurring_child',
                'expense_date' => '2026-06-01 00:00:00',
                'draft' => true,
            ]);

            $recurring->refresh();
            $this->assertEquals('2026-07-01 00:00:00', $recurring->next_due_date->format('Y-m-d H:i:s'));
        } finally {
            Carbon::setTestNow(null);
        }
    }

    public function test_it_skips_inactive_recurring_expenses(): void
    {
        Carbon::setTestNow('2026-06-28 00:00:00');

        try {
            $user = $this->track(User::factory()->create());
            $category = $this->track(Category::factory()->for($user)->create());
            $recurring = $this->track(RecurringExpense::factory()->for($user)->create([
                'category_id' => $category->id,
                'custom_interval_value' => 1,
                'custom_interval_unit' => 'months',
                'next_due_date' => '2026-06-01 00:00:00',
                'is_active' => false,
            ]));

            $this->artisan(GenerateRecurringExpenses::class)
                ->assertSuccessful();

            $this->assertDatabaseMissing('expenses', [
                'recurring_expense_id' => $recurring->id,
            ]);
        } finally {
            Carbon::setTestNow(null);
        }
    }

    public function test_it_skips_future_next_due_date(): void
    {
        Carbon::setTestNow('2026-06-28 00:00:00');

        try {
            $user = $this->track(User::factory()->create());
            $category = $this->track(Category::factory()->for($user)->create());
            $recurring = $this->track(RecurringExpense::factory()->for($user)->create([
                'category_id' => $category->id,
                'custom_interval_value' => 1,
                'custom_interval_unit' => 'months',
                'next_due_date' => '2026-07-01 00:00:00',
                'is_active' => true,
            ]));

            $this->artisan(GenerateRecurringExpenses::class)
                ->assertSuccessful();

            $this->assertDatabaseMissing('expenses', [
                'recurring_expense_id' => $recurring->id,
            ]);
        } finally {
            Carbon::setTestNow(null);
        }
    }

    public function test_it_catches_up_multiple_missed_intervals(): void
    {
        Carbon::setTestNow('2026-06-28 00:00:00');

        try {
            $user = $this->track(User::factory()->create());
            $category = $this->track(Category::factory()->for($user)->create());
            $recurring = $this->track(RecurringExpense::factory()->for($user)->create([
                'category_id' => $category->id,
                'custom_interval_value' => 1,
                'custom_interval_unit' => 'months',
                'next_due_date' => '2026-03-01 00:00:00',
                'is_active' => true,
            ]));

            $this->artisan(GenerateRecurringExpenses::class)
                ->assertSuccessful();

            $generatedExpenses = Expense::query()
                ->where('recurring_expense_id', $recurring->id)
                ->where('type', 'recurring_child')
                ->where('draft', true)
                ->orderBy('expense_date', 'asc')
                ->get();

            $this->assertCount(4, $generatedExpenses);

            $expectedDates = [
                '2026-03-01 00:00:00',
                '2026-04-01 00:00:00',
                '2026-05-01 00:00:00',
                '2026-06-01 00:00:00',
            ];

            foreach ($expectedDates as $index => $expectedDate) {
                $this->assertEquals($expectedDate, $generatedExpenses[$index]->expense_date->format('Y-m-d H:i:s'));
            }

            $recurring->refresh();
            $this->assertEquals('2026-07-01 00:00:00', $recurring->next_due_date->format('Y-m-d H:i:s'));
        } finally {
            Carbon::setTestNow(null);
        }
    }

    public function test_it_advances_next_due_date_by_interval_unit(): void
    {
        Carbon::setTestNow('2026-06-28 00:00:00');

        $scenarios = [
            ['unit' => 'days', 'value' => 5, 'expected' => '2026-07-02 00:00:00'],
            ['unit' => 'weeks', 'value' => 2, 'expected' => '2026-07-11 00:00:00'],
            ['unit' => 'months', 'value' => 1, 'expected' => '2026-07-27 00:00:00'],
            ['unit' => 'years', 'value' => 1, 'expected' => '2027-06-27 00:00:00'],
        ];

        try {
            foreach ($scenarios as $scenario) {
                $user = $this->track(User::factory()->create());
                $category = $this->track(Category::factory()->for($user)->create());
                $recurring = $this->track(RecurringExpense::factory()->for($user)->create([
                    'category_id' => $category->id,
                    'custom_interval_value' => $scenario['value'],
                    'custom_interval_unit' => $scenario['unit'],
                    'next_due_date' => '2026-06-27 00:00:00',
                    'is_active' => true,
                ]));

                $this->artisan(GenerateRecurringExpenses::class)
                    ->assertSuccessful();

                $recurring->refresh();
                $this->assertEquals($scenario['expected'], $recurring->next_due_date->format('Y-m-d H:i:s'));
            }
        } finally {
            Carbon::setTestNow(null);
        }
    }

    public function test_it_generates_unique_codes_per_user(): void
    {
        Carbon::setTestNow('2026-06-28 00:00:00');

        try {
            $user = $this->track(User::factory()->create());
            $category = $this->track(Category::factory()->for($user)->create());
            $existingExpense = $this->track(Expense::factory()->for($user)->create([
                'category_id' => $category->id,
                'code' => '12345678',
            ]));
            $recurring = $this->track(RecurringExpense::factory()->for($user)->create([
                'category_id' => $category->id,
                'custom_interval_value' => 1,
                'custom_interval_unit' => 'months',
                'next_due_date' => '2026-06-01 00:00:00',
                'is_active' => true,
            ]));

            $this->artisan(GenerateRecurringExpenses::class)
                ->assertSuccessful();

            $generatedExpense = Expense::query()
                ->where('recurring_expense_id', $recurring->id)
                ->first();

            $this->assertNotNull($generatedExpense);
            $this->assertNotEquals($existingExpense->code, $generatedExpense->code);
        } finally {
            Carbon::setTestNow(null);
        }
    }
}
