<?php

namespace Modules\Settings\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Settings\Models\Currency;
use Modules\Settings\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usd = Currency::where('code', 'USD')->first();

        Setting::updateOrCreate(
            ['id' => 1],
            [
                'company_name'   => 'Velntra Store',
                'ruc'            => '1790012345001',
                'phone'          => '0999999999',
                'email'          => 'contacto@velntra.test',
                'address'        => 'Av. Principal 123 y Secundaria',
                'logo'           => null,
                'currency_id'    => $usd?->id ?? Currency::value('id'),
                'tax_percentage' => 15.00,
            ]
        );
    }
}
