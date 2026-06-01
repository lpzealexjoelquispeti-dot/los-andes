<?php

namespace App\Models;

use App\Models\SancionAlquiler; // ◄— AGREGA ESTA LÍNEA SI NO ESTÁ
use App\Models\Notificacion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Alquiler extends Model
{
    use SoftDeletes;

    protected $table = 'alquileres';
    protected $primaryKey = 'cod_alquiler';

    protected $fillable = [
        'cod_usuario_cli',
        'cod_unidad_alq',
        'cod_evento_alq',
        'fec_salida',
        'fec_retorno_prev',
        'fec_retorno_real',
        'monto_total',
        'garantia',
        'est_alquiler',
        'comprobante_pago_path',
    ];

    protected $casts = [
        'fec_salida' => 'date',
        'fec_retorno_prev' => 'date',
        'fec_retorno_real' => 'date',
        'monto_total' => 'decimal:2',
        'garantia' => 'decimal:2',
    ];

    public function estaActivo(): bool
    {
        return in_array($this->est_alquiler, ['Reservado', 'Entregado', 'En Mora'], true);
    }

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cod_usuario_cli', 'id');
    }

    public function unidadFisica()
    {
        return $this->belongsTo(InventarioUnidad::class, 'cod_unidad_alq', 'cod_unidad');
    }

    public function evento()
    {
        return $this->belongsTo(EventoFolclorico::class, 'cod_evento_alq', 'cod_evento');
    }
public function sanciones()
{
    // Sincronizado con tu modelo SancionAlquiler y su clave foránea real
    return $this->hasMany(SancionAlquiler::class, 'cod_alquiler_ref', 'cod_alquiler');
}
}
