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
            'cod_unidad_alq'    => ['required', 'exists:inventario_unidades,cod_unidad'],
            'fec_salida'        => ['required', 'date', 'after_or_equal:today', 'before_or_equal:' . now()->addDays(7)->toDateString()],
            'fec_retorno_prev'  => ['required', 'date', 'after_or_equal:fec_salida'],
            'nom_evento'        => ['required', 'string', 'max:255'],
            'ubicacion'         => ['nullable', 'string', 'max:255'],
            'nro_celular_cliente' => ['required', 'string', 'max:20'],
            'nombre_garante'      => ['required', 'string', 'max:255'],
            'ci_garante'          => ['required', 'string', 'max:20'],
            'comprobante_pago'    => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        return DB::transaction(function () use ($data, $request) {
            $unidad = InventarioUnidad::with(['traje.tienda.vendedor', 'alquilerActivo'])
                ->lockForUpdate()
                ->findOrFail($data['cod_unidad_alq']);

            if (! $unidad->disponibilidad || $unidad->alquilerActivo) {
                return back()->withErrors([
                    'cod_unidad_alq' => 'La prenda seleccionada ya no está disponible.',
                ])->withInput();
            }

            // 🟢 REGULA DE ORO: Guardar en el disco 'public' (Almacenamiento simbólico)
            // Esto creará el archivo en storage/app/public/comprobantes/...
            $comprobantePath = $request->file('comprobante_pago')->store('comprobantes', 'public');

            $evento = EventoFolclorico::create([
                'nom_evento' => $data['nom_evento'],
                'fec_inicio' => $data['fec_salida'],
                'fec_fin'    => $data['fec_retorno_prev'],
                'ubicacion'  => $data['ubicacion'] ?? null,
            ]);

            $dias   = Carbon::parse($data['fec_salida'])->diffInDays(Carbon::parse($data['fec_retorno_prev'])) + 1;
            $monto  = $dias * (float) $unidad->traje->pre_alquiler;
            $sena   = round($monto * 0.40, 2); 

            $alquiler = Alquiler::create([
                'cod_usuario_cli'     => auth()->id(),
                'cod_unidad_alq'      => $unidad->cod_unidad,
                'cod_evento_alq'      => $evento->cod_evento,
                'fec_salida'          => $data['fec_salida'],
                'fec_retorno_prev'    => $data['fec_retorno_prev'],
                'monto_total'         => $monto,
                'garantia'            => 0,
                'est_alquiler'        => 'Pendiente_Aprobacion', 
                'nro_celular_cliente' => $data['nro_celular_cliente'],
                'nombre_garante'      => $data['nombre_garante'],
                'ci_garante'          => $data['ci_garante'],
                
                // ⚠️ VERIFICA AQUÍ: Debe llamarse igual al campo de tu migración
                // Si en tu base de datos la columna es 'comprobante_pago_path', déjala así. 
                // Si la llamaste 'ruta_comprobante' o 'comprobante_pago', cámbiala aquí:
                'comprobante_pago_path' => $comprobantePath, 
                
                'monto_sena'          => $sena,
                'fecha_limite_pago'   => null, 
            ]);

            // Notificaciones...
            Notificacion::create([
                'cod_usuario_not' => auth()->id(),
                'titulo'  => 'Solicitud enviada — en revisión',
                'mensaje' => 'Tu solicitud de alquiler de ' . $unidad->traje->nom_traje . ' fue recibida.',
                'tipo'    => 'info',
            ]);

            $vendedor = $unidad->traje->tienda?->vendedor;
            if ($vendedor) {
                Notificacion::create([
                    'cod_usuario_not' => $vendedor->id,
                    'titulo'  => 'Nueva solicitud de alquiler',
                    'mensaje' => auth()->user()->name . ' solicita ' . $unidad->traje->nom_traje . ' — comprobante adjunto.',
                    'tipo'    => 'alerta',
                ]);
            }

            return redirect()
                ->route('cliente.alquileres.index')
                ->with('success', 'Solicitud enviada. El vendedor revisará tu comprobante de pago.');
        });
    }

    public function cancelar(Alquiler $alquiler)
    {
        abort_unless($alquiler->cod_usuario_cli === auth()->id(), 403);
        // Ahora también puede cancelar si está Pendiente_Aprobacion
        abort_if(! in_array($alquiler->est_alquiler, ['Pendiente_Aprobacion', 'Reservado'], true), 422);

        $alquiler->update(['est_alquiler' => 'Cancelado']);

        // Solo liberar la unidad si ya estaba bloqueada (estado Reservado)
        if ($alquiler->getOriginal('est_alquiler') === 'Reservado') {
            $unidad = $alquiler->unidadFisica;
            $unidad?->update([
                'disponibilidad' => in_array($unidad->estado_fisico, ['Nuevo', 'Buen Estado'], true),
            ]);
        }

        Notificacion::create([
            'cod_usuario_not' => auth()->id(),
            'titulo'  => 'Solicitud cancelada',
            'mensaje' => 'Cancelaste la solicitud de alquiler #' . $alquiler->cod_alquiler . '.',
            'tipo'    => 'alerta',
        ]);

        return back()->with('success', 'Solicitud cancelada correctamente.');
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
    /**
 * Vendedor aprueba la solicitud → unidad se bloquea y estado pasa a Reservado
 */
public function aprobar(Alquiler $alquiler)
    {
        abort_unless($alquiler->unidadFisica->traje->tienda->cod_usuario_tie === auth()->id(), 403);
        abort_if($alquiler->est_alquiler !== 'Pendiente_Aprobacion', 422);

        DB::transaction(function () use ($alquiler) {
            $alquiler->update(['est_alquiler' => 'Reservado']);
            $alquiler->unidadFisica->update(['disponibilidad' => false]);

            Notificacion::create([
                'cod_usuario_not' => $alquiler->cod_usuario_cli,
                'titulo'  => '¡Alquiler confirmado!',
                'mensaje' => 'El vendedor aprobó tu solicitud de ' . $alquiler->unidadFisica->traje->nom_traje . '.',
                'tipo'    => 'exito',
            ]);
        });

        return back()->with('success', 'Alquiler aprobado y unidad bloqueada.');
    }

    public function rechazar(Request $request, Alquiler $alquiler)
    {
        abort_unless($alquiler->unidadFisica->traje->tienda->cod_usuario_tie === auth()->id(), 403);
        abort_if($alquiler->est_alquiler !== 'Pendiente_Aprobacion', 422);

        $data = $request->validate([
            'motivo_rechazo' => ['required', 'string', 'max:500'],
        ]);

        $alquiler->update([
            'est_alquiler'   => 'Cancelado',
            'motivo_rechazo' => $data['motivo_rechazo'],
        ]);

        Notificacion::create([
            'cod_usuario_not' => $alquiler->cod_usuario_cli,
            'titulo'  => 'Solicitud no aprobada',
            'mensaje' => 'Tu solicitud de alquiler #' . $alquiler->cod_alquiler . ' fue rechazada. Motivo: ' . $data['motivo_rechazo'],
            'tipo'    => 'alerta',
        ]);

        return back()->with('success', 'Solicitud rechazada.');
    }
    /**
     * Aplicar una sanción o registrar daños sobre el alquiler
     */
 /**
     * Aplicar una sanción o registrar daños sobre el alquiler
     */
    public function sancionar(Request $request, Alquiler $alquiler)
    {
        abort_unless($alquiler->unidadFisica->traje->tienda->cod_usuario_tie === auth()->id(), 403);
        abort_if(!in_array($alquiler->est_alquiler, ['Entregado', 'En Mora']), 422);

        // 1. Validamos que venga estrictamente un número entre 1 y 4
        $data = $request->validate([
            'tipo_sancion'  => ['required', 'integer', 'in:1,2,3,4'],
            'monto_sancion' => ['required', 'numeric', 'min:0'],
            'descripcion'   => ['required', 'string', 'max:1000'],
        ]);

        // 2. Traducimos el número al string real que espera tu base de datos
        $textoTipoSancion = match ((int)$data['tipo_sancion']) {
            1 => 'Entrega Tardia',
            2 => 'Prenda Danada',
            3 => 'Accesorio Faltante',
            4 => 'Perdida Total',
        };

        \DB::transaction(function () use ($alquiler, $data, $textoTipoSancion) {
            
            // 3. Creamos el registro con el texto traducido
            $alquiler->sanciones()->create([
                'cod_alquiler_ref' => $alquiler->cod_alquiler,
                'tipo_sancion'     => $textoTipoSancion, 
                'monto_sancion'    => $data['monto_sancion'],
                'descripcion'      => $data['descripcion'],
                'pagada'           => false, 
            ]);

            $alquiler->update([
                'est_alquiler' => 'En Mora'
            ]);

            // Si es daño crítico, remover del catálogo público
            if (in_array((int)$data['tipo_sancion'], [2, 4], true)) {
                $alquiler->unidadFisica->update([
                    'disponibilidad' => false
                ]);
            }

            // Notificación al cliente
            \App\Models\Notificacion::create([
                'cod_usuario_not' => $alquiler->cod_usuario_cli,
                'titulo'          => '⚠ Has recibido una sanción',
                'mensaje'         => 'Se aplicó una penalización de Bs. ' . $data['monto_sancion'] . ' a tu alquiler de ' . $alquiler->unidadFisica->traje->nom_traje . '. Motivo: ' . $data['descripcion'],
                'tipo'            => 'alerta',
            ]);
            
        });

        return back()->with('success', 'Sanción aplicada correctamente.');
    }

}
