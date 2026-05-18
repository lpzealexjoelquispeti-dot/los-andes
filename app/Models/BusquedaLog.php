<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusquedaLog extends Model
{
    protected $table = 'busqueda_logs';

    protected $fillable = [
        'termino_buscado',
        'tipo_resultado',
        'cod_danza_ref',
        'user_id',
    ];

    public function danza()
    {
        return $this->belongsTo(Danza::class, 'cod_danza_ref', 'cod_danza');
    }
}