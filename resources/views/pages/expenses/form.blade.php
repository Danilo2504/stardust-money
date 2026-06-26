<div class="modal fade" id="expenseModal" tabindex="-1" aria-labelledby="expenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="expenseModalLabel">
                    <i class="bi bi-plus-circle me-2 text-primary"></i>
                    <span id="expenseModalTitle">Registrar gasto</span>
                </h5>
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
                    window.addEventListener('expense-saved', handler);

                    const modalEl = document.getElementById('expenseModal');
                    modalEl.addEventListener('hidden.bs.modal', function () {
                        document.getElementById('expenseModalTitle').textContent = 'Registrar gasto';
                        window.dispatchEvent(new Event('reset-expense-form'));
                    });
                })();
            </script>
        @endpush
    @endonce

