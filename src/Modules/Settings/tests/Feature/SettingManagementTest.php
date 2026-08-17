<?php

namespace Modules\Settings\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Modules\Administration\Models\User;
use Modules\Settings\Database\Seeders\CurrencySeeder;
use Modules\Settings\Database\Seeders\SettingSeeder;
use Modules\Settings\Livewire\Settings\SettingIndex;
use Modules\Settings\Models\Currency;
use Modules\Settings\Models\Setting;
use Modules\Settings\Services\CurrencyService;
use Modules\Settings\Services\SettingService;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SettingManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $viewOnlyUser;
    protected User $unauthorizedUser;
    protected Currency $usdCurrency;
    protected Currency $eurCurrency;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $permissions = [
            'settings.view',
            'settings.update',
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
        $this->viewOnlyUser->givePermissionTo('settings.view');

        $this->unauthorizedUser = User::create([
            'name'     => 'Unauthorized User',
            'email'    => 'unauth@velntra.test',
            'password' => bcrypt('password123'),
        ]);

        $this->usdCurrency = Currency::firstOrCreate(
            ['code' => 'USD'],
            [
                'name'      => 'Dólar Estadounidense',
                'symbol'    => '$',
                'is_active' => true,
            ]
        );

        $this->eurCurrency = Currency::firstOrCreate(
            ['code' => 'EUR'],
            [
                'name'      => 'Euro',
                'symbol'    => '€',
                'is_active' => true,
            ]
        );
    }

    public function test_currency_service_returns_active_currencies(): void
    {
        $service = app(CurrencyService::class);

        $inactive = Currency::create([
            'name'      => 'Inactive Currency',
            'code'      => 'INACT',
            'symbol'    => '?',
            'is_active' => false,
        ]);

        $currencies = $service->getActiveCurrencies();

        $this->assertTrue($currencies->contains('code', 'USD'));
        $this->assertTrue($currencies->contains('code', 'EUR'));
        $this->assertFalse($currencies->contains('code', 'INACT'));
    }

    public function test_setting_service_returns_or_creates_singleton_settings(): void
    {
        $service = app(SettingService::class);

        $setting = $service->getSettings();

        $this->assertNotNull($setting);
        $this->assertEquals(1, $setting->id);
        $this->assertDatabaseHas('settings', ['id' => 1]);
    }

    public function test_setting_service_updates_settings_data(): void
    {
        $service = app(SettingService::class);
        $setting = $service->getSettings();

        $updated = $service->update($setting, [
            'company_name'   => 'Global Corporation',
            'ruc'            => '1790099887001',
            'phone'          => '0988888888',
            'email'          => 'info@globalcorp.test',
            'address'        => 'Avenida 10 de Agosto y Colón',
            'currency_id'    => $this->eurCurrency->id,
            'tax_percentage' => 12.00,
        ]);

        $this->assertEquals('Global Corporation', $updated->company_name);
        $this->assertEquals('1790099887001', $updated->ruc);
        $this->assertEquals($this->eurCurrency->id, $updated->currency_id);
        $this->assertEquals('12.00', (string) $updated->tax_percentage);
    }

    public function test_setting_service_handles_logo_upload_and_removal(): void
    {
        $service = app(SettingService::class);
        $setting = $service->getSettings();

        $fakeLogo = UploadedFile::fake()->image('business_logo.png', 300, 300);

        $updatedWithLogo = $service->update($setting, [
            'company_name'   => 'Velntra Corp',
            'currency_id'    => $this->usdCurrency->id,
            'tax_percentage' => 15.00,
        ], $fakeLogo);

        $this->assertNotNull($updatedWithLogo->logo);
        Storage::disk('public')->assertExists($updatedWithLogo->logo);

        // Remove logo
        $updatedWithoutLogo = $service->update($updatedWithLogo, [
            'company_name'   => 'Velntra Corp',
            'currency_id'    => $this->usdCurrency->id,
            'tax_percentage' => 15.00,
        ], null, true);

        $this->assertNull($updatedWithoutLogo->logo);
    }

    public function test_setting_index_component_authorizes_admin_and_renders(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(SettingIndex::class)
            ->assertStatus(200)
            ->assertSee($this->usdCurrency->code);
    }

    public function test_setting_index_component_denies_access_to_unauthorized_user(): void
    {
        $this->actingAs($this->unauthorizedUser);

        Livewire::test(SettingIndex::class)
            ->assertForbidden();
    }

    public function test_setting_index_can_update_settings_via_livewire(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(SettingIndex::class)
            ->set('company_name', 'Tech Solutions S.A.')
            ->set('ruc', '1791122334001')
            ->set('phone', '0911223344')
            ->set('email', 'contact@techsolutions.test')
            ->set('address', 'Calle Los Sauces 456')
            ->set('currency_id', $this->eurCurrency->id)
            ->set('tax_percentage', '15.00')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('toast');

        $this->assertDatabaseHas('settings', [
            'id'           => 1,
            'company_name' => 'Tech Solutions S.A.',
            'ruc'          => '1791122334001',
            'currency_id'  => $this->eurCurrency->id,
        ]);
    }

    public function test_setting_index_validates_required_fields(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(SettingIndex::class)
            ->set('company_name', '')
            ->set('currency_id', 999999)
            ->set('tax_percentage', 150)
            ->call('save')
            ->assertHasErrors(['company_name', 'currency_id', 'tax_percentage']);
    }

    public function test_setting_index_can_upload_and_remove_logo_via_livewire(): void
    {
        $this->actingAs($this->adminUser);

        $fakeLogo = UploadedFile::fake()->image('company_logo.png', 400, 400);

        Livewire::test(SettingIndex::class)
            ->set('company_name', 'Logo Test Company')
            ->set('currency_id', $this->usdCurrency->id)
            ->set('tax_percentage', 15.00)
            ->set('logoFile', $fakeLogo)
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('toast');

        $setting = Setting::first();
        $this->assertNotNull($setting->logo);
        Storage::disk('public')->assertExists($setting->logo);

        // Remove logo via Livewire
        Livewire::test(SettingIndex::class)
            ->call('removeLogo')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('toast');

        $this->assertNull($setting->fresh()->logo);
    }

    public function test_currency_and_setting_seeders_seed_database_properly(): void
    {
        $this->seed(CurrencySeeder::class);
        $this->seed(SettingSeeder::class);

        $this->assertDatabaseHas('currencies', ['code' => 'USD']);
        $this->assertDatabaseHas('currencies', ['code' => 'EUR']);
        $this->assertDatabaseHas('currencies', ['code' => 'COP']);
        $this->assertDatabaseHas('settings', ['id' => 1]);
    }
}
