@extends('layouts.main')

@section('title', 'Categorías')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Categorías</h1>
            <p class="page-subtitle">Gestiona tus categorías de gastos</p>
        </div>
        <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#categoryModal">
            <i class="bi bi-plus-lg"></i>
            <span class="d-none d-md-inline">Nueva categoría</span>
        </button>
    </div>

    <div class="table-card">
        <div class="table-responsive">
            <table id="categories-table" class="table table-hover align-middle mb-0" style="width:100%">
                <thead>
                    <tr>
                        <th>Color</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    @include('pages.categories.form')
@endsection

@push('scripts')
    @include('pages.categories.scripts')
@endpush
