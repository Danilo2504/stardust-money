<div class="modal fade" id="sharedReportModal" tabindex="-1" aria-labelledby="sharedReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sharedReportModalLabel">
                    <i class="bi bi-link-45deg me-2 text-primary"></i>
                    <span id="sharedReportModalTitle">Nuevo reporte compartido</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <livewire:shared-reports.shared-report-form />
            </div>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            (function () {
                const handler = () => {
                    const el = document.getElementById('sharedReportModal');
                    if (! el || ! window.bootstrap) return;
                    const instance = window.bootstrap.Modal.getInstance(el);
                    if (instance) instance.hide();
                };
                window.addEventListener('shared-report-saved', handler);

                const modalEl = document.getElementById('sharedReportModal');
                modalEl.addEventListener('hidden.bs.modal', function () {
                    document.getElementById('sharedReportModalTitle').textContent = 'Nuevo reporte compartido';
                    window.dispatchEvent(new Event('reset-shared-report-form'));
                });
            })();
        </script>
    @endpush
@endonce
