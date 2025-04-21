<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    use HasFactory;

    protected $table = 'pacientes';

    protected $fillable = [
        'dni',
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'correo',
        'telefono',
        'usuario_id',
        'doctor_id'
    ];

    /**
     * Obtiene el usuario asociado al paciente
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
    
    /**
     * Obtiene el doctor asociado al paciente
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    /**
     * Relación: obtiene todas las citas asociadas a este paciente
     */
    public function citas()
    {
        return $this->hasMany(\App\Models\Cita::class, 'paciente_id');
    }

    /**
     * Obtiene el nombre completo del paciente
     */
    public function getNombreCompletoAttribute()
    {
        return "{$this->nombre} {$this->apellido_paterno} {$this->apellido_materno}";
    }
}