<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, create a temporary column to store the numeric values
        Schema::table('tareas', function (Blueprint $table) {
            $table->integer('dia_semana_numeric')->nullable()->after('dia_semana');
        });

        // Convert existing string values to numeric values
        DB::table('tareas')->get()->each(function ($tarea) {
            $dayMap = [
                'Lunes' => 1,
                'Martes' => 2,
                'Miércoles' => 3,
                'Miercoles' => 3,
                'Jueves' => 4,
                'Viernes' => 5,
                'Sábado' => 6,
                'Sabado' => 6,
                'Domingo' => 7
            ];
            
            // Normalize the day name to handle accents and capitalization
            $normalizedDay = ucfirst(strtolower(trim(
                preg_replace('/[\p{M}]/u', '', normalizer_normalize($tarea->dia_semana, Normalizer::FORM_D))
            )));
            
            $numericDay = $dayMap[$normalizedDay] ?? 1; // Default to Monday if mapping fails
            
            DB::table('tareas')
                ->where('id', $tarea->id)
                ->update(['dia_semana_numeric' => $numericDay]);
        });

        // Drop the old string column and rename the numeric column
        Schema::table('tareas', function (Blueprint $table) {
            $table->dropColumn('dia_semana');
        });

        Schema::table('tareas', function (Blueprint $table) {
            $table->renameColumn('dia_semana_numeric', 'dia_semana');
        });

        // Ensure the column is not nullable
        Schema::table('tareas', function (Blueprint $table) {
            $table->integer('dia_semana')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, create a temporary column to store the string values
        Schema::table('tareas', function (Blueprint $table) {
            $table->string('dia_semana_string')->nullable()->after('dia_semana');
        });

        // Convert numeric values back to string values
        DB::table('tareas')->get()->each(function ($tarea) {
            $dayMap = [
                1 => 'Lunes',
                2 => 'Martes',
                3 => 'Miércoles',
                4 => 'Jueves',
                5 => 'Viernes',
                6 => 'Sábado',
                7 => 'Domingo'
            ];
            
            $stringDay = $dayMap[$tarea->dia_semana] ?? 'Lunes'; // Default to Monday if mapping fails
            
            DB::table('tareas')
                ->where('id', $tarea->id)
                ->update(['dia_semana_string' => $stringDay]);
        });

        // Drop the numeric column and rename the string column
        Schema::table('tareas', function (Blueprint $table) {
            $table->dropColumn('dia_semana');
        });

        Schema::table('tareas', function (Blueprint $table) {
            $table->renameColumn('dia_semana_string', 'dia_semana');
        });

        // Ensure the column is not nullable
        Schema::table('tareas', function (Blueprint $table) {
            $table->string('dia_semana')->nullable(false)->change();
        });
    }
};
