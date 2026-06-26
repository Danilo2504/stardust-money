<?php

namespace App\Policies;

use App\Models\RecurringExpense;
use App\Models\User;

class RecurringExpensePolicy
{
    private function isOwner(User $user, RecurringExpense $recurringExpense): bool
    {
        return $user->id === $recurringExpense->user_id;
    }

    public function view(User $user, RecurringExpense $recurringExpense): bool
    {
        return $this->isOwner($user, $recurringExpense);
    }

    public function update(User $user, RecurringExpense $recurringExpense): bool
    {
        return $this->isOwner($user, $recurringExpense);
    }

    public function delete(User $user, RecurringExpense $recurringExpense): bool
    {
        return $this->isOwner($user, $recurringExpense);
    }
}
