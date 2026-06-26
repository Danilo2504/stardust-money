<div class="d-flex justify-content-end gap-2">
    <button class="btn btn-sm btn-outline-secondary copy-report-url"
            data-url="{{ $report->url }}"
            title="Copiar link">
        <i class="bi bi-clipboard"></i>
    </button>
    <button class="btn btn-sm btn-outline-primary edit-shared-report"
            data-id="{{ $report->id }}"
            title="Editar">
        <i class="bi bi-pencil"></i>
    </button>
    <button class="btn btn-sm btn-outline-danger delete-shared-report"
            data-id="{{ $report->id }}"
            title="Eliminar">
        <i class="bi bi-trash"></i>
    </button>
</div>
