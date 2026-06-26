<?php

namespace Tests\Feature;

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
            ->assertSee('Reporte compartido de gastos');
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
}
