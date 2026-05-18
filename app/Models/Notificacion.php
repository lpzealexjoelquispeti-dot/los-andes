<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $table = 'notificaciones';
    protected $primaryKey = 'cod_notificacion';

    protected $fillable = [
        'cod_usuario_not',
        'titulo',
        'mensaje',
        'leido',
        'tipo'
    ];

    // Destinatario de la notificación
    public function usuario()
    {
        return $this->belongsTo(User::class, 'cod_usuario_not', 'id');
    }
}