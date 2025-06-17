<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Historial extends Model
{
    use HasFactory;

    protected $table = 'historiales';

    protected $fillable = [
        'paciente_id',
        'cita_id',
        'fecha',
        'motivo',
        'descripcion',
        'diagnostico',
        'tratamiento'
    ];

    /**
     * Obtiene el paciente asociado al historial
     */
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    /**
     * Obtiene la cita asociada al historial
     */
    public function cita()
    {
        return $this->belongsTo(Cita::class, 'cita_id');
    }
}