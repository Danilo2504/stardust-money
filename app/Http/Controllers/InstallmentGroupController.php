<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInstallmentGroupRequest;
use App\Http\Requests\UpdateInstallmentGroupRequest;
use App\Models\InstallmentGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InstallmentGroupController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $installmentGroups = InstallmentGroup::where('user_id', $request->user()->id)->get();

        return response()->json($installmentGroups);
    }

    public function store(StoreInstallmentGroupRequest $request): JsonResponse
    {
        $installmentGroup = InstallmentGroup::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        return response()->json($installmentGroup, 201);
    }

    public function show(InstallmentGroup $installmentGroup): JsonResponse
    {
        return response()->json($installmentGroup);
    }

    public function update(UpdateInstallmentGroupRequest $request, InstallmentGroup $installmentGroup): JsonResponse
    {
        $installmentGroup->update($request->validated());

        return response()->json($installmentGroup);
    }

    public function destroy(InstallmentGroup $installmentGroup): JsonResponse
    {
        $installmentGroup->delete();

        return response()->json(null, 204);
    }
}
