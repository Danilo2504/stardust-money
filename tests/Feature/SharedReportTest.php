<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Expense;
use App\Models\SharedReport;
use App\Models\User;
use Tests\TestCase;

class SharedReportTest extends TestCase
{
    public function test_user_can_create_shared_report(): void
    {
        $user = $this->track(User::factory()->create());

        $response = $this->actingAs($user)
            ->postJson(route('shared-reports.store'), [
                'label' => 'Reporte de prueba',
                'filters' => ['type' => 'one_time'],
                'expires_at' => '2026-12-31',
            ]);

        $response->assertCreated();
        $this->assertDatabaseHas('shared_reports', [
            'user_id' => $user->id,
            'label' => 'Reporte de prueba',
        ]);
    }

    public function test_user_can_delete_own_shared_report(): void
    {
        $user = $this->track(User::factory()->create());
        $report = $this->track(SharedReport::factory()->for($user)->create());

        $response = $this->actingAs($user)
            ->deleteJson(route('shared-reports.destroy', $report));

        $response->assertNoContent();
        $this->assertDatabaseMissing('shared_reports', ['id' => $report->id]);
    }

    public function test_public_shared_report_displays_expenses(): void
    {
        $user = $this->track(User::factory()->create());
        $expense = $this->track(Expense::factory()->for($user)->create([
            'description' => 'Gasto compartido',
            'type' => 'one_time',
        ]));
        $report = $this->track(SharedReport::factory()->for($user)->create([
            'filters' => ['type' => 'one_time'],
        ]));

        $response = $this->get(route('shared-reports.public', $report->token));

        $response->assertOk()
            ->assertSee('Gasto compartido')
            ->assertSee('Gastos del reporte');
    }

    public function test_expired_shared_report_returns_not_found(): void
    {
        $user = $this->track(User::factory()->create());
        $report = $this->track(SharedReport::factory()->for($user)->create([
            'expires_at' => now()->subDay(),
        ]));

        $response = $this->get(route('shared-reports.public', $report->token));

        $response->assertNotFound();
    }

    public function test_user_can_export_shared_report_csv(): void
    {
        $user = $this->track(User::factory()->create());
        $category = $this->track(Category::factory()->create([
            'user_id' => $user->id,
        ]));
        $includedExpense = $this->track(Expense::factory()->for($user)->create([
            'description' => 'Gasto incluido',
            'type' => 'one_time',
            'category_id' => $category->id,
        ]));
        $this->track(Expense::factory()->for($user)->create([
            'description' => 'Gasto excluido',
            'type' => 'installment',
            'category_id' => $category->id,
        ]));
        $report = $this->track(SharedReport::factory()->for($user)->create([
            'filters' => ['type' => 'one_time'],
        ]));

        $response = $this->actingAs($user)
            ->get(route('shared-reports.export', $report));

        $response->assertOk()
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->assertHeader('Content-Disposition', 'attachment; filename=reporte.csv');

        ob_start();
        $response->baseResponse->sendContent();
        $csv = ob_get_clean();

        $this->assertStringContainsString('Gasto incluido', $csv);
        $this->assertStringNotContainsString('Gasto excluido', $csv);
        $this->assertStringContainsString($category->name, $csv);
    }

    public function test_user_cannot_export_foreign_shared_report_csv(): void
    {
        $owner = $this->track(User::factory()->create());
        $other = $this->track(User::factory()->create());
        $report = $this->track(SharedReport::factory()->for($owner)->create());

        $response = $this->actingAs($other)
            ->get(route('shared-reports.export', $report));

        $response->assertForbidden();
    }
}
