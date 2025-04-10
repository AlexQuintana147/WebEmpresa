<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $table = 'doctores';

    protected $fillable = [
        'dni',
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'telefono',
        'correo',
        'especialidad',
        'usuario_id'
    ];

    /**
     * Obtiene el usuario asociado al doctor
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Obtiene los pacientes asociados al doctor
     */
    public function pacientes()
    {
        return $this->hasMany(Paciente::class, 'doctor_id');
    }

    /**
     * Obtiene el nombre completo del doctor
     */
    public function getNombreCompletoAttribute()
    {
        return "{$this->nombre} {$this->apellido_paterno} {$this->apellido_materno}";
    }
}