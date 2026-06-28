<?php

namespace App\Http\Controllers;

use App\Filters\ExpenseFilter;
use App\Http\Requests\StoreSharedReportRequest;
use App\Http\Requests\UpdateSharedReportRequest;
use App\Models\Expense;
use App\Models\SharedReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Yajra\DataTables\Facades\DataTables;

class SharedReportController extends Controller
{
    public function index(Request $request): View
    {
        return view('pages.shared-reports.index');
    }

    public function data(Request $request): JsonResponse
    {
        $query = SharedReport::byAuthor($request->user()->id)
            ->orderBy('created_at', 'desc');

        return DataTables::eloquent($query)
            ->editColumn('expires_at', fn(SharedReport $report) => $report->expires_at?->format('d/m/Y') ?? 'Sin expiración')
            ->editColumn('status', fn(SharedReport $report) => $report->expires_at && $report->expires_at->isPast()
                ? '<span class="badge rounded-pill badge-danger">Expirado</span>'
                : '<span class="badge rounded-pill badge-success">Activo</span>')
            ->addColumn('url', fn(SharedReport $report) => route('shared-reports.public', $report->token))
            ->addColumn('actions', fn(SharedReport $report) => view('components.shared-reports.actions', compact('report'))->render())
            ->rawColumns(['status', 'actions'])
            ->toJson();
    }

    public function store(StoreSharedReportRequest $request): JsonResponse
    {
        $sharedReport = SharedReport::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
            'token' => Str::random(64),
        ]);

        return response()->json($sharedReport, 201);
    }

    public function show(SharedReport $sharedReport): JsonResponse
    {
        $this->authorize('view', $sharedReport);

        return response()->json($sharedReport);
    }

    public function update(UpdateSharedReportRequest $request, SharedReport $sharedReport): JsonResponse
    {
        $this->authorize('update', $sharedReport);
        $sharedReport->update($request->validated());

        return response()->json($sharedReport);
    }

    public function destroy(SharedReport $sharedReport): JsonResponse
    {
        $this->authorize('delete', $sharedReport);
        $sharedReport->delete();

        return response()->json(null, 204);
    }

    public function export(SharedReport $sharedReport): StreamedResponse
    {
        $this->authorize('view', $sharedReport);

        $filters = ExpenseFilter::fromArray([
            ...($sharedReport->filters ?? []),
            'user_id' => $sharedReport->user_id,
        ]);

        $expenses = Expense::listAll($filters);

        $typeLabels = [
            'one_time' => 'De una vez',
            'recurring_child' => 'Recurrente',
            'installment' => 'A cuotas',
        ];

        return response()->streamDownload(function () use ($expenses, $typeLabels) {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, ['Código', 'Descripción', 'Categoría', 'Importe', 'Tipo', 'Fecha']);

            foreach ($expenses as $expense) {
                fputcsv($output, [
                    $expense->code,
                    $expense->description,
                    $expense->category?->name ?? 'Sin categoría',
                    '€' . number_format((float) $expense->amount, 2, ',', '.'),
                    $typeLabels[$expense->type] ?? $expense->type,
                    $expense->expense_date?->format('d/m/Y') ?? '',
                ]);
            }

            fclose($output);
        }, 'reporte.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function publicShow(string $token): View
    {
        $report = SharedReport::where('token', $token)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->firstOrFail();

        $filters = ExpenseFilter::fromArray([
            ...($report->filters ?? []),
            'user_id' => $report->user_id,
        ]);

        $expenses = Expense::listAll($filters);

        return view('pages.shared-reports.public', compact('report', 'expenses'));
    }
}
