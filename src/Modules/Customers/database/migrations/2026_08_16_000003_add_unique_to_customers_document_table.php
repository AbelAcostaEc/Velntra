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
        // El documento se valida a nivel de aplicación con Rule::unique()->whereNull('deleted_at')
        // para permitir la reutilización de documentos de clientes eliminados con SoftDeletes.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
