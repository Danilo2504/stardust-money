<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;

class ExpensePolicy
{
    private function isOwner(User $user, Expense $expense): bool
    {
        return $user->id === $expense->user_id;
    }

    public function view(User $user, Expense $expense): bool
    {
        return $this->isOwner($user, $expense);
    }

    public function update(User $user, Expense $expense): bool
    {
        return $this->isOwner($user, $expense);
    }

    public function delete(User $user, Expense $expense): bool
    {
        return $this->isOwner($user, $expense);
    }

    public function confirm(User $user, Expense $expense): bool
    {
        return $this->isOwner($user, $expense);
    }
}
