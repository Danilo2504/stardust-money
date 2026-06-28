<div class="modal fade" id="recurringExpenseModal" tabindex="-1" aria-labelledby="recurringExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="recurringExpenseModalLabel">
                    <i class="fas fa-sync-alt me-2 text-primary"></i>
                    <span id="recurringExpenseModalTitle">Nueva plantilla</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <livewire:recurring-expenses.recurring-expense-form />
            </div>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            (function () {
                const handler = () => {
                    const el = document.getElementById('recurringExpenseModal');
                    if (! el || ! window.bootstrap) return;
                    const instance = window.bootstrap.Modal.getInstance(el);
                    if (instance) instance.hide();
                };
                window.addEventListener('recurring-expense-saved', handler);

                const modalEl = document.getElementById('recurringExpenseModal');
                modalEl.addEventListener('hidden.bs.modal', function () {
                    document.getElementById('recurringExpenseModalTitle').textContent = 'Nueva plantilla';
                    window.dispatchEvent(new Event('reset-recurring-expense-form'));
                });
            })();
        </script>
    @endpush
@endonce
