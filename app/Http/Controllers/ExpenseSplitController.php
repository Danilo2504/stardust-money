<?php

namespace App\Http\Controllers;

use App\Models\ExpenseSplit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpenseSplitController extends Controller
{
    public function index(): JsonResponse
    {
        $splits = ExpenseSplit::all();

        return response()->json($splits);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'expense_id' => 'required|exists:expenses,id',
            'person_name' => 'required|string|max:255',
            'amount' => 'nullable|numeric|min:0',
        ]);

        $split = ExpenseSplit::create($data);

        return response()->json($split, 201);
    }

    public function show(ExpenseSplit $expenseSplit): JsonResponse
    {
        return response()->json($expenseSplit);
    }

    public function update(Request $request, ExpenseSplit $expenseSplit): JsonResponse
    {
        $data = $request->validate([
            'expense_id' => 'required|exists:expenses,id',
            'person_name' => 'required|string|max:255',
            'amount' => 'nullable|numeric|min:0',
        ]);

        $expenseSplit->update($data);

        return response()->json($expenseSplit);
    }

    public function destroy(ExpenseSplit $expenseSplit): JsonResponse
    {
        $expenseSplit->delete();

        return response()->json(null, 204);
    }
}
