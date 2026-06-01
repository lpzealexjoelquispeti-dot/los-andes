<?php

namespace App\Http\Controllers;

use App\Models\Alquiler;
use App\Models\EventoFolclorico;
use App\Models\InventarioUnidad;
use App\Models\Notificacion;
use App\Models\Valoracion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlquilerController extends Controller
{
    public function index()
    {
        $alquileres = Alquiler::with(['unidadFisica.traje.tienda', 'evento', 'sanciones', 'unidadFisica.traje.valoraciones'])
            ->where('cod_usuario_cli', auth()->id())
            ->latest('cod_alquiler')
            ->paginate(10);

        return view('cliente.alquileres.index', compact('alquileres'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cod_unidad_alq' => ['required', 'exists:inventario_unidades,cod_unidad'],
            'fec_salida' => ['required', 'date', 'after_or_equal:today'],
            'fec_retorno_prev' => ['required', 'date', 'after_or_equal:fec_salida'],
            'garantia' => ['nullable', 'numeric', 'min:0'],
            'nom_evento' => ['required', 'string', 'max:255'],
            'ubicacion' => ['nullable', 'string', 'max:255'],
        ]);

        return DB::transaction(function () use ($data) {
            $unidad = InventarioUnidad::with(['traje.tienda.vendedor', 'alquilerActivo'])
                ->lockForUpdate()
                ->findOrFail($data['cod_unidad_alq']);

            if (! $unidad->disponibilidad || $unidad->alquilerActivo) {
                return back()->withErrors([
                    'cod_unidad_alq' => 'La prenda seleccionada ya no esta disponible.',
                ])->withInput();
            }

            $evento = EventoFolclorico::create([
                'nom_evento' => $data['nom_evento'],
                'fec_inicio' => $data['fec_salida'],
                'fec_fin' => $data['fec_retorno_prev'],
                'ubicacion' => $data['ubicacion'] ?? null,
            ]);

            $dias = Carbon::parse($data['fec_salida'])->diffInDays(Carbon::parse($data['fec_retorno_prev'])) + 1;
            $monto = $dias * (float) $unidad->traje->pre_alquiler;

            $alquiler = Alquiler::create([
                'cod_usuario_cli' => auth()->id(),
                'cod_unidad_alq' => $unidad->cod_unidad,
                'cod_evento_alq' => $evento->cod_evento,
                'fec_salida' => $data['fec_salida'],
                'fec_retorno_prev' => $data['fec_retorno_prev'],
                'monto_total' => $monto,
                'garantia' => $data['garantia'] ?? 0,
                'est_alquiler' => 'Reservado',
            ]);

            $unidad->update(['disponibilidad' => false]);

            Notificacion::create([
                'cod_usuario_not' => auth()->id(),
                'titulo' => 'Reserva registrada',
                'mensaje' => 'Tu alquiler de '.$unidad->traje->nom_traje.' quedo reservado.',
                'tipo' => 'exito',
            ]);

            $vendedor = $unidad->traje->tienda?->vendedor;
            if ($vendedor) {
                Notificacion::create([
                    'cod_usuario_not' => $vendedor->id,
                    'titulo' => 'Nuevo alquiler pendiente',
                    'mensaje' => auth()->user()->name.' reservo '.$unidad->traje->nom_traje.' para '.$evento->nom_evento.'.',
                    'tipo' => 'info',
                ]);
            }

            return redirect()
                ->route('cliente.alquileres.index')
                ->with('success', 'Alquiler reservado correctamente.');
        });
    }

    public function cancelar(Alquiler $alquiler)
    {
        abort_unless($alquiler->cod_usuario_cli === auth()->id(), 403);
        abort_if(! in_array($alquiler->est_alquiler, ['Reservado'], true), 422);

        $alquiler->update(['est_alquiler' => 'Cancelado']);

        $unidad = $alquiler->unidadFisica;
        $unidad?->update([
            'disponibilidad' => in_array($unidad->estado_fisico, ['Nuevo', 'Buen Estado'], true),
        ]);

        Notificacion::create([
            'cod_usuario_not' => auth()->id(),
            'titulo' => 'Alquiler cancelado',
            'mensaje' => 'Cancelaste la reserva #'.$alquiler->cod_alquiler.'.',
            'tipo' => 'alerta',
        ]);

        return back()->with('success', 'Reserva cancelada correctamente.');
    }

    public function valorar(Request $request, Alquiler $alquiler)
    {
        abort_unless($alquiler->cod_usuario_cli === auth()->id(), 403);
        abort_unless($alquiler->est_alquiler === 'Devuelto', 422);

        $data = $request->validate([
            'puntuacion' => ['required', 'integer', 'between:1,5'],
            'comentario' => ['nullable', 'string', 'max:1000'],
        ]);

        $traje = $alquiler->unidadFisica->traje;

        Valoracion::updateOrCreate(
            [
                'cod_usuario_val' => auth()->id(),
                'cod_traje_val' => $traje->cod_traje,
            ],
            [
                'puntuacion' => $data['puntuacion'],
                'comentario' => $data['comentario'] ?? null,
            ]
        );

        Notificacion::create([
            'cod_usuario_not' => auth()->id(),
            'titulo' => 'Valoracion guardada',
            'mensaje' => 'Gracias por calificar '.$traje->nom_traje.'.',
            'tipo' => 'exito',
        ]);

        return back()->with('success', 'Valoracion registrada correctamente.');
    }
}
