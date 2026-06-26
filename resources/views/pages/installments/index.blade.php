@extends('layouts.main')

@section('title', 'Cuotas')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Cuotas</h1>
            <p class="page-subtitle">Agrupa pagos en cuotas y haz seguimiento</p>
        </div>
        <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#installmentGroupModal">
            <i class="bi bi-plus-lg"></i>
            <span class="d-none d-md-inline">Nuevo grupo</span>
        </button>
    </div>

    <div class="table-card">
        <div class="table-responsive">
            <table id="installment-groups-table" class="table table-hover align-middle mb-0" style="width:100%">
                <thead>
                    <tr>
                        <th>Descripción</th>
                        <th class="text-end">Monto total</th>
                        <th class="text-end">Cuotas</th>
                        <th>Progreso</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    @include('pages.installments.form')
@endsection

@push('scripts')
    @include('pages.installments.scripts')
@endpush
