<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    /**
     * Determine whether the user can delete the model.
     */
    public function update(User $user, Category $category): bool
    {
        return ($category->user_id === $user->id) && ! $category->is_default;
    }

    public function delete(User $user, Category $category): bool
    {
        return ($category->user_id === $user->id) && ! $category->is_default;
    }
}
