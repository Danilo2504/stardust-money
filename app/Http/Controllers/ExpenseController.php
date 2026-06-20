<?php

namespace App\Http\Controllers;

use App\Filters\ExpenseFilter;
use App\Http\Requests\ExpenseRequest;
use App\Models\Expense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        return view('pages.expenses.index');
    }

    public function data(Request $request): JsonResponse
    {
        $query = Expense::datatable(
            ExpenseFilter::fromRequest($request)
        );

        return DataTables::query($query)
            ->editColumn('amount', fn ($expense) => number_format((float) $expense->amount, 2, ',', '.'))
            ->editColumn('expense_date', fn ($expense) => $expense->expense_date
                ? $expense->expense_date->format('d/m/Y')
                : '—')
            ->editColumn('draft', fn ($expense) => $expense->draft
                ? '<span class="badge bg-warning text-dark">Borrador</span>'
                : '<span class="badge bg-success">Confirmado</span>')
            ->editColumn('type', fn ($expense) => match ($expense->type) {
                'one_time' => 'Único',
                'recurring_child' => 'Recurrente',
                'installment' => 'Cuota',
                default => '—',
            })
            ->addColumn('actions', fn ($expense) => view('components.expenses.actions', compact('expense'))->render())
            ->rawColumns(['draft', 'actions'])
            ->toJson();
    }

    public function store(ExpenseRequest $request): JsonResponse
    {
        $expense = Expense::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        return response()->json($expense, 201);
    }

    public function show(Expense $expense): JsonResponse
    {
        return response()->json($expense);
    }

    public function update(ExpenseRequest $request, Expense $expense): JsonResponse
    {
        $this->authorize('update', $expense);
        $expense->update($request->validated());

        return response()->json($expense);
    }

    public function destroy(Expense $expense): JsonResponse
    {
        $this->authorize('delete', $expense);
        $expense->delete();

        return response()->json(null, 204);
    }

    public function confirm(Expense $expense): JsonResponse
    {
        $this->authorize('confirm', $expense);
        $expense->approve();

        return response()->json($expense);
    }
}
