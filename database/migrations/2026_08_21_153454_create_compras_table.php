<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabla Principal de Compras
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->string('comprobante')->nullable();
            $table->date('fecha');
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });

        // 2. Tabla Detalle de Compras (asociada a la compra y al producto)
        Schema::create('detalle_compras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compra_id')->constrained('compras')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->integer('cantidad');
            $table->decimal('precio_compra', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_compras');
        Schema::dropIfExists('compras');
    }
};