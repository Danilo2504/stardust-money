<?php

namespace App\Http\Controllers;

use App\Filters\ExpenseFilter;
use App\Http\Requests\ExpenseRequest;
use App\Models\Expense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $expenses = Expense::listAll(
            ExpenseFilter::fromRequest($request)
        );

        return view('expenses.index', compact('expenses'));
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
