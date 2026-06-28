@extends('layouts.main')

@section('title', 'Gastos')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gastos</h1>
        <button type="button" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#expenseModal">
            <i class="fas fa-plus fa-sm text-white-50 me-1"></i> Nuevo gasto
        </button>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter me-2"></i>Filtros</h6>
        </div>
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label" for="filter-category">Categoría</label>
                    <select id="filter-category" class="form-select form-select-sm">
                        <option value="">Todas</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="filter-type">Tipo</label>
                    <select id="filter-type" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="one_time">Único</option>
                        <option value="recurring_child">Recurrente</option>
                        <option value="installment">Cuota</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="filter-draft">Estado</label>
                    <select id="filter-draft" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="0">Confirmado</option>
                        <option value="1">Borrador</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="filter-date-from">Desde</label>
                    <input type="date" id="filter-date-from" class="form-control form-control-sm">
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="filter-date-to">Hasta</label>
                    <input type="date" id="filter-date-to" class="form-control form-control-sm">
                </div>
                <div class="col-lg-1 col-md-12">
                    <button id="btn-filter" class="btn btn-sm btn-primary w-100">
                        <i class="fas fa-filter fa-sm me-1"></i> Filtrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Listado de gastos</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="expenses-table" class="table table-hover align-middle mb-0" style="width:100%">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Categoría</th>
                            <th class="text-end">Importe</th>
                            <th>Tipo</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('pages.expenses.form')
@endsection

@push('scripts')
    @include('pages.expenses.scripts')
@endpush
