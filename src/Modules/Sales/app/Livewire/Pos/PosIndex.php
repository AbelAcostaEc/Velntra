<?php

namespace Modules\Sales\Livewire\Pos;

// Framework & Livewire
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

// Models
use Modules\Customers\Models\Customer;
use Modules\Inventory\Models\Category;
use Modules\Inventory\Models\Product;
use Modules\Sales\Models\Sale;
use Modules\Settings\Models\Setting;

// Services
use Modules\Customers\Services\CustomerService;
use Modules\Sales\Services\SaleService;

#[Layout('layouts.app')]
class PosIndex extends Component
{
    use WithPagination;

    /**
     * Pestaña activa ('pos' o 'history').
     */
    public string $activeTab = 'pos';

    /**
     * Búsqueda reactiva de productos en el catálogo POS.
     */
    public string $searchProduct = '';

    /**
     * Filtro por categoría de productos.
     */
    public string $categoryFilter = '';

    /**
     * Carrito de compras actual.
     * Estructura por producto: [product_id => ['product_id', 'name', 'sku', 'barcode', 'price', 'cost', 'quantity', 'stock', 'subtotal', 'image']]
     *
     * @var array<int, array{
     *     product_id: int,
     *     name: string,
     *     sku: string,
     *     barcode: ?string,
     *     price: float,
     *     cost: float,
     *     quantity: int,
     *     stock: int,
     *     subtotal: float,
     *     image: ?string
     * }>
     */
    public array $cart = [];

    /**
     * ID del cliente seleccionado para la venta.
     */
    public ?int $selectedCustomerId = null;

    /**
     * Término de búsqueda para seleccionar cliente.
     */
    public string $customerSearch = '';

    /**
     * Descuento global aplicado a la venta.
     */
    public $discount = 0.00;

    /**
     * Modal de Cobro / Pago.
     */
    public string $paymentMethod = 'cash';
    public $amountPaid = '';
    public string $notes = '';

    /**
     * ID de la venta en espera actual (si se reanudó una existente).
     */
    public ?int $resumedSaleId = null;

    /**
     * ID de la venta seleccionada para anulación.
     */
    public ?int $saleToCancelId = null;
    public string $cancelReason = '';

    /**
     * Venta recién completada para visualización de recibo.
     */
    public ?int $completedSaleId = null;

    /**
     * Campos para creación rápida de cliente desde el POS.
     */
    public string $newCustomerName = '';
    public ?string $newCustomerDocument = '';
    public ?string $newCustomerPhone = '';
    public ?string $newCustomerEmail = '';

    /**
     * Campos para edición de cliente directamente desde el POS.
     */
    public ?int $editingCustomerId = null;
    public string $editCustomerName = '';
    public ?string $editCustomerDocument = '';
    public ?string $editCustomerPhone = '';
    public ?string $editCustomerEmail = '';
    public ?string $editCustomerAddress = '';

    /**
     * Búsqueda y paginación para el historial de ventas.
     */
    public string $historySearch = '';
    public string $historyStatusFilter = '';
    public int $perPage = 10;

    /**
     * Inicializar el componente y cargar cliente por defecto.
     */
    public function mount(): void
    {
        $this->authorize('viewAny', Sale::class);

        $defaultCustomer = Customer::where('document', '9999999999999')
            ->orWhere('name', 'Consumidor Final')
            ->first() ?? Customer::first();

        if ($defaultCustomer) {
            $this->selectedCustomerId = $defaultCustomer->id;
        }
    }

    /**
     * Reiniciar paginación al cambiar filtros de catálogo o historial.
     */
    public function updatingSearchProduct(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updatingHistorySearch(): void
    {
        $this->resetPage();
    }

    public function updatingHistoryStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    /**
     * Escanear o buscar código exacto de barras o SKU al presionar Enter.
     */
    public function scanBarcode(): void
    {
        $term = trim($this->searchProduct);
        if (empty($term)) {
            return;
        }

        $product = Product::active()
            ->where(function ($q) use ($term) {
                $q->where('barcode', $term)
                  ->orWhere('sku', $term);
            })
            ->first();

        if ($product) {
            $this->addToCart($product->id);
            $this->searchProduct = '';
        }
    }

    /**
     * Agregar un producto al carrito o incrementar su cantidad.
     */
    public function addToCart(int $productId, int $qty = 1): void
    {
        $this->authorize('create', Sale::class);

        $product = Product::findOrFail($productId);

        if (!$product->is_active) {
            $this->dispatch('toast', [
                'type'    => 'danger',
                'message' => 'El producto seleccionado no está activo.',
            ]);
            return;
        }

        $currentQty = isset($this->cart[$productId]) ? $this->cart[$productId]['quantity'] : 0;
        $targetQty = $currentQty + $qty;

        if ($product->stock < $targetQty) {
            $this->dispatch('toast', [
                'type'    => 'danger',
                'message' => __t('stock_limit_reached', 'sales') . " (Stock: {$product->stock})",
            ]);
            return;
        }

        $price = (float) $product->price;
        $cost = (float) $product->cost;

        $this->cart[$productId] = [
            'product_id' => $product->id,
            'name'       => $product->name,
            'sku'        => $product->sku,
            'barcode'    => $product->barcode,
            'price'      => $price,
            'cost'       => $cost,
            'quantity'   => $targetQty,
            'stock'      => (int) $product->stock,
            'subtotal'   => round($targetQty * $price, 2),
            'image'      => $product->image_url,
        ];

        $this->dispatch('toast', [
            'type'    => 'success',
            'message' => __t('product_added', 'sales'),
        ]);
    }

    /**
     * Actualizar directamente la cantidad de un ítem en el carrito.
     */
    public function updateQuantity(int $productId, int $qty): void
    {
        if ($qty <= 0) {
            $this->removeFromCart($productId);
            return;
        }

        if (!isset($this->cart[$productId])) {
            return;
        }

        $product = Product::findOrFail($productId);

        if ($product->stock < $qty) {
            $this->dispatch('toast', [
                'type'    => 'danger',
                'message' => __t('stock_limit_reached', 'sales') . " (Stock: {$product->stock})",
            ]);
            return;
        }

        $this->cart[$productId]['quantity'] = $qty;
        $this->cart[$productId]['subtotal'] = round($qty * $this->cart[$productId]['price'], 2);
    }

    /**
     * Eliminar un producto del carrito.
     */
    public function removeFromCart(int $productId): void
    {
        unset($this->cart[$productId]);
    }

    /**
     * Vaciar todo el carrito.
     */
    public function clearCart(): void
    {
        $this->cart = [];
        $this->resumedSaleId = null;
        $this->discount = 0.00;

        $this->dispatch('toast', [
            'type'    => 'info',
            'message' => __t('cart_cleared', 'sales'),
        ]);
    }

    /**
     * Obtener los totales calculados del carrito.
     *
     * @return array{
     *     subtotal: float,
     *     discount: float,
     *     taxable: float,
     *     tax_percentage: float,
     *     tax: float,
     *     total: float,
     *     items_count: int
     * }
     */
    public function getTotalsProperty(): array
    {
        $subtotal = 0.00;
        $itemsCount = 0;

        foreach ($this->cart as $item) {
            $subtotal += (float) $item['subtotal'];
            $itemsCount += (int) $item['quantity'];
        }

        $discount = min($subtotal, max(0.00, (float) $this->discount));
        $taxable = max(0.00, $subtotal - $discount);

        $settings = Setting::getSettings();
        $taxPercentage = (float) ($settings->tax_percentage ?? 15.00);
        $tax = round($taxable * ($taxPercentage / 100), 2);
        $total = round($taxable + $tax, 2);

        return [
            'subtotal'       => round($subtotal, 2),
            'discount'       => round($discount, 2),
            'taxable'        => round($taxable, 2),
            'tax_percentage' => $taxPercentage,
            'tax'            => $tax,
            'total'          => $total,
            'items_count'    => $itemsCount,
        ];
    }

    /**
     * Seleccionar cliente desde la lista de búsqueda.
     */
    public function selectCustomer(int $customerId): void
    {
        $this->selectedCustomerId = $customerId;
        $this->customerSearch = '';
        $this->dispatch('close-modal', 'select-customer-modal');
    }

    /**
     * Crear cliente rápido desde el POS.
     */
    public function createQuickCustomer(CustomerService $customerService): void
    {
        $this->validate([
            'newCustomerName'     => ['required', 'string', 'max:255'],
            'newCustomerDocument' => [
                'nullable',
                'string',
                'max:30',
                Rule::unique('customers', 'document')->whereNull('deleted_at'),
            ],
            'newCustomerPhone'    => ['nullable', 'string', 'max:30'],
            'newCustomerEmail'    => ['nullable', 'email', 'max:255'],
        ]);

        $customer = $customerService->create([
            'name'      => $this->newCustomerName,
            'document'  => $this->newCustomerDocument,
            'phone'     => $this->newCustomerPhone,
            'email'     => $this->newCustomerEmail,
            'is_active' => true,
        ]);

        $this->selectedCustomerId = $customer->id;
        $this->reset(['newCustomerName', 'newCustomerDocument', 'newCustomerPhone', 'newCustomerEmail']);
        $this->dispatch('close-modal', 'quick-customer-modal');

        $this->dispatch('toast', [
            'type'    => 'success',
            'message' => 'Cliente creado y seleccionado.',
        ]);
    }

    /**
     * Abrir modal para editar el cliente seleccionado directamente desde el POS.
     */
    public function openEditCustomerModal(): void
    {
        if (!$this->selectedCustomerId) {
            return;
        }

        $customer = Customer::findOrFail($this->selectedCustomerId);

        $this->editingCustomerId = $customer->id;
        $this->editCustomerName = $customer->name;
        $this->editCustomerDocument = $customer->document ?? '';
        $this->editCustomerPhone = $customer->phone ?? '';
        $this->editCustomerEmail = $customer->email ?? '';
        $this->editCustomerAddress = $customer->address ?? '';

        $this->resetValidation();
        $this->dispatch('open-modal', 'edit-customer-modal');
    }

    /**
     * Guardar los cambios del cliente editado desde el POS.
     */
    public function updateCustomer(CustomerService $customerService): void
    {
        if (!$this->editingCustomerId) {
            return;
        }

        $this->validate([
            'editCustomerName'     => ['required', 'string', 'max:255'],
            'editCustomerDocument' => [
                'nullable',
                'string',
                'max:30',
                Rule::unique('customers', 'document')
                    ->ignore($this->editingCustomerId)
                    ->whereNull('deleted_at'),
            ],
            'editCustomerPhone'    => ['nullable', 'string', 'max:30'],
            'editCustomerEmail'    => ['nullable', 'email', 'max:255'],
            'editCustomerAddress'  => ['nullable', 'string', 'max:500'],
        ]);

        $customer = $customerService->find($this->editingCustomerId);
        $customerService->update($customer, [
            'name'     => $this->editCustomerName,
            'document' => $this->editCustomerDocument,
            'phone'    => $this->editCustomerPhone,
            'email'    => $this->editCustomerEmail,
            'address'  => $this->editCustomerAddress,
        ]);

        $this->dispatch('close-modal', 'edit-customer-modal');

        $this->dispatch('toast', [
            'type'    => 'success',
            'message' => 'Cliente actualizado exitosamente.',
        ]);
    }

    /**
     * Guardar la venta actual en espera (Hold Sale / Pending).
     */
    public function holdCurrentSale(SaleService $saleService): void
    {
        $this->authorize('create', Sale::class);

        if (empty($this->cart)) {
            $this->dispatch('toast', [
                'type'    => 'danger',
                'message' => 'El carrito está vacío, no se puede guardar en espera.',
            ]);
            return;
        }

        $items = array_values($this->cart);

        // Si ya era una venta en espera reanudada, se elimina la anterior para re-guardar
        if ($this->resumedSaleId) {
            $prev = Sale::find($this->resumedSaleId);
            if ($prev && $prev->isPending()) {
                $saleService->deletePendingSale($prev);
            }
        }

        $saleService->createSale([
            'customer_id' => $this->selectedCustomerId,
            'user_id'     => Auth::id() ?? 1,
            'discount'    => $this->totals['discount'],
            'status'      => 'pending',
            'notes'       => $this->notes ?: 'Venta en espera',
        ], $items);

        $this->clearCart();

        // Re-asignar cliente Consumidor Final por defecto para el siguiente cliente
        $defaultCustomer = Customer::where('document', '9999999999999')->orWhere('name', 'Consumidor Final')->first();
        if ($defaultCustomer) {
            $this->selectedCustomerId = $defaultCustomer->id;
        }

        $this->dispatch('toast', [
            'type'    => 'info',
            'message' => __t('sale_held', 'sales'),
        ]);
    }

    /**
     * Reanudar una venta en espera y cargar sus ítems al carrito activo.
     */
    public function resumeHeldSale(int $saleId, SaleService $saleService): void
    {
        $sale = $saleService->find($saleId);

        if (!$sale->isPending()) {
            $this->dispatch('toast', [
                'type'    => 'danger',
                'message' => 'Esta venta ya no se encuentra en espera.',
            ]);
            return;
        }

        // Cargar ítems al carrito
        $this->cart = [];
        foreach ($sale->items as $item) {
            $product = $item->product;
            $this->cart[$product->id] = [
                'product_id' => $product->id,
                'name'       => $product->name,
                'sku'        => $product->sku,
                'barcode'    => $product->barcode,
                'price'      => (float) $item->price,
                'cost'       => (float) $item->cost,
                'quantity'   => (int) $item->quantity,
                'stock'      => (int) $product->stock,
                'subtotal'   => (float) $item->subtotal,
                'image'      => $product->image_url,
            ];
        }

        $this->selectedCustomerId = $sale->customer_id;
        $this->discount = (float) $sale->discount;
        $this->notes = $sale->notes ?? '';
        $this->resumedSaleId = $sale->id;

        // Eliminar el registro pendiente anterior para evitar duplicidad al facturar
        $saleService->deletePendingSale($sale);

        $this->dispatch('close-modal', 'held-sales-modal');

        $this->dispatch('toast', [
            'type'    => 'success',
            'message' => __t('sale_resumed', 'sales'),
        ]);
    }

    /**
     * Descartar / eliminar una venta en espera.
     */
    public function deleteHeldSale(int $saleId, SaleService $saleService): void
    {
        $sale = $saleService->find($saleId);
        $saleService->deletePendingSale($sale);

        $this->dispatch('toast', [
            'type'    => 'warning',
            'message' => __t('held_sale_deleted', 'sales'),
        ]);
    }

    /**
     * Abrir modal de pago y prellenar monto recibido.
     */
    public function openPaymentModal(): void
    {
        if (empty($this->cart)) {
            $this->dispatch('toast', [
                'type'    => 'danger',
                'message' => 'El carrito está vacío.',
            ]);
            return;
        }

        $this->amountPaid = (string) $this->totals['total'];
        $this->dispatch('open-modal', 'payment-modal');
    }

    /**
     * Establecer monto exacto en el modal de cobro.
     */
    public function setExactAmount(): void
    {
        $this->amountPaid = (string) $this->totals['total'];
    }

    /**
     * Establecer denominación rápida de billete.
     */
    public function setQuickAmount(float $amount): void
    {
        $this->amountPaid = (string) $amount;
    }

    /**
     * Procesar y completar la venta actual.
     */
    public function processPayment(SaleService $saleService): void
    {
        $this->authorize('create', Sale::class);

        if (empty($this->cart)) {
            $this->dispatch('toast', [
                'type'    => 'danger',
                'message' => 'El carrito está vacío.',
            ]);
            return;
        }

        $totalDue = $this->totals['total'];
        $paid = (float) $this->amountPaid;

        if ($paid < $totalDue && $this->paymentMethod === 'cash') {
            $this->dispatch('toast', [
                'type'    => 'danger',
                'message' => 'El monto recibido no puede ser inferior al total a pagar.',
            ]);
            return;
        }

        $items = array_values($this->cart);

        // Si fue una venta en espera reanudada, se asegura que el id no choque
        if ($this->resumedSaleId) {
            $prev = Sale::find($this->resumedSaleId);
            if ($prev && $prev->isPending()) {
                $saleService->deletePendingSale($prev);
            }
        }

        $sale = $saleService->createSale([
            'customer_id'    => $this->selectedCustomerId,
            'user_id'        => Auth::id() ?? 1,
            'discount'       => $this->totals['discount'],
            'payment_method' => $this->paymentMethod,
            'amount_paid'    => $paid,
            'change'         => max(0.00, $paid - $totalDue),
            'status'         => 'completed',
            'notes'          => $this->notes ?: null,
        ], $items);

        $this->completedSaleId = $sale->id;
        $this->clearCart();
        $this->dispatch('close-modal', 'payment-modal');
        $this->dispatch('open-modal', 'receipt-modal');

        $this->dispatch('toast', [
            'type'    => 'success',
            'message' => __t('sale_completed', 'sales'),
        ]);
    }

    /**
     * Preparar anulación de venta.
     */
    public function openCancelModal(int $saleId): void
    {
        $this->saleToCancelId = $saleId;
        $this->cancelReason = '';
        $this->dispatch('open-modal', 'cancel-sale-modal');
    }

    /**
     * Confirmar y ejecutar anulación de venta con restitución de inventario.
     */
    public function cancelSale(SaleService $saleService): void
    {
        if (!$this->saleToCancelId) {
            return;
        }

        $sale = $saleService->find($this->saleToCancelId);
        $this->authorize('cancel', $sale);

        $saleService->cancelSale($sale, $this->cancelReason);

        $this->dispatch('close-modal', 'cancel-sale-modal');
        $this->reset(['saleToCancelId', 'cancelReason']);

        $this->dispatch('toast', [
            'type'    => 'warning',
            'message' => __t('sale_cancelled', 'sales'),
        ]);
    }

    /**
     * Renderizar el componente POS y sus pestañas.
     */
    public function render(SaleService $saleService): View
    {
        $this->authorize('viewAny', Sale::class);

        $settings = Setting::getSettings();
        $categories = Category::active()->orderBy('name')->get();
        $heldSales = $saleService->getPendingSales();
        $heldCount = $heldSales->count();

        // Productos para el catálogo visual del POS
        $productsQuery = Product::active()
            ->with('categories')
            ->search($this->searchProduct);

        if (!empty($this->categoryFilter)) {
            $productsQuery->whereHas('categories', function ($q) {
                $q->where('categories.id', $this->categoryFilter);
            });
        }

        $products = $productsQuery->orderBy('name')->paginate(12, ['*'], 'posPage');

        // Búsqueda de clientes para el modal selector
        $customersList = [];
        if (strlen($this->customerSearch) >= 1) {
            $customersList = Customer::active()
                ->search($this->customerSearch)
                ->limit(10)
                ->get();
        }

        $selectedCustomer = $this->selectedCustomerId
            ? Customer::find($this->selectedCustomerId)
            : null;

        // Historial de ventas si la pestaña activa es 'history'
        $historySales = null;
        if ($this->activeTab === 'history') {
            $hq = Sale::with(['customer', 'user', 'items.product'])
                ->search($this->historySearch);

            if ($this->historyStatusFilter) {
                $hq->where('status', $this->historyStatusFilter);
            }

            $historySales = $hq->latest('id')->paginate($this->perPage, ['*'], 'historyPage');
        }

        $completedSale = $this->completedSaleId ? Sale::with(['items.product', 'customer', 'user'])->find($this->completedSaleId) : null;

        return view('sales::livewire.pos.pos-index', [
            'settings'          => $settings,
            'categories'        => $categories,
            'products'          => $products,
            'customersList'     => $customersList,
            'selectedCustomer'  => $selectedCustomer,
            'heldSales'         => $heldSales,
            'heldCount'         => $heldCount,
            'historySales'      => $historySales,
            'completedSale'     => $completedSale,
            'totals'            => $this->totals,
        ]);
    }
}
