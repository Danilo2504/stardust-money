<div class="d-flex justify-content-end gap-2">
    <button class="btn btn-sm btn-outline-primary edit-recurring-expense"
            data-id="{{ $recurring->id }}"
            title="Editar">
        <i class="bi bi-pencil"></i>
    </button>
    <button class="btn btn-sm btn-outline-danger delete-recurring-expense"
            data-id="{{ $recurring->id }}"
            title="Eliminar">
        <i class="bi bi-trash"></i>
    </button>
</div>
