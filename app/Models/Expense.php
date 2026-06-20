<?php

namespace App\Models;

use App\Filters\ExpenseFilter;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Carbon;

/**
 * App\Models\Expense
 *
 * @property string $id
 * @property string|null $user_id
 * @property int|null $code
 * @property bool|null $draft
 * @property string|null $description
 * @property float|null $amount
 * @property string|null $category_id
 * @property string|null $notes
 * @property string|null $type
 * @property Carbon|null $expense_date
 * @property string|null $recurring_expense_id
 * @property string|null $installment_group_id
 * @property int|null $installment_number
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User|null $user
 * @property-read Category|null $category
 * @property-read RecurringExpense|null $recurringExpense
 * @property-read InstallmentGroup|null $installmentGroup
 * @property-read Collection|ExpenseSplit[] $splits
 */
class Expense extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'code',
        'draft',
        'description',
        'amount',
        'category_id',
        'notes',
        'type',
        'expense_date',
        'recurring_expense_id',
        'installment_group_id',
        'installment_number',
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'expense_date' => 'datetime',
        'draft' => 'boolean',
        'code' => 'integer',
        'installment_number' => 'integer',
    ];

    public static function listAll(ExpenseFilter $filter): Collection
    {
        $query = (new static)->select('expenses.*')
            ->categoriesJoin()
            ->recurringExpensesJoin()
            ->installmentGroupsJoin();

        return $filter->apply($query)
            ->orderBy('expenses.expense_date', 'desc')
            ->orderBy('expenses.created_at', 'desc')
            ->get();
    }

    public static function datatable(ExpenseFilter $filter): Builder
    {
        $query = (new static)->select([
            'expenses.id',
            'expenses.code',
            'expenses.description',
            'expenses.amount',
            'expenses.type',
            'expenses.expense_date',
            'expenses.draft',
            'expenses.user_id',
            'expenses.category_id',
            'categories.name as category_name',
        ])
            ->categoriesJoin()
            ->recurringExpensesJoin()
            ->installmentGroupsJoin();

        return $filter->apply($query)
            ->orderBy('expenses.expense_date', 'desc')
            ->orderBy('expenses.created_at', 'desc');
    }

    public function generateCode(): string
    {
        return str_pad((string) random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
    }

    #[Scope]
    protected function categoriesJoin(Builder $query): Builder
    {
        return $query->leftJoin('categories', function (JoinClause $join) {
            $join->on('expenses.category_id', '=', 'categories.id')
                ->whereNull('categories.deleted_at');
        });
    }

    #[Scope]
    protected function recurringExpensesJoin(Builder $query): Builder
    {
        return $query->leftJoin('recurring_expenses', function (JoinClause $join) {
            $join->on('expenses.recurring_expense_id', '=', 'recurring_expenses.id')
                ->whereNull('recurring_expenses.deleted_at');
        });
    }

    #[Scope]
    protected function installmentGroupsJoin(Builder $query): Builder
    {
        return $query->leftJoin('installment_groups', function (JoinClause $join) {
            $join->on('expenses.installment_group_id', '=', 'installment_groups.id')
                ->whereNull('installment_groups.deleted_at');
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function recurringExpense(): BelongsTo
    {
        return $this->belongsTo(RecurringExpense::class);
    }

    public function installmentGroup(): BelongsTo
    {
        return $this->belongsTo(InstallmentGroup::class);
    }

    public function splits(): HasMany
    {
        return $this->hasMany(ExpenseSplit::class);
    }
}
