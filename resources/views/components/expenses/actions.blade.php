<div class="d-flex gap-1">
    <button class="btn btn-sm btn-outline-primary edit-expense"
            data-id="{{ $expense->id }}"
            title="Editar">
        <i class="bi bi-pencil"></i>
    </button>
    @if($expense->draft)
    <button class="btn btn-sm btn-outline-success confirm-expense"
            data-id="{{ $expense->id }}"
            title="Confirmar">
        <i class="bi bi-check-lg"></i>
    </button>
    @endif
    <button class="btn btn-sm btn-outline-danger delete-expense"
            data-id="{{ $expense->id }}"
            title="Eliminar">
        <i class="bi bi-trash"></i>
    </button>
</div>
