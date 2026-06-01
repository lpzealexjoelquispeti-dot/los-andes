<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notificacion extends Model
{
    use SoftDeletes;

    protected $table = 'notificaciones';
    protected $primaryKey = 'cod_notificacion';

    protected $fillable = [
        'cod_usuario_not',
        'titulo',
        'mensaje',
        'leido',
        'tipo',
    ];

    protected $casts = [
        'leido' => 'boolean',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'cod_usuario_not', 'id');
    }
}
