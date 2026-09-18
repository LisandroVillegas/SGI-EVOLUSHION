<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->foreignId('turno_pago_id')->nullable()->after('turno_id')->constrained('turnos')->nullOnDelete();
            $table->timestamp('fecha_pago')->nullable()->after('estado_pago');
            $table->string('metodo_pago_saldo')->nullable()->after('fecha_pago');
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropForeign(['turno_pago_id']);
            $table->dropColumn(['turno_pago_id', 'fecha_pago', 'metodo_pago_saldo']);
        });
    }
};

