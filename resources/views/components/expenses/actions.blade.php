<div class="d-flex justify-content-end gap-2">
    @if($expense->draft)
        <button class="btn btn-sm btn-success confirm-draft"
                data-id="{{ $expense->id }}"
                title="Confirmar">
            <i class="fas fa-check"></i>
        </button>
    @endif

    <button class="btn btn-sm btn-outline-primary edit-expense"
            data-id="{{ $expense->id }}"
            title="Editar">
        <i class="fas fa-edit"></i>
    </button>

    <button class="btn btn-sm btn-outline-danger delete-expense"
            data-id="{{ $expense->id }}"
            title="Eliminar">
        <i class="fas fa-trash-alt"></i>
    </button>
</div>
