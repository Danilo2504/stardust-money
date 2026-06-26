<?php

use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\Category;
use App\Models\Expense;
use App\Models\InstallmentGroup;
use App\Models\RecurringExpense;

new class extends Component
{
    public string $description = '';
    public string $type = 'one_time';
    public string $amount = '';
    public ?string $category_id = null;
    public string $expense_date = '';
    public string $notes = '';

    public ?string $installment_group_id = null;
    public ?int $installment_number = null;
    public ?string $recurring_expense_id = null;

    public array $splits = [];
    public bool $showSuccess = false;
    public ?string $expenseId = null;

    public function mount(): void
    {
        $this->expense_date = now()->format('Y-m-d');
    }

    public function addSplit(): void
    {
        $this->splits[] = [
            'person_name' => '',
            'amount' => null,
        ];
    }

    public function removeSplit(int $index): void
    {
        unset($this->splits[$index]);
        $this->splits = array_values($this->splits);
    }

    public function rules(): array
    {
        return [
            'description'          => 'required',
            'amount'               => 'required|numeric|gt:0|max:9999.9999',
            'category_id'          => 'nullable|exists:categories,id',
            'expense_date'         => 'required|date',
            'type'                 => 'required|in:one_time,recurring_child,installment',
            'notes'                => 'nullable',
            'installment_group_id' => $this->type === 'installment' ? 'required' : 'nullable',
            'installment_number'   => $this->type === 'installment' ? 'required|integer' : 'nullable',
            'recurring_expense_id' => $this->type === 'recurring_child' ? 'required' : 'nullable',
            'splits'               => 'array',
            'splits.*.person_name' => 'required_with:splits.*.amount|string',
            'splits.*.amount'      => 'nullable|numeric|min:0',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $totalSplits = collect($this->splits)->sum('amount');

        if ($totalSplits > $this->amount) {
            $this->addError('splits', 'La suma de las partes (€' . $totalSplits . ') no puede superar el total del gasto (€' . $this->amount . ').');
            return;
        }

        if ($this->expenseId) {
            $expense = Expense::findOrFail($this->expenseId);
            $this->ensureOwned($expense);

            $expense->update([
                'description'          => $this->description,
                'amount'               => $this->amount,
                'category_id'          => $this->category_id,
                'expense_date'         => $this->expense_date,
                'notes'                => $this->notes,
                'type'                 => $this->type,
                'installment_group_id' => $this->installment_group_id,
                'recurring_expense_id' => $this->recurring_expense_id,
                'installment_number'   => $this->installment_number,
            ]);

            $expense->splits()->delete();
        } else {
            $expense = Expense::create([
                'user_id'              => auth()->id(),
                'code'                 => (new Expense)->generateCode(),
                'description'          => $this->description,
                'amount'               => $this->amount,
                'category_id'          => $this->category_id,
                'expense_date'         => $this->expense_date,
                'notes'                => $this->notes,
                'type'                 => $this->type,
                'installment_group_id' => $this->installment_group_id,
                'recurring_expense_id' => $this->recurring_expense_id,
                'installment_number'   => $this->installment_number,
                'draft'                => false,
            ]);
        }

        foreach ($this->splits as $split) {
            if (blank($split['person_name']) && blank($split['amount'])) {
                continue;
            }

            $expense->splits()->create([
                'person_name' => $split['person_name'],
                'amount'      => $split['amount'],
                'user_id'     => auth()->id(),
            ]);
        }

        $this->resetForm();
        $this->showSuccess = true;
        $this->dispatch('expense-saved');

        $this->js("setTimeout(() => { \$wire.set('showSuccess', false) }, 2500)");
    }

    #[On('edit-expense')]
    public function edit(string $id): void
    {
        $expense = Expense::with('splits')->findOrFail($id);
        $this->ensureOwned($expense);

        $this->expenseId = $expense->id;
        $this->description = $expense->description;
        $this->amount = (string) $expense->amount;
        $this->category_id = $expense->category_id;
        $this->expense_date = $expense->expense_date?->format('Y-m-d') ?? now()->format('Y-m-d');
        $this->notes = $expense->notes ?? '';
        $this->type = $expense->type;
        $this->installment_group_id = $expense->installment_group_id;
        $this->installment_number = $expense->installment_number;
        $this->recurring_expense_id = $expense->recurring_expense_id;
        $this->splits = $expense->splits->map(fn ($split) => [
            'person_name' => $split->person_name,
            'amount'      => $split->amount,
        ])->toArray();
    }

    private function ensureOwned(Expense $expense): void
    {
        if ($expense->user_id !== auth()->id()) {
            abort(403, 'No puedes gestionar este gasto.');
        }
    }

    #[On('reset-expense-form')]
    public function resetForm(): void
    {
        $this->reset([
            'description',
            'amount',
            'category_id',
            'notes',
            'type',
            'installment_group_id',
            'installment_number',
            'recurring_expense_id',
            'splits',
            'expenseId',
        ]);
        $this->type = 'one_time';
        $this->expense_date = now()->format('Y-m-d');
        $this->resetValidation();
    }

    #[Computed]
    public function types()
    {
        return Expense::types();
    }

    #[Computed]
    #[On('categories-updated')]
    public function categories()
    {
        return Category::where('user_id', auth()->id())
            ->orWhere('is_default', true)
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function installmentGroups()
    {
        return InstallmentGroup::byAuthor(auth()->id())->get();
    }

    #[Computed]
    public function recurringExpenses()
    {
        return RecurringExpense::byAuthor(auth()->id())
            ->where('is_active', true)
            ->get();
    }
};
?>

<form wire:submit="save" x-data="{ type: @js($type) }">
    @if($showSuccess)
        <div class="alert alert-success d-flex align-items-center mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <div>{{ $expenseId ? 'Gasto actualizado correctamente.' : 'Gasto guardado correctamente.' }}</div>
        </div>
    @endif

    <div class="form-section mb-3">
        <h6 class="form-section-title"><i class="bi bi-card-text me-2"></i>Información general</h6>
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label" for="description">Descripción</label>
                <input type="text"
                       id="description"
                       wire:model="description"
                       class="form-control @error('description') is-invalid @enderror"
                       placeholder="Ej: Compra supermercado"
                       required>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label" for="amount">Monto (€)</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-currency-euro"></i></span>
                    <input type="number"
                           id="amount"
                           wire:model="amount"
                           step="0.01"
                           min="0.01"
                           max="9999.9999"
                           class="form-control @error('amount') is-invalid @enderror"
                           placeholder="0,00"
                           required>
                </div>
                @error('amount')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label" for="expense_date">Fecha</label>
                <input type="date"
                       id="expense_date"
                       wire:model="expense_date"
                       class="form-control @error('expense_date') is-invalid @enderror"
                       required>
                @error('expense_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="form-section mb-3">
        <h6 class="form-section-title"><i class="bi bi-tags me-2"></i>Clasificación</h6>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="type">Tipo de gasto</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-collection"></i></span>
                    <select id="type"
                            wire:model="type"
                            x-model="type"
                            class="form-select @error('type') is-invalid @enderror"
                            required>
                        @foreach($this->types as $type)
                            <option value="{{ $type->value }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label" for="category_id">Categoría</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-folder"></i></span>
                    <select id="category_id"
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

    <div class="form-section mb-3"
         x-show="type === 'recurring_child'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-1"
         style="display: none;">
        <h6 class="form-section-title"><i class="bi bi-arrow-repeat me-2"></i>Recurrencia</h6>
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label" for="recurring_expense_id">Gasto recurrente</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-calendar-week"></i></span>
                    <select id="recurring_expense_id"
                            wire:model="recurring_expense_id"
                            class="form-select @error('recurring_expense_id') is-invalid @enderror">
                        <option value="">Ninguno</option>
                        @foreach($this->recurringExpenses as $recurringExpense)
                            <option value="{{ $recurringExpense->id }}">{{ $recurringExpense->description }}</option>
                        @endforeach
                    </select>
                </div>
                @error('recurring_expense_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="form-section mb-3"
         x-show="type === 'installment'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-1"
         style="display: none;">
        <h6 class="form-section-title"><i class="bi bi-layers me-2"></i>Cuotas</h6>
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label" for="installment_group_id">Grupo de cuotas</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-folder-symlink"></i></span>
                    <select id="installment_group_id"
                            wire:model="installment_group_id"
                            class="form-select @error('installment_group_id') is-invalid @enderror">
                        <option value="">Ninguno</option>
                        @foreach($this->installmentGroups as $group)
                            <option value="{{ $group->id }}">{{ $group->description }}</option>
                        @endforeach
                    </select>
                </div>
                @error('installment_group_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="installment_number">Número de cuota</label>
                <input type="number"
                       id="installment_number"
                       wire:model="installment_number"
                       step="1"
                       min="1"
                       class="form-control @error('installment_number') is-invalid @enderror"
                       placeholder="Ej: 3">
                @error('installment_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="form-section mb-3">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h6 class="form-section-title mb-0"><i class="bi bi-people me-2"></i>Gasto compartido</h6>
            <button type="button" wire:click="addSplit" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-plus-lg me-1"></i> Añadir persona
            </button>
        </div>

        @error('splits')
            <div class="alert alert-danger py-2 small mb-2">{{ $message }}</div>
        @enderror

        <div class="d-flex align-items-center gap-2 small text-muted mb-2">
            <i class="bi bi-pie-chart"></i>
            <span>Total asignado:</span>
            <span class="fw-semibold">€{{ number_format(collect($splits)->sum('amount'), 2, ',', '.') }}</span>
            <span>/</span>
            <span>€{{ number_format((float) $amount, 2, ',', '.') }}</span>
        </div>

        <div class="d-flex flex-column gap-2">
            @foreach($splits as $index => $split)
                <div class="card-custom p-2"
                     wire:key="split-{{ $index }}">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label small">Persona</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text"
                                       class="form-control"
                                       wire:model="splits.{{ $index }}.person_name"
                                       placeholder="Ej: Juan">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small">Parte (€)</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">€</span>
                                <input type="number"
                                       step="0.01"
                                       class="form-control"
                                       wire:model.live="splits.{{ $index }}.amount"
                                       placeholder="0,00">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="button"
                                    wire:click="removeSplit({{ $index }})"
                                    class="btn btn-sm btn-outline-danger w-100">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="form-section mb-3">
        <h6 class="form-section-title"><i class="bi bi-sticky me-2"></i>Notas</h6>
        <textarea id="notes"
                  wire:model="notes"
                  class="form-control @error('notes') is-invalid @enderror"
                  rows="2"
                  placeholder="Opcional..."></textarea>
        @error('notes')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            Cancelar
        </button>
        <button type="submit" class="btn btn-accent" wire:loading.attr="disabled">
            <i class="bi bi-check-lg me-1"></i>
            <span>{{ $expenseId ? 'Guardar cambios' : 'Guardar gasto' }}</span>
            <span wire:loading class="spinner-border spinner-border-sm ms-1" role="status" aria-hidden="true"></span>
        </button>
    </div>
</form>
