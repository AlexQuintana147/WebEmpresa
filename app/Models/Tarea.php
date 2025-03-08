<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Tarea extends Model
{
    use HasFactory;

    protected $table = 'tareas';

    protected $fillable = [
        'usuario_id',
        'titulo',
        'descripcion',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
        'color',
        'icono'
    ];

    /**
     * Get the user that owns the task.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}