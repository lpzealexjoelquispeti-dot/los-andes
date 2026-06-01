<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traje;

class InventarioUnidad extends Model
{
    use SoftDeletes;

    protected $table = 'inventario_unidades';
    protected $primaryKey = 'cod_unidad';

    protected $fillable = [
    'cod_traje_base',
    'talla',
    'nro_serie_interno',
    'estado_fisico',
    'observaciones', // <--- ¡AQUÍ AGREGAMOS LA NUEVA COLUMNA!
    'disponibilidad'
];

    // Saber a qué diseño o modelo de traje pertenece esta unidad física
   // Saber a qué diseño o modelo de traje pertenece esta unidad física
public function traje()
{
    // CAMBIAMOS $table POR $this
    return $this->belongsTo(Traje::class, 'cod_traje_base', 'cod_traje');
}

    // Historial de alquileres de esta unidad en específico
    public function alquileres()
    {
        return $this->hasMany(Alquiler::class, 'cod_unidad_alq', 'cod_unidad');
    }

    public function alquilerActivo()
    {
        return $this->hasOne(Alquiler::class, 'cod_unidad_alq', 'cod_unidad')
            ->whereIn('est_alquiler', ['Reservado', 'Entregado', 'En Mora'])
            ->latestOfMany('cod_alquiler');
    }
}
