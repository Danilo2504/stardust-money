@extends('layouts.main')

@section('title', 'Reportes compartidos')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Reportes compartidos</h1>
            <p class="page-subtitle">Crea links públicos para compartir gastos</p>
        </div>
        <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#sharedReportModal">
            <i class="bi bi-plus-lg"></i>
            <span class="d-none d-md-inline">Nuevo reporte</span>
        </button>
    </div>

    <div class="table-card">
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

    @include('pages.shared-reports.form')
@endsection

@push('scripts')
    @include('pages.shared-reports.scripts')
@endpush
