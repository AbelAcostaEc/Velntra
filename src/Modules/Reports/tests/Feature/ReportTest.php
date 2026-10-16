<?php

namespace Modules\Reports\Tests\Feature;

use Carbon\CarbonImmutable;
use DOMDocument;
use DOMXPath;
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
use PhpOffice\PhpSpreadsheet\IOFactory;
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

    public function test_each_report_displays_record_cards_on_mobile_and_a_table_on_desktop(): void
    {
        Product::create($this->productData('REPORT-MOBILE', 'Mobile product', 5, 2, 4));
        $this->createSale('VTA-MOBILE-001', $this->customer, 'completed', 25, 3, now());
        $this->actingAs($this->authorizedUser);

        foreach ([
            'reports.sales' => ['sales', 'VTA-MOBILE-001'],
            'reports.inventory' => ['inventory', 'REPORT-MOBILE'],
            'reports.customers' => ['customers', 'Report Customer'],
        ] as $routeName => [$report, $expectedText]) {
            $document = new DOMDocument;
            @$document->loadHTML($this->get(route($routeName))->assertOk()->getContent());
            $xpath = new DOMXPath($document);

            $cards = $xpath->query('//article[@data-report-mobile-card="'.$report.'"]');
            $mobileList = $xpath->query('//article[@data-report-mobile-card="'.$report.'"]/parent::div');
            $desktopTables = $xpath->query('//div[@data-report-desktop-table="'.$report.'"]/table');
            $desktopWrapper = $xpath->query('//div[@data-report-desktop-table="'.$report.'"]');

            $this->assertCount(1, $cards, "{$routeName}: falta la tarjeta móvil.");
            $this->assertStringContainsString($expectedText, $cards->item(0)->textContent);
            $this->assertCount(1, $mobileList);
            $this->assertContains('md:hidden', explode(' ', $mobileList->item(0)->getAttribute('class')));
            $this->assertCount(1, $desktopTables, "{$routeName}: falta la tabla de escritorio.");
            $this->assertCount(1, $desktopWrapper);
            $this->assertContains('hidden', explode(' ', $desktopWrapper->item(0)->getAttribute('class')));
            $this->assertContains('md:block', explode(' ', $desktopWrapper->item(0)->getAttribute('class')));
        }
    }

    public function test_export_buttons_render_inside_their_livewire_component(): void
    {
        $this->actingAs($this->authorizedUser);

        foreach (['reports.sales', 'reports.inventory', 'reports.customers'] as $routeName) {
            $document = new DOMDocument;
            @$document->loadHTML($this->get(route($routeName))->assertOk()->getContent());
            $xpath = new DOMXPath($document);

            foreach (['exportExcel', 'exportPdf'] as $action) {
                $buttons = $xpath->query('//button[@*[name()="wire:click" and .="'.$action.'"]]');

                $this->assertCount(1, $buttons, "{$routeName}: falta el botón {$action}.");
                $this->assertCount(1, $xpath->query('ancestor::*[@*[name()="wire:id"]]', $buttons->item(0)), "{$routeName}: {$action} está fuera de Livewire.");
            }
        }
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
        $salesPdf = Livewire::test(SalesReport::class)
            ->call('exportPdf')
            ->assertFileDownloaded('reporte-ventas-2026-10-01-2026-10-07.pdf');
        $this->assertPdfDownload($salesPdf->effects);

        Livewire::test(InventoryReport::class)
            ->call('exportExcel')
            ->assertFileDownloaded('reporte-inventario-2026-10-07.xlsx');
        $inventoryPdf = Livewire::test(InventoryReport::class)
            ->call('exportPdf')
            ->assertFileDownloaded('reporte-inventario-2026-10-07.pdf');
        $this->assertPdfDownload($inventoryPdf->effects);

        Livewire::test(CustomerReport::class)
            ->call('exportExcel')
            ->assertFileDownloaded('reporte-clientes-2026-01-01-2026-10-07.xlsx');
        $customersPdf = Livewire::test(CustomerReport::class)
            ->call('exportPdf')
            ->assertFileDownloaded('reporte-clientes-2026-01-01-2026-10-07.pdf');
        $this->assertPdfDownload($customersPdf->effects);
    }

    public function test_excel_exports_contain_only_the_filtered_data_and_correct_values(): void
    {
        $otherCustomer = Customer::create([
            'name' => 'Other Customer',
            'document' => 'REPORT-002',
            'is_active' => true,
        ]);
        $this->createSale('VTA-IN-RANGE', $this->customer, 'completed', 115, 15, now());
        $this->createSale('VTA-CANCELLED', $this->customer, 'cancelled', 230, 30, now());
        $this->createSale('VTA-OUT-RANGE', $this->customer, 'completed', 60, 0, now()->subMonth());
        $this->createSale('VTA-OTHER-CUSTOMER', $otherCustomer, 'completed', 40, 0, now());
        Product::create($this->productData('REPORT-ALPHA', 'Alpha', 5, 2, 4));
        Product::create($this->productData('REPORT-BETA', 'Beta', 3, 2, 8));
        $this->actingAs($this->authorizedUser);

        $salesDownload = Livewire::test(SalesReport::class)
            ->call('exportExcel')
            ->assertFileDownloaded();
        $salesRows = $this->excelRows($salesDownload->effects);
        $this->assertCount(3, $salesRows);
        $salesByNumber = collect(array_slice($salesRows, 1))->keyBy(0);
        $this->assertEqualsCanonicalizing(['VTA-IN-RANGE', 'VTA-OTHER-CUSTOMER'], $salesByNumber->keys()->all());
        $this->assertSame(15.0, $salesByNumber['VTA-IN-RANGE'][7]);
        $this->assertSame(115.0, $salesByNumber['VTA-IN-RANGE'][8]);

        $inventoryDownload = Livewire::test(InventoryReport::class)
            ->set('search', 'Alpha')
            ->call('exportExcel')
            ->assertFileDownloaded();
        $inventoryRows = $this->excelRows($inventoryDownload->effects);
        $this->assertCount(2, $inventoryRows);
        $this->assertSame('REPORT-ALPHA', $inventoryRows[1][0]);
        $this->assertSame(5, $inventoryRows[1][3]);
        $this->assertSame(10.0, $inventoryRows[1][7]);
        $this->assertSame(20.0, $inventoryRows[1][8]);

        $customerDownload = Livewire::test(CustomerReport::class)
            ->set('search', 'REPORT-002')
            ->call('exportExcel')
            ->assertFileDownloaded();
        $customerRows = $this->excelRows($customerDownload->effects);
        $this->assertCount(2, $customerRows);
        $this->assertSame('Other Customer', $customerRows[1][1]);
        $this->assertSame(1, $customerRows[1][5]);
        $this->assertSame(40.0, $customerRows[1][6]);
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

    /**
     * @param array<string, mixed> $effects
     * @return list<list<mixed>>
     */
    private function excelRows(array $effects): array
    {
        $this->assertStringEndsWith('.xlsx', $effects['download']['name']);
        $file = tmpfile();
        $this->assertIsResource($file);

        try {
            fwrite($file, base64_decode($effects['download']['content'], true));
            $spreadsheet = IOFactory::load(stream_get_meta_data($file)['uri']);

            return $spreadsheet->getActiveSheet()->toArray(null, true, false);
        } finally {
            fclose($file);
        }
    }

    /** @param array<string, mixed> $effects */
    private function assertPdfDownload(array $effects): void
    {
        $this->assertSame('application/pdf', $effects['download']['contentType']);
        $this->assertStringStartsWith('%PDF-', base64_decode($effects['download']['content'], true));
    }
}
