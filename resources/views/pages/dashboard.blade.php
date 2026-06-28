@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <button type="button" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#expenseModal">
            <i class="fas fa-plus fa-sm text-white-50 me-1"></i> Registrar gasto
        </button>
    </div>

    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Gastado este mes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">€{{ number_format($totalSpent, 2, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-euro-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Gastos este mes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $expenseCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-receipt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pendientes de confirmar</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingDrafts }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Hoy</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ now()->format('d/m/Y') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history me-2"></i>Últimos gastos</h6>
            <a href="{{ route('expenses.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-list fa-sm text-white-50 me-1"></i> Ver todos
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
                                    <div class="font-weight-bold text-gray-800">{{ $expense->description }}</div>
                                    <small class="text-muted">#{{ $expense->code }}</small>
                                </td>
                                <td>
                                    <span class="badge" style="background: {{ $expense->category?->color ?? '#858796' }}20; color: {{ $expense->category?->color ?? '#858796' }};">
                                        {{ $expense->category?->name ?? 'Sin categoría' }}
                                    </span>
                                </td>
                                <td class="text-end font-weight-bold">
                                    €{{ number_format((float) $expense->amount, 2, ',', '.') }}
                                </td>
                                <td>
                                    {{ $expense->expense_date?->format('d/m/Y') ?? '—' }}
                                </td>
                                <td>
                                    @if($expense->draft)
                                        <span class="badge badge-warning">Borrador</span>
                                    @else
                                        <span class="badge badge-success">Confirmado</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-center text-gray-500 py-5">
                                        <i class="fas fa-inbox fa-3x mb-3 text-gray-300"></i>
                                        <h6>Aún no tienes gastos registrados</h6>
                                        <p class="small mb-0">Haz clic en "Registrar gasto" para comenzar.</p>
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
