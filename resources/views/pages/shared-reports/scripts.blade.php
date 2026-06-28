<script type="text/javascript">
$(document).ready(function() {
    let table = $('#shared-reports-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("shared-reports.data") }}',
        },
        columns: [
            { data: 'label', name: 'label' },
            { data: 'url', name: 'url', orderable: false, searchable: false },
            { data: 'expires_at', name: 'expires_at' },
            { data: 'status', name: 'status', className: 'text-center' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/2.2.2/i18n/es-ES.json'
        },
        order: [[2, 'desc']]
    });

    sbDatatableProcessing(table);

    window.addEventListener('shared-report-saved', function () {
        table.draw(false);
    });

    $('#shared-reports-table').on('click', '.copy-report-url', function() {
        let url = $(this).data('url');
        navigator.clipboard.writeText(url).then(function() {
            alert('Link copiado al portapapeles.');
        }, function() {
            prompt('Copia este link:', url);
        });
    });

    $('#shared-reports-table').on('click', '.edit-shared-report', function() {
        let id = $(this).data('id');

        document.getElementById('sharedReportModalTitle').textContent = 'Editar reporte compartido';
        window.dispatchEvent(new CustomEvent('edit-shared-report', { detail: [id] }));

        let modal = new bootstrap.Modal(document.getElementById('sharedReportModal'));
        modal.show();
    });

    $('#shared-reports-table').on('click', '.delete-shared-report', function() {
        let id = $(this).data('id');

        if (! confirm('¿Eliminar este reporte compartido?')) return;

        $.ajax({
            url: '{{ url("shared-reports") }}/' + id,
            type: 'DELETE',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function() {
                table.draw(false);
            },
            error: function() {
                alert('No se pudo eliminar el reporte.');
            }
        });
    });

    $('#shared-reports-table').on('click', '.export-report-csv', function() {
        let id = $(this).data('id');
        let iframe = $('<iframe>', {
            src: '{{ url("shared-reports") }}/' + id + '/export',
            css: { display: 'none' }
        });

        $('body').append(iframe);

        setTimeout(function() {
            iframe.remove();
        }, 5000);
    });
});
</script>
