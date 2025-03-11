<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    use HasFactory;

    protected $table = 'notas';

    protected $fillable = [
        'usuario_id',
        'titulo',
        'contenido',
        'categoria',
        'color',
        'isPinned',
        'isArchived'
    ];

    /**
     * Get the user that owns the note.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}