<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->string('cliente_fiado')->nullable()->after('metodo_pago');
            $table->enum('estado_pago', ['pagado', 'pendiente'])->default('pagado')->after('cliente_fiado');
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn(['cliente_fiado', 'estado_pago']);
        });
    }
};