<?php

namespace Modules\Inventory\Livewire\Products;

use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Inventory\Models\Category;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Services\ProductService;

#[Layout('layouts.app')]
class ProductIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $categoryFilter = '';

    public string $statusFilter = '';

    public ?int $selectedProductId = null;

    public string $sku = '';

    public ?string $barcode = '';

    public string $type = 'simple';

    public string $name = '';

    public ?string $description = '';

    public $cost = '0.00';

    public $price = '0.00';

    public $stock = 0;

    public $minimum_stock = 0;

    public ?string $image = '';

    public bool $is_active = true;

    /**
     * @var list<int|string>
     */
    public array $selectedCategories = [];

    public string $deletingProductName = '';

    /**
     * Reset pagination when search query updates.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when category filter updates.
     */
    public function updatingCategoryFilter(): void
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
     * Validation rules for product form.
     *
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'sku' => [
                'required',
                'string',
                'max:50',
                Rule::unique('products', 'sku')
                    ->ignore($this->selectedProductId)
                    ->whereNull('deleted_at'),
            ],
            'barcode' => ['nullable', 'string', 'max:50'],
            'type' => ['required', 'string', 'in:simple,composite'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'cost' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
            'selectedCategories' => ['array'],
            'selectedCategories.*' => ['exists:categories,id'],
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
            'sku'                 => __t('field_sku', 'inventory'),
            'barcode'             => __t('field_barcode', 'inventory'),
            'type'                => __t('field_type', 'inventory'),
            'name'                => __t('field_product_name', 'inventory'),
            'description'         => __t('field_description', 'inventory'),
            'cost'                => __t('field_cost', 'inventory'),
            'price'               => __t('field_price', 'inventory'),
            'stock'               => __t('field_stock', 'inventory'),
            'minimum_stock'       => __t('field_minimum_stock', 'inventory'),
            'image'               => __t('field_image', 'inventory'),
            'is_active'           => __t('field_is_active', 'inventory'),
            'selectedCategories'  => __t('field_categories', 'inventory'),
        ];
    }

    /**
     * Open modal for creating a new product.
     */
    public function openCreateModal(): void
    {
        $this->authorize('create', Product::class);

        $this->resetErrorBag();
        $this->reset([
            'selectedProductId',
            'sku',
            'barcode',
            'name',
            'description',
            'image',
            'selectedCategories',
        ]);
        $this->type = 'simple';
        $this->cost = '0.00';
        $this->price = '0.00';
        $this->stock = 0;
        $this->minimum_stock = 0;
        $this->is_active = true;

        $this->dispatch('open-modal', 'product-form');
    }

    /**
     * Open modal for editing an existing product.
     */
    public function openEditModal(int $id, ProductService $service): void
    {
        $product = $service->find($id);

        $this->authorize('update', $product);

        $this->resetErrorBag();
        $this->selectedProductId = $product->id;
        $this->sku = $product->sku;
        $this->barcode = $product->barcode ?? '';
        $this->type = $product->type;
        $this->name = $product->name;
        $this->description = $product->description ?? '';
        $this->cost = (string) $product->cost;
        $this->price = (string) $product->price;
        $this->stock = (int) $product->stock;
        $this->minimum_stock = (int) $product->minimum_stock;
        $this->image = $product->image ?? '';
        $this->is_active = (bool) $product->is_active;
        $this->selectedCategories = $product->categories->pluck('id')->map(fn($id) => (int)$id)->toArray();

        $this->dispatch('open-modal', 'product-form');
    }

    /**
     * Open confirmation modal for deleting a product.
     */
    public function openDeleteModal(int $id, ProductService $service): void
    {
        $product = $service->find($id);

        $this->authorize('delete', $product);

        $this->selectedProductId = $product->id;
        $this->deletingProductName = $product->name;

        $this->dispatch('open-modal', 'delete-product');
    }

    /**
     * Save product (create or update).
     */
    public function save(ProductService $service): void
    {
        $validated = $this->validate();
        $categoryIds = array_map('intval', $this->selectedCategories);

        if ($this->selectedProductId) {
            $product = $service->find($this->selectedProductId);
            $this->authorize('update', $product);

            $service->update($product, $validated, $categoryIds);
            $message = __t('product_updated', 'inventory');
        } else {
            $this->authorize('create', Product::class);

            $service->create($validated, $categoryIds);
            $message = __t('product_created', 'inventory');
        }

        $this->dispatch('close-modal', 'product-form');
        $this->dispatch('toast', [
            'type'    => 'success',
            'message' => $message,
        ]);

        $this->reset([
            'selectedProductId',
            'sku',
            'barcode',
            'type',
            'name',
            'description',
            'cost',
            'price',
            'stock',
            'minimum_stock',
            'image',
            'is_active',
            'selectedCategories',
        ]);
    }

    /**
     * Delete the selected product.
     */
    public function delete(ProductService $service): void
    {
        if (! $this->selectedProductId) {
            return;
        }

        $product = $service->find($this->selectedProductId);
        $this->authorize('delete', $product);

        $service->delete($product);

        $this->dispatch('close-modal', 'delete-product');
        $this->dispatch('toast', [
            'type'    => 'success',
            'message' => __t('product_deleted', 'inventory'),
        ]);

        $this->reset(['selectedProductId', 'deletingProductName']);
    }

    /**
     * Quick toggle for product status.
     */
    public function toggleStatus(int $id, ProductService $service): void
    {
        $product = $service->find($id);
        $this->authorize('update', $product);

        $service->toggleStatus($product);

        $this->dispatch('toast', [
            'type'    => 'success',
            'message' => __t('product_status_updated', 'inventory'),
        ]);
    }

    /**
     * Render the Livewire component.
     */
    public function render(): View
    {
        $this->authorize('viewAny', Product::class);

        $query = Product::query()->with('categories');

        if (filled($this->search)) {
            $term = trim($this->search);
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('sku', 'like', "%{$term}%")
                  ->orWhere('barcode', 'like', "%{$term}%");
            });
        }

        if (filled($this->categoryFilter)) {
            $query->whereHas('categories', function ($q) {
                $q->where('categories.id', $this->categoryFilter);
            });
        }

        if ($this->statusFilter === 'active') {
            $query->where('is_active', true);
        } elseif ($this->statusFilter === 'inactive') {
            $query->where('is_active', false);
        }

        $products = $query->latest('id')->paginate(10);

        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();
        $lowStockProducts = Product::whereColumn('stock', '<=', 'minimum_stock')->count();

        $allCategories = Category::active()->orderBy('name')->get();

        return view('inventory::livewire.products.product-index', [
            'products'         => $products,
            'totalProducts'    => $totalProducts,
            'activeProducts'   => $activeProducts,
            'lowStockProducts' => $lowStockProducts,
            'allCategories'    => $allCategories,
        ]);
    }
}
