<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    use HasFactory;

    protected $table = 'citas';

    protected $fillable = [
        'paciente_id',
        'doctor_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'motivo_consulta',
        'descripcion_malestar',
        'estado'
    ];

    /**
     * Obtiene el paciente asociado a la cita
     */
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    /**
     * Obtiene el doctor asociado a la cita
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    /**
     * Verifica si la cita se solapa con otra cita del doctor
     */
    public function seSolapaCon($fecha, $horaInicio, $horaFin, $exceptoId = null)
    {
        $query = self::where('doctor_id', $this->doctor_id)
            ->where('fecha', $fecha)
            ->where(function($q) use ($horaInicio, $horaFin) {
                $q->whereBetween('hora_inicio', [$horaInicio, $horaFin])
                  ->orWhereBetween('hora_fin', [$horaInicio, $horaFin])
                  ->orWhere(function($q2) use ($horaInicio, $horaFin) {
                      $q2->where('hora_inicio', '<=', $horaInicio)
                         ->where('hora_fin', '>=', $horaFin);
                  });
            });
            
        if ($exceptoId) {
            $query->where('id', '!=', $exceptoId);
        }
        
        return $query->exists();
    }
}