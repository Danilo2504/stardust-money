@extends('layouts.main')

@section('title', 'Gastos')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Gastos</h1>
            <p class="page-subtitle">Gestiona todos tus egresos</p>
        </div>
        <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#expenseModal">
            <i class="bi bi-plus-lg"></i>
            <span class="d-none d-md-inline">Nuevo gasto</span>
        </button>
    </div>

    <div class="card-custom p-3 mb-4">
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
                <button id="btn-filter" class="btn btn-sm btn-primary-custom w-100">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
            </div>
        </div>
    </div>

    <div class="table-card">
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

    @include('pages.expenses.form')
@endsection

@push('scripts')
    @include('pages.expenses.scripts')
@endpush
