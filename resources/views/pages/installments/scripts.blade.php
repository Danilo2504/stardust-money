<script type="text/javascript">
$(document).ready(function() {
    let table = $('#installment-groups-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("installment-groups.data") }}',
        },
        columns: [
            { data: 'description', name: 'description' },
            { data: 'total_amount', name: 'total_amount', className: 'text-end' },
            { data: 'total_installments', name: 'total_installments', className: 'text-end' },
            { data: 'progress', name: 'progress', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/2.2.2/i18n/es-ES.json'
        },
        order: [[0, 'asc']]
    });

    sbDatatableProcessing(table);

    window.addEventListener('installment-group-saved', function () {
        table.draw(false);
    });

    $('#installment-groups-table').on('click', '.edit-installment-group', function() {
        let id = $(this).data('id');

        document.getElementById('installmentGroupModalTitle').textContent = 'Editar grupo de cuotas';
        window.dispatchEvent(new CustomEvent('edit-installment-group', { detail: id }));

        let modal = new bootstrap.Modal(document.getElementById('installmentGroupModal'));
        modal.show();
    });

    $('#installment-groups-table').on('click', '.delete-installment-group', function() {
        let id = $(this).data('id');

        if (! confirm('¿Eliminar este grupo de cuotas?')) return;

        $.ajax({
            url: '{{ url("installment-groups") }}/' + id,
            type: 'DELETE',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function() {
                table.draw(false);
            },
            error: function() {
                alert('No se pudo eliminar el grupo.');
            }
        });
    });
});
</script>
