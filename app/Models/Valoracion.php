<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Valoracion extends Model
{
    use SoftDeletes;

    protected $table = 'valoraciones';
    protected $primaryKey = 'cod_valoracion';

    protected $fillable = [
        'cod_usuario_val',
        'cod_traje_val',
        'puntuacion',
        'comentario',
    ];

    protected $casts = [
        'puntuacion' => 'integer',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'cod_usuario_val', 'id');
    }

    public function traje()
    {
        return $this->belongsTo(Traje::class, 'cod_traje_val', 'cod_traje');
    }
}
