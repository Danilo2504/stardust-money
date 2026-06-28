@extends('layouts.main')

@section('title', 'Gastos recurrentes')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gastos recurrentes</h1>
        <button type="button" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#recurringExpenseModal">
            <i class="fas fa-plus fa-sm text-white-50 me-1"></i> Nueva plantilla
        </button>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Listado de plantillas</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="recurring-expenses-table" class="table table-hover align-middle mb-0" style="width:100%">
                    <thead>
                        <tr>
                            <th>Descripción</th>
                            <th>Categoría</th>
                            <th class="text-end">Monto</th>
                            <th>Frecuencia</th>
                            <th>Próxima fecha</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('pages.recurring-expenses.form')
@endsection

@push('scripts')
    @include('pages.recurring-expenses.scripts')
@endpush
