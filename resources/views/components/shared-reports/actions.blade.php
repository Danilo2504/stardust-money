<div class="d-flex justify-content-end gap-2">
    <button class="btn btn-sm btn-outline-secondary copy-report-url"
            data-url="{{ $report->url }}"
            title="Copiar link">
        <i class="fas fa-clipboard"></i>
    </button>
    <button class="btn btn-sm btn-outline-success export-report-csv"
            data-id="{{ $report->id }}"
            title="Exportar CSV">
        <i class="fa-solid fa-file-csv"></i>
    </button>
    <button class="btn btn-sm btn-outline-primary edit-shared-report"
            data-id="{{ $report->id }}"
            title="Editar">
        <i class="fas fa-edit"></i>
    </button>
    <button class="btn btn-sm btn-outline-danger delete-shared-report"
            data-id="{{ $report->id }}"
            title="Eliminar">
        <i class="fas fa-trash-alt"></i>
    </button>
</div>
