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
        Schema::create('promociones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Ej: "Promo Cócteles 2x10k", "Descuento Cervezas"
            $table->foreignId('categoria_id')->constrained('categorias')->onDelete('cascade');
            $table->integer('cantidad_minima')->default(2); // Cantidad de ítems necesarios (Ej: 2)
            $table->decimal('descuento', 10, 2); // Monto a descontar (Ej: 4000.00)
            $table->boolean('estado')->default(true); // true = Activa, false = Inactiva
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promociones');
    }
};