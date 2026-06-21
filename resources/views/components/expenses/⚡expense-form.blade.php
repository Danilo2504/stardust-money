<?php

use Livewire\Attributes\Computed;
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

        foreach ($this->splits as $split) {
            $expense->splits()->create([
                'person_name' => $split['person_name'],
                'amount'      => $split['amount'],
                'user_id'     => auth()->id(),
            ]);
        }

        $this->resetForm();
        $this->dispatch('expense-created');
    }

    private function resetForm(): void
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
        ]);
        $this->type = 'one_time';
        $this->expense_date = now()->format('Y-m-d');
    }

    #[Computed]
    public function types()
    {
        return Expense::types();
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

<form wire:submit="save"
    x-data="{ type: @js($type) }">
    <!-- Descripción -->
    <div class="mb-3">
        <label class="form-label">Descripción</label>
        <input type="text" wire:model="description" class="form-control @error('description') is-invalid @enderror" name="description" placeholder="Ej: Compra supermercado" required>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="row">
        <!-- Monto -->
        <div class="col-md-6 mb-3">
            <label class="form-label">Monto (€)</label>
            <div class="input-group">
                <span class="input-group-text">€</span>
                <input type="number" wire:model="amount" step="0.01"  class="form-control @error('amount') is-invalid @enderror" name="amount" placeholder="0.00" required>
                @error('amount')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Fecha -->
        <div class="col-md-6 mb-3">
            <label class="form-label">Fecha</label>
            <input type="text" wire:model="expense_date" value="{{$expense_date}}" class="form-control datepicker @error('expense_date') is-invalid @enderror" name="expense_date" placeholder="Selecciona fecha" required>
            @error('expense_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="row">
        <!-- Type -->
        <div class="col-md-12 mb-3">
            <label class="form-label">Tipo de gasto</label>
            <select class="form-select @error('type') is-invalid @enderror" name="type" wire:model="type" x-model="type">
                @foreach($this->types as $type)
                    <option value="{{ $type->value }}">{{ $type->name }}</option>
                @endforeach
            </select>
            @error('type')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="row">
        <!-- Categoría -->
        <div class="col-md-12 mb-3">
            <label class="form-label">Categoría</label>
            <select class="form-select @error('category_id') is-invalid @enderror" name="category_id" wire:model="category_id">
                <option value="">Seleccionar...</option>
                @foreach($this->categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            @error('category_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div x-show="type === 'recurring_child'"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2">
        <!-- Gasto recurrente -->
        <div class="col-md-12 mb-3">
            <label class="form-label">Gasto recurrente</label>
            <select class="form-select @error('recurring_expense_id') is-invalid @enderror" name="recurring_expense_id" wire:model="recurring_expense_id">
                <option value="">Ninguno</option>
                @foreach($this->recurringExpenses as $recurringExpense)
                    <option value="{{ $recurringExpense->id }}">{{ $recurringExpense->description }}</option>
                @endforeach
            </select>
            @error('recurring_expense_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div x-show="type === 'installment'"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2">
        <div class="row">
            <!-- Grupo de cuotas -->
            <div class="col-md-6 mb-3">
                <label class="form-label">Grupo de cuotas</label>
                <select class="form-select @error('installment_group_id') is-invalid @enderror" name="installment_group_id" wire:model="installment_group_id">
                    <option value="">Ninguno</option>
                    @foreach($this->installmentGroups as $group)
                        <option value="{{ $group->id }}">{{ $group->description }}</option>
                    @endforeach
                </select>
                @error('installment_group_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Número de cuota</label>
                <input type="number" name="installment_number" step="1" min="1" wire:model="installment_number" class="form-control @error('installment_number') is-invalid @enderror">
                @error('installment_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Splits -->
        <div class="col-md-12 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <label class="form-label mb-0">Gasto compartido &bullet; <span class="mt-2 small text-muted">Total asignado: {{ collect($splits)->sum('amount') }} €</span></label>
                <button type="button" wire:click="addSplit" class="btn btn-sm btn-outline-primary">
                    + Añadir persona
                </button>
            </div>
            @error('splits')
                <div class="alert alert-danger py-1 px-2 small mb-2">{{ $message }}</div>
            @enderror
            <div class="list-group">
                @foreach($splits as $index => $split)
                    <div class="list-group-item mb-2 rounded shadow-sm"
                        wire:key="split-{{ $index }}"
                        x-data
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-2">

                        <div class="row align-items-end">
                            <!-- Nombre -->
                            <div class="col-md-5">
                                <label class="form-label">Persona</label>
                                <input type="text"
                                    class="form-control"
                                    wire:model="splits.{{ $index }}.person_name"
                                    placeholder="Ej: Juan">
                            </div>

                            <!-- Monto -->
                            <div class="col-md-5">
                                <label class="form-label">Parte correspondiente (€)</label>
                                <div class="input-group">
                                    <span class="input-group-text">€</span>
                                    <input type="number"
                                        step="0.01"
                                        class="form-control"
                                        wire:model.live="splits.{{ $index }}.amount"
                                        placeholder="0.00">
                                </div>
                            </div>

                            <!-- Eliminar -->
                            <div class="col-md-2 text-end">
                                <button type="button"
                                        wire:click="removeSplit({{ $index }})"
                                        class="btn btn-outline-danger w-100">
                                    ✕
                                </button>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Notas -->
    <div class="mb-3">
        <label class="form-label">Notas</label>
        <textarea class="form-control @error('notes') is-invalid @enderror" name="notes" wire:model="notes" rows="2" placeholder="Opcional..."></textarea>
        @error('notes')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Botones -->
    <div class="d-flex justify-content-end">
        <button type="reset" class="btn btn-outline-secondary me-2">Cancelar</button>
        <button type="submit" class="btn btn-primary">
            Guardar Gasto
            <div wire:loading>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            </div>
        </button>
    </div>

</form>