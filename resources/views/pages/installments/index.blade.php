@extends('layouts.main')

@section('title', 'Cuotas')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Cuotas</h1>
        <button type="button" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#installmentGroupModal">
            <i class="fas fa-plus fa-sm text-white-50 me-1"></i> Nuevo grupo
        </button>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Listado de grupos de cuotas</h6>
        </div>
        <div class="card-body p-0">
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
    </div>

    @include('pages.installments.form')
@endsection

@push('scripts')
    @include('pages.installments.scripts')
@endpush
