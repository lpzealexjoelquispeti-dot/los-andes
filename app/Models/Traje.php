<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Traje extends Model
{
    use SoftDeletes;

    protected $table = 'trajes'; // Asegúrate que este sea el nombre de tu tabla
    protected $primaryKey = 'cod_traje';
    
    // Carbon convertirá estos campos a objetos de fecha automáticamente
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'nom_traje', 
        'des_traje', 
        'pre_alquiler', 
        'talla_traje', 
        'color_traje', 
        'cod_tienda_traje', // Llave foránea hacia Tiendas
        'cod_danza_traje'   // Llave foránea hacia Danzas
    ];

    /**
     * RELACIÓN: Un traje pertenece a una TIENDA
     * Esto soluciona el error "RelationNotFoundException"
     */
    public function tienda(): BelongsTo
    {
        // 'cod_tienda_traje' es la FK en esta tabla
        // 'cod_tienda' es la PK en la tabla tiendas
        return $this->belongsTo(Tienda::class, 'cod_tienda_traje', 'cod_tienda');
    }

    /**
     * RELACIÓN: Un traje pertenece a una DANZA
     */
    public function danza(): BelongsTo
    {
        return $this->belongsTo(Danza::class, 'cod_danza_traje', 'cod_danza');
    }

    /**
     * RELACIÓN: Un traje tiene muchas IMÁGENES
     */
    public function imagenes(): HasMany
    {
        return $this->hasMany(ImagenTraje::class, 'cod_traje_img', 'cod_traje');
    }
   public function unidades()
{
    // El segundo parámetro le dice a Laravel el nombre real de tu columna foránea
    return $this->hasMany(InventarioUnidad::class, 'cod_traje_base', 'cod_traje');
}
}