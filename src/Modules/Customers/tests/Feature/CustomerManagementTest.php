<?php

namespace Modules\Customers\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Livewire\Livewire;
use Modules\Administration\Models\User;
use Modules\Customers\Database\Seeders\CustomerSeeder;
use Modules\Customers\Livewire\Customers\CustomerIndex;
use Modules\Customers\Models\Customer;
use Modules\Customers\Services\CustomerService;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CustomerManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $viewOnlyUser;
    protected User $unauthorizedUser;

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'customers.view',
            'customers.create',
            'customers.update',
            'customers.delete',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions($permissions);

        $this->adminUser = User::create([
            'name'     => 'Admin User',
            'email'    => 'admin@velntra.test',
            'password' => bcrypt('password123'),
        ]);
        $this->adminUser->assignRole('admin');

        $this->viewOnlyUser = User::create([
            'name'     => 'View Only User',
            'email'    => 'viewer@velntra.test',
            'password' => bcrypt('password123'),
        ]);
        $this->viewOnlyUser->givePermissionTo('customers.view');

        $this->unauthorizedUser = User::create([
            'name'     => 'Unauthorized User',
            'email'    => 'unauth@velntra.test',
            'password' => bcrypt('password123'),
        ]);
    }

    public function test_customer_service_creates_and_updates_customer(): void
    {
        $service = app(CustomerService::class);

        $customer = $service->create([
            'document' => '1712345678',
            'name'     => 'Acme Corporation',
            'phone'    => '0987654321',
            'email'    => 'contact@acme.test',
            'address'  => '123 Business Way',
            'is_active'=> true,
        ]);

        $this->assertDatabaseHas('customers', [
            'id'       => $customer->id,
            'document' => '1712345678',
            'name'     => 'Acme Corporation',
        ]);

        $updated = $service->update($customer, [
            'document' => '1712345678',
            'name'     => 'Acme Corporation Updated',
            'phone'    => '0999999999',
            'email'    => 'updated@acme.test',
            'address'  => '456 Commercial Blvd',
            'is_active'=> true,
        ]);

        $this->assertEquals('Acme Corporation Updated', $updated->fresh()->name);
        $this->assertEquals('456 Commercial Blvd', $updated->fresh()->address);
    }

    public function test_customer_service_deletes_regular_customer(): void
    {
        $service = app(CustomerService::class);

        $customer = Customer::create([
            'document'  => '1700000001',
            'name'      => 'Regular Customer',
            'is_active' => true,
        ]);

        $this->assertTrue($service->delete($customer));
        $this->assertSoftDeleted('customers', ['id' => $customer->id]);
    }

    public function test_customer_service_prevents_deleting_final_consumer(): void
    {
        $service = app(CustomerService::class);

        $finalConsumer = Customer::firstOrCreate(
            ['document' => '9999999999999'],
            ['name' => 'Consumidor Final', 'is_active' => true]
        );

        $this->expectException(InvalidArgumentException::class);
        $service->delete($finalConsumer);
    }

    public function test_customer_service_toggles_status(): void
    {
        $service = app(CustomerService::class);

        $customer = Customer::create([
            'name'      => 'Status Test Customer',
            'is_active' => true,
        ]);

        $service->toggleStatus($customer);
        $this->assertFalse($customer->fresh()->is_active);

        $service->toggleStatus($customer);
        $this->assertTrue($customer->fresh()->is_active);
    }

    public function test_customer_seeder_creates_or_updates_final_consumer(): void
    {
        $this->seed(CustomerSeeder::class);

        $this->assertDatabaseHas('customers', [
            'document'  => '9999999999999',
            'name'      => 'Consumidor Final',
            'is_active' => true,
        ]);
    }

    public function test_customer_index_component_authorizes_admin_and_renders(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(CustomerIndex::class)
            ->assertStatus(200);
    }

    public function test_customer_index_component_denies_access_to_unauthorized_user(): void
    {
        $this->actingAs($this->unauthorizedUser);

        Livewire::test(CustomerIndex::class)
            ->assertForbidden();
    }

    public function test_customer_index_can_create_customer_via_livewire(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(CustomerIndex::class)
            ->set('document', '1799887766')
            ->set('name', 'Livewire Created Customer')
            ->set('phone', '0912345678')
            ->set('email', 'livewire_customer@velntra.test')
            ->set('address', 'Calle 10 y Ave Central')
            ->set('is_active', true)
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('close-modal', 'customer-form')
            ->assertDispatched('toast');

        $this->assertDatabaseHas('customers', [
            'document' => '1799887766',
            'name'     => 'Livewire Created Customer',
            'email'    => 'livewire_customer@velntra.test',
        ]);
    }

    public function test_customer_index_can_edit_customer_via_livewire(): void
    {
        $this->actingAs($this->adminUser);

        $customer = Customer::create([
            'document'  => '1700112233',
            'name'      => 'Initial Customer Name',
            'is_active' => true,
        ]);

        Livewire::test(CustomerIndex::class)
            ->call('openEditModal', $customer->id)
            ->assertSet('name', 'Initial Customer Name')
            ->assertSet('document', '1700112233')
            ->set('name', 'Updated Customer Name')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('close-modal', 'customer-form')
            ->assertDispatched('toast');

        $this->assertEquals('Updated Customer Name', $customer->fresh()->name);
    }

    public function test_customer_index_can_delete_regular_customer(): void
    {
        $this->actingAs($this->adminUser);

        $customer = Customer::create([
            'document'  => '1744556677',
            'name'      => 'Customer to Delete',
            'is_active' => true,
        ]);

        Livewire::test(CustomerIndex::class)
            ->call('openDeleteModal', $customer->id)
            ->call('delete')
            ->assertDispatched('close-modal', 'delete-customer')
            ->assertDispatched('toast');

        $this->assertSoftDeleted('customers', ['id' => $customer->id]);
    }

    public function test_customer_index_displays_action_buttons_when_permitted(): void
    {
        $this->actingAs($this->adminUser);

        Customer::create([
            'name'      => 'Test Button Customer',
            'is_active' => true,
        ]);

        Livewire::test(CustomerIndex::class)
            ->assertSeeHtml('wire:click="openCreateModal"')
            ->assertSeeHtml('openEditModal')
            ->assertSeeHtml('openDeleteModal');
    }

    public function test_customer_index_hides_mutation_buttons_for_view_only_user(): void
    {
        $this->actingAs($this->viewOnlyUser);

        Customer::create([
            'name'      => 'Test Button Customer',
            'is_active' => true,
        ]);

        Livewire::test(CustomerIndex::class)
            ->assertStatus(200)
            ->assertDontSeeHtml('wire:click="openCreateModal"')
            ->assertDontSeeHtml('wire:click="openEditModal')
            ->assertDontSeeHtml('wire:click="openDeleteModal');
    }

    public function test_customer_index_supports_dynamic_per_page_and_search_filter(): void
    {
        $this->actingAs($this->adminUser);

        Customer::factory()->count(15)->create();
        Customer::create([
            'document'  => '1234567890',
            'name'      => 'Unique Search Customer',
            'email'     => 'unique_search@test.com',
            'is_active' => true,
        ]);

        Livewire::test(CustomerIndex::class)
            ->set('perPage', 5)
            ->assertViewHas('customers', fn($c) => $c->perPage() === 5)
            ->set('search', 'Unique Search')
            ->assertViewHas('customers', fn($c) => $c->total() === 1)
            ->set('search', 'Nonexistent Customer')
            ->assertViewHas('customers', fn($c) => $c->total() === 0);
    }

    public function test_customer_index_validates_unique_document(): void
    {
        $this->actingAs($this->adminUser);

        Customer::create([
            'document'  => '1799887766001',
            'name'      => 'Existing Customer With RUC',
            'is_active' => true,
        ]);

        Livewire::test(CustomerIndex::class)
            ->call('openCreateModal')
            ->set('name', 'Duplicate RUC Customer')
            ->set('document', '1799887766001')
            ->call('save')
            ->assertHasErrors(['document']);
    }

    public function test_customer_can_be_created_with_document_of_deleted_customer(): void
    {
        $service = app(CustomerService::class);

        $customerA = Customer::create([
            'document'  => '2345',
            'name'      => 'Customer to Delete',
            'is_active' => true,
        ]);

        $service->delete($customerA);
        $this->assertSoftDeleted('customers', ['id' => $customerA->id]);

        $customerB = $service->create([
            'document'  => '2345',
            'name'      => 'New Customer with Released Document',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('customers', [
            'id'       => $customerB->id,
            'document' => '2345',
        ]);
    }
}
