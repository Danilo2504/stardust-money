<div class="d-flex justify-content-end gap-2">
    @if(! $category->is_default && $category->user_id === auth()->id())
        <button class="btn btn-sm btn-outline-primary edit-category"
                data-id="{{ $category->id }}"
                data-name="{{ $category->name }}"
                data-color="{{ $category->color }}"
                title="Editar">
            <i class="fas fa-edit"></i>
        </button>
        <button class="btn btn-sm btn-outline-danger delete-category"
                data-id="{{ $category->id }}"
                title="Eliminar">
            <i class="fas fa-trash-alt"></i>
        </button>
    @else
        <span class="text-muted small">No editable</span>
    @endif
</div>
