<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\InstallmentGroup
 *
 * @property string $id
 * @property string|null $user_id
 * @property string|null $description
 * @property float|null $total_amount
 * @property int|null $total_installments
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User|null $user
 * @property-read Collection|Expense[] $expenses
 */
class InstallmentGroup extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'description',
        'total_amount',
        'total_installments',
    ];

    protected $casts = [
        'total_amount' => 'decimal:4',
        'total_installments' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
