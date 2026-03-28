<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseSplitRequest;
use App\Http\Requests\UpdateExpenseSplitRequest;
use App\Models\ExpenseSplit;
use Illuminate\Http\JsonResponse;

class ExpenseSplitController extends Controller
{
    public function index(): JsonResponse
    {
        $splits = ExpenseSplit::all();

        return response()->json($splits);
    }

    public function store(StoreExpenseSplitRequest $request): JsonResponse
    {
        $split = ExpenseSplit::create($request->validated());

        return response()->json($split, 201);
    }

    public function show(ExpenseSplit $expenseSplit): JsonResponse
    {
        return response()->json($expenseSplit);
    }

    public function update(UpdateExpenseSplitRequest $request, ExpenseSplit $expenseSplit): JsonResponse
    {
        $expenseSplit->update($request->validated());

        return response()->json($expenseSplit);
    }

    public function destroy(ExpenseSplit $expenseSplit): JsonResponse
    {
        $expenseSplit->delete();

        return response()->json(null, 204);
    }
}
