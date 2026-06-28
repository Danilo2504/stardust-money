@extends('layouts.main')

@section('title', 'Reportes compartidos')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Reportes compartidos</h1>
        <button type="button" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#sharedReportModal">
            <i class="fas fa-plus fa-sm text-white-50 me-1"></i> Nuevo reporte
        </button>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Listado de reportes</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="shared-reports-table" class="table table-hover align-middle mb-0" style="width:100%">
                    <thead>
                        <tr>
                            <th>Etiqueta</th>
                            <th>URL</th>
                            <th>Expira</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('pages.shared-reports.form')
@endsection

@push('scripts')
    @include('pages.shared-reports.scripts')
@endpush
