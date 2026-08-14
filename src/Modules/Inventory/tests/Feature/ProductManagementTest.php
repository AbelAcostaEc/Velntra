<?php

namespace Modules\Inventory\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Administration\Models\User;
use Modules\Inventory\Livewire\Products\ProductIndex;
use Modules\Inventory\Models\Category;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Services\ProductService;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected User $unauthorizedUser;

    protected ProductService $productService;

    protected Category $categoryA;

    protected Category $categoryB;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'dashboard.view',
            'categories.view',
            'categories.create',
            'categories.update',
            'categories.delete',
            'products.view',
            'products.create',
            'products.update',
            'products.delete',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Create Admin Role
        $adminRole = Role::firstOrCreate(['name' => 'Administrator', 'guard_name' => 'web']);
        $adminRole->syncPermissions($permissions);

        // Create Admin User
        $this->adminUser = User::factory()->create([
            'email' => 'admin@velntra.test',
        ]);
        $this->adminUser->assignRole($adminRole);

        // Create Unauthorized User
        $this->unauthorizedUser = User::factory()->create([
            'email' => 'user@velntra.test',
        ]);

        $this->productService = new ProductService();

        $this->categoryA = Category::create([
            'code'      => 'CAT-A',
            'name'      => 'Category A',
            'is_active' => true,
        ]);

        $this->categoryB = Category::create([
            'code'      => 'CAT-B',
            'name'      => 'Category B',
            'is_active' => true,
        ]);
    }

    public function test_product_service_creates_product_with_categories(): void
    {
        $this->actingAs($this->adminUser);

        $product = $this->productService->create([
            'sku'           => 'PROD-001',
            'barcode'       => '123456789',
            'type'          => 'simple',
            'name'          => 'Test Product',
            'description'   => 'Test description',
            'cost'          => 10.50,
            'price'         => 19.99,
            'stock'         => 50,
            'minimum_stock' => 5,
            'is_active'     => true,
        ], [$this->categoryA->id, $this->categoryB->id]);

        $this->assertDatabaseHas('products', [
            'id'         => $product->id,
            'sku'        => 'PROD-001',
            'name'       => 'Test Product',
            'created_by' => $this->adminUser->id,
        ]);

        $this->assertCount(2, $product->categories);
        $this->assertTrue($product->categories->contains($this->categoryA));
        $this->assertTrue($product->categories->contains($this->categoryB));
    }

    public function test_product_service_updates_product_with_categories(): void
    {
        $this->actingAs($this->adminUser);

        $product = Product::create([
            'sku'           => 'PROD-OLD',
            'name'          => 'Old Name',
            'type'          => 'simple',
            'cost'          => 5.00,
            'price'         => 10.00,
            'stock'         => 20,
            'minimum_stock' => 2,
            'is_active'     => true,
        ]);
        $product->categories()->attach([$this->categoryA->id]);

        $updated = $this->productService->update($product, [
            'sku'           => 'PROD-NEW',
            'name'          => 'Updated Name',
            'price'         => 15.00,
            'is_active'     => false,
        ], [$this->categoryB->id]);

        $this->assertSame('PROD-NEW', $updated->sku);
        $this->assertSame('Updated Name', $updated->name);
        $this->assertFalse($updated->is_active);
        $this->assertSame($this->adminUser->id, $updated->updated_by);

        $this->assertCount(1, $updated->categories);
        $this->assertTrue($updated->categories->contains($this->categoryB));
        $this->assertFalse($updated->categories->contains($this->categoryA));
    }

    public function test_product_service_deletes_product(): void
    {
        $this->actingAs($this->adminUser);

        $product = Product::create([
            'sku'           => 'PROD-DEL',
            'name'          => 'To Delete',
            'type'          => 'simple',
            'cost'          => 1.00,
            'price'         => 2.00,
            'stock'         => 10,
            'minimum_stock' => 1,
            'is_active'     => true,
        ]);

        $result = $this->productService->delete($product);

        $this->assertTrue($result);
        $this->assertSoftDeleted('products', [
            'id'         => $product->id,
            'deleted_by' => $this->adminUser->id,
        ]);
    }

    public function test_product_service_toggles_status(): void
    {
        $this->actingAs($this->adminUser);

        $product = Product::create([
            'sku'           => 'PROD-TOGGLE',
            'name'          => 'Toggle Product',
            'type'          => 'simple',
            'cost'          => 1.00,
            'price'         => 2.00,
            'stock'         => 10,
            'minimum_stock' => 1,
            'is_active'     => true,
        ]);

        $toggled = $this->productService->toggleStatus($product);
        $this->assertFalse($toggled->is_active);

        $toggledAgain = $this->productService->toggleStatus($toggled);
        $this->assertTrue($toggledAgain->is_active);
    }

    public function test_product_index_component_authorizes_admin_and_renders(): void
    {
        $this->actingAs($this->adminUser);

        Product::create([
            'sku'           => 'PROD-RENDER',
            'name'          => 'Render Product',
            'type'          => 'simple',
            'cost'          => 2.00,
            'price'         => 5.00,
            'stock'         => 15,
            'minimum_stock' => 3,
            'is_active'     => true,
        ]);

        $response = $this->get(route('products.index'));

        $response->assertOk()
            ->assertSee('Render Product')
            ->assertSee('PROD-RENDER');
    }

    public function test_product_index_component_denies_access_to_unauthorized_user(): void
    {
        $this->actingAs($this->unauthorizedUser);

        $response = $this->get(route('products.index'));
        $response->assertForbidden();

        Livewire::test(ProductIndex::class)
            ->assertForbidden();
    }

    public function test_product_index_can_create_product_with_categories_via_livewire(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(ProductIndex::class)
            ->call('openCreateModal')
            ->set('sku', 'PROD-LIV-01')
            ->set('barcode', '78612345678')
            ->set('type', 'simple')
            ->set('name', 'Livewire Product')
            ->set('description', 'Livewire product description')
            ->set('cost', '12.50')
            ->set('price', '25.00')
            ->set('stock', 100)
            ->set('minimum_stock', 10)
            ->set('is_active', true)
            ->set('selectedCategories', [(string)$this->categoryA->id, (string)$this->categoryB->id])
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('close-modal', 'product-form')
            ->assertDispatched('toast');

        $this->assertDatabaseHas('products', [
            'sku'        => 'PROD-LIV-01',
            'name'       => 'Livewire Product',
            'created_by' => $this->adminUser->id,
        ]);

        $product = Product::where('sku', 'PROD-LIV-01')->first();
        $this->assertNotNull($product);
        $this->assertCount(2, $product->categories);
    }

    public function test_product_index_validates_required_and_unique_sku(): void
    {
        $this->actingAs($this->adminUser);

        Product::create([
            'sku'           => 'PROD-EXISTING',
            'name'          => 'Existing Product',
            'type'          => 'simple',
            'cost'          => 1.00,
            'price'         => 2.00,
            'stock'         => 10,
            'minimum_stock' => 1,
            'is_active'     => true,
        ]);

        Livewire::test(ProductIndex::class)
            ->set('sku', '')
            ->set('name', '')
            ->set('cost', '')
            ->set('price', '')
            ->call('save')
            ->assertHasErrors(['sku', 'name', 'cost', 'price']);

        Livewire::test(ProductIndex::class)
            ->set('sku', 'PROD-EXISTING')
            ->set('name', 'Another Name')
            ->set('cost', '5.00')
            ->set('price', '10.00')
            ->set('stock', 10)
            ->set('minimum_stock', 1)
            ->call('save')
            ->assertHasErrors(['sku']);
    }

    public function test_product_index_can_edit_product_and_sync_categories_via_livewire(): void
    {
        $this->actingAs($this->adminUser);

        $product = Product::create([
            'sku'           => 'PROD-EDIT-TEST',
            'name'          => 'Initial Name',
            'type'          => 'simple',
            'cost'          => 10.00,
            'price'         => 20.00,
            'stock'         => 30,
            'minimum_stock' => 5,
            'is_active'     => true,
        ]);
        $product->categories()->attach([$this->categoryA->id]);

        Livewire::test(ProductIndex::class)
            ->call('openEditModal', $product->id)
            ->assertSet('sku', 'PROD-EDIT-TEST')
            ->assertSet('name', 'Initial Name')
            ->set('name', 'Updated Name via Livewire')
            ->set('price', '29.99')
            ->set('selectedCategories', [(string)$this->categoryB->id])
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('close-modal', 'product-form')
            ->assertDispatched('toast');

        $this->assertDatabaseHas('products', [
            'id'    => $product->id,
            'name'  => 'Updated Name via Livewire',
            'price' => 29.99,
        ]);

        $fresh = $product->fresh();
        $this->assertCount(1, $fresh->categories);
        $this->assertTrue($fresh->categories->contains($this->categoryB));
    }

    public function test_product_index_can_delete_product_via_livewire(): void
    {
        $this->actingAs($this->adminUser);

        $product = Product::create([
            'sku'           => 'PROD-TO-DELETE',
            'name'          => 'Product to Delete',
            'type'          => 'simple',
            'cost'          => 5.00,
            'price'         => 10.00,
            'stock'         => 20,
            'minimum_stock' => 2,
            'is_active'     => true,
        ]);

        Livewire::test(ProductIndex::class)
            ->call('openDeleteModal', $product->id)
            ->assertSet('selectedProductId', $product->id)
            ->assertDispatched('open-modal', 'delete-product')
            ->call('delete')
            ->assertDispatched('close-modal', 'delete-product')
            ->assertDispatched('toast');

        $this->assertSoftDeleted('products', [
            'id' => $product->id,
        ]);
    }

    public function test_product_index_can_toggle_product_status_via_livewire(): void
    {
        $this->actingAs($this->adminUser);

        $product = Product::create([
            'sku'           => 'PROD-TOG',
            'name'          => 'Toggle Product',
            'type'          => 'simple',
            'cost'          => 5.00,
            'price'         => 10.00,
            'stock'         => 20,
            'minimum_stock' => 2,
            'is_active'     => true,
        ]);

        Livewire::test(ProductIndex::class)
            ->call('toggleStatus', $product->id)
            ->assertDispatched('toast');

        $this->assertFalse($product->fresh()->is_active);
    }

    public function test_product_index_can_search_and_filter_by_category_and_status(): void
    {
        $this->actingAs($this->adminUser);

        $p1 = Product::create([
            'sku'           => 'PROD-COCA',
            'name'          => 'Coca Cola',
            'type'          => 'simple',
            'cost'          => 1.00,
            'price'         => 2.00,
            'stock'         => 50,
            'minimum_stock' => 10,
            'is_active'     => true,
        ]);
        $p1->categories()->attach([$this->categoryA->id]);

        $p2 = Product::create([
            'sku'           => 'PROD-PEPSI',
            'name'          => 'Pepsi Cola',
            'type'          => 'simple',
            'cost'          => 1.00,
            'price'         => 2.00,
            'stock'         => 50,
            'minimum_stock' => 10,
            'is_active'     => false,
        ]);
        $p2->categories()->attach([$this->categoryB->id]);

        Livewire::test(ProductIndex::class)
            ->set('search', 'Coca')
            ->assertSee('PROD-COCA')
            ->assertDontSee('PROD-PEPSI')
            ->set('search', '')
            ->set('categoryFilter', (string)$this->categoryB->id)
            ->assertSee('PROD-PEPSI')
            ->assertDontSee('PROD-COCA')
            ->set('categoryFilter', '')
            ->set('statusFilter', 'inactive')
            ->assertSee('PROD-PEPSI')
            ->assertDontSee('PROD-COCA');
    }

    public function test_product_index_displays_action_buttons_when_permitted(): void
    {
        $this->actingAs($this->adminUser);

        $product = Product::create([
            'sku'           => 'PROD-VISIBLE',
            'name'          => 'Visible Product',
            'type'          => 'simple',
            'cost'          => 1.00,
            'price'         => 2.00,
            'stock'         => 10,
            'minimum_stock' => 2,
            'is_active'     => true,
        ]);

        Livewire::test(ProductIndex::class)
            ->assertSeeHtml('wire:click="openCreateModal"')
            ->assertSeeHtml("wire:click=\"openEditModal({$product->id})\"")
            ->assertSeeHtml("wire:click=\"openDeleteModal({$product->id})\"");
    }

    public function test_product_index_hides_mutation_buttons_for_view_only_user(): void
    {
        $viewOnlyUser = User::factory()->create();
        $viewOnlyUser->givePermissionTo('products.view');

        $this->actingAs($viewOnlyUser);

        $product = Product::create([
            'sku'           => 'PROD-VIEW-ONLY',
            'name'          => 'View Only Product',
            'type'          => 'simple',
            'cost'          => 1.00,
            'price'         => 2.00,
            'stock'         => 10,
            'minimum_stock' => 2,
            'is_active'     => true,
        ]);

        Livewire::test(ProductIndex::class)
            ->assertDontSeeHtml('wire:click="openCreateModal"')
            ->assertDontSeeHtml("wire:click=\"openEditModal({$product->id})\"")
            ->assertDontSeeHtml("wire:click=\"openDeleteModal({$product->id})\"");
    }
}
