<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\SharedReport
 *
 * @property string $id
 * @property string|null $user_id
 * @property string $token
 * @property array|null $filters
 * @property string|null $label
 * @property Carbon|null $expires_at
 * @property-read User|null $user
 */
class SharedReport extends BaseModel
{
    protected $fillable = [
        'user_id',
        'token',
        'filters',
        'label',
        'expires_at',
    ];

    public $timestamps = false;

    protected $casts = [
        'filters' => 'json',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
