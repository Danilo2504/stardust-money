<div class="d-flex justify-content-end gap-2">
    @if(! $category->is_default && $category->user_id === auth()->id())
        <button class="btn btn-sm btn-outline-primary edit-category"
                data-id="{{ $category->id }}"
                data-name="{{ $category->name }}"
                data-color="{{ $category->color }}"
                title="Editar">
            <i class="bi bi-pencil"></i>
        </button>
        <button class="btn btn-sm btn-outline-danger delete-category"
                data-id="{{ $category->id }}"
                title="Eliminar">
            <i class="bi bi-trash"></i>
        </button>
    @else
        <span class="text-muted small">No editable</span>
    @endif
</div>
