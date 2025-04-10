<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('doctores', function (Blueprint $table) {
            $table->id();
            $table->string('dni', 8)->unique();
            $table->string('nombre');
            $table->string('apellido_paterno');
            $table->string('apellido_materno');
            $table->string('telefono', 15)->nullable();
            $table->string('correo')->nullable();
            $table->string('especialidad')->nullable();
            $table->unsignedBigInteger('usuario_id')->unique();
            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('doctores');
    }
};