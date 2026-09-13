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
        Schema::table('compras', function (Blueprint $table) {
            // Agrega la columna turno_id después del ID y la relaciona con la tabla turnos
            $table->foreignId('turno_id')->nullable()->after('id')->constrained('turnos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            // Elimina la clave foránea y la columna en caso de hacer rollback
            $table->dropForeign(['turno_id']);
            $table->dropColumn('turno_id');
        });
    }
};