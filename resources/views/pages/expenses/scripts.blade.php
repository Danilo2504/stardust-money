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
            { data: 'draft', name: 'expenses.draft', className: 'text-center' },
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

    window.addEventListener('expense-saved', function () {
        table.draw(false);
    });

    window.addEventListener('categories-updated', function () {
        loadCategories(true);
    });

    loadCategories();

    function loadCategories(reset = false) {
        $.get('{{ route("categories.select") }}', function(data) {
            let select = $('#filter-category');
            let currentValue = select.val();

            if (reset) {
                select.empty().append('<option value="">Todas</option>');
            }

            data.forEach(function(cat) {
                if (select.find(`option[value="${cat.id}"]`).length === 0) {
                    select.append(`<option value="${cat.id}">${cat.name}</option>`);
                }
            });

            if (reset && currentValue) {
                select.val(currentValue);
            }
        });
    }

    $('#expenses-table').on('click', '.confirm-draft', function() {
        let id = $(this).data('id');
        let $btn = $(this);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.ajax({
            url: '{{ url("expenses") }}/' + id + '/confirm',
            type: 'PATCH',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function() {
                table.draw(false);
            },
            error: function() {
                alert('No se pudo confirmar el gasto.');
                $btn.prop('disabled', false).html('<i class="bi bi-check-lg"></i>');
            }
        });
    });

    $('#expenses-table').on('click', '.edit-expense', function() {
        let id = $(this).data('id');

        document.getElementById('expenseModalTitle').textContent = 'Editar gasto';
        window.dispatchEvent(new CustomEvent('edit-expense', { detail: id }));

        let modal = new bootstrap.Modal(document.getElementById('expenseModal'));
        modal.show();
    });

    $('#expenses-table').on('click', '.delete-expense', function() {
        let id = $(this).data('id');

        if (! confirm('¿Eliminar este gasto? Esta acción no se puede deshacer.')) return;

        $.ajax({
            url: '{{ url("expenses") }}/' + id,
            type: 'DELETE',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function() {
                table.draw(false);
            },
            error: function() {
                alert('No se pudo eliminar el gasto.');
            }
        });
    });
});
</script>
