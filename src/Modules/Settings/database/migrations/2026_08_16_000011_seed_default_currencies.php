<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
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
            $exists = DB::table('currencies')->where('code', $currency['code'])->first();
            if ($exists) {
                DB::table('currencies')->where('id', $exists->id)->update([
                    'name'       => $currency['name'],
                    'symbol'     => $currency['symbol'],
                    'is_active'  => $currency['is_active'],
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('currencies')->insert(array_merge($currency, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('currencies')->whereIn('code', ['USD', 'EUR', 'COP', 'PEN', 'MXN', 'ARS', 'CLP', 'BRL', 'GBP'])->delete();
    }
};
