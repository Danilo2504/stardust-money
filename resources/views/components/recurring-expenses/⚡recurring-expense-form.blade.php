<?php

use App\Models\Category;
use App\Models\RecurringExpense;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public ?string $recurringExpenseId = null;
    public string $description = '';
    public string $amount = '';
    public ?string $category_id = null;
    public int $custom_interval_value = 1;
    public string $custom_interval_unit = 'months';
    public string $next_due_date = '';
    public bool $is_active = true;

    public function mount(): void
    {
        $this->next_due_date = now()->format('Y-m-d');
    }

    public function rules(): array
    {
        return [
            'description'           => 'required|string|max:255',
            'amount'                => 'required|numeric|gt:0|max:9999.9999',
            'category_id'           => 'nullable|exists:categories,id',
            'custom_interval_value' => 'required|integer|min:1',
            'custom_interval_unit'  => 'required|in:days,weeks,months,years',
            'next_due_date'         => 'required|date',
            'is_active'             => 'boolean',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'description'           => $this->description,
            'amount'                => $this->amount,
            'category_id'           => $this->category_id,
            'custom_interval_value' => $this->custom_interval_value,
            'custom_interval_unit'  => $this->custom_interval_unit,
            'next_due_date'         => $this->next_due_date,
            'is_active'             => $this->is_active,
        ];

        if ($this->recurringExpenseId) {
            $recurring = RecurringExpense::findOrFail($this->recurringExpenseId);
            $this->ensureOwned($recurring);
            $recurring->update($data);
        } else {
            RecurringExpense::create([
                ...$data,
                'user_id' => auth()->id(),
            ]);
        }

        $this->resetForm();
        $this->dispatch('recurring-expense-saved');
    }

    #[On('edit-recurring-expense')]
    public function edit(string $id): void
    {
        $recurring = RecurringExpense::findOrFail($id);
        $this->ensureOwned($recurring);

        $this->recurringExpenseId = $recurring->id;
        $this->description = $recurring->description;
        $this->amount = (string) $recurring->amount;
        $this->category_id = $recurring->category_id;
        $this->custom_interval_value = $recurring->custom_interval_value;
        $this->custom_interval_unit = $recurring->custom_interval_unit;
        $this->next_due_date = $recurring->next_due_date?->format('Y-m-d') ?? now()->format('Y-m-d');
        $this->is_active = $recurring->is_active;
    }

    #[On('reset-recurring-expense-form')]
    public function resetForm(): void
    {
        $this->reset([
            'recurringExpenseId',
            'description',
            'amount',
            'category_id',
            'custom_interval_value',
            'custom_interval_unit',
            'next_due_date',
            'is_active',
        ]);
        $this->custom_interval_value = 1;
        $this->custom_interval_unit = 'months';
        $this->next_due_date = now()->format('Y-m-d');
        $this->is_active = true;
        $this->resetValidation();
    }

    private function ensureOwned(RecurringExpense $recurring): void
    {
        if ($recurring->user_id !== auth()->id()) {
            abort(403, 'No puedes gestionar esta plantilla.');
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
};
?>

<form wire:submit="save">
    <div class="form-section mb-3">
        <h6 class="form-section-title"><i class="fas fa-file-alt me-2"></i>Información general</h6>
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label" for="recurring-description">Descripción</label>
                <input type="text"
                       id="recurring-description"
                       wire:model="description"
                       class="form-control @error('description') is-invalid @enderror"
                       placeholder="Ej: Alquiler"
                       required>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label" for="recurring-amount">Monto referencial (€)</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-euro-sign"></i></span>
                    <input type="number"
                           id="recurring-amount"
                           wire:model="amount"
                           step="0.01"
                           min="0.01"
                           class="form-control @error('amount') is-invalid @enderror"
                           placeholder="0,00"
                           required>
                </div>
                @error('amount')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label" for="recurring-category">Categoría</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-folder"></i></span>
                    <select id="recurring-category"
                            wire:model="category_id"
                            class="form-select @error('category_id') is-invalid @enderror">
                        <option value="">Seleccionar...</option>
                        @foreach($this->categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                @error('category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="form-section mb-3">
        <h6 class="form-section-title"><i class="fas fa-clock me-2"></i>Frecuencia</h6>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="recurring-interval-value">Cada</label>
                <input type="number"
                       id="recurring-interval-value"
                       wire:model="custom_interval_value"
                       step="1"
                       min="1"
                       class="form-control @error('custom_interval_value') is-invalid @enderror"
                       required>
                @error('custom_interval_value')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="recurring-interval-unit">Periodo</label>
                <select id="recurring-interval-unit"
                        wire:model="custom_interval_unit"
                        class="form-select @error('custom_interval_unit') is-invalid @enderror">
                        <option value="days">Día(s)</option>
                    <option value="weeks">Semana(s)</option>
                    <option value="months">Mes(es)</option>
                    <option value="years">Año(s)</option>

                </select>
                @error('custom_interval_unit')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="form-section mb-3">
        <h6 class="form-section-title"><i class="fas fa-calendar-alt me-2"></i>Programación</h6>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="recurring-next-due">Próxima fecha</label>
                <input type="date"
                       id="recurring-next-due"
                       wire:model="next_due_date"
                       class="form-control @error('next_due_date') is-invalid @enderror"
                       required>
                @error('next_due_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="recurring-active">Estado</label>
                <select id="recurring-active"
                        wire:model="is_active"
                        class="form-select">
                    <option value="1">Activo</option>
                    <option value="0">Pausado</option>
                </select>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            Cancelar
        </button>
        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
            <i class="fas fa-check me-1"></i>
            <span>{{ $recurringExpenseId ? 'Guardar cambios' : 'Crear plantilla' }}</span>
            <span wire:loading class="spinner-border spinner-border-sm ms-1" role="status" aria-hidden="true"></span>
        </button>
    </div>
</form>
