<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            // Cajero/Usuario que realiza la venta
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // Turno activo en el que se registra la venta
            $table->foreignId('turno_id')->constrained('turnos')->onDelete('cascade');
            
            $table->decimal('total', 12, 2);
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'mixto'])->default('efectivo');
            $table->decimal('pago_efectivo', 12, 2)->default(0);
            $table->decimal('pago_transferencia', 12, 2)->default(0);
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};