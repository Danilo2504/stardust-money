<div class="modal fade" id="expenseModal" tabindex="-1" aria-labelledby="expenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="expenseModalLabel">Registrar gasto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <livewire:expenses.expense-form />
            </div>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            (function () {
                const handler = () => {
                    const el = document.getElementById('expenseModal');
                    if (! el || ! window.bootstrap) return;
                    const instance = window.bootstrap.Modal.getInstance(el);
                    if (instance) instance.hide();
                };
                window.addEventListener('expense-created', handler);
            })();
        </script>
    @endpush
@endonce
