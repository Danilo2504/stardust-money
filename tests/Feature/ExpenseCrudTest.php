<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\User;
use Tests\TestCase;

class ExpenseCrudTest extends TestCase
{
    public function test_user_can_delete_own_expense(): void
    {
        $user = $this->track(User::factory()->create());
        $expense = $this->track(Expense::factory()->for($user)->create());

        $response = $this->actingAs($user)
            ->deleteJson(route('expenses.destroy', $expense));

        $response->assertNoContent();
        $this->assertSoftDeleted('expenses', ['id' => $expense->id]);
    }

    public function test_user_cannot_delete_another_users_expense(): void
    {
        $user = $this->track(User::factory()->create());
        $otherUser = $this->track(User::factory()->create());
        $expense = $this->track(Expense::factory()->for($otherUser)->create());

        $response = $this->actingAs($user)
            ->deleteJson(route('expenses.destroy', $expense));

        $response->assertForbidden();
        $this->assertNotSoftDeleted('expenses', ['id' => $expense->id]);
    }

    public function test_user_can_confirm_own_draft_expense(): void
    {
        $user = $this->track(User::factory()->create());
        $expense = $this->track(Expense::factory()->for($user)->draft()->create());

        $this->assertTrue($expense->draft);

        $response = $this->actingAs($user)
            ->patchJson(route('expenses.confirm', $expense));

        $response->assertOk();
        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'draft' => false,
        ]);
    }

    public function test_user_cannot_confirm_another_users_expense(): void
    {
        $user = $this->track(User::factory()->create());
        $otherUser = $this->track(User::factory()->create());
        $expense = $this->track(Expense::factory()->for($otherUser)->draft()->create());

        $response = $this->actingAs($user)
            ->patchJson(route('expenses.confirm', $expense));

        $response->assertForbidden();
        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'draft' => true,
        ]);
    }
}
