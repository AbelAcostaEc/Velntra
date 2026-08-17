<?php

namespace Modules\Customers\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Customers\Models\Customer;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Customer>
     */
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'document'   => fake()->unique()->numerify('##########'),
            'name'       => fake()->name(),
            'phone'      => fake()->phoneNumber(),
            'email'      => fake()->unique()->safeEmail(),
            'address'    => fake()->address(),
            'is_active'  => true,
        ];
    }

    /**
     * Indicate that the customer is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
