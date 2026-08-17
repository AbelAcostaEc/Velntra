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
        $exists = DB::table('customers')
            ->where('document', '9999999999999')
            ->orWhere('name', 'Consumidor Final')
            ->first();

        if ($exists) {
            DB::table('customers')->where('id', $exists->id)->update([
                'document'   => '9999999999999',
                'name'       => 'Consumidor Final',
                'is_active'  => true,
                'updated_at' => now(),
            ]);
        } else {
            DB::table('customers')->insert([
                'document'   => '9999999999999',
                'name'       => 'Consumidor Final',
                'phone'      => null,
                'email'      => null,
                'address'    => null,
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('customers')
            ->where('document', '9999999999999')
            ->delete();
    }
};
