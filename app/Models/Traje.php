<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne; // <-- Importante para la variante

class Traje extends Model
{
    use SoftDeletes;

    protected $table = 'trajes'; 
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
        'cod_danza_traje',   // Llave foránea hacia Danzas
        'cod_traje_padre'   // <-- Agregado para permitir registrar variantes sin errores de MassAssignment
    ];

    /**
     * RELACIÓN AUTORREFERENCIA (CLAVE): Permite al Traje Padre encontrar a su Variante Femenina
     */
    public function varianteFemenina(): HasOne
    {
        // 'cod_traje_padre' es la FK en la tabla trajes que apunta al 'cod_traje' del padre
        return $this->hasOne(Traje::class, 'cod_traje_padre', 'cod_traje');
    }

    /**
     * RELACIÓN AUTORREFERENCIA INVERSA: Permite a la variante saber quién es su Traje Padre
     */
    public function trajePadre(): BelongsTo
    {
        return $this->belongsTo(Traje::class, 'cod_traje_padre', 'cod_traje');
    }

    /**
     * RELACIÓN: Un traje pertenece a una TIENDA
     */
    public function tienda(): BelongsTo
    {
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

    /**
     * RELACIÓN: Un traje tiene muchas UNIDADES en el inventario
     */
    public function unidades(): HasMany
    {
        return $this->hasMany(InventarioUnidad::class, 'cod_traje_base', 'cod_traje');
    }
}