<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaccion extends Model
{
    use HasFactory;

    protected $table = 'transacciones';

    protected $fillable = [
        'descripcion',
        'monto',
        'tipo',
        'fecha',
        'categoria_id',
        'user_id'
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2'
    ];

    /**
     * Get the user that owns the transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the category of the transaction.
     */
    public function categoria()
    {
        return $this->belongsTo(CategoriaPresupuesto::class, 'categoria_id');
    }
}