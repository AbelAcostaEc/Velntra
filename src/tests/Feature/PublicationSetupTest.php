<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Database\Seeders\PortfolioDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Modules\Administration\Database\Seeders\UserSeeder;
use Modules\Administration\Models\User;
use Modules\Inventory\Models\Category;
use Modules\Inventory\Models\Product;
use Modules\Sales\Models\Sale;
use Modules\Settings\Models\Currency;
use Modules\Settings\Models\Setting;
use RuntimeException;
use Tests\TestCase;

class PublicationSetupTest extends TestCase
{
    use RefreshDatabase;

    public function test_structural_seeders_create_defaults_without_overwriting_business_settings(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::where('email', config('initial_admin.email'))->firstOrFail();
        $this->assertTrue($admin->hasRole('admin'));
        $this->assertSame(9, Currency::count());
        $this->assertSame(1, Setting::count());

        $admin->update(['password' => Hash::make('changed-password')]);
        $setting = Setting::firstOrFail();
        $setting->update(['company_name' => 'Mi negocio', 'tax_percentage' => 12]);
        Currency::where('code', 'EUR')->update(['is_active' => false]);

        $this->seed(DatabaseSeeder::class);

        $this->assertSame(1, User::where('email', $admin->email)->count());
        $this->assertTrue(Hash::check('changed-password', $admin->fresh()->password));
        $this->assertSame('Mi negocio', $setting->fresh()->company_name);
        $this->assertEquals(12, $setting->fresh()->tax_percentage);
        $this->assertFalse(Currency::where('code', 'EUR')->firstOrFail()->is_active);

        $originalEmail = config('initial_admin.email');
        config()->set('initial_admin.email', 'another-admin@example.com');

        try {
            $this->seed(DatabaseSeeder::class);
            $this->assertSame(0, User::where('email', 'another-admin@example.com')->count());
        } finally {
            config()->set('initial_admin.email', $originalEmail);
        }
    }

    public function test_optional_portfolio_data_is_repeatable_and_never_part_of_default_seed(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->assertSame(0, Category::where('code', 'like', 'DEMO-%')->count());

        $this->seed(PortfolioDemoSeeder::class);

        $this->assertSame(3, Category::where('code', 'like', 'DEMO-%')->count());
        $this->assertSame(6, Product::where('sku', 'like', 'DEMO-%')->count());
        $this->assertSame(4, Sale::where('status', 'completed')->where('notes', 'like', 'DEMO:%')->count());
        $this->assertSame(1, Sale::where('status', 'pending')->where('notes', 'DEMO: venta en espera')->count());

        $stock = Product::where('sku', 'DEMO-CUA-001')->firstOrFail()->stock;
        $this->seed(PortfolioDemoSeeder::class);

        $this->assertSame(4, Sale::where('status', 'completed')->where('notes', 'like', 'DEMO:%')->count());
        $this->assertSame($stock, Product::where('sku', 'DEMO-CUA-001')->firstOrFail()->stock);
    }

    public function test_default_admin_credentials_are_rejected_in_production(): void
    {
        $originalEnvironment = app()->environment();
        $originalEmail = config('initial_admin.email');
        $originalPassword = config('initial_admin.password');
        app()->instance('env', 'production');
        config()->set('initial_admin.email', 'admin@velntra.com');
        config()->set('initial_admin.password', 'admin@velntra.com');

        try {
            $this->expectException(RuntimeException::class);
            app(UserSeeder::class)->run();
        } finally {
            app()->instance('env', $originalEnvironment);
            config()->set('initial_admin.email', $originalEmail);
            config()->set('initial_admin.password', $originalPassword);
        }
    }

    public function test_existing_admin_with_known_password_is_rejected_in_production(): void
    {
        $this->seed(DatabaseSeeder::class);

        $originalEnvironment = app()->environment();
        app()->instance('env', 'production');

        try {
            $this->expectException(RuntimeException::class);
            app(UserSeeder::class)->run();
        } finally {
            app()->instance('env', $originalEnvironment);
        }
    }
}
