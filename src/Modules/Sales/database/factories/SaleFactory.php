<?php

namespace Modules\Sales\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Administration\Models\User;
use Modules\Customers\Models\Customer;
use Modules\Sales\Models\Sale;

/**
 * @extends Factory<Sale>
 */
class SaleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Sale>
     */
    protected $model = Sale::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 10, 200);
        $tax = round($subtotal * 0.15, 2);
        $total = $subtotal + $tax;

        return [
            'number'         => Sale::generateNextNumber(),
            'customer_id'    => Customer::factory(),
            'user_id'        => User::factory(),
            'subtotal'       => $subtotal,
            'discount'       => 0.00,
            'tax'            => $tax,
            'tax_percentage' => 15.00,
            'total'          => $total,
            'payment_method' => 'cash',
            'amount_paid'    => $total,
            'change'         => 0.00,
            'status'         => 'completed',
            'notes'          => null,
        ];
    }

    /**
     * Indicate that the sale is pending (held).
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'      => 'pending',
            'amount_paid' => null,
            'change'      => 0.00,
        ]);
    }

    /**
     * Indicate that the sale is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
}
