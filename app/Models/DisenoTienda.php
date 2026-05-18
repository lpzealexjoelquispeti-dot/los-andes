<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class DisenoTienda extends Model
{
    //
    use SoftDeletes;
    protected $table = 'disenos_tiendas';
protected $primaryKey = 'cod_diseno';

protected $fillable = [
    'cod_tienda_dis', 
    'color_primario', 
    'color_fondo', 
    'tipografia', 
    'logo_path',      // <--- Añadir estos
    'banner_path',
    'horario_tie',    // <--- Añadir estos
    'slogan',         // <--- Añadir estos
    'link_facebook',  // <--- Añadir estos
    'link_whatsapp',  // <--- Añadir estos
    'modo_oscuro',
];
}
