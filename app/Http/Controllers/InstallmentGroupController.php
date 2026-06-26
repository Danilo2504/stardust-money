<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInstallmentGroupRequest;
use App\Http\Requests\UpdateInstallmentGroupRequest;
use App\Models\InstallmentGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class InstallmentGroupController extends Controller
{
    public function index(Request $request): View
    {
        return view('pages.installments.index');
    }

    public function data(Request $request): JsonResponse
    {
        $query = InstallmentGroup::byAuthor($request->user()->id)
            ->withCount('expenses')
            ->orderBy('description');

        return DataTables::eloquent($query)
            ->editColumn('total_amount', fn (InstallmentGroup $group) => number_format((float) $group->total_amount, 2, ',', '.'))
            ->editColumn('progress', fn (InstallmentGroup $group) => $this->progressBadge($group))
            ->addColumn('actions', fn (InstallmentGroup $group) => view('components.installments.actions', compact('group'))->render())
            ->rawColumns(['progress', 'actions'])
            ->toJson();
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
        $this->authorize('view', $installmentGroup);

        return response()->json($installmentGroup);
    }

    public function update(UpdateInstallmentGroupRequest $request, InstallmentGroup $installmentGroup): JsonResponse
    {
        $this->authorize('update', $installmentGroup);
        $installmentGroup->update($request->validated());

        return response()->json($installmentGroup);
    }

    public function destroy(InstallmentGroup $installmentGroup): JsonResponse
    {
        $this->authorize('delete', $installmentGroup);
        $installmentGroup->delete();

        return response()->json(null, 204);
    }

    private function progressBadge(InstallmentGroup $group): string
    {
        $paid = $group->expenses_count;
        $total = $group->total_installments;
        $percentage = $total > 0 ? round(($paid / $total) * 100) : 0;

        return '<div class="d-flex align-items-center gap-2">'
            .'<div class="progress flex-grow-1" style="height:6px;">'
            .'<div class="progress-bar" role="progressbar" style="width: '.$percentage.'%"></div>'
            .'</div>'
            .'<span class="small text-muted">'.$paid.'/'.$total.'</span>'
            .'</div>';
    }
}
