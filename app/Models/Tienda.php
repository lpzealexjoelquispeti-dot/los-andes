<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tienda extends Model
{
    use HasFactory;
use SoftDeletes;
    // 1. Especificar la llave primaria personalizada
    protected $primaryKey = 'cod_tienda';
    protected $table = 'tiendas';
    

    // 2. Definir los campos que se pueden llenar masivamente
    protected $fillable = [
    'nom_tie',
    'dir_tie',
    'tel_tie',
    'horario_tie', // ← nuevo
    'foto_ref',
    'latitud',
    'longitud',
    'est_tie',
    'cod_usuario_tie',
];

    // 3. Relación: Una tienda pertenece a un usuario (Vendedor)
    public function vendedor()
    {
        return $this->belongsTo(User::class, 'cod_usuario_tie', 'id');
    }
    public function diseno()
    {
        // Una tienda tiene UN diseño
        return $this->hasOne(DisenoTienda::class, 'cod_tienda_dis', 'cod_tienda');
    }
    public function trajes()
    {
        // 'cod_tienda_traje' es la llave foránea en la tabla trajes
        // 'cod_tienda' es la llave primaria en tu tabla tiendas
        return $this->hasMany(Traje::class, 'cod_tienda_traje', 'cod_tienda');
    }
}