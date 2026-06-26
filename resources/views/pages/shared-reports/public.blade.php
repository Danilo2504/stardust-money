@extends('layouts.main')

@section('title', $report->label)

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $report->label }}</h1>
            <p class="page-subtitle">Reporte compartido de gastos</p>
        </div>
        <span class="badge badge-soft-info">Solo lectura</span>
    </div>

    <div class="card-custom mb-4">
        <div class="card-body">
            <h6 class="mb-3">Filtros aplicados</h6>
            <div class="d-flex flex-wrap gap-2">
                @forelse($report->filters ?? [] as $key => $value)
                    <span class="badge badge-soft-secondary">
                        {{ ucfirst(str_replace('_', ' ', $key)) }}: {{ $value }}
                    </span>
                @empty
                    <span class="text-muted">Ninguno</span>
                @endforelse
            </div>
        </div>
    </div>

    <div class="table-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Categoría</th>
                        <th class="text-end">Importe</th>
                        <th>Tipo</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                        <tr>
                            <td>{{ $expense->code }}</td>
                            <td>{{ $expense->description }}</td>
                            <td>
                                <span class="badge" style="background: {{ $expense->category?->color ?? '#64748b' }}20; color: {{ $expense->category?->color ?? '#64748b' }};">
                                    {{ $expense->category?->name ?? 'Sin categoría' }}
                                </span>
                            </td>
                            <td class="text-end fw-semibold">€{{ number_format((float) $expense->amount, 2, ',', '.') }}</td>
                            <td>
                                @if($expense->type === 'one_time')
                                    Único
                                @elseif($expense->type === 'recurring_child')
                                    Recurrente
                                @elseif($expense->type === 'installment')
                                    Cuota
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $expense->expense_date?->format('d/m/Y') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="bi bi-inbox"></i>
                                    </div>
                                    <h6 class="text-muted">No hay gastos en este reporte</h6>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
