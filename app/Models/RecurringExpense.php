<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\RecurringExpense
 *
 * @property string $id
 * @property string|null $user_id
 * @property string|null $description
 * @property float|null $amount
 * @property string|null $category_id
 * @property int|null $custom_interval_value
 * @property string|null $custom_interval_unit
 * @property \Illuminate\Support\Carbon|null $next_due_date
 * @property bool|null $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read User|null $user
 * @property-read Category|null $category
 * @property-read \Illuminate\Database\Eloquent\Collection|Expense[] $expenses
 */
class RecurringExpense extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'description',
        'amount',
        'category_id',
        'custom_interval_value',
        'custom_interval_unit',
        'next_due_date',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'next_due_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
