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
    Schema::table('promociones', function (Blueprint $table) {
        $table->foreignId('producto_id')
              ->nullable()
              ->after('categoria_id')
              ->constrained('productos')
              ->nullOnDelete();
    });
}

public function down(): void
{
    Schema::table('promociones', function (Blueprint $table) {
        $table->dropForeign(['producto_id']);
        $table->dropColumn('producto_id');
    });
}
};
