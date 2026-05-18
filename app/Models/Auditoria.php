<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    protected $table = 'auditoria';
    protected $primaryKey = 'cod_auditoria';

    protected $fillable = [
        'cod_usuario_aud',
        'tabla_afectada',
        'accion',
        'valor_anterior',
        'valor_nuevo',
        'ip_address'
    ];

    // Casts automáticos para los campos JSON de auditoría
    protected $casts = [
        'valor_anterior' => 'array',
        'valor_nuevo'    => 'array',
    ];

    // Usuario responsable de la acción guardada
    public function usuario()
    {
        return $this->belongsTo(User::class, 'cod_usuario_aud', 'id');
    }
}