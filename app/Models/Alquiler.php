<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Alquiler extends Model
{
    use SoftDeletes;

    protected $table = 'alquileres';
    protected $primaryKey = 'cod_alquiler';

    protected $fillable = [
        'cod_usuario_cli',
        'cod_unidad_alq',
        'cod_evento_alq',
        'fec_salida',
        'fec_retorno_prev',
        'fec_retorno_real',
        'monto_total',
        'garantia',
        'est_alquiler',
    ];

    protected $casts = [
        'fec_salida' => 'date',
        'fec_retorno_prev' => 'date',
        'fec_retorno_real' => 'date',
        'monto_total' => 'decimal:2',
        'garantia' => 'decimal:2',
    ];

    public function estaActivo(): bool
    {
        return in_array($this->est_alquiler, ['Reservado', 'Entregado', 'En Mora'], true);
    }

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cod_usuario_cli', 'id');
    }

    public function unidadFisica()
    {
        return $this->belongsTo(InventarioUnidad::class, 'cod_unidad_alq', 'cod_unidad');
    }

    public function evento()
    {
        return $this->belongsTo(EventoFolclorico::class, 'cod_evento_alq', 'cod_evento');
    }

    public function sanciones()
    {
        return $this->hasMany(SancionAlquiler::class, 'cod_alquiler_ref', 'cod_alquiler');
    }
}
