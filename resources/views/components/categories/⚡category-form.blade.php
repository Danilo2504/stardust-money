<?php

use App\Models\Category;
use App\Rules\HexColor;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public ?string $categoryId = null;
    public string $name = '';
    public string $color = '#2563eb';

    protected function rules(): array
    {
        return [
            'name'  => 'required|string|max:255',
            'color' => ['nullable', new HexColor(allowAlpha: true)],
        ];
    }

    public function mount(): void
    {
        $this->resetForm();
    }

    public function save(): void
    {
        $this->validate();

        if ($this->categoryId) {
            $category = Category::findOrFail($this->categoryId);
            $this->authorizeModification($category);
            $category->update([
                'name'  => $this->name,
                'color' => $this->color,
            ]);
        } else {
            Category::create([
                'user_id' => auth()->id(),
                'name'    => $this->name,
                'color'   => $this->color,
            ]);
        }

        $this->resetForm();
        $this->dispatch('category-saved');
        $this->dispatch('categories-updated');
    }

    #[On('load-category')]
    public function loadCategory(string $id): void
    {
        $category = Category::findOrFail($id);
        $this->authorizeModification($category);

        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->color = $category->color ?? '#2563eb';
    }

    #[On('reset-category-form')]
    public function resetForm(): void
    {
        $this->categoryId = null;
        $this->name = '';
        $this->color = '#2563eb';
        $this->resetValidation();
    }

    private function authorizeModification(Category $category): void
    {
        if ($category->is_default || $category->user_id !== auth()->id()) {
            abort(403, 'No puedes gestionar esta categoría.');
        }
    }
};
?>

<form wire:submit="save">
    <div class="mb-3">
        <label class="form-label" for="category-name">Nombre</label>
        <input type="text"
               id="category-name"
               wire:model="name"
               class="form-control @error('name') is-invalid @enderror"
               placeholder="Ej: Transporte"
               required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label" for="category-color">Color</label>
        <div class="d-flex align-items-center gap-2">
            <input type="color"
                   id="category-color"
                   wire:model="color"
                   class="form-control form-control-color"
                   style="width: 60px; height: 42px; padding: 0.25rem;"
                   title="Elige un color">
            <input type="text"
                   wire:model="color"
                   class="form-control @error('color') is-invalid @enderror"
                   placeholder="#2563eb"
                   readonly>
        </div>
        @error('color')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            Cancelar
        </button>
        <button type="submit" class="btn btn-accent" wire:loading.attr="disabled">
            <i class="bi bi-check-lg me-1"></i>
            <span>{{ $categoryId ? 'Actualizar' : 'Crear' }}</span>
            <span wire:loading class="spinner-border spinner-border-sm ms-1" role="status" aria-hidden="true"></span>
        </button>
    </div>
</form>
