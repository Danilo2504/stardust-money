<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use LogicException;

abstract class BaseModel extends Model
{
    use HasUuid;

    protected $keyType = 'string';

    public $incrementing = false;

    protected string $authorColumn = 'user_id';

    protected static array $columnsCache = [];

    protected function ensureColumnExists(string $column): void
    {
        $table = $this->getTable();

        if (! isset(self::$columnsCache[$table])) {
            self::$columnsCache[$table] = Schema::getColumnListing($table);
        }

        if (! in_array($column, self::$columnsCache[$table], true)) {
            throw new LogicException(sprintf(
                'Column "%s" does not exist on table "%s" (model: %s)',
                $column,
                $table,
                static::class
            ));
        }
    }

    #[Scope]
    protected function byAuthor(Builder $query, ?string $userId): Builder
    {
        $this->ensureColumnExists($this->authorColumn);

        return $query->when(
            $userId,
            fn (Builder $q) => $q->where($this->authorColumn, $userId)
        );
    }

    public function approve(): void
    {
        $this->ensureColumnExists('draft');

        $this->update(['draft' => true]);
    }
}
