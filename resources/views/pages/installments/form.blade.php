<div class="modal fade" id="installmentGroupModal" tabindex="-1" aria-labelledby="installmentGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="installmentGroupModalLabel">
                    <i class="fas fa-layer-group me-2 text-primary"></i>
                    <span id="installmentGroupModalTitle">Nuevo grupo de cuotas</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <livewire:installments.installment-group-form />
            </div>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            (function () {
                const handler = () => {
                    const el = document.getElementById('installmentGroupModal');
                    if (! el || ! window.bootstrap) return;
                    const instance = window.bootstrap.Modal.getInstance(el);
                    if (instance) instance.hide();
                };
                window.addEventListener('installment-group-saved', handler);

                const modalEl = document.getElementById('installmentGroupModal');
                modalEl.addEventListener('hidden.bs.modal', function () {
                    document.getElementById('installmentGroupModalTitle').textContent = 'Nuevo grupo de cuotas';
                    window.dispatchEvent(new Event('reset-installment-group-form'));
                });
            })();
        </script>
    @endpush
@endonce
