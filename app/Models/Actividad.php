<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Actividad extends Model
{
    use HasFactory;

    protected $table = 'actividades';

    protected $fillable = [
        'usuario_id',
        'titulo',
        'descripcion',
        'nivel',
        'estado',
        'fecha_limite',
        'hora_limite',
        'color',
        'icono',
        'prioridad',
        'actividad_padre_id'
    ];

    /**
     * Get the user that owns the activity.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Get the parent activity.
     */
    public function actividadPadre()
    {
        return $this->belongsTo(Actividad::class, 'actividad_padre_id');
    }

    /**
     * Get the child activities.
     */
    public function actividadesHijas()
    {
        return $this->hasMany(Actividad::class, 'actividad_padre_id');
    }

    /**
     * Scope a query to only include principal activities.
     */
    public function scopePrincipales($query)
    {
        return $query->where('nivel', 'principal');
    }

    /**
     * Scope a query to only include secondary activities.
     */
    public function scopeSecundarias($query)
    {
        return $query->where('nivel', 'secundaria');
    }

    /**
     * Scope a query to only include tertiary activities.
     */
    public function scopeTerciarias($query)
    {
        return $query->where('nivel', 'terciaria');
    }

    /**
     * Scope a query to only include pending activities.
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    /**
     * Scope a query to only include in progress activities.
     */
    public function scopeEnProgreso($query)
    {
        return $query->where('estado', 'en_progreso');
    }

    /**
     * Scope a query to only include completed activities.
     */
    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completada');
    }
}