<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSharedReportRequest;
use App\Http\Requests\UpdateSharedReportRequest;
use App\Models\SharedReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SharedReportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $sharedReports = SharedReport::where('user_id', $request->user()->id)->get();

        return response()->json($sharedReports);
    }

    public function store(StoreSharedReportRequest $request): JsonResponse
    {
        $sharedReport = SharedReport::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        return response()->json($sharedReport, 201);
    }

    public function show(SharedReport $sharedReport): JsonResponse
    {
        return response()->json($sharedReport);
    }

    public function update(UpdateSharedReportRequest $request, SharedReport $sharedReport): JsonResponse
    {
        $sharedReport->update($request->validated());

        return response()->json($sharedReport);
    }

    public function destroy(SharedReport $sharedReport): JsonResponse
    {
        $sharedReport->delete();

        return response()->json(null, 204);
    }
}
