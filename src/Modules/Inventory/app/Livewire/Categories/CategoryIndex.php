<?php

namespace Modules\Inventory\Livewire\Categories;

use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Inventory\Models\Category;
use Modules\Inventory\Services\CategoryService;

#[Layout('layouts.app')]
class CategoryIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public int $perPage = 10;

    public ?int $selectedCategoryId = null;

    public string $code = '';

    public string $name = '';

    public ?string $description = '';

    public bool $is_active = true;

    public string $deletingCategoryName = '';

    /**
     * Reset pagination when search query updates.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when status filter updates.
     */
    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when items per page updates.
     */
    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    /**
     * Validation rules for category form.
     *
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('categories', 'code')
                    ->ignore($this->selectedCategoryId)
                    ->whereNull('deleted_at'),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Custom validation attributes for localized error messages.
     *
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'code'        => __t('field_code', 'inventory'),
            'name'        => __t('field_name', 'inventory'),
            'description' => __t('field_description', 'inventory'),
            'is_active'   => __t('field_is_active', 'inventory'),
        ];
    }

    /**
     * Open modal for creating a new category.
     */
    public function openCreateModal(): void
    {
        $this->authorize('create', Category::class);

        $this->resetErrorBag();
        $this->reset(['selectedCategoryId', 'code', 'name', 'description']);
        $this->is_active = true;

        $this->dispatch('open-modal', 'category-form');
    }

    /**
     * Open modal for editing an existing category.
     */
    public function openEditModal(int $id, CategoryService $service): void
    {
        $category = $service->find($id);

        $this->authorize('update', $category);

        $this->resetErrorBag();
        $this->selectedCategoryId = $category->id;
        $this->code = $category->code;
        $this->name = $category->name;
        $this->description = $category->description ?? '';
        $this->is_active = (bool) $category->is_active;

        $this->dispatch('open-modal', 'category-form');
    }

    /**
     * Open confirmation modal for deleting a category.
     */
    public function openDeleteModal(int $id, CategoryService $service): void
    {
        $category = $service->find($id);

        $this->authorize('delete', $category);

        $this->selectedCategoryId = $category->id;
        $this->deletingCategoryName = $category->name;

        $this->dispatch('open-modal', 'delete-category');
    }

    /**
     * Save category (create or update).
     */
    public function save(CategoryService $service): void
    {
        $validated = $this->validate();

        if ($this->selectedCategoryId) {
            $category = $service->find($this->selectedCategoryId);
            $this->authorize('update', $category);

            $service->update($category, $validated);
            $message = __t('category_updated', 'inventory');
        } else {
            $this->authorize('create', Category::class);

            $service->create($validated);
            $message = __t('category_created', 'inventory');
        }

        $this->dispatch('close-modal', 'category-form');
        $this->dispatch('toast', [
            'type'    => 'success',
            'message' => $message,
        ]);

        $this->reset(['selectedCategoryId', 'code', 'name', 'description', 'is_active']);
    }

    /**
     * Delete the selected category.
     */
    public function delete(CategoryService $service): void
    {
        if (! $this->selectedCategoryId) {
            return;
        }

        $category = $service->find($this->selectedCategoryId);
        $this->authorize('delete', $category);

        $service->delete($category);

        $this->dispatch('close-modal', 'delete-category');
        $this->dispatch('toast', [
            'type'    => 'success',
            'message' => __t('category_deleted', 'inventory'),
        ]);

        $this->reset(['selectedCategoryId', 'deletingCategoryName']);
    }

    /**
     * Quick toggle for category status.
     */
    public function toggleStatus(int $id, CategoryService $service): void
    {
        $category = $service->find($id);
        $this->authorize('update', $category);

        $service->toggleStatus($category);

        $this->dispatch('toast', [
            'type'    => 'success',
            'message' => __t('category_status_updated', 'inventory'),
        ]);
    }

    /**
     * Render the Livewire component.
     */
    public function render(): View
    {
        $this->authorize('viewAny', Category::class);

        $query = Category::query()->when($this->search, function ($query) {
            $query->where('name', 'like', "%{$this->search}%")
                ->orWhere('code', 'like', "%{$this->search}%");
        });

        if ($this->statusFilter === 'active') {
            $query->where('is_active', true);
        } elseif ($this->statusFilter === 'inactive') {
            $query->where('is_active', false);
        }

        $categories = $query->latest('id')->paginate($this->perPage);

        $totalCategories = Category::count();
        $activeCategories = Category::where('is_active', true)->count();
        $inactiveCategories = Category::where('is_active', false)->count();

        return view('inventory::livewire.categories.category-index', [
            'categories'         => $categories,
            'totalCategories'    => $totalCategories,
            'activeCategories'   => $activeCategories,
            'inactiveCategories' => $inactiveCategories,
        ]);
    }
}
