@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">Resumen de tus gastos</p>
        </div>
        <button type="button" class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#expenseModal">
            <i class="bi bi-plus-lg"></i>
            <span class="d-none d-md-inline">Registrar gasto</span>
        </button>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="stat-card accent h-100">
                <div class="stat-icon">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div class="stat-label">Gastado este mes</div>
                <div class="stat-value">€{{ number_format($totalSpent, 2, ',', '.') }}</div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="stat-card h-100">
                <div class="stat-icon">
                    <i class="bi bi-receipt"></i>
                </div>
                <div class="stat-label">Gastos este mes</div>
                <div class="stat-value">{{ $expenseCount }}</div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="stat-card warning h-100">
                <div class="stat-icon">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="stat-label">Pendientes de confirmar</div>
                <div class="stat-value">{{ $pendingDrafts }}</div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="stat-card success h-100">
                <div class="stat-icon">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div class="stat-label">Hoy</div>
                <div class="stat-value">{{ now()->format('d/m/Y') }}</div>
            </div>
        </div>
    </div>

    <div class="card-custom">
        <div class="card-header d-flex align-items-center justify-content-between">
            <span><i class="bi bi-clock-history me-2"></i>Últimos gastos</span>
            <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-primary-custom">
                Ver todos
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Descripción</th>
                            <th>Categoría</th>
                            <th class="text-end">Importe</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentExpenses as $expense)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $expense->description }}</div>
                                    <small class="text-muted">#{{ $expense->code }}</small>
                                </td>
                                <td>
                                    <span class="badge" style="background: {{ $expense->category?->color ?? '#64748b' }}20; color: {{ $expense->category?->color ?? '#64748b' }};">
                                        {{ $expense->category?->name ?? 'Sin categoría' }}
                                    </span>
                                </td>
                                <td class="text-end fw-semibold">
                                    €{{ number_format((float) $expense->amount, 2, ',', '.') }}
                                </td>
                                <td>
                                    {{ $expense->expense_date?->format('d/m/Y') ?? '—' }}
                                </td>
                                <td>
                                    @if($expense->draft)
                                        <span class="badge rounded-pill badge-soft-warning">Borrador</span>
                                    @else
                                        <span class="badge rounded-pill badge-soft-success">Confirmado</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <i class="bi bi-inbox"></i>
                                        </div>
                                        <h6 class="text-muted">Aún no tienes gastos registrados</h6>
                                        <p class="small text-muted mb-0">Haz clic en "Registrar gasto" para comenzar.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('pages.expenses.form')
@endsection
