<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->unsignedBigInteger('doctor_id')->nullable()->after('usuario_id');
            $table->foreign('doctor_id')->references('id')->on('doctores')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropForeign(['doctor_id']);
            $table->dropColumn('doctor_id');
        });
    }
};