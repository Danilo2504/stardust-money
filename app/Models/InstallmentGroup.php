<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\InstallmentGroup
 *
 * @property string $id
 * @property string|null $user_id
 * @property string|null $description
 * @property float|null $total_amount
 * @property int|null $total_installments
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read User|null $user
 * @property-read \Illuminate\Database\Eloquent\Collection|Expense[] $expenses
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
