<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tesauro extends Model
{
    // Habilitamos el borrado lógico para no perder historial de búsqueda
    use SoftDeletes;

    // Nombre de la tabla según tu migración
    protected $table = 'tesauro';

    // Definimos la llave primaria personalizada
    protected $primaryKey = 'cod_termino';

    // Campos que permitimos llenar masivamente
    protected $fillable = [
        'termino_usuario',
        'cod_danza_ref',
        'tipo'
    ];

    /**
     * Relación: Un término del Tesauro pertenece a una Danza oficial.
     * Esto permite acceder a: $tesauro->danza->nom_danza
     */
    public function danza(): BelongsTo
    {
        return $this->belongsTo(Danza::class, 'cod_danza_ref', 'cod_danza');
    }
}