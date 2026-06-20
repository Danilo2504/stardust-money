<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\ExpenseSplit
 *
 * @property string $id
 * @property string|null $expense_id
 * @property string|null $person_name
 * @property float|null $amount
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Expense|null $expense
 */
class ExpenseSplit extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'expense_id',
        'person_name',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:4',
    ];

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }
}
