<div class="d-flex gap-1">
    <button class="btn btn-sm btn-outline-primary edit-expense"
            data-id="{{ $expense->id }}"
            title="Editar">
        <i class="bi bi-pencil"></i>
    </button>
    <button class="btn btn-sm btn-outline-danger delete-expense"
            data-id="{{ $expense->id }}"
            title="Eliminar">
        <i class="bi bi-trash"></i>
    </button>
</div>
