<script type="text/javascript">
$(document).ready(function() {
    let table = $('#recurring-expenses-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("recurring-expenses.data") }}',
        },
        columns: [
            { data: 'description', name: 'description' },
            { data: 'category.name', name: 'category.name', defaultContent: '—' },
            { data: 'amount', name: 'amount', className: 'text-end' },
            { data: 'frequency', name: 'frequency', orderable: false, searchable: false },
            { data: 'next_due_date', name: 'next_due_date' },
            { data: 'is_active', name: 'is_active', className: 'text-center' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/2.2.2/i18n/es-ES.json'
        },
        order: [[0, 'asc']]
    });

    sbDatatableProcessing(table);

    window.addEventListener('recurring-expense-saved', function () {
        table.draw(false);
    });

    $('#recurring-expenses-table').on('click', '.edit-recurring-expense', function() {
        let id = $(this).data('id');

        document.getElementById('recurringExpenseModalTitle').textContent = 'Editar plantilla';
        window.dispatchEvent(new CustomEvent('edit-recurring-expense', { detail: id }));

        let modal = new bootstrap.Modal(document.getElementById('recurringExpenseModal'));
        modal.show();
    });

    $('#recurring-expenses-table').on('click', '.delete-recurring-expense', function() {
        let id = $(this).data('id');

        if (! confirm('¿Eliminar esta plantilla recurrente?')) return;

        $.ajax({
            url: '{{ url("recurring-expenses") }}/' + id,
            type: 'DELETE',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function() {
                table.draw(false);
            },
            error: function() {
                alert('No se pudo eliminar la plantilla.');
            }
        });
    });
});
</script>
