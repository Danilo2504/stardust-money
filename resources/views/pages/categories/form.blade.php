<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalLabel">
                    <i class="bi bi-folder-plus me-2 text-primary"></i>
                    <span id="categoryModalTitle">Nueva categoría</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <livewire:categories.category-form />
            </div>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            (function () {
                const handler = () => {
                    const el = document.getElementById('categoryModal');
                    if (! el || ! window.bootstrap) return;
                    const instance = window.bootstrap.Modal.getInstance(el);
                    if (instance) instance.hide();
                };
                window.addEventListener('category-saved', handler);
            })();
        </script>
    @endpush
@endonce
