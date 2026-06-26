@extends('layouts.main')

@section('title', 'Gastos recurrentes')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Gastos recurrentes</h1>
            <p class="page-subtitle">Plantillas que generan gastos automáticamente</p>
        </div>
        <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#recurringExpenseModal">
            <i class="bi bi-plus-lg"></i>
            <span class="d-none d-md-inline">Nueva plantilla</span>
        </button>
    </div>

    <div class="table-card">
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

    @include('pages.recurring-expenses.form')
@endsection

@push('scripts')
    @include('pages.recurring-expenses.scripts')
@endpush
