<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Transaccion;
use App\Models\Tarea;
use App\Models\CategoriaPresupuesto;
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'correo',
        'contrasena',
        'imagen',
        'rol_id'
    ];

    protected $hidden = [
        'contrasena',
        'remember_token',
    ];

    protected $casts = [
        'correo_verified_at' => 'datetime',
        // Removemos el cast 'hashed' para evitar doble hash
    ];

    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    public function getEmailForPasswordReset()
    {
        return $this->correo;
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($user) {
            if (!$user->rol_id) {
                $user->rol_id = 2;
            }
        });
    }
    
    /**
     * Get the budget categories for the user.
     */
    public function categoriasPresupuesto()
    {
        return $this->hasMany(CategoriaPresupuesto::class, 'user_id');
    }
    
    /**
     * Get the transactions for the user.
     */
    public function transacciones()
    {
        return $this->hasMany(Transaccion::class, 'user_id');
    }
    
    /**
     * Get the tasks for the user.
     */
    public function tareas()
    {
        return $this->hasMany(Tarea::class, 'usuario_id');
    }
}