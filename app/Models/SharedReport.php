<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\SharedReport
 *
 * @property string $id
 * @property string|null $user_id
 * @property string $token
 * @property array|null $filters
 * @property string|null $label
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read User|null $user
 */
class SharedReport extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'token',
        'filters',
        'label',
        'expires_at',
    ];

    protected $casts = [
        'filters' => 'json',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
