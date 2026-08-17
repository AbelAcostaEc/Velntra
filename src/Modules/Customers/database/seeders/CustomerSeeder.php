<?php

namespace Modules\Customers\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Customers\Models\Customer;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::updateOrCreate(
            ['document' => '9999999999999'],
            [
                'name'      => 'Consumidor Final',
                'phone'     => null,
                'email'     => null,
                'address'   => null,
                'is_active' => true,
            ]
        );
    }
}
