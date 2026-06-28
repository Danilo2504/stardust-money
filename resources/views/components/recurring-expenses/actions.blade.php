<div class="d-flex justify-content-end gap-2">
    <button class="btn btn-sm btn-outline-primary edit-recurring-expense"
            data-id="{{ $recurring->id }}"
            title="Editar">
        <i class="fas fa-edit"></i>
    </button>
    <button class="btn btn-sm btn-outline-danger delete-recurring-expense"
            data-id="{{ $recurring->id }}"
            title="Eliminar">
        <i class="fas fa-trash-alt"></i>
    </button>
</div>
