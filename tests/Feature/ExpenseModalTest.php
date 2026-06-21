<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Tests\TestCase;

class ExpenseModalTest extends TestCase
{
    public function test_dashboard_has_register_expense_button_and_modal(): void
    {
        $user = $this->track(User::factory()->create());

        $response = $this->actingAs($user)->get('/');

        $response
            ->assertOk()
            ->assertSee('Registrar gasto')
            ->assertSee('data-bs-target="#expenseModal"', false)
            ->assertSee('id="expenseModal"', false)
            ->assertSee('wire:submit="save"', false);
    }

    public function test_expenses_index_includes_the_create_modal(): void
    {
        $user = $this->track(User::factory()->create());

        $response = $this->actingAs($user)->get('/expenses');

        $response
            ->assertOk()
            ->assertSee('id="expenseModal"', false)
            ->assertSee('wire:submit="save"', false);
    }

    public function test_dashboard_modal_lists_user_categories(): void
    {
        $user = $this->track(User::factory()->create());
        $category = $this->track(Category::factory()->for($user)->create(['name' => 'Salud']));

        $response = $this->actingAs($user)->get('/');

        $response->assertSee('Salud');
    }

    public function test_saving_expense_via_dashboard_persists_in_the_database(): void
    {
        $user = $this->track(User::factory()->create());
        $category = $this->track(Category::factory()->for($user)->create());

        $this->actingAs($user)
            ->get('/')
            ->assertOk();

        $expense = Expense::create([
            'user_id' => $user->id,
            'code' => (new Expense)->generateCode(),
            'description' => 'Compra',
            'amount' => 25.50,
            'category_id' => $category->id,
            'type' => 'one_time',
            'expense_date' => now(),
            'draft' => false,
        ]);

        $this->track($expense);

        $this->assertDatabaseHas('expenses', [
            'user_id' => $user->id,
            'description' => 'Compra',
            'amount' => '25.5000',
            'category_id' => $category->id,
        ]);
    }
}
