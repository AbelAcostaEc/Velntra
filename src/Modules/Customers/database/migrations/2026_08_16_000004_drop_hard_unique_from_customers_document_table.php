<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropUnique(['document']);
            });
        } catch (\Throwable $e) {
            // Si el índice no existe (ej. SQLite en memoria para testing), se ignora de forma segura
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
