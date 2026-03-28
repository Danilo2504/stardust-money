<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Category
 *
 * @property string $id
 * @property string|null $name
 * @property string|null $color
 * @property bool|null $is_default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|RecurringExpense[] $recurringExpenses
 * @property-read \Illuminate\Database\Eloquent\Collection|Expense[] $expenses
 * @property-read User|null $user
 */
class Category extends BaseModel
{
    use SoftDeletes;

    protected $table = 'categories';

    protected $fillable = [
        'user_id',
        'name',
        'color'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public static function listForSelect(?string $userId = null): Collection
    {
        $query = Category::select('name AS description, id AS value', 'color')->orderBy('name');

        if ($userId && $userId !== '') {
            $query->byAuthor($userId);
        }

        return $query->get();
    }

    public function recurringExpenses(): HasMany
    {
        return $this->hasMany(RecurringExpense::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
