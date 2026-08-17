<?php

namespace Modules\Customers\Livewire\Customers;

// Framework & Livewire
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

// Models
use Modules\Customers\Models\Customer;
use Modules\Sales\Models\Sale;
use Modules\Settings\Models\Setting;

#[Layout('layouts.app')]
class CustomerPurchaseHistory extends Component
{
    use WithPagination;

    /**
     * Instancia del cliente consultado.
     */
    public Customer $customer;

    /**
     * Búsqueda por número de comprobante.
     */
    public string $search = '';

    /**
     * Filtro por estado de venta ('', 'completed', 'pending', 'cancelled').
     */
    public string $statusFilter = '';

    /**
     * Cantidad de registros por página.
     */
    public int $perPage = 10;

    /**
     * ID de la venta seleccionada para ver el comprobante/recibo.
     */
    public ?int $selectedSaleId = null;

    /**
     * Inicializar el componente con el cliente recibido por ruta.
     */
    public function mount(Customer $customer): void
    {
        $this->authorize('view', $customer);
        $this->customer = $customer;
    }

    /**
     * Reiniciar paginación al cambiar filtros.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    /**
     * Abrir el modal de detalle del recibo de una venta específica.
     */
    public function openReceiptModal(int $saleId): void
    {
        $this->selectedSaleId = $saleId;
        $this->dispatch('open-modal', 'customer-receipt-modal');
    }

    /**
     * Obtener estadísticas de consumo del cliente.
     *
     * @return array{
     *     total_spent: float,
     *     total_purchases: int,
     *     average_ticket: float,
     *     last_purchase: ?Sale
     * }
     */
    public function getStatsProperty(): array
    {
        $completedSalesQuery = $this->customer->sales()->completed();

        $totalPurchases = (int) $completedSalesQuery->count();
        $totalSpent = (float) $completedSalesQuery->sum('total');
        $averageTicket = $totalPurchases > 0 ? round($totalSpent / $totalPurchases, 2) : 0.00;
        $lastPurchase = $this->customer->sales()->completed()->latest('created_at')->first();

        return [
            'total_spent'     => $totalSpent,
            'total_purchases' => $totalPurchases,
            'average_ticket'  => $averageTicket,
            'last_purchase'   => $lastPurchase,
        ];
    }

    /**
     * Renderizar la vista de historial de compras.
     */
    public function render(): View
    {
        $this->authorize('view', $this->customer);

        $settings = Setting::getSettings();

        $salesQuery = $this->customer->sales()
            ->with(['items.product', 'user']);

        if (!empty($this->search)) {
            $term = trim($this->search);
            $salesQuery->where('number', 'like', "%{$term}%");
        }

        if (!empty($this->statusFilter)) {
            $salesQuery->where('status', $this->statusFilter);
        }

        $sales = $salesQuery->latest('id')->paginate($this->perPage);

        $selectedSale = $this->selectedSaleId
            ? Sale::with(['items.product', 'customer', 'user'])->find($this->selectedSaleId)
            : null;

        return view('customers::livewire.customers.customer-purchase-history', [
            'settings'     => $settings,
            'sales'        => $sales,
            'selectedSale' => $selectedSale,
            'stats'        => $this->stats,
        ]);
    }
}
