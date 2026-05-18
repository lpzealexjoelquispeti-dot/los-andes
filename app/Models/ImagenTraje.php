<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImagenTraje extends Model
{
    use HasFactory;
use SoftDeletes;
    protected $table = 'imagen_trajes'; // Nombre de tu tabla
    protected $primaryKey = 'cod_imagen'; // Tu llave primaria personalizada

    // AQUÍ ESTÁ EL TRUCO: Permitimos que estas columnas reciban datos
    protected $fillable = [
        'ruta_img',
        'cod_traje_img'
    ];

    // Relación inversa (opcional, pero recomendada)
    public function traje()
    {
        return $this->belongsTo(Traje::class, 'cod_traje_img');
    }
}