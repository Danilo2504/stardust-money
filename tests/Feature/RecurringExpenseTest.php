<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\RecurringExpense;
use App\Models\User;
use Tests\TestCase;

class RecurringExpenseTest extends TestCase
{
    public function test_user_can_create_recurring_expense(): void
    {
        $user = $this->track(User::factory()->create());
        $category = $this->track(Category::factory()->for($user)->create());

        $response = $this->actingAs($user)
            ->postJson(route('recurring-expenses.store'), [
                'description' => 'Alquiler',
                'amount' => 500,
                'category_id' => $category->id,
                'custom_interval_value' => 1,
                'custom_interval_unit' => 'months',
                'next_due_date' => '2026-07-01',
                'is_active' => true,
            ]);

        $response->assertCreated();
        $this->assertDatabaseHas('recurring_expenses', [
            'user_id' => $user->id,
            'description' => 'Alquiler',
            'amount' => '500.0000',
        ]);
    }

    public function test_user_can_update_own_recurring_expense(): void
    {
        $user = $this->track(User::factory()->create());
        $recurring = $this->track(RecurringExpense::factory()->for($user)->create());

        $response = $this->actingAs($user)
            ->patchJson(route('recurring-expenses.update', $recurring), [
                'description' => 'Alquiler actualizado',
                'amount' => 550,
                'category_id' => null,
                'custom_interval_value' => 1,
                'custom_interval_unit' => 'months',
                'next_due_date' => '2026-07-01',
                'is_active' => true,
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('recurring_expenses', [
            'id' => $recurring->id,
            'description' => 'Alquiler actualizado',
            'amount' => '550.0000',
        ]);
    }

    public function test_user_can_delete_own_recurring_expense(): void
    {
        $user = $this->track(User::factory()->create());
        $recurring = $this->track(RecurringExpense::factory()->for($user)->create());

        $response = $this->actingAs($user)
            ->deleteJson(route('recurring-expenses.destroy', $recurring));

        $response->assertNoContent();
        $this->assertSoftDeleted('recurring_expenses', ['id' => $recurring->id]);
    }

    public function test_user_cannot_delete_another_users_recurring_expense(): void
    {
        $user = $this->track(User::factory()->create());
        $otherUser = $this->track(User::factory()->create());
        $recurring = $this->track(RecurringExpense::factory()->for($otherUser)->create());

        $response = $this->actingAs($user)
            ->deleteJson(route('recurring-expenses.destroy', $recurring));

        $response->assertForbidden();
    }
}
