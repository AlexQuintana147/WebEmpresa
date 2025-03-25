<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actividades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->enum('nivel', ['principal', 'secundaria', 'terciaria']);
            $table->enum('estado', ['pendiente', 'en_progreso', 'completada'])->default('pendiente');
            $table->date('fecha_limite')->nullable();
            $table->time('hora_limite')->nullable();
            $table->string('color')->default('#4A90E2');
            $table->string('icono')->default('fa-tasks');
            $table->integer('prioridad')->default(1); // 1: baja, 2: media, 3: alta
            $table->foreignId('actividad_padre_id')->nullable()->constrained('actividades')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actividades');
    }
};