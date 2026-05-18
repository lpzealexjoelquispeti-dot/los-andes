<?php

namespace App\Observers;

use App\Models\Auditoria;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditoriaObserver
{
    /**
     * Captura cuando se crea un nuevo registro.
     */
    public function created(Model $model): void
    {
        $this->registrarAuditoria($model, 'INSERTAR', null, $model->getAttributes());
    }

    /**
     * Captura actualizaciones normales de datos (Nombre, precio, etc.).
     */
    public function updated(Model $model): void
    {
        // 🔥 FILTRO DETECTOR: Si el cambio involucra la papelera de reciclaje,
        // salimos de aquí inmediatamente para evitar el registro duplicado.
        if (method_exists($model, 'isForceDeleting') && $model->wasChanged('deleted_at')) {
            return;
        }

        if ($model->wasChanged()) {
            $this->registrarAuditoria($model, 'ACTUALIZAR', $model->getOriginal(), $model->getAttributes());
        }
    }

    /**
     * Captura de forma nativa la eliminación (Soft Delete o Física).
     */
    public function deleted(Model $model): void
    {
        // Detectamos si fue un forceDelete() real o un Soft Delete normal
        $esFisico = method_exists($model, 'isForceDeleting') && $model->isForceDeleting();
        $accion = $esFisico ? 'ELIMINAR_FISICO' : 'ELIMINAR';

        $this->registrarAuditoria($model, $accion, $model->getOriginal(), null);
    }

    /**
     * Captura de forma nativa la restauración desde la papelera.
     */
    public function restored(Model $model): void
    {
        // Registra la restauración de manera única y limpia
        $this->registrarAuditoria(
            $model, 
            'RESTAURAR', 
            ['deleted_at' => 'EN_PAPELERA'], 
            $model->getAttributes()
        );
    }

    /**
     * Centralizador del guardado en la base de datos.
     */
    private function registrarAuditoria(Model $model, string $accion, ?array $anterior, ?array $nuevo): void
    {
        $usuarioId = Auth::check() ? Auth::id() : null;

        Auditoria::create([
            'cod_usuario_aud' => $usuarioId, 
            'tabla_afectada'  => $model->getTable(), 
            'accion'          => $accion,
            'valor_anterior'  => $anterior,
            'valor_nuevo'     => $nuevo,
            'ip_address'      => Request::ip(),
        ]);
    }
}