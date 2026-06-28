<script type="text/javascript">
$(document).ready(function() {
    let table = $('#categories-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("categories.data") }}',
        },
        columns: [
            { data: 'color', name: 'color', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'is_default', name: 'is_default', className: 'text-center' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/2.2.2/i18n/es-ES.json'
        },
        order: [[1, 'asc']]
    });

    sbDatatableProcessing(table);

    window.addEventListener('category-saved', function () {
        table.draw(false);
    });

    window.addEventListener('categories-updated', function () {
        table.draw(false);
    });

    $('#categories-table').on('click', '.edit-category', function() {
        let id = $(this).data('id');
        let name = $(this).data('name');
        let color = $(this).data('color');

        $('#categoryModalTitle').text('Editar categoría');

        window.dispatchEvent(new CustomEvent('load-category', { detail: id }));

        let modal = new bootstrap.Modal(document.getElementById('categoryModal'));
        modal.show();
    });

    $('#categories-table').on('click', '.delete-category', function() {
        let id = $(this).data('id');

        if (! confirm('¿Eliminar esta categoría?')) return;

        $.ajax({
            url: '{{ url("categories") }}/' + id,
            type: 'DELETE',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function() {
                table.draw(false);
                window.dispatchEvent(new Event('categories-updated'));
            },
            error: function(xhr) {
                let message = xhr.responseJSON?.message || 'No se pudo eliminar la categoría.';
                alert(message);
            }
        });
    });

    $('#categoryModal').on('hidden.bs.modal', function () {
        $('#categoryModalTitle').text('Nueva categoría');
        window.dispatchEvent(new Event('reset-category-form'));
    });
});
</script>
