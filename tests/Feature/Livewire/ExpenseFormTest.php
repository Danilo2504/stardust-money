<?php

namespace Tests\Feature\Livewire;

use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

class ExpenseFormTest extends TestCase
{
    public function test_component_renders_with_today_date_prefilled(): void
    {
        $user = $this->track(User::factory()->create());

        $this->actingAs($user);

        Livewire::test('expenses.expense-form')
            ->assertSet('description', '')
            ->assertSet('amount', '')
            ->assertSet('category_id', null)
            ->assertSet('type', 'one_time')
            ->assertSet('expense_date', now()->format('Y-m-d'))
            ->assertSee('Guardar Gasto', false);

        $this->assertDatabaseMissing('expenses', [
            'user_id' => $user->id,
        ]);
    }

    public function test_user_categories_are_listed_in_the_select(): void
    {
        $user = $this->track(User::factory()->create());
        $category = $this->track(Category::factory()->for($user)->create(['name' => 'Transporte']));
        $otherCategory = Category::factory()->create(['name' => 'Otro usuario']);
        $this->track($otherCategory);
        $this->track($otherCategory->user);

        $this->actingAs($user);

        Livewire::test('expenses.expense-form')
            ->assertSee('Transporte')
            ->assertDontSee('Otro usuario')
            ->assertSee($category->id, false);
    }

    public function test_expense_is_created_with_valid_data(): void
    {
        $user = $this->track(User::factory()->create());
        $category = $this->track(Category::factory()->for($user)->create());

        $this->actingAs($user);

        Livewire::test('expenses.expense-form')
            ->set('description', 'Cena con amigos')
            ->set('amount', '42.50')
            ->set('category_id', $category->id)
            ->set('expense_date', '2026-06-15')
            ->set('type', 'one_time')
            ->set('notes', 'Restaurante italiano')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('expense-created');

        $expense = Expense::where('user_id', $user->id)
            ->where('description', 'Cena con amigos')
            ->firstOrFail();
        $this->track($expense);

        $this->assertDatabaseHas('expenses', [
            'user_id' => $user->id,
            'description' => 'Cena con amigos',
            'amount' => '42.5000',
            'category_id' => $category->id,
            'type' => 'one_time',
            'notes' => 'Restaurante italiano',
            'draft' => false,
        ]);
    }

    public function test_manual_creation_always_persists_as_confirmed(): void
    {
        $user = $this->track(User::factory()->create());

        $this->actingAs($user);

        Livewire::test('expenses.expense-form')
            ->set('description', 'Gasto confirmado')
            ->set('amount', '10')
            ->set('expense_date', '2026-06-15')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('expenses', [
            'user_id' => $user->id,
            'description' => 'Gasto confirmado',
            'draft' => false,
        ]);
    }

    public function test_description_is_required(): void
    {
        $user = $this->track(User::factory()->create());

        $this->actingAs($user);

        Livewire::test('expenses.expense-form')
            ->set('description', '')
            ->set('amount', '10')
            ->set('expense_date', '2026-06-15')
            ->call('save')
            ->assertHasErrors(['description' => 'required']);

        $this->assertDatabaseMissing('expenses', [
            'user_id' => $user->id,
            'description' => '',
        ]);
    }

    public function test_amount_must_be_positive(): void
    {
        $user = $this->track(User::factory()->create());

        $this->actingAs($user);

        Livewire::test('expenses.expense-form')
            ->set('description', 'Importe negativo')
            ->set('amount', '0')
            ->set('expense_date', '2026-06-15')
            ->call('save')
            ->assertHasErrors(['amount' => 'gt:0']);

        $this->assertDatabaseMissing('expenses', [
            'user_id' => $user->id,
            'description' => 'Importe negativo',
        ]);
    }

    public function test_amount_must_be_numeric(): void
    {
        $user = $this->track(User::factory()->create());

        $this->actingAs($user);

        Livewire::test('expenses.expense-form')
            ->set('description', 'Importe no numérico')
            ->set('amount', 'abc')
            ->set('expense_date', '2026-06-15')
            ->call('save')
            ->assertHasErrors(['amount' => 'numeric']);
    }

    public function test_expense_date_is_required(): void
    {
        $user = $this->track(User::factory()->create());

        $this->actingAs($user);

        Livewire::test('expenses.expense-form')
            ->set('description', 'Sin fecha')
            ->set('amount', '10')
            ->set('expense_date', '')
            ->call('save')
            ->assertHasErrors(['expense_date' => 'required']);
    }

    public function test_type_must_be_a_valid_value(): void
    {
        $user = $this->track(User::factory()->create());

        $this->actingAs($user);

        Livewire::test('expenses.expense-form')
            ->set('description', 'Tipo inválido')
            ->set('amount', '10')
            ->set('expense_date', '2026-06-15')
            ->set('type', 'invalid_type')
            ->call('save')
            ->assertHasErrors(['type' => 'in']);
    }

    public function test_category_must_exist(): void
    {
        $user = $this->track(User::factory()->create());

        $this->actingAs($user);

        Livewire::test('expenses.expense-form')
            ->set('description', 'Categoría inexistente')
            ->set('amount', '10')
            ->set('expense_date', '2026-06-15')
            ->set('category_id', '00000000-0000-0000-0000-000000000000')
            ->call('save')
            ->assertHasErrors(['category_id' => 'exists']);
    }

    public function test_form_is_reset_after_saving(): void
    {
        $user = $this->track(User::factory()->create());

        $this->actingAs($user);

        Livewire::test('expenses.expense-form')
            ->set('description', 'Primer gasto')
            ->set('amount', '15')
            ->set('expense_date', '2026-06-15')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('description', '')
            ->assertSet('amount', '')
            ->assertSet('category_id', null)
            ->assertSet('notes', '')
            ->assertSet('type', 'one_time')
            ->assertSet('expense_date', now()->format('Y-m-d'));

        $expense = Expense::where('user_id', $user->id)
            ->where('description', 'Primer gasto')
            ->firstOrFail();
        $this->track($expense);
    }

    public function test_a_code_is_generated_automatically(): void
    {
        $user = $this->track(User::factory()->create());

        $this->actingAs($user);

        Livewire::test('expenses.expense-form')
            ->set('description', 'Con código auto')
            ->set('amount', '5')
            ->set('expense_date', '2026-06-15')
            ->call('save')
            ->assertHasNoErrors();

        $expense = Expense::where('user_id', $user->id)
            ->where('description', 'Con código auto')
            ->firstOrFail();
        $this->track($expense);

        $this->assertNotEmpty($expense->code);
        $this->assertSame(8, strlen((string) $expense->code));
    }
}
