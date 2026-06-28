@extends('layouts.main')

@section('title', $report->label)

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">{{ $report->label }}</h1>
            <p class="page-subtitle">Reporte compartido de gastos</p>
        </div>
        <span class="badge badge-info">Solo lectura</span>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtros aplicados</h6>
        </div>
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
                @forelse($report->filters ?? [] as $key => $value)
                    <span class="badge badge-secondary">
                        {{ ucfirst(str_replace('_', ' ', $key)) }}: {{ $value }}
                    </span>
                @empty
                    <span class="text-muted">Ninguno</span>
                @endforelse
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Gastos del reporte</h6>
        </div>
        <div class="card-body p-0">
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
                                    <span class="badge" style="background: {{ $expense->category?->color ?? '#858796' }}20; color: {{ $expense->category?->color ?? '#858796' }};">
                                        {{ $expense->category?->name ?? 'Sin categoría' }}
                                    </span>
                                </td>
                                <td class="text-end font-weight-bold">€{{ number_format((float) $expense->amount, 2, ',', '.') }}</td>
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
                                    <div class="text-center text-gray-500 py-5">
                                        <i class="fas fa-inbox fa-3x mb-3 text-gray-300"></i>
                                        <h6>No hay gastos en este reporte</h6>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
