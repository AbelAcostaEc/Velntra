<?php

namespace Modules\Inventory\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Administration\Models\User;
use Modules\Inventory\Livewire\Categories\CategoryIndex;
use Modules\Inventory\Models\Category;
use Modules\Inventory\Services\CategoryService;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected User $unauthorizedUser;

    protected CategoryService $categoryService;

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

        $this->categoryService = new CategoryService();
    }

    public function test_category_service_creates_category(): void
    {
        $this->actingAs($this->adminUser);

        $category = $this->categoryService->create([
            'code'        => 'CAT-TEST-01',
            'name'        => 'Test Category',
            'description' => 'Test description',
            'is_active'   => true,
        ]);

        $this->assertDatabaseHas('categories', [
            'id'         => $category->id,
            'code'       => 'CAT-TEST-01',
            'name'       => 'Test Category',
            'created_by' => $this->adminUser->id,
        ]);
    }

    public function test_category_service_updates_category(): void
    {
        $this->actingAs($this->adminUser);

        $category = Category::create([
            'code'      => 'CAT-OLD',
            'name'      => 'Old Name',
            'is_active' => true,
        ]);

        $updated = $this->categoryService->update($category, [
            'code'        => 'CAT-NEW',
            'name'        => 'New Name',
            'description' => 'Updated description',
            'is_active'   => false,
        ]);

        $this->assertSame('CAT-NEW', $updated->code);
        $this->assertSame('New Name', $updated->name);
        $this->assertFalse($updated->is_active);
        $this->assertSame($this->adminUser->id, $updated->updated_by);
    }

    public function test_category_service_deletes_category(): void
    {
        $this->actingAs($this->adminUser);

        $category = Category::create([
            'code'      => 'CAT-DEL',
            'name'      => 'To Delete',
            'is_active' => true,
        ]);

        $result = $this->categoryService->delete($category);

        $this->assertTrue($result);
        $this->assertSoftDeleted('categories', [
            'id'         => $category->id,
            'deleted_by' => $this->adminUser->id,
        ]);
    }

    public function test_category_service_toggles_status(): void
    {
        $this->actingAs($this->adminUser);

        $category = Category::create([
            'code'      => 'CAT-TOGGLE',
            'name'      => 'Toggle Category',
            'is_active' => true,
        ]);

        $toggled = $this->categoryService->toggleStatus($category);
        $this->assertFalse($toggled->is_active);

        $toggledAgain = $this->categoryService->toggleStatus($toggled);
        $this->assertTrue($toggledAgain->is_active);
    }

    public function test_category_index_component_authorizes_admin_and_renders(): void
    {
        $this->actingAs($this->adminUser);

        Category::create([
            'code'      => 'CAT-RENDER',
            'name'      => 'Render Category',
            'is_active' => true,
        ]);

        $response = $this->get(route('categories.index'));

        $response->assertOk()
            ->assertSee('Render Category')
            ->assertSee('CAT-RENDER');
    }

    public function test_category_index_component_denies_access_to_unauthorized_user(): void
    {
        $this->actingAs($this->unauthorizedUser);

        $response = $this->get(route('categories.index'));
        $response->assertForbidden();

        Livewire::test(CategoryIndex::class)
            ->assertForbidden();
    }

    public function test_category_index_can_create_category_via_livewire(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(CategoryIndex::class)
            ->call('openCreateModal')
            ->set('code', 'CAT-LIV-01')
            ->set('name', 'Livewire Created Category')
            ->set('description', 'Livewire description')
            ->set('is_active', true)
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('close-modal', 'category-form')
            ->assertDispatched('toast');

        $this->assertDatabaseHas('categories', [
            'code'       => 'CAT-LIV-01',
            'name'       => 'Livewire Created Category',
            'created_by' => $this->adminUser->id,
        ]);
    }

    public function test_category_index_validates_required_and_unique_code(): void
    {
        $this->actingAs($this->adminUser);

        Category::create([
            'code'      => 'CAT-EXISTING',
            'name'      => 'Existing Category',
            'is_active' => true,
        ]);

        Livewire::test(CategoryIndex::class)
            ->set('code', '')
            ->set('name', '')
            ->call('save')
            ->assertHasErrors(['code', 'name']);

        Livewire::test(CategoryIndex::class)
            ->set('code', 'CAT-EXISTING')
            ->set('name', 'Another Category')
            ->call('save')
            ->assertHasErrors(['code']);
    }

    public function test_category_index_can_edit_category_via_livewire(): void
    {
        $this->actingAs($this->adminUser);

        $category = Category::create([
            'code'        => 'CAT-ORIG',
            'name'        => 'Original Name',
            'description' => 'Orig desc',
            'is_active'   => true,
        ]);

        Livewire::test(CategoryIndex::class)
            ->call('openEditModal', $category->id)
            ->assertSet('code', 'CAT-ORIG')
            ->assertSet('name', 'Original Name')
            ->set('name', 'Updated via Livewire')
            ->set('is_active', false)
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('close-modal', 'category-form')
            ->assertDispatched('toast');

        $this->assertDatabaseHas('categories', [
            'id'        => $category->id,
            'name'      => 'Updated via Livewire',
            'is_active' => false,
        ]);
    }

    public function test_category_index_can_delete_category_via_livewire(): void
    {
        $this->actingAs($this->adminUser);

        $category = Category::create([
            'code'      => 'CAT-TO-DEL',
            'name'      => 'Category to Delete',
            'is_active' => true,
        ]);

        Livewire::test(CategoryIndex::class)
            ->call('openDeleteModal', $category->id)
            ->assertSet('selectedCategoryId', $category->id)
            ->assertDispatched('open-modal', 'delete-category')
            ->call('delete')
            ->assertDispatched('close-modal', 'delete-category')
            ->assertDispatched('toast');

        $this->assertSoftDeleted('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_category_index_can_toggle_category_status_via_livewire(): void
    {
        $this->actingAs($this->adminUser);

        $category = Category::create([
            'code'      => 'CAT-TOG',
            'name'      => 'Toggle Category',
            'is_active' => true,
        ]);

        Livewire::test(CategoryIndex::class)
            ->call('toggleStatus', $category->id)
            ->assertDispatched('toast');

        $this->assertFalse($category->fresh()->is_active);
    }

    public function test_category_index_can_search_and_filter_by_status(): void
    {
        $this->actingAs($this->adminUser);

        Category::create(['code' => 'CAT-ALPHA', 'name' => 'Alpha Category', 'is_active' => true]);
        Category::create(['code' => 'CAT-BETA', 'name' => 'Beta Category', 'is_active' => false]);

        Livewire::test(CategoryIndex::class)
            ->set('search', 'Alpha')
            ->assertSee('CAT-ALPHA')
            ->assertDontSee('CAT-BETA')
            ->set('search', '')
            ->set('statusFilter', 'inactive')
            ->assertSee('CAT-BETA')
            ->assertDontSee('CAT-ALPHA');
    }

    public function test_category_index_displays_action_buttons_when_permitted(): void
    {
        $this->actingAs($this->adminUser);

        $category = Category::create([
            'code'      => 'CAT-VISIBLE',
            'name'      => 'Visible Category',
            'is_active' => true,
        ]);

        Livewire::test(CategoryIndex::class)
            ->assertSeeHtml('wire:click="openCreateModal"')
            ->assertSeeHtml("wire:click=\"openEditModal({$category->id})\"")
            ->assertSeeHtml("wire:click=\"openDeleteModal({$category->id})\"");
    }

    public function test_category_index_hides_mutation_buttons_for_view_only_user(): void
    {
        $viewOnlyUser = User::factory()->create();
        $viewOnlyUser->givePermissionTo('categories.view');

        $this->actingAs($viewOnlyUser);

        $category = Category::create([
            'code'      => 'CAT-VIEW-ONLY',
            'name'      => 'View Only Category',
            'is_active' => true,
        ]);

        Livewire::test(CategoryIndex::class)
            ->assertDontSeeHtml('wire:click="openCreateModal"')
            ->assertDontSeeHtml("wire:click=\"openEditModal({$category->id})\"")
            ->assertDontSeeHtml("wire:click=\"openDeleteModal({$category->id})\"");
    }

    public function test_category_index_supports_dynamic_per_page(): void
    {
        $this->actingAs($this->adminUser);

        for ($i = 1; $i <= 15; $i++) {
            Category::create([
                'code'      => "CAT-PAGE-{$i}",
                'name'      => "Category {$i}",
                'is_active' => true,
            ]);
        }

        Livewire::test(CategoryIndex::class)
            ->set('perPage', 5)
            ->assertViewHas('categories', fn($cats) => $cats->perPage() === 5)
            ->set('perPage', 30)
            ->assertViewHas('categories', fn($cats) => $cats->perPage() === 30);
    }
}
