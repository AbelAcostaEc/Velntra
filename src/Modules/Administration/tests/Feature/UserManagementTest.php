<?php

namespace Modules\Administration\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Administration\Livewire\Users\UserIndex;
use Modules\Administration\Models\User;
use Modules\Administration\Services\UserService;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $regularUser;
    protected Role $adminRole;
    protected Role $sellerRole;

    protected function setUp(): void
    {
        parent::setUp();

        // Create permissions
        $permissions = [
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Create roles
        $this->adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $this->adminRole->syncPermissions($permissions);

        $this->sellerRole = Role::firstOrCreate(['name' => 'seller', 'guard_name' => 'web']);

        // Create admin user with permissions
        $this->adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@velntra.test',
            'password' => bcrypt('password123'),
        ]);
        $this->adminUser->assignRole('admin');

        // Create regular user without user management permissions
        $this->regularUser = User::create([
            'name' => 'Regular User',
            'email' => 'regular@velntra.test',
            'password' => bcrypt('password123'),
        ]);
        $this->regularUser->assignRole('seller');
    }

    public function test_user_service_creates_user_with_roles(): void
    {
        $service = app(UserService::class);

        $user = $service->create([
            'name' => 'New User',
            'email' => 'newuser@velntra.test',
            'password' => 'secret1234',
        ], ['seller']);

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@velntra.test',
            'name' => 'New User',
        ]);
        $this->assertTrue($user->hasRole('seller'));
    }

    public function test_user_service_updates_user_with_and_without_password(): void
    {
        $service = app(UserService::class);

        $user = User::create([
            'name' => 'Old Name',
            'email' => 'old@velntra.test',
            'password' => bcrypt('oldpassword'),
        ]);
        $user->assignRole('seller');

        // Update without password
        $updatedUser = $service->update($user, [
            'name' => 'Updated Name',
            'email' => 'updated@velntra.test',
            'password' => '',
        ], ['admin']);

        $this->assertEquals('Updated Name', $updatedUser->fresh()->name);
        $this->assertEquals('updated@velntra.test', $updatedUser->fresh()->email);
        $this->assertTrue($updatedUser->fresh()->hasRole('admin'));

        // Update with password
        $service->update($updatedUser, [
            'name' => 'Updated Name 2',
            'email' => 'updated@velntra.test',
            'password' => 'newpassword123',
        ], ['admin', 'seller']);

        $this->assertEquals('Updated Name 2', $updatedUser->fresh()->name);
        $this->assertTrue($updatedUser->fresh()->hasRole('seller'));
    }

    public function test_user_service_deletes_user(): void
    {
        $service = app(UserService::class);

        $user = User::create([
            'name' => 'To Delete',
            'email' => 'delete@velntra.test',
            'password' => bcrypt('password123'),
        ]);

        $this->assertTrue($service->delete($user));
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_user_index_component_authorizes_admin_and_renders(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(UserIndex::class)
            ->assertStatus(200)
            ->assertSee($this->adminUser->name);
    }

    public function test_user_index_component_denies_access_to_unauthorized_user(): void
    {
        $this->actingAs($this->regularUser);

        Livewire::test(UserIndex::class)
            ->assertForbidden();
    }

    public function test_user_index_can_create_user_via_livewire(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(UserIndex::class)
            ->set('name', 'Livewire Created')
            ->set('email', 'livewire@velntra.test')
            ->set('password', 'securepass123')
            ->set('selectedRoles', ['admin', 'seller'])
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('close-modal', 'user-form');

        $this->assertDatabaseHas('users', ['email' => 'livewire@velntra.test']);
        $created = User::where('email', 'livewire@velntra.test')->first();
        $this->assertTrue($created->hasRole('admin'));
        $this->assertTrue($created->hasRole('seller'));
    }

    public function test_user_index_can_edit_user_without_modifying_password(): void
    {
        $this->actingAs($this->adminUser);

        $targetUser = User::create([
            'name' => 'Target User',
            'email' => 'target@velntra.test',
            'password' => bcrypt('originalpassword'),
        ]);
        $targetUser->assignRole('seller');

        Livewire::test(UserIndex::class)
            ->call('openEditModal', $targetUser->id)
            ->assertSet('name', 'Target User')
            ->assertSet('email', 'target@velntra.test')
            ->assertSet('password', '')
            ->set('name', 'Target Edited')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertEquals('Target Edited', $targetUser->fresh()->name);
    }

    public function test_user_index_can_delete_user(): void
    {
        $this->actingAs($this->adminUser);

        $targetUser = User::create([
            'name' => 'Target User 2',
            'email' => 'target2@velntra.test',
            'password' => bcrypt('originalpassword'),
        ]);

        Livewire::test(UserIndex::class)
            ->call('openDeleteModal', $targetUser->id)
            ->call('delete')
            ->assertDispatched('close-modal', 'delete-user');

        $this->assertDatabaseMissing('users', ['id' => $targetUser->id]);
    }

    public function test_user_index_displays_action_buttons_when_permitted(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(UserIndex::class)
            ->assertSeeHtml('openCreateModal')
            ->assertSeeHtml('openEditModal')
            ->assertSeeHtml('openDeleteModal');
    }

    public function test_user_index_hides_mutation_buttons_for_view_only_user(): void
    {
        $viewOnlyUser = User::create([
            'name' => 'Viewer User',
            'email' => 'viewer@velntra.test',
            'password' => bcrypt('password123'),
        ]);
        $viewOnlyUser->givePermissionTo('users.view');

        $this->actingAs($viewOnlyUser);

        Livewire::test(UserIndex::class)
            ->assertStatus(200)
            ->assertDontSeeHtml('openCreateModal')
            ->assertDontSeeHtml('openEditModal')
            ->assertDontSeeHtml('openDeleteModal');
    }
}
