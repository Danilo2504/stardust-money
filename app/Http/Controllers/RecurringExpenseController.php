<?php

namespace App\Http\Controllers;

use App\Models\RecurringExpense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecurringExpenseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $recurringExpenses = RecurringExpense::where('user_id', $request->user()->id)->get();

        return response()->json($recurringExpenses);
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
        return response()->json($recurringExpense);
    }

    public function update(UpdateRecurringExpenseRequest $request, RecurringExpense $recurringExpense): JsonResponse
    {
        $recurringExpense->update($request->validated());

        return response()->json($recurringExpense);
    }

    public function destroy(RecurringExpense $recurringExpense): JsonResponse
    {
        $recurringExpense->delete();

        return response()->json(null, 204);
    }
}
