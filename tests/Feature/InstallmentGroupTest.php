<?php

namespace Tests\Feature;

use App\Models\InstallmentGroup;
use App\Models\User;
use Tests\TestCase;

class InstallmentGroupTest extends TestCase
{
    public function test_user_can_create_installment_group(): void
    {
        $user = $this->track(User::factory()->create());

        $response = $this->actingAs($user)
            ->postJson(route('installment-groups.store'), [
                'description' => 'Notebook',
                'total_amount' => 1200,
                'total_installments' => 12,
            ]);

        $response->assertCreated();
        $this->assertDatabaseHas('installment_groups', [
            'user_id' => $user->id,
            'description' => 'Notebook',
            'total_amount' => '1200.0000',
            'total_installments' => 12,
        ]);
    }

    public function test_user_can_update_own_installment_group(): void
    {
        $user = $this->track(User::factory()->create());
        $group = $this->track(InstallmentGroup::factory()->for($user)->create());

        $response = $this->actingAs($user)
            ->patchJson(route('installment-groups.update', $group), [
                'description' => 'Notebook actualizada',
                'total_amount' => 1300,
                'total_installments' => 10,
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('installment_groups', [
            'id' => $group->id,
            'description' => 'Notebook actualizada',
            'total_amount' => '1300.0000',
            'total_installments' => 10,
        ]);
    }

    public function test_user_can_delete_own_installment_group(): void
    {
        $user = $this->track(User::factory()->create());
        $group = $this->track(InstallmentGroup::factory()->for($user)->create());

        $response = $this->actingAs($user)
            ->deleteJson(route('installment-groups.destroy', $group));

        $response->assertNoContent();
        $this->assertSoftDeleted('installment_groups', ['id' => $group->id]);
    }

    public function test_user_cannot_delete_another_users_installment_group(): void
    {
        $user = $this->track(User::factory()->create());
        $otherUser = $this->track(User::factory()->create());
        $group = $this->track(InstallmentGroup::factory()->for($otherUser)->create());

        $response = $this->actingAs($user)
            ->deleteJson(route('installment-groups.destroy', $group));

        $response->assertForbidden();
    }
}
