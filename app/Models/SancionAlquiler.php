<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SancionAlquiler extends Model
{
    protected $table = 'sanciones_alquiler';
    protected $primaryKey = 'cod_sancion';

    protected $fillable = [
        'cod_alquiler_ref',
        'tipo_sancion',
        'monto_sancion',
        'descripcion',
        'pagada'
    ];

    // Alquiler del cual se originó la sanción
    public function alquiler()
    {
        return $this->belongsTo(Alquiler::class, 'cod_alquiler_ref', 'cod_alquiler');
    }
}