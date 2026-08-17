<?php

namespace Modules\Settings\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Settings\Models\Currency;
use Modules\Settings\Models\Setting;

/**
 * @extends Factory<Setting>
 */
class SettingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Setting>
     */
    protected $model = Setting::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_name'   => fake()->company(),
            'ruc'            => fake()->numerify('179001#######'),
            'phone'          => fake()->phoneNumber(),
            'email'          => fake()->companyEmail(),
            'address'        => fake()->address(),
            'logo'           => null,
            'currency_id'    => Currency::factory(),
            'tax_percentage' => 15.00,
        ];
    }
}
