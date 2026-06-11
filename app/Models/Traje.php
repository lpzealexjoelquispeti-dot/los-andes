<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Traje extends Model
{
    use SoftDeletes;

    protected $table = 'trajes'; 
    protected $primaryKey = 'cod_traje';
    
    // 🌟 Estándar moderno de Laravel 12 para mutación de fechas (Reemplaza a $dates)
    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    protected $fillable = [
        'nom_traje',
        'des_traje',
        'pre_alquiler',
        'talla_traje',
        'color_traje',
        'genero',
        'fraternidad',
        'nivel_uso_alquileres',
        'cod_tienda_traje',
        'cod_danza_traje',
        'cod_traje_padre'
    ];

    /**
     * RELACIÓN AUTORREFERENCIA (CLAVE): Permite al Traje Padre encontrar a su Variante Femenina
     */
    public function varianteFemenina(): HasOne
    {
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
     * RELACIÓN: Un traje belongsTo a una DANZA
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

    public function valoraciones(): HasMany
    {
        return $this->hasMany(Valoracion::class, 'cod_traje_val', 'cod_traje');
    }
}