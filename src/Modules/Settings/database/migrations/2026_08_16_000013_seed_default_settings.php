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
        $usd = DB::table('currencies')->where('code', 'USD')->first();
        $currencyId = $usd ? $usd->id : DB::table('currencies')->value('id');

        $exists = DB::table('settings')->where('id', 1)->first();

        if ($exists) {
            DB::table('settings')->where('id', 1)->update([
                'company_name'   => $exists->company_name ?: 'Velntra Store',
                'currency_id'    => $exists->currency_id ?: $currencyId,
                'tax_percentage' => $exists->tax_percentage ?: 15.00,
                'updated_at'     => now(),
            ]);
        } else {
            DB::table('settings')->insert([
                'id'             => 1,
                'company_name'   => 'Velntra Store',
                'ruc'            => '1790012345001',
                'phone'          => '0999999999',
                'email'          => 'contacto@velntra.test',
                'address'        => 'Av. Principal 123 y Secundaria',
                'logo'           => null,
                'currency_id'    => $currencyId,
                'tax_percentage' => 15.00,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->where('id', 1)->delete();
    }
};
