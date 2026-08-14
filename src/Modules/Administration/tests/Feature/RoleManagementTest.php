<?php

namespace Modules\Administration\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Administration\Livewire\Roles\RoleIndex;
use Modules\Administration\Models\User;
use Modules\Administration\Services\RoleService;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $regularUser;
    protected Role $adminRole;
    protected Role $sellerRole;

    protected function setUp(): void
    {
        parent::setUp();

        // Create sample permissions
        $permissions = [
            'roles.view',
            'roles.create',
            'roles.update',
            'roles.delete',
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'products.view',
            'products.create',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Create admin role with permissions
        $this->adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $this->adminRole->syncPermissions($permissions);

        // Create seller role with limited permissions
        $this->sellerRole = Role::firstOrCreate(['name' => 'seller', 'guard_name' => 'web']);
        $this->sellerRole->syncPermissions(['products.view', 'products.create']);

        // Create admin user
        $this->adminUser = User::create([
            'name'     => 'Admin User',
            'email'    => 'admin@velntra.test',
            'password' => bcrypt('password123'),
        ]);
        $this->adminUser->assignRole('admin');

        // Create regular user without role permissions
        $this->regularUser = User::create([
            'name'     => 'Regular User',
            'email'    => 'regular@velntra.test',
            'password' => bcrypt('password123'),
        ]);
        $this->regularUser->assignRole('seller');
    }

    public function test_role_service_creates_role_with_permissions(): void
    {
        $service = app(RoleService::class);

        $role = $service->create([
            'name' => 'manager',
        ], ['users.view', 'products.view']);

        $this->assertDatabaseHas('roles', [
            'name'       => 'manager',
            'guard_name' => 'web',
        ]);
        $this->assertTrue($role->hasPermissionTo('users.view'));
        $this->assertTrue($role->hasPermissionTo('products.view'));
        $this->assertFalse($role->hasPermissionTo('roles.delete'));
    }

    public function test_role_service_updates_role_with_permissions(): void
    {
        $service = app(RoleService::class);

        $role = Role::create(['name' => 'supervisor', 'guard_name' => 'web']);
        $role->syncPermissions(['users.view']);

        $updatedRole = $service->update($role, [
            'name' => 'lead_supervisor',
        ], ['users.view', 'users.create', 'products.view']);

        $this->assertEquals('lead_supervisor', $updatedRole->fresh()->name);
        $this->assertTrue($updatedRole->fresh()->hasPermissionTo('users.create'));
        $this->assertTrue($updatedRole->fresh()->hasPermissionTo('products.view'));
    }

    public function test_role_service_deletes_role(): void
    {
        $service = app(RoleService::class);

        $role = Role::create(['name' => 'to_delete', 'guard_name' => 'web']);

        $this->assertTrue($service->delete($role));
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    public function test_role_index_component_authorizes_admin_and_renders(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(RoleIndex::class)
            ->assertStatus(200)
            ->assertSee('Admin')
            ->assertSee('Seller');
    }

    public function test_role_index_component_denies_access_to_unauthorized_user(): void
    {
        $this->actingAs($this->regularUser);

        Livewire::test(RoleIndex::class)
            ->assertForbidden();
    }

    public function test_role_index_can_create_role_via_livewire(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(RoleIndex::class)
            ->set('name', 'Auditor')
            ->set('selectedPermissions', ['users.view', 'products.view'])
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('close-modal', 'role-form')
            ->assertDispatched('toast');

        $this->assertDatabaseHas('roles', ['name' => 'auditor']);
        $created = Role::where('name', 'auditor')->first();
        $this->assertTrue($created->hasPermissionTo('users.view'));
        $this->assertTrue($created->hasPermissionTo('products.view'));
    }

    public function test_role_index_validates_unique_name(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(RoleIndex::class)
            ->set('name', 'admin') // Already exists
            ->call('save')
            ->assertHasErrors(['name']);
    }

    public function test_role_index_can_edit_role_and_sync_permissions(): void
    {
        $this->actingAs($this->adminUser);

        $targetRole = Role::create(['name' => 'cashier', 'guard_name' => 'web']);
        $targetRole->syncPermissions(['products.view']);

        Livewire::test(RoleIndex::class)
            ->call('openEditModal', $targetRole->id)
            ->assertSet('name', 'cashier')
            ->assertSet('selectedPermissions', ['products.view'])
            ->set('name', 'chief_cashier')
            ->set('selectedPermissions', ['products.view', 'products.create'])
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('toast');

        $this->assertEquals('chief_cashier', $targetRole->fresh()->name);
        $this->assertTrue($targetRole->fresh()->hasPermissionTo('products.create'));
    }

    public function test_role_index_can_delete_role(): void
    {
        $this->actingAs($this->adminUser);

        $targetRole = Role::create(['name' => 'temp_role', 'guard_name' => 'web']);

        Livewire::test(RoleIndex::class)
            ->call('openDeleteModal', $targetRole->id)
            ->call('delete')
            ->assertDispatched('close-modal', 'delete-role')
            ->assertDispatched('toast');

        $this->assertDatabaseMissing('roles', ['id' => $targetRole->id]);
    }

    public function test_role_index_can_select_and_deselect_all_permissions(): void
    {
        $this->actingAs($this->adminUser);

        $allPermissionNames = Permission::pluck('name')->toArray();

        Livewire::test(RoleIndex::class)
            ->call('selectAllPermissions')
            ->assertSet('selectedPermissions', $allPermissionNames)
            ->call('deselectAllPermissions')
            ->assertSet('selectedPermissions', []);
    }

    public function test_role_index_can_toggle_module_permissions(): void
    {
        $this->actingAs($this->adminUser);

        $userPerms = ['users.view', 'users.create', 'users.update', 'users.delete'];

        Livewire::test(RoleIndex::class)
            ->set('selectedPermissions', [])
            ->call('toggleModulePermissions', $userPerms)
            ->assertSet('selectedPermissions', $userPerms)
            ->call('toggleModulePermissions', $userPerms)
            ->assertSet('selectedPermissions', []);
    }

    public function test_role_index_displays_action_buttons_when_permitted(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(RoleIndex::class)
            ->assertSeeHtml('wire:click="openCreateModal"')
            ->assertSeeHtml('openEditModal')
            ->assertSeeHtml('openDeleteModal');
    }

    public function test_role_index_hides_mutation_buttons_for_view_only_user(): void
    {
        $viewOnlyUser = User::create([
            'name'     => 'Viewer User',
            'email'    => 'viewer@velntra.test',
            'password' => bcrypt('password123'),
        ]);
        $viewOnlyUser->givePermissionTo('roles.view');

        $this->actingAs($viewOnlyUser);

        Livewire::test(RoleIndex::class)
            ->assertStatus(200)
            ->assertDontSeeHtml('wire:click="openCreateModal"')
            ->assertDontSeeHtml('wire:click="openEditModal')
            ->assertDontSeeHtml('wire:click="openDeleteModal');
    }

    public function test_role_index_supports_dynamic_per_page(): void
    {
        $this->actingAs($this->adminUser);

        for ($i = 1; $i <= 15; $i++) {
            Role::create(['name' => "Custom Role {$i}", 'guard_name' => 'web']);
        }

        Livewire::test(RoleIndex::class)
            ->set('perPage', 5)
            ->assertViewHas('roles', fn($roles) => $roles->perPage() === 5)
            ->set('perPage', 20)
            ->assertViewHas('roles', fn($roles) => $roles->perPage() === 20);
    }
}
