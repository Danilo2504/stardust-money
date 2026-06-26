<?php

use App\Models\Category;
use App\Models\Expense;
use App\Models\SharedReport;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public ?string $sharedReportId = null;
    public string $label = '';
    public ?string $filterType = null;
    public ?string $filterCategoryId = null;
    public ?string $filterDateFrom = null;
    public ?string $filterDateTo = null;
    public ?string $expiresAt = null;

    public function rules(): array
    {
        return [
            'label'            => 'required|string|max:255',
            'filterType'       => 'nullable|in:one_time,recurring_child,installment',
            'filterCategoryId' => 'nullable|exists:categories,id',
            'filterDateFrom'   => 'nullable|date',
            'filterDateTo'     => 'nullable|date|after_or_equal:filterDateFrom',
            'expiresAt'        => 'nullable|date|after_or_equal:today',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $filters = array_filter([
            'type'        => $this->filterType,
            'category_id' => $this->filterCategoryId,
            'date_from'   => $this->filterDateFrom,
            'date_to'     => $this->filterDateTo,
        ], fn ($value) => $value !== null && $value !== '');

        $data = [
            'label'      => $this->label,
            'filters'    => $filters,
            'expires_at' => $this->expiresAt,
        ];

        if ($this->sharedReportId) {
            $report = SharedReport::findOrFail($this->sharedReportId);
            $this->ensureOwned($report);
            $report->update($data);
        } else {
            SharedReport::create([
                ...$data,
                'user_id' => auth()->id(),
                'token'   => bin2hex(random_bytes(32)),
            ]);
        }

        $this->resetForm();
        $this->dispatch('shared-report-saved');
    }

    #[On('edit-shared-report')]
    public function edit(string $id): void
    {
        $report = SharedReport::findOrFail($id);
        $this->ensureOwned($report);

        $this->sharedReportId = $report->id;
        $this->label = $report->label;
        $this->filterType = $report->filters['type'] ?? null;
        $this->filterCategoryId = $report->filters['category_id'] ?? null;
        $this->filterDateFrom = $report->filters['date_from'] ?? null;
        $this->filterDateTo = $report->filters['date_to'] ?? null;
        $this->expiresAt = $report->expires_at?->format('Y-m-d');
    }

    #[On('reset-shared-report-form')]
    public function resetForm(): void
    {
        $this->reset([
            'sharedReportId',
            'label',
            'filterType',
            'filterCategoryId',
            'filterDateFrom',
            'filterDateTo',
            'expiresAt',
        ]);
        $this->resetValidation();
    }

    private function ensureOwned(SharedReport $report): void
    {
        if ($report->user_id !== auth()->id()) {
            abort(403, 'No puedes gestionar este reporte.');
        }
    }

    #[Computed]
    public function categories()
    {
        return Category::where('user_id', auth()->id())
            ->orWhere('is_default', true)
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function types()
    {
        return Expense::types();
    }
};
?>

<form wire:submit="save">
    <div class="form-section mb-3">
        <h6 class="form-section-title"><i class="bi bi-card-text me-2"></i>Información</h6>
        <div class="mb-3">
            <label class="form-label" for="report-label">Etiqueta</label>
            <input type="text"
                   id="report-label"
                   wire:model="label"
                   class="form-control @error('label') is-invalid @enderror"
                   placeholder="Ej: Clases de violín"
                   required>
            @error('label')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-0">
            <label class="form-label" for="report-expires">Fecha de expiración (opcional)</label>
            <input type="date"
                   id="report-expires"
                   wire:model="expiresAt"
                   class="form-control @error('expiresAt') is-invalid @enderror">
            @error('expiresAt')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="form-section mb-3">
        <h6 class="form-section-title"><i class="bi bi-funnel me-2"></i>Filtros del reporte</h6>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="report-type">Tipo</label>
                <select id="report-type"
                        wire:model="filterType"
                        class="form-select @error('filterType') is-invalid @enderror">
                    <option value="">Todos</option>
                    @foreach($this->types as $type)
                        <option value="{{ $type->value }}">{{ $type->name }}</option>
                    @endforeach
                </select>
                @error('filterType')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label" for="report-category">Categoría</label>
                <select id="report-category"
                        wire:model="filterCategoryId"
                        class="form-select @error('filterCategoryId') is-invalid @enderror">
                    <option value="">Todas</option>
                    @foreach($this->categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('filterCategoryId')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label" for="report-date-from">Desde</label>
                <input type="date"
                       id="report-date-from"
                       wire:model="filterDateFrom"
                       class="form-control @error('filterDateFrom') is-invalid @enderror">
                @error('filterDateFrom')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label" for="report-date-to">Hasta</label>
                <input type="date"
                       id="report-date-to"
                       wire:model="filterDateTo"
                       class="form-control @error('filterDateTo') is-invalid @enderror">
                @error('filterDateTo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            Cancelar
        </button>
        <button type="submit" class="btn btn-accent" wire:loading.attr="disabled">
            <i class="bi bi-check-lg me-1"></i>
            <span>{{ $sharedReportId ? 'Guardar cambios' : 'Crear reporte' }}</span>
            <span wire:loading class="spinner-border spinner-border-sm ms-1" role="status" aria-hidden="true"></span>
        </button>
    </div>
</form>
