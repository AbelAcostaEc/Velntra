<?php

namespace Modules\Reports\Tests\Feature;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Administration\Models\User;
use Modules\Customers\Models\Customer;
use Modules\Inventory\Models\Product;
use Modules\Reports\Livewire\CustomerReport;
use Modules\Reports\Livewire\InventoryReport;
use Modules\Reports\Livewire\SalesReport;
use Modules\Reports\Services\ReportService;
use Modules\Sales\Models\Sale;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    private User $authorizedUser;

    private User $unauthorizedUser;

    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        CarbonImmutable::setTestNow('2026-10-07 12:00:00');
        Permission::findOrCreate('reports.view');

        $this->authorizedUser = User::factory()->create();
        $this->authorizedUser->givePermissionTo('reports.view');
        $this->unauthorizedUser = User::factory()->create();
        $this->customer = Customer::create([
            'name' => 'Report Customer',
            'document' => 'REPORT-001',
            'is_active' => true,
        ]);
    }

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    public function test_sales_report_only_summarizes_completed_sales_in_period(): void
    {
        $this->createSale('VTA-REPORT-001', $this->customer, 'completed', 100, 15, now());
        $this->createSale('VTA-REPORT-002', $this->customer, 'cancelled', 80, 12, now());
        $this->createSale('VTA-REPORT-003', $this->customer, 'completed', 50, 7.5, now()->subMonth());

        $service = app(ReportService::class);
        $summary = $service->salesSummary('2026-10-01', '2026-10-31');
        $sales = $service->salesQuery('2026-10-01', '2026-10-31')->get();

        $this->assertSame(100.0, $summary['total']);
        $this->assertSame(15.0, $summary['tax']);
        $this->assertSame(1, $summary['count']);
        $this->assertSame(100.0, $summary['average']);
        $this->assertSame(['VTA-REPORT-001'], $sales->pluck('number')->all());
    }

    public function test_inventory_report_calculates_stock_and_values(): void
    {
        Product::create($this->productData('REPORT-A', 'Alpha', 5, 2, 4));
        Product::create($this->productData('REPORT-B', 'Beta', 2, 3, 8));

        $summary = app(ReportService::class)->inventorySummary();

        $this->assertSame(2, $summary['products']);
        $this->assertSame(7, $summary['units']);
        $this->assertSame(14.0, $summary['cost_value']);
        $this->assertSame(36.0, $summary['retail_value']);
        $this->assertSame(1, $summary['low_stock']);
    }

    public function test_inventory_report_filters_search_and_low_stock(): void
    {
        Product::create($this->productData('REPORT-LOW', 'Low product', 1, 2, 4));
        Product::create($this->productData('REPORT-OK', 'Healthy product', 8, 2, 4));

        $products = app(ReportService::class)->inventoryQuery('Low', 'low_stock')->get();

        $this->assertSame(['REPORT-LOW'], $products->pluck('sku')->all());
    }

    public function test_customer_report_ranks_completed_purchases_and_excludes_cancelled_sales(): void
    {
        $secondCustomer = Customer::create([
            'name' => 'Second Customer',
            'document' => 'REPORT-002',
            'is_active' => true,
        ]);
        $this->createSale('VTA-REPORT-010', $this->customer, 'completed', 120, 0, now());
        $this->createSale('VTA-REPORT-011', $secondCustomer, 'completed', 40, 0, now());
        $this->createSale('VTA-REPORT-012', $secondCustomer, 'cancelled', 500, 0, now());

        $service = app(ReportService::class);
        $customers = $service->customersQuery('2026-10-01', '2026-10-31')->get();
        $summary = $service->customersSummary('2026-10-01', '2026-10-31');

        $this->assertSame([$this->customer->id, $secondCustomer->id], $customers->pluck('id')->all());
        $this->assertSame(2, $summary['customers']);
        $this->assertSame(2, $summary['purchases']);
        $this->assertSame(160.0, $summary['total']);
    }

    public function test_each_report_has_its_own_route_and_component(): void
    {
        $this->actingAs($this->authorizedUser);

        $this->get(route('reports.sales'))->assertOk()->assertSeeLivewire(SalesReport::class);
        $this->get(route('reports.inventory'))->assertOk()->assertSeeLivewire(InventoryReport::class);
        $this->get(route('reports.customers'))->assertOk()->assertSeeLivewire(CustomerReport::class);
    }

    public function test_report_components_reject_unauthorized_users(): void
    {
        $this->actingAs($this->unauthorizedUser);

        Livewire::test(SalesReport::class)->assertForbidden();
        Livewire::test(InventoryReport::class)->assertForbidden();
        Livewire::test(CustomerReport::class)->assertForbidden();
    }

    public function test_date_reports_validate_the_selected_range(): void
    {
        $this->actingAs($this->authorizedUser);

        Livewire::test(SalesReport::class)
            ->set('dateFrom', '2026-10-10')
            ->set('dateTo', '2026-10-01')
            ->call('applyFilters')
            ->assertHasErrors(['dateTo']);

        Livewire::test(CustomerReport::class)
            ->set('dateFrom', '2026-10-10')
            ->set('dateTo', '2026-10-01')
            ->call('applyFilters')
            ->assertHasErrors(['dateTo']);
    }

    public function test_each_report_can_be_exported_to_excel_and_pdf(): void
    {
        Product::create($this->productData('EXPORT-001', 'Export product', 5, 2, 4));
        $this->createSale('VTA-EXPORT-001', $this->customer, 'completed', 25, 3, now());
        $this->actingAs($this->authorizedUser);

        Livewire::test(SalesReport::class)
            ->call('exportExcel')
            ->assertFileDownloaded('reporte-ventas-2026-10-01-2026-10-07.xlsx');
        Livewire::test(SalesReport::class)
            ->call('exportPdf')
            ->assertFileDownloaded('reporte-ventas-2026-10-01-2026-10-07.pdf');

        Livewire::test(InventoryReport::class)
            ->call('exportExcel')
            ->assertFileDownloaded('reporte-inventario-2026-10-07.xlsx');
        Livewire::test(InventoryReport::class)
            ->call('exportPdf')
            ->assertFileDownloaded('reporte-inventario-2026-10-07.pdf');

        Livewire::test(CustomerReport::class)
            ->call('exportExcel')
            ->assertFileDownloaded('reporte-clientes-2026-01-01-2026-10-07.xlsx');
        Livewire::test(CustomerReport::class)
            ->call('exportPdf')
            ->assertFileDownloaded('reporte-clientes-2026-01-01-2026-10-07.pdf');
    }

    public function test_exports_with_invalid_dates_download_empty_reports_without_validation_errors(): void
    {
        $this->createSale('VTA-INVALID-RANGE', $this->customer, 'completed', 25, 3, now());
        $this->actingAs($this->authorizedUser);

        Livewire::test(SalesReport::class)
            ->set('dateFrom', 'invalid-date')
            ->set('dateTo', '')
            ->call('exportExcel')
            ->assertHasNoErrors()
            ->assertFileDownloaded('reporte-ventas-sin-datos.xlsx');

        Livewire::test(SalesReport::class)
            ->set('dateFrom', '2026-10-10')
            ->set('dateTo', '2026-10-01')
            ->call('exportPdf')
            ->assertHasNoErrors()
            ->assertFileDownloaded('reporte-ventas-sin-datos.pdf');

        Livewire::test(CustomerReport::class)
            ->set('dateFrom', 'invalid-date')
            ->set('dateTo', '')
            ->call('exportExcel')
            ->assertHasNoErrors()
            ->assertFileDownloaded('reporte-clientes-sin-datos.xlsx');

        Livewire::test(CustomerReport::class)
            ->set('dateFrom', '2026-10-10')
            ->set('dateTo', '2026-10-01')
            ->call('exportPdf')
            ->assertHasNoErrors()
            ->assertFileDownloaded('reporte-clientes-sin-datos.pdf');
    }

    /** @return array<string, mixed> */
    private function productData(string $sku, string $name, int $stock, int $minimumStock, float $price): array
    {
        return [
            'sku' => $sku,
            'name' => $name,
            'cost' => 2,
            'price' => $price,
            'stock' => $stock,
            'minimum_stock' => $minimumStock,
            'is_active' => true,
        ];
    }

    private function createSale(
        string $number,
        Customer $customer,
        string $status,
        float $total,
        float $tax,
        mixed $createdAt,
    ): Sale {
        $sale = Sale::create([
            'number' => $number,
            'customer_id' => $customer->id,
            'user_id' => $this->authorizedUser->id,
            'subtotal' => $total - $tax,
            'discount' => 0,
            'tax' => $tax,
            'tax_percentage' => 15,
            'total' => $total,
            'payment_method' => 'cash',
            'amount_paid' => $total,
            'change' => 0,
            'status' => $status,
        ]);
        $sale->forceFill(['created_at' => $createdAt, 'updated_at' => $createdAt])->save();

        return $sale;
    }
}
