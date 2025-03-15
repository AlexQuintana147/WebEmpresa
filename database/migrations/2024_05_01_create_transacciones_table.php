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
        Schema::create('transacciones', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion');
            $table->decimal('monto', 10, 2);
            $table->enum('tipo', ['ingreso', 'gasto']);
            $table->date('fecha');
            $table->foreignId('categoria_id')->nullable()->constrained('categorias_presupuesto')->onDelete('set null');
            $table->foreignId('user_id')->constrained('usuarios')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transacciones');
    }
};