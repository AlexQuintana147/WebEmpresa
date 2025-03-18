<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaPresupuesto extends Model
{
    use HasFactory;

    protected $table = 'categorias_presupuesto';

    protected $fillable = [
        'nombre',
        'icono',
        'color',
        'presupuesto',
        'user_id'
    ];

    /**
     * Get the user that owns the category.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the transactions for the category.
     */
    public function transacciones()
    {
        return $this->hasMany(Transaccion::class, 'categoria_id');
    }
}