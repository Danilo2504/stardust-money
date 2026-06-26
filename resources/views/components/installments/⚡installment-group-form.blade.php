<?php

use App\Models\InstallmentGroup;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public ?string $installmentGroupId = null;
    public string $description = '';
    public string $total_amount = '';
    public int $total_installments = 12;

    public function rules(): array
    {
        return [
            'description'        => 'required|string|max:255',
            'total_amount'       => 'required|numeric|gt:0|max:9999.9999',
            'total_installments' => 'required|integer|min:2',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'description'        => $this->description,
            'total_amount'       => $this->total_amount,
            'total_installments' => $this->total_installments,
        ];

        if ($this->installmentGroupId) {
            $group = InstallmentGroup::findOrFail($this->installmentGroupId);
            $this->ensureOwned($group);
            $group->update($data);
        } else {
            InstallmentGroup::create([
                ...$data,
                'user_id' => auth()->id(),
            ]);
        }

        $this->resetForm();
        $this->dispatch('installment-group-saved');
    }

    #[On('edit-installment-group')]
    public function edit(string $id): void
    {
        $group = InstallmentGroup::findOrFail($id);
        $this->ensureOwned($group);

        $this->installmentGroupId = $group->id;
        $this->description = $group->description;
        $this->total_amount = (string) $group->total_amount;
        $this->total_installments = $group->total_installments;
    }

    #[On('reset-installment-group-form')]
    public function resetForm(): void
    {
        $this->reset([
            'installmentGroupId',
            'description',
            'total_amount',
            'total_installments',
        ]);
        $this->total_installments = 12;
        $this->resetValidation();
    }

    private function ensureOwned(InstallmentGroup $group): void
    {
        if ($group->user_id !== auth()->id()) {
            abort(403, 'No puedes gestionar este grupo.');
        }
    }
};
?>

<form wire:submit="save">
    <div class="form-section mb-3">
        <h6 class="form-section-title"><i class="bi bi-card-text me-2"></i>Información general</h6>
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label" for="installment-description">Descripción</label>
                <input type="text"
                       id="installment-description"
                       wire:model="description"
                       class="form-control @error('description') is-invalid @enderror"
                       placeholder="Ej: Notebook"
                       required>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label" for="installment-total">Monto total (€)</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-currency-euro"></i></span>
                    <input type="number"
                           id="installment-total"
                           wire:model="total_amount"
                           step="0.01"
                           min="0.01"
                           class="form-control @error('total_amount') is-invalid @enderror"
                           placeholder="0,00"
                           required>
                </div>
                @error('total_amount')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label" for="installment-count">Cantidad de cuotas</label>
                <input type="number"
                       id="installment-count"
                       wire:model="total_installments"
                       step="1"
                       min="2"
                       class="form-control @error('total_installments') is-invalid @enderror"
                       required>
                @error('total_installments')
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
            <span>{{ $installmentGroupId ? 'Guardar cambios' : 'Crear grupo' }}</span>
            <span wire:loading class="spinner-border spinner-border-sm ms-1" role="status" aria-hidden="true"></span>
        </button>
    </div>
</form>
