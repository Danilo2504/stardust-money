<script type="text/javascript">
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