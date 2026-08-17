<?php

namespace Modules\Sales\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Inventory\Models\Product;
use Modules\Sales\Models\Sale;
use Modules\Sales\Models\SaleItem;

/**
 * @extends Factory<SaleItem>
 */
class SaleItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<SaleItem>
     */
    protected $model = SaleItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 5);
        $price = fake()->randomFloat(2, 5, 50);

        return [
            'sale_id'    => Sale::factory(),
            'product_id' => Product::factory(),
            'quantity'   => $quantity,
            'price'      => $price,
            'cost'       => round($price * 0.7, 2),
            'subtotal'   => round($quantity * $price, 2),
        ];
    }
}
