<?php

namespace Modules\Sales\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Livewire\Livewire;
use Modules\Administration\Models\User;
use Modules\Customers\Models\Customer;
use Modules\Inventory\Models\Category;
use Modules\Inventory\Models\Product;
use Modules\Sales\Livewire\Pos\PosIndex;
use Modules\Sales\Models\Sale;
use Modules\Sales\Models\SaleItem;
use Modules\Sales\Services\SaleService;
use Modules\Settings\Models\Currency;
use Modules\Settings\Models\Setting;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SaleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $viewOnlyUser;
    protected User $unauthorizedUser;
    protected Customer $customer;
    protected Product $productA;
    protected Product $productB;

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'sales.view',
            'sales.create',
            'sales.update',
            'sales.delete',
            'sales.cancel',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions($permissions);

        $this->adminUser = User::create([
            'name'     => 'Admin Cashier',
            'email'    => 'cashier@velntra.test',
            'password' => bcrypt('password123'),
        ]);
        $this->adminUser->assignRole('admin');

        $this->viewOnlyUser = User::create([
            'name'     => 'Viewer User',
            'email'    => 'viewer@velntra.test',
            'password' => bcrypt('password123'),
        ]);
        $this->viewOnlyUser->givePermissionTo('sales.view');

        $this->unauthorizedUser = User::create([
            'name'     => 'Unauthorized User',
            'email'    => 'unauth@velntra.test',
            'password' => bcrypt('password123'),
        ]);

        $this->customer = Customer::create([
            'name'      => 'Consumidor Final',
            'document'  => '9999999999999',
            'is_active' => true,
        ]);

        $this->productA = Product::create([
            'sku'           => 'PROD-001',
            'barcode'       => '7861001001',
            'type'          => 'simple',
            'name'          => 'Bebida Energética 500ml',
            'price'         => 2.50,
            'cost'          => 1.50,
            'stock'         => 20,
            'minimum_stock' => 5,
            'is_active'     => true,
        ]);

        $this->productB = Product::create([
            'sku'           => 'PROD-002',
            'barcode'       => '7861001002',
            'type'          => 'simple',
            'name'          => 'Snack de Papas 150g',
            'price'         => 1.80,
            'cost'          => 1.00,
            'stock'         => 15,
            'minimum_stock' => 3,
            'is_active'     => true,
        ]);

        $currency = Currency::firstOrCreate(
            ['code' => 'USD'],
            ['name' => 'Dólar Estadounidense', 'symbol' => '$', 'is_active' => true]
        );

        Setting::firstOrCreate(
            ['id' => 1],
            [
                'company_name'   => 'Velntra Store',
                'currency_id'    => $currency->id,
                'tax_percentage' => 15.00,
            ]
        );
    }

    public function test_sale_service_creates_completed_sale_and_decrements_stock(): void
    {
        $service = app(SaleService::class);

        $sale = $service->createSale([
            'customer_id'    => $this->customer->id,
            'user_id'        => $this->adminUser->id,
            'discount'       => 0.00,
            'payment_method' => 'cash',
            'status'         => 'completed',
        ], [
            ['product_id' => $this->productA->id, 'quantity' => 2, 'price' => 2.50],
            ['product_id' => $this->productB->id, 'quantity' => 1, 'price' => 1.80],
        ]);

        $this->assertDatabaseHas('sales', [
            'id'          => $sale->id,
            'customer_id' => $this->customer->id,
            'status'      => 'completed',
            'subtotal'    => 6.80,
        ]);

        // Stock decreased: 20 - 2 = 18 and 15 - 1 = 14
        $this->assertEquals(18, $this->productA->fresh()->stock);
        $this->assertEquals(14, $this->productB->fresh()->stock);
    }

    public function test_sale_service_validates_insufficient_stock_and_throws_exception(): void
    {
        $service = app(SaleService::class);

        $this->expectException(InvalidArgumentException::class);

        $service->createSale([
            'customer_id' => $this->customer->id,
            'user_id'     => $this->adminUser->id,
            'status'      => 'completed',
        ], [
            ['product_id' => $this->productA->id, 'quantity' => 999, 'price' => 2.50],
        ]);
    }

    public function test_sale_service_creates_pending_held_sale_without_decrementing_stock(): void
    {
        $service = app(SaleService::class);

        $sale = $service->createSale([
            'customer_id' => $this->customer->id,
            'user_id'     => $this->adminUser->id,
            'status'      => 'pending',
            'notes'       => 'Venta en espera para cliente regular',
        ], [
            ['product_id' => $this->productA->id, 'quantity' => 3, 'price' => 2.50],
        ]);

        $this->assertEquals('pending', $sale->status);
        // Stock should NOT be decremented yet
        $this->assertEquals(20, $this->productA->fresh()->stock);
    }

    public function test_sale_service_completes_pending_sale_and_decrements_stock(): void
    {
        $service = app(SaleService::class);

        $sale = $service->createSale([
            'customer_id' => $this->customer->id,
            'user_id'     => $this->adminUser->id,
            'status'      => 'pending',
        ], [
            ['product_id' => $this->productA->id, 'quantity' => 4, 'price' => 2.50],
        ]);

        $completed = $service->completePendingSale($sale, [
            'payment_method' => 'card',
            'amount_paid'    => $sale->total,
        ]);

        $this->assertEquals('completed', $completed->status);
        $this->assertEquals(16, $this->productA->fresh()->stock);
    }

    public function test_sale_service_cancels_sale_and_restores_product_stock(): void
    {
        $service = app(SaleService::class);

        $sale = $service->createSale([
            'customer_id' => $this->customer->id,
            'user_id'     => $this->adminUser->id,
            'status'      => 'completed',
        ], [
            ['product_id' => $this->productA->id, 'quantity' => 5, 'price' => 2.50],
        ]);

        $this->assertEquals(15, $this->productA->fresh()->stock);

        $cancelled = $service->cancelSale($sale, 'Cliente solicitó anulación');

        $this->assertEquals('cancelled', $cancelled->status);
        $this->assertEquals(20, $this->productA->fresh()->stock); // Restored
    }

    public function test_pos_index_component_authorizes_admin_and_renders(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(PosIndex::class)
            ->assertStatus(200)
            ->assertSee($this->productA->name)
            ->assertSee($this->customer->name);
    }

    public function test_pos_index_component_denies_access_to_unauthorized_user(): void
    {
        $this->actingAs($this->unauthorizedUser);

        Livewire::test(PosIndex::class)
            ->assertForbidden();
    }

    public function test_pos_index_adds_products_to_cart_and_calculates_totals(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(PosIndex::class)
            ->call('addToCart', $this->productA->id)
            ->call('addToCart', $this->productA->id)
            ->call('addToCart', $this->productB->id)
            ->assertSet('cart.' . $this->productA->id . '.quantity', 2)
            ->assertSet('cart.' . $this->productB->id . '.quantity', 1)
            ->assertViewHas('totals', function ($totals) {
                return $totals['subtotal'] == 6.80 && $totals['items_count'] == 3;
            });
    }

    public function test_pos_index_holds_and_resumes_sales(): void
    {
        $this->actingAs($this->adminUser);

        // 1. Add items and hold sale
        Livewire::test(PosIndex::class)
            ->call('addToCart', $this->productA->id, 3)
            ->call('holdCurrentSale')
            ->assertSet('cart', [])
            ->assertDispatched('toast');

        $this->assertDatabaseHas('sales', [
            'status' => 'pending',
        ]);

        $heldSale = Sale::pending()->first();
        $this->assertNotNull($heldSale);

        // 2. Resume held sale into cart
        Livewire::test(PosIndex::class)
            ->call('resumeHeldSale', $heldSale->id)
            ->assertSet('cart.' . $this->productA->id . '.quantity', 3)
            ->assertDispatched('toast');
    }

    public function test_pos_index_processes_payment_and_creates_completed_sale(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(PosIndex::class)
            ->call('addToCart', $this->productA->id, 2)
            ->call('openPaymentModal')
            ->set('paymentMethod', 'cash')
            ->set('amountPaid', '10.00')
            ->call('processPayment')
            ->assertSet('cart', [])
            ->assertDispatched('open-modal', 'receipt-modal')
            ->assertDispatched('toast');

        $this->assertDatabaseHas('sales', [
            'status'         => 'completed',
            'payment_method' => 'cash',
        ]);

        $this->assertEquals(18, $this->productA->fresh()->stock);
    }

    public function test_pos_index_can_cancel_completed_sale(): void
    {
        $this->actingAs($this->adminUser);

        $service = app(SaleService::class);
        $sale = $service->createSale([
            'customer_id' => $this->customer->id,
            'user_id'     => $this->adminUser->id,
            'status'      => 'completed',
        ], [
            ['product_id' => $this->productA->id, 'quantity' => 2, 'price' => 2.50],
        ]);

        Livewire::test(PosIndex::class)
            ->set('activeTab', 'history')
            ->call('openCancelModal', $sale->id)
            ->set('cancelReason', 'Devolución de mercadería')
            ->call('cancelSale')
            ->assertDispatched('toast');

        $this->assertEquals('cancelled', $sale->fresh()->status);
        $this->assertEquals(20, $this->productA->fresh()->stock); // Stock restored
    }
}
