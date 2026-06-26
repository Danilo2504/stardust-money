<?php

namespace App\Policies;

use App\Models\InstallmentGroup;
use App\Models\User;

class InstallmentGroupPolicy
{
    private function isOwner(User $user, InstallmentGroup $installmentGroup): bool
    {
        return $user->id === $installmentGroup->user_id;
    }

    public function view(User $user, InstallmentGroup $installmentGroup): bool
    {
        return $this->isOwner($user, $installmentGroup);
    }

    public function update(User $user, InstallmentGroup $installmentGroup): bool
    {
        return $this->isOwner($user, $installmentGroup);
    }

    public function delete(User $user, InstallmentGroup $installmentGroup): bool
    {
        return $this->isOwner($user, $installmentGroup);
    }
}
