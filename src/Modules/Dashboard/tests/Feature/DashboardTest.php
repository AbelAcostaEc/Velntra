<?php

namespace Modules\Dashboard\Tests\Feature;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Administration\Models\User;
use Modules\Customers\Models\Customer;
use Modules\Dashboard\Livewire\DashboardIndex;
use Modules\Dashboard\Services\DashboardService;
use Modules\Inventory\Models\Product;
use Modules\Sales\Models\Sale;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $authorizedUser;

    protected User $unauthorizedUser;

    protected Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        CarbonImmutable::setTestNow('2026-10-07 12:00:00');
        Permission::firstOrCreate(['name' => 'dashboard.view', 'guard_name' => 'web']);

        $this->authorizedUser = User::create([
            'name' => 'Dashboard User',
            'email' => 'dashboard@velntra.test',
            'password' => 'password123',
            'email_verified_at' => now(),
        ]);
        $this->authorizedUser->givePermissionTo('dashboard.view');

        $this->unauthorizedUser = User::create([
            'name' => 'Restricted User',
            'email' => 'restricted-dashboard@velntra.test',
            'password' => 'password123',
            'email_verified_at' => now(),
        ]);

        $this->customer = Customer::create([
            'name' => 'Dashboard Customer',
            'document' => 'DASH-001',
            'is_active' => true,
        ]);
    }

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    public function test_metrics_use_real_data_and_exclude_cancelled_sales(): void
    {
        Product::create($this->productData('ACTIVE-1', true));
        Product::create($this->productData('INACTIVE-1', false));
        Customer::create(['name' => 'Inactive Customer', 'document' => 'DASH-002', 'is_active' => false]);

        $this->createSale('VTA-DASH-001', 'completed', 25.50, now());
        $this->createSale('VTA-DASH-002', 'cancelled', 99.00, now());
        $this->createSale('VTA-DASH-003', 'completed', 10.00, now()->subMonth());

        $metrics = app(DashboardService::class)->metrics();

        $this->assertSame(25.50, $metrics['today_sales']);
        $this->assertSame(25.50, $metrics['monthly_sales']);
        $this->assertSame(1, $metrics['total_products']);
        $this->assertSame(2, $metrics['total_customers']);
    }

    public function test_latest_sales_only_returns_completed_sales_in_descending_order(): void
    {
        $older = $this->createSale('VTA-DASH-010', 'completed', 10.00, now()->subHour());
        $this->createSale('VTA-DASH-011', 'cancelled', 20.00, now());
        $newer = $this->createSale('VTA-DASH-012', 'completed', 30.00, now());

        $sales = app(DashboardService::class)->latestSales();

        $this->assertCount(2, $sales);
        $this->assertSame([$newer->id, $older->id], $sales->pluck('id')->all());
    }

    public function test_low_stock_returns_only_active_products_at_or_below_minimum(): void
    {
        $low = Product::create($this->productData('LOW-1', true, 2, 2));
        Product::create($this->productData('HEALTHY-1', true, 8, 2));
        Product::create($this->productData('INACTIVE-LOW', false, 0, 2));

        $products = app(DashboardService::class)->lowStockProducts();

        $this->assertCount(1, $products);
        $this->assertTrue($products->first()->is($low));
    }

    public function test_chart_contains_seven_days_and_excludes_cancelled_sales(): void
    {
        $this->createSale('VTA-DASH-020', 'completed', 15.00, now()->subDays(2));
        $this->createSale('VTA-DASH-021', 'cancelled', 80.00, now()->subDays(2));

        $chart = app(DashboardService::class)->salesChart();

        $this->assertCount(7, $chart);
        $this->assertSame('2026-10-01', $chart[0]['date']);
        $this->assertSame('2026-10-07', $chart[6]['date']);
        $this->assertSame(15.00, collect($chart)->firstWhere('date', '2026-10-05')['total']);
    }

    public function test_authorized_user_can_render_dashboard(): void
    {
        $this->actingAs($this->authorizedUser);

        Livewire::test(DashboardIndex::class)
            ->assertOk()
            ->assertSee(__t('today_sales', 'dashboard'));
    }

    public function test_user_can_change_chart_period_and_apply_custom_dates(): void
    {
        $this->actingAs($this->authorizedUser);

        Livewire::test(DashboardIndex::class)
            ->set('chartPeriod', 'previous_month')
            ->assertViewHas('chart', fn (array $chart): bool => count($chart) === 30
                && $chart[0]['date'] === '2026-09-01'
                && $chart[29]['date'] === '2026-09-30')
            ->set('chartPeriod', 'custom')
            ->set('dateFrom', '2026-09-10')
            ->set('dateTo', '2026-09-15')
            ->call('applyCustomPeriod')
            ->assertHasNoErrors()
            ->assertViewHas('chart', fn (array $chart): bool => count($chart) === 6
                && $chart[0]['date'] === '2026-09-10'
                && $chart[5]['date'] === '2026-09-15');
    }

    public function test_custom_chart_period_validates_dates_and_limits_its_size(): void
    {
        $this->actingAs($this->authorizedUser);

        Livewire::test(DashboardIndex::class)
            ->set('chartPeriod', 'custom')
            ->set('dateFrom', '2026-10-10')
            ->set('dateTo', '2026-10-01')
            ->call('applyCustomPeriod')
            ->assertHasErrors(['dateTo'])
            ->set('dateFrom', '2026-01-01')
            ->set('dateTo', '2026-10-01')
            ->call('applyCustomPeriod')
            ->assertHasErrors(['dateTo']);
    }

    public function test_unauthorized_user_cannot_render_dashboard(): void
    {
        $this->actingAs($this->unauthorizedUser);

        Livewire::test(DashboardIndex::class)->assertForbidden();
    }

    /**
     * @return array<string, mixed>
     */
    private function productData(string $sku, bool $active, int $stock = 10, int $minimumStock = 2): array
    {
        return [
            'sku' => $sku,
            'name' => 'Product '.$sku,
            'cost' => 1.00,
            'price' => 2.00,
            'stock' => $stock,
            'minimum_stock' => $minimumStock,
            'is_active' => $active,
        ];
    }

    private function createSale(string $number, string $status, float $total, CarbonInterface $createdAt): Sale
    {
        $sale = Sale::create([
            'number' => $number,
            'customer_id' => $this->customer->id,
            'user_id' => $this->authorizedUser->id,
            'subtotal' => $total,
            'discount' => 0,
            'tax' => 0,
            'tax_percentage' => 0,
            'total' => $total,
            'payment_method' => 'cash',
            'amount_paid' => $total,
            'change' => 0,
            'status' => $status,
        ]);

        $sale->forceFill([
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ])->save();

        return $sale;
    }
}
