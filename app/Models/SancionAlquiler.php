<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SancionAlquiler extends Model
{
    use SoftDeletes;

    protected $table = 'sanciones_alquiler';
    protected $primaryKey = 'cod_sancion';

    protected $fillable = [
        'cod_alquiler_ref',
        'tipo_sancion',
        'monto_sancion',
        'descripcion',
        'pagada',
    ];

    protected $casts = [
        'monto_sancion' => 'decimal:2',
        'pagada' => 'boolean',
    ];

    public function alquiler()
    {
        return $this->belongsTo(Alquiler::class, 'cod_alquiler_ref', 'cod_alquiler');
    }
}
