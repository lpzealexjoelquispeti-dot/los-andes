<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Valoracion extends Model
{
    protected $table = 'valoraciones';
    protected $primaryKey = 'cod_valoracion';

    protected $fillable = [
        'cod_usuario_val',
        'cod_traje_val',
        'puntuacion',
        'comentario'
    ];

    // Usuario que calificó
    public function usuario()
    {
        return $this->belongsTo(User::class, 'cod_usuario_val', 'id');
    }

    // Traje que fue calificado
    public function traje()
    {
        return $this->belongsTo(Traje::class, 'cod_traje_val', 'cod_traje');
    }
}