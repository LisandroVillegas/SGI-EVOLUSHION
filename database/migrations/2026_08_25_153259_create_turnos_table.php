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
        Schema::create('turnos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->timestamp('fecha_inicio');
        $table->timestamp('fecha_cierre')->nullable();
        $table->decimal('base_caja', 10, 2);
        $table->decimal('total_efectivo_esperado', 10, 2)->default(0);
        $table->decimal('total_efectivo_real', 10, 2)->default(0);
        $table->decimal('total_descuadre_dinero', 10, 2)->default(0);
        $table->enum('estado', ['abierto', 'cerrado_ok', 'cerrado_con_descuadre'])->default('abierto');
        $table->text('notas')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('turnos');
    }
};
