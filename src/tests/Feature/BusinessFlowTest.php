<?php

namespace Tests\Feature;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Livewire\Volt\Volt;
use Modules\Administration\Models\User;
use Modules\Customers\Livewire\Customers\CustomerIndex;
use Modules\Customers\Models\Customer;
use Modules\Dashboard\Services\DashboardService;
use Modules\Inventory\Livewire\Categories\CategoryIndex;
use Modules\Inventory\Livewire\Products\ProductIndex;
use Modules\Inventory\Models\Category;
use Modules\Inventory\Models\Product;
use Modules\Reports\Services\ReportService;
use Modules\Sales\Livewire\Pos\PosIndex;
use Modules\Sales\Models\Sale;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class BusinessFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    public function test_login_catalog_customer_sale_cancellation_and_reports_stay_consistent(): void
    {
        CarbonImmutable::setTestNow('2026-10-15 12:00:00');

        $user = User::factory()->create();
        foreach ([
            'dashboard.view',
            'categories.view', 'categories.create',
            'products.view', 'products.create',
            'customers.view', 'customers.create',
            'sales.view', 'sales.create', 'sales.cancel',
            'reports.view',
        ] as $permission) {
            $user->givePermissionTo(Permission::findOrCreate($permission));
        }

        Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticatedAs($user);
        $this->get(route('dashboard'))->assertOk();

        Livewire::test(CategoryIndex::class)
            ->call('openCreateModal')
            ->set('code', 'FLOW-CAT')
            ->set('name', 'Categoría del flujo')
            ->call('save')
            ->assertHasNoErrors();
        $category = Category::where('code', 'FLOW-CAT')->firstOrFail();

        Livewire::test(ProductIndex::class)
            ->call('openCreateModal')
            ->set('sku', 'FLOW-PROD')
            ->set('name', 'Producto del flujo')
            ->set('cost', '6.00')
            ->set('price', '10.00')
            ->set('stock', 10)
            ->set('minimum_stock', 2)
            ->set('selectedCategories', [(string) $category->id])
            ->call('save')
            ->assertHasNoErrors();
        $product = Product::where('sku', 'FLOW-PROD')->firstOrFail();
        $this->assertTrue($product->categories->contains($category));

        Livewire::test(CustomerIndex::class)
            ->call('openCreateModal')
            ->set('document', 'FLOW-001')
            ->set('name', 'Cliente del flujo')
            ->call('save')
            ->assertHasNoErrors();
        $customer = Customer::where('document', 'FLOW-001')->firstOrFail();

        Livewire::test(PosIndex::class)
            ->set('selectedCustomerId', $customer->id)
            ->call('addToCart', $product->id, 2)
            ->call('openPaymentModal')
            ->set('paymentMethod', 'cash')
            ->set('amountPaid', '25.00')
            ->call('processPayment')
            ->assertHasNoErrors()
            ->assertDispatched('open-modal', 'receipt-modal');

        $sale = Sale::where('customer_id', $customer->id)->firstOrFail();
        $this->assertSame('completed', $sale->status);
        $this->assertSame(8, $product->fresh()->stock);
        $this->assertSame('23.00', $sale->total);

        $reports = app(ReportService::class);
        $dashboard = app(DashboardService::class);
        $this->assertSame(23.0, $dashboard->metrics()['today_sales']);
        $this->assertSame(23.0, $reports->salesSummary('2026-10-15', '2026-10-15')['total']);
        $this->assertSame(1, $reports->customersSummary('2026-10-15', '2026-10-15')['purchases']);

        Livewire::test(PosIndex::class)
            ->set('activeTab', 'history')
            ->call('openCancelModal', $sale->id)
            ->set('cancelReason', 'Prueba integral')
            ->call('cancelSale')
            ->assertHasNoErrors();

        $this->assertSame('cancelled', $sale->fresh()->status);
        $this->assertSame(10, $product->fresh()->stock);
        $this->assertSame(0.0, $dashboard->metrics()['today_sales']);
        $this->assertSame(0, $reports->salesSummary('2026-10-15', '2026-10-15')['count']);
        $this->assertSame(0, $reports->customersSummary('2026-10-15', '2026-10-15')['purchases']);
    }
}
