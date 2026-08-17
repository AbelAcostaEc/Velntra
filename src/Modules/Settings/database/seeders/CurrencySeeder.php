<?php

namespace Modules\Settings\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Settings\Models\Currency;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            ['code' => 'USD', 'name' => 'Dólar Estadounidense', 'symbol' => '$', 'is_active' => true],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'is_active' => true],
            ['code' => 'COP', 'name' => 'Peso Colombiano', 'symbol' => '$', 'is_active' => true],
            ['code' => 'PEN', 'name' => 'Sol Peruano', 'symbol' => 'S/', 'is_active' => true],
            ['code' => 'MXN', 'name' => 'Peso Mexicano', 'symbol' => '$', 'is_active' => true],
            ['code' => 'ARS', 'name' => 'Peso Argentino', 'symbol' => '$', 'is_active' => true],
            ['code' => 'CLP', 'name' => 'Peso Chileno', 'symbol' => '$', 'is_active' => true],
            ['code' => 'BRL', 'name' => 'Real Brasileño', 'symbol' => 'R$', 'is_active' => true],
            ['code' => 'GBP', 'name' => 'Libra Esterlina', 'symbol' => '£', 'is_active' => true],
        ];

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(
                ['code' => $currency['code']],
                [
                    'name'      => $currency['name'],
                    'symbol'    => $currency['symbol'],
                    'is_active' => $currency['is_active'],
                ]
            );
        }
    }
}
