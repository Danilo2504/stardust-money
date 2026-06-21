<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ExpenseFilter
{
    public function __construct(protected array $filters) {}

    public static function fromArray(array $filters): self
    {
        return new self($filters);
    }

    public static function fromRequest(Request $request): self
    {
        return new self([
            ...$request->all(),
            'user_id' => $request->user()->id,
        ]);
    }

    public function apply(Builder $query): Builder
    {
        return $query
            ->when($this->filters['user_id'] ?? null,
                fn ($q, $v) => $q->byAuthor($v))
            ->when($this->filters['type'] ?? null,
                fn ($q, $v) => $q->where('type', $v))
            ->when(isset($this->filters['draft']),
                fn ($q) => $q->where('draft', $this->filters['draft']))
            ->when($this->filters['category_id'] ?? null,
                fn ($q, $v) => $q->where('expenses.category_id', $v))
            ->when($this->filters['date_from'] ?? null,
                fn ($q, $v) => $q->where('expense_date', '>=', $v))
            ->when($this->filters['date_to'] ?? null,
                fn ($q, $v) => $q->where('expense_date', '<=', $v));
    }

    public function toArray(): array
    {
        return $this->filters;
    }
}
