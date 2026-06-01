<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\Alquiler;
use App\Models\Notificacion;
use App\Models\SancionAlquiler;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AlquilerController extends Controller
{
    public function index()
    {
        $alquileres = $this->baseQuery()
            ->latest('cod_alquiler')
            ->paginate(12);

        return view('vendedor.alquileres.index', compact('alquileres'));
    }

    public function show(Alquiler $alquiler)
    {
        $this->autorizarAlquiler($alquiler);

        $alquiler->load(['cliente', 'evento', 'unidadFisica.traje.tienda', 'sanciones']);

        return view('vendedor.alquileres.show', compact('alquiler'));
    }

    public function entregar(Alquiler $alquiler)
    {
        $this->autorizarAlquiler($alquiler);
        abort_unless($alquiler->est_alquiler === 'Reservado', 422);

        $alquiler->update(['est_alquiler' => 'Entregado']);

        Notificacion::create([
            'cod_usuario_not' => $alquiler->cod_usuario_cli,
            'titulo' => 'Prenda entregada',
            'mensaje' => 'El alquiler #'.$alquiler->cod_alquiler.' fue marcado como entregado.',
            'tipo' => 'info',
        ]);

        return back()->with('success', 'Alquiler marcado como entregado.');
    }

    public function devolver(Request $request, Alquiler $alquiler)
    {
        $this->autorizarAlquiler($alquiler);
        abort_unless(in_array($alquiler->est_alquiler, ['Entregado', 'En Mora'], true), 422);

        $data = $request->validate([
            'fec_retorno_real' => ['required', 'date', 'after_or_equal:'.$alquiler->fec_salida->format('Y-m-d')],
            'estado_fisico' => ['required', 'in:Nuevo,Buen Estado,Desgastado,En Reparación'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ]);

        $enMora = Carbon::parse($data['fec_retorno_real'])->greaterThan($alquiler->fec_retorno_prev);

        $alquiler->update([
            'fec_retorno_real' => $data['fec_retorno_real'],
            'est_alquiler' => $enMora ? 'En Mora' : 'Devuelto',
        ]);

        $alquiler->unidadFisica?->update([
            'estado_fisico' => $data['estado_fisico'],
            'observaciones' => $data['observaciones'] ?? null,
            'disponibilidad' => $data['estado_fisico'] !== 'En Reparación',
        ]);

        Notificacion::create([
            'cod_usuario_not' => $alquiler->cod_usuario_cli,
            'titulo' => $enMora ? 'Alquiler en mora' : 'Alquiler devuelto',
            'mensaje' => $enMora
                ? 'Tu alquiler fue recibido fuera de fecha. Revisa si corresponde una sancion.'
                : 'Tu alquiler fue devuelto. Ya puedes dejar una valoracion.',
            'tipo' => $enMora ? 'alerta' : 'exito',
        ]);

        return back()->with('success', 'Devolucion registrada correctamente.');
    }

    public function sancionar(Request $request, Alquiler $alquiler)
    {
        $this->autorizarAlquiler($alquiler);

        $data = $request->validate([
            'tipo_sancion' => ['required', 'in:Retraso,Daño,Perdida,Limpieza'],
            'monto_sancion' => ['required', 'numeric', 'min:0.01'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
        ]);

        $sancion = SancionAlquiler::create([
            'cod_alquiler_ref' => $alquiler->cod_alquiler,
            'tipo_sancion' => $data['tipo_sancion'],
            'monto_sancion' => $data['monto_sancion'],
            'descripcion' => $data['descripcion'] ?? null,
            'pagada' => false,
        ]);

        $alquiler->update(['est_alquiler' => 'En Mora']);

        Notificacion::create([
            'cod_usuario_not' => $alquiler->cod_usuario_cli,
            'titulo' => 'Sancion registrada',
            'mensaje' => 'Se registro una sancion de Bs. '.$sancion->monto_sancion.' por '.$sancion->tipo_sancion.'.',
            'tipo' => 'alerta',
        ]);

        return back()->with('success', 'Sancion registrada correctamente.');
    }

    public function pagarSancion(SancionAlquiler $sancion)
    {
        $alquiler = $sancion->alquiler;
        $this->autorizarAlquiler($alquiler);

        $sancion->update(['pagada' => true]);

        if (! $alquiler->sanciones()->where('pagada', false)->exists()) {
            $alquiler->update(['est_alquiler' => 'Devuelto']);
            $alquiler->unidadFisica?->update(['disponibilidad' => true]);
        }

        Notificacion::create([
            'cod_usuario_not' => $alquiler->cod_usuario_cli,
            'titulo' => 'Sancion pagada',
            'mensaje' => 'La sancion #'.$sancion->cod_sancion.' fue marcada como pagada.',
            'tipo' => 'exito',
        ]);

        return back()->with('success', 'Sancion marcada como pagada.');
    }

    public function cancelar(Alquiler $alquiler)
    {
        $this->autorizarAlquiler($alquiler);
        abort_unless(in_array($alquiler->est_alquiler, ['Reservado', 'Entregado', 'En Mora'], true), 422);

        $alquiler->update(['est_alquiler' => 'Cancelado']);
        $alquiler->unidadFisica?->update(['disponibilidad' => true]);

        Notificacion::create([
            'cod_usuario_not' => $alquiler->cod_usuario_cli,
            'titulo' => 'Alquiler cancelado',
            'mensaje' => 'La tienda cancelo el alquiler #'.$alquiler->cod_alquiler.'.',
            'tipo' => 'alerta',
        ]);

        return redirect()->route('vendedor.alquileres.index')->with('success', 'Alquiler cancelado.');
    }

    private function baseQuery()
    {
        return Alquiler::with(['cliente', 'evento', 'unidadFisica.traje.tienda', 'sanciones'])
            ->whereHas('unidadFisica.traje.tienda', function ($query) {
                $query->where('cod_usuario_tie', auth()->id());
            });
    }

    private function autorizarAlquiler(Alquiler $alquiler): void
    {
        $pertenece = Alquiler::whereKey($alquiler->cod_alquiler)
            ->whereHas('unidadFisica.traje.tienda', function ($query) {
                $query->where('cod_usuario_tie', auth()->id());
            })
            ->exists();

        abort_unless($pertenece, 403);
    }
}
