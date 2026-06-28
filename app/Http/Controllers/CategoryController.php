<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        return view('pages.categories.index');
    }

    public function data(Request $request): JsonResponse
    {
        $userId = $request->user()?->id;

        $query = Category::where('user_id', $userId)
            ->orWhere('is_default', true)
            ->orderByRaw('is_default DESC')
            ->orderBy('name');

        return DataTables::eloquent($query)
            ->editColumn('is_default', fn(Category $category) => $category->is_default
                ? '<span class="badge rounded-pill badge-secondary">Default</span>'
                : '<span class="badge rounded-pill badge-info">Personalizada</span>')
            ->editColumn('color', fn(Category $category) => $category->color
                ? '<span class="d-inline-block rounded-circle me-2" style="width:12px;height:12px;background:' . $category->color . ';"></span>' . $category->color
                : '—')
            ->addColumn('actions', fn(Category $category) => view('components.categories.actions', compact('category'))->render())
            ->rawColumns(['is_default', 'color', 'actions'])
            ->toJson();
    }

    public function select(Request $request): JsonResponse
    {
        $userId = $request->user()?->id;

        $categories = Category::where('user_id', $userId)
            ->orWhere('is_default', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($categories);
    }

    public function store(CategoryRequest $request): JsonResponse
    {
        $category = Category::create([
            ...$request->validated(),
            'user_id' => $request->user()?->id,
        ]);

        return response()->json($category, 201);
    }

    public function show(Category $category): JsonResponse
    {
        return response()->json($category);
    }

    public function update(CategoryRequest $request, Category $category): JsonResponse
    {
        $this->authorize('update', $category);
        $category->update($request->validated());

        return response()->json($category);
    }

    public function destroy(Category $category): JsonResponse
    {
        $this->authorize('delete', $category);
        $category->delete();

        return response()->json(null, 204);
    }
}
