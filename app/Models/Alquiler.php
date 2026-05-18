<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alquiler extends Model
{
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
        'est_alquiler'
    ];

    // Cliente que alquiló el traje
    public function cliente()
    {
        return $this->belongsTo(User::class, 'cod_usuario_cli', 'id');
    }

    // La prenda física exacta alquilada
    public function unidadFisica()
    {
        return $this->belongsTo(InventarioUnidad::class, 'cod_unidad_alq', 'cod_unidad');
    }

    // El evento para el cual se alquiló
    public function evento()
    {
        return $this->belongsTo(EventoFolclorico::class, 'cod_evento_alq', 'cod_evento');
    }

    // Sanciones asociadas a este alquiler (si las hay)
    public function sanciones()
    {
        return $this->hasMany(SancionAlquiler::class, 'cod_alquiler_ref', 'cod_alquiler');
    }
}