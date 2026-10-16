<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Administration\Models\User;
use Modules\Customers\Models\Customer;
use Modules\Inventory\Models\Category;
use Modules\Inventory\Models\Product;
use Modules\Sales\Models\Sale;
use Modules\Sales\Services\SaleService;
use RuntimeException;

class PortfolioDemoSeeder extends Seeder
{
    /**
     * Datos ficticios para una base de demostración aislada. Nunca es parte de DatabaseSeeder.
     */
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new RuntimeException('PortfolioDemoSeeder no debe ejecutarse en producción. Usa una base de demostración aislada.');
        }

        $this->call(DatabaseSeeder::class);

        $admin = User::where('email', config('initial_admin.email'))->firstOrFail();

        $categories = [];
        foreach ([
            'DEMO-PAP' => 'Papelería demo',
            'DEMO-ESC' => 'Escritura demo',
            'DEMO-ACC' => 'Accesorios demo',
        ] as $code => $name) {
            $categories[$code] = Category::firstOrCreate(
                ['code' => $code],
                ['name' => $name, 'description' => 'Categoría ficticia para el portafolio.', 'is_active' => true, 'created_by' => $admin->id]
            );
        }

        $products = [];
        foreach ([
            ['DEMO-CUA-001', 'Cuaderno rayado demo', 2.10, 3.50, 48, 12, 'DEMO-PAP'],
            ['DEMO-AGE-001', 'Agenda semanal demo', 5.00, 8.90, 24, 6, 'DEMO-PAP'],
            ['DEMO-BOL-001', 'Bolígrafo azul demo', 0.35, 0.85, 120, 20, 'DEMO-ESC'],
            ['DEMO-MAR-001', 'Marcadores de colores demo', 3.20, 5.75, 17, 5, 'DEMO-ESC'],
            ['DEMO-CAR-001', 'Carpeta organizadora demo', 1.40, 2.95, 31, 8, 'DEMO-ACC'],
            ['DEMO-LAM-001', 'Lámpara de escritorio demo', 10.00, 16.90, 3, 5, 'DEMO-ACC'],
        ] as [$sku, $name, $cost, $price, $stock, $minimumStock, $categoryCode]) {
            $product = Product::firstOrCreate(
                ['sku' => $sku],
                [
                    'type' => 'simple',
                    'name' => $name,
                    'description' => 'Producto ficticio para capturas y demostraciones.',
                    'cost' => $cost,
                    'price' => $price,
                    'stock' => $stock,
                    'minimum_stock' => $minimumStock,
                    'is_active' => true,
                    'created_by' => $admin->id,
                ]
            );
            $product->categories()->syncWithoutDetaching([$categories[$categoryCode]->id]);
            $products[$sku] = $product;
        }

        $customers = [];
        foreach ([
            ['DEMO-CLI-001', 'Cliente Demo Uno', 'cliente-uno@example.com'],
            ['DEMO-CLI-002', 'Cliente Demo Dos', 'cliente-dos@example.com'],
        ] as [$document, $name, $email]) {
            $customers[$document] = Customer::firstOrCreate(
                ['document' => $document],
                ['name' => $name, 'email' => $email, 'is_active' => true]
            );
        }

        $saleService = app(SaleService::class);

        foreach ([
            ['DEMO: venta 1', 'DEMO-CLI-001', 'cash', 6, [['DEMO-CUA-001', 2], ['DEMO-BOL-001', 4]]],
            ['DEMO: venta 2', 'DEMO-CLI-002', 'card', 4, [['DEMO-AGE-001', 1], ['DEMO-MAR-001', 1]]],
            ['DEMO: venta 3', 'DEMO-CLI-001', 'transfer', 2, [['DEMO-CAR-001', 3], ['DEMO-BOL-001', 2]]],
            ['DEMO: venta 4', 'DEMO-CLI-002', 'cash', 0, [['DEMO-CUA-001', 1], ['DEMO-AGE-001', 1]]],
        ] as [$marker, $customerDocument, $paymentMethod, $daysAgo, $lines]) {
            if (Sale::where('notes', $marker)->exists()) {
                continue;
            }

            $items = array_map(fn (array $line): array => [
                'product_id' => $products[$line[0]]->id,
                'quantity' => $line[1],
            ], $lines);

            $sale = $saleService->createSale([
                'customer_id' => $customers[$customerDocument]->id,
                'user_id' => $admin->id,
                'payment_method' => $paymentMethod,
                'status' => 'completed',
                'notes' => $marker,
            ], $items);

            $sale->forceFill(['created_at' => now()->subDays($daysAgo)])->saveQuietly();
        }

        if (! Sale::where('notes', 'DEMO: venta en espera')->exists()) {
            $saleService->createSale([
                'customer_id' => $customers['DEMO-CLI-001']->id,
                'user_id' => $admin->id,
                'status' => 'pending',
                'notes' => 'DEMO: venta en espera',
            ], [
                ['product_id' => $products['DEMO-LAM-001']->id, 'quantity' => 1],
            ]);
        }
    }
}
