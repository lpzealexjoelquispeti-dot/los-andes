<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventoFolclorico extends Model
{
    use SoftDeletes;

    protected $table = 'eventos_folcloricos';
    protected $primaryKey = 'cod_evento';

    protected $fillable = [
        'nom_evento',
        'fec_inicio',
        'fec_fin',
        'ubicacion'
    ];

    // Relación N:M con Danzas
    public function danzas()
    {
        return $this->belongsToMany(Danza::class, 'danza_evento', 'cod_evento_ref', 'cod_danza_ref');
    }

    // Relación con los alquileres destinados a este evento
    public function alquileres()
    {
        return $this->hasMany(Alquiler::class, 'cod_evento_alq', 'cod_evento');
    }
}