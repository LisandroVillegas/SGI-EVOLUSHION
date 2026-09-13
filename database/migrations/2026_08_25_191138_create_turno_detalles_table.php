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
       Schema::create('turno_detalles', function (Blueprint $table) {
        $table->id();
        $table->foreignId('turno_id')->constrained('turnos')->onDelete('cascade');
        $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
        
        // Conteo y stock al abrir el turno
        $table->integer('stock_sistema_apertura');
        $table->integer('stock_fisico_apertura');
        $table->integer('diferencia_apertura')->default(0);

        // Conteo y stock al cerrar el turno (se llenan en el cierre)
        $table->integer('stock_sistema_cierre')->nullable();
        $table->integer('stock_fisico_cierre')->nullable();
        $table->integer('diferencia_cierre')->nullable();

        $table->text('observacion')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('turno_detalles');
    }
};
