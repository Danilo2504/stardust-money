<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRecurringExpenseRequest;
use App\Http\Requests\UpdateRecurringExpenseRequest;
use App\Models\RecurringExpense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class RecurringExpenseController extends Controller
{
    public function index(Request $request): View
    {
        return view('pages.recurring-expenses.index');
    }

    public function data(Request $request): JsonResponse
    {
        $query = RecurringExpense::byAuthor($request->user()->id)
            ->with('category')
            ->orderBy('description');

        return DataTables::eloquent($query)
            ->editColumn('amount', fn (RecurringExpense $recurring) => number_format((float) $recurring->amount, 2, ',', '.'))
            ->editColumn('next_due_date', fn (RecurringExpense $recurring) => $recurring->next_due_date?->format('d/m/Y') ?? '—')
            ->editColumn('is_active', fn (RecurringExpense $recurring) => $recurring->is_active
                ? '<span class="badge rounded-pill badge-soft-success">Activo</span>'
                : '<span class="badge rounded-pill badge-soft-secondary">Pausado</span>')
            ->editColumn('frequency', fn (RecurringExpense $recurring) => $recurring->custom_interval_value.' '.match ($recurring->custom_interval_unit) {
                'days' => 'día(s)',
                'weeks' => 'semana(s)',
                'months' => 'mes(es)',
                'years' => 'año(s)',
                default => $recurring->custom_interval_unit,
            })
            ->addColumn('actions', fn (RecurringExpense $recurring) => view('components.recurring-expenses.actions', compact('recurring'))->render())
            ->rawColumns(['is_active', 'actions'])
            ->toJson();
    }

    public function store(StoreRecurringExpenseRequest $request): JsonResponse
    {
        $recurringExpense = RecurringExpense::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        return response()->json($recurringExpense, 201);
    }

    public function show(RecurringExpense $recurringExpense): JsonResponse
    {
        $this->authorize('view', $recurringExpense);

        return response()->json($recurringExpense);
    }

    public function update(UpdateRecurringExpenseRequest $request, RecurringExpense $recurringExpense): JsonResponse
    {
        $this->authorize('update', $recurringExpense);
        $recurringExpense->update($request->validated());

        return response()->json($recurringExpense);
    }

    public function destroy(RecurringExpense $recurringExpense): JsonResponse
    {
        $this->authorize('delete', $recurringExpense);
        $recurringExpense->delete();

        return response()->json(null, 204);
    }
}
