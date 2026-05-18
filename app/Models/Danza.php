<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Danza extends Model
{
    use SoftDeletes;

    protected $table = 'danzas';
    protected $primaryKey = 'cod_danza';

    protected $fillable = [
        'nom_danza',
        'clasificacion',
        'descripcion',
        'imagen_danza'
    ];
    public function tesauros(): HasMany
    {
        // 'cod_danza_ref' es la llave foránea en la tabla tesauro
        // 'cod_danza' es la llave local en la tabla danzas
        return $this->hasMany(Tesauro::class, 'cod_danza_ref', 'cod_danza');
    }
}
