@extends('layouts.main')

@section('title', 'Gastos')

@section('content')
<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="m-0">Gastos</h4>
        <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#expenseModal">
            + Nuevo gasto
        </button>
    </div>

    <div class="card card-custom p-3 mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small">Categoría</label>
                <select id="filter-category" class="form-select form-select-sm">
                    <option value="">Todas</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Tipo</label>
                <select id="filter-type" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="one_time">Único</option>
                    <option value="recurring_child">Recurrente</option>
                    <option value="installment">Cuota</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Estado</label>
                <select id="filter-draft" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="0">Confirmado</option>
                    <option value="1">Borrador</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Desde</label>
                <input type="date" id="filter-date-from" class="form-control form-control-sm datepicker">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Hasta</label>
                <input type="date" id="filter-date-to" class="form-control form-control-sm datepicker">
            </div>
            <div class="col-md-1">
                <button id="btn-filter" class="btn btn-sm btn-primary-custom w-100">Filtrar</button>
            </div>
        </div>
    </div>

    <div class="card card-custom">
        <div class="table-responsive">
            <table id="expenses-table" class="table table-hover align-middle mb-0" style="width:100%">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Categoría</th>
                        <th>Importe</th>
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
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let table = $('#expenses-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("expenses.data") }}',
            data: function(d) {
                d.category_id = $('#filter-category').val();
                d.type = $('#filter-type').val();
                d.draft = $('#filter-draft').val();
                d.date_from = $('#filter-date-from').val();
                d.date_to = $('#filter-date-to').val();
            }
        },
        columns: [
            { data: 'code', name: 'expenses.code' },
            { data: 'description', name: 'expenses.description' },
            { data: 'category_name', name: 'categories.name' },
            { data: 'amount', name: 'expenses.amount', className: 'text-end' },
            { data: 'type', name: 'expenses.type' },
            { data: 'expense_date', name: 'expenses.expense_date' },
            { data: 'draft', name: 'expenses.draft' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/2.2.2/i18n/es-ES.json'
        },
        order: [[5, 'desc']],
        drawCallback: function() {
            $('.select2').select2({ width: '100%', placeholder: 'Seleccionar...' });
        }
    });

    $('#btn-filter').on('click', function() {
        table.draw();
    });

    loadCategories();

    function loadCategories() {
        $.get('{{ route("categories.index") }}', function(data) {
            let select = $('#filter-category');
            data.forEach(function(cat) {
                select.append(`<option value="${cat.id}">${cat.name || cat.description}</option>`);
            });
        });
    }
});
</script>
@endpush
