<?php

namespace App\Policies;

use App\Models\SharedReport;
use App\Models\User;

class SharedReportPolicy
{
    private function isOwner(User $user, SharedReport $sharedReport): bool
    {
        return $user->id === $sharedReport->user_id;
    }

    public function view(User $user, SharedReport $sharedReport): bool
    {
        return $this->isOwner($user, $sharedReport);
    }

    public function update(User $user, SharedReport $sharedReport): bool
    {
        return $this->isOwner($user, $sharedReport);
    }

    public function delete(User $user, SharedReport $sharedReport): bool
    {
        return $this->isOwner($user, $sharedReport);
    }
}
