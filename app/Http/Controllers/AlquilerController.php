<?php

namespace App\Http\Controllers;

use App\Models\Alquiler;
use App\Models\EventoFolclorico;
use App\Models\InventarioUnidad;
use App\Models\Notificacion;
use App\Models\Valoracion;
use App\Models\SancionAlquiler; // 🌟 IMPORTACIÓN ADICIONADA: Evita el error de clase no encontrada
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
            'cod_unidad_alq'      => ['required', 'exists:inventario_unidades,cod_unidad'],
            'fec_salida'          => ['required', 'date', 'after_or_equal:today', 'before_or_equal:' . now()->addDays(7)->toDateString()],
            'fec_retorno_prev'    => ['required', 'date', 'after_or_equal:fec_salida'],
            'nom_evento'          => ['required', 'string', 'max:255'],
            'ubicacion'           => ['nullable', 'string', 'max:255'],
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

            // 🟢 REGLA DE ORO: Guardar en el disco 'public' (Almacenamiento simbólico)
            $comprobantePath = $request->file('comprobante_pago')->store('comprobantes', 'public');

            $evento = EventoFolclorico::create([
                'nom_evento' => $data['nom_evento'],
                'fec_inicio' => $data['fec_salida'],
                'fec_fin'    => $data['fec_retorno_prev'],
                'ubicacion'  => $data['ubicacion'] ?? null,
            ]);

           
            $dias   = Carbon::parse($data['fec_salida'])->diffInDays(Carbon::parse($data['fec_retorno_prev'])) + 1;

            // ✅ Precio efectivo según nivel de uso:
            // 1ra (nivel 0): 0% desc
            // 2da-4ta (nivel 1..3): -15%
            // 3ra+ (nivel >=4): -20%
            $nivel = (int) ($unidad->traje->nivel_uso_alquileres ?? 0);
            $desc = 0;
            if ($nivel >= 1 && $nivel <= 3) {
                $desc = 0.15;
            } elseif ($nivel >= 4) {
                $desc = 0.20;
            }

            $precioEfectivo = round(((1 - $desc) * (float) $unidad->traje->pre_alquiler), 2);
            $monto  = $dias * $precioEfectivo;
            $sena   = round($monto * 0.40, 2);

            $alquiler = Alquiler::create([
                'cod_usuario_cli'       => auth()->id(),
                'cod_unidad_alq'        => $unidad->cod_unidad,
                'cod_evento_alq'        => $evento->cod_evento,
                'fec_salida'            => $data['fec_salida'],
                'fec_retorno_prev'      => $data['fec_retorno_prev'],
                'monto_total'           => $monto,
                'garantia'              => 0,
                'est_alquiler'          => 'Pendiente_Aprobacion', 
                'nro_celular_cliente'   => $data['nro_celular_cliente'],
                'nombre_garante'        => $data['nombre_garante'],
                'ci_garante'            => $data['ci_garante'],
                'comprobante_pago_path' => $comprobantePath, 
                'monto_sena'            => $sena,
                'fecha_limite_pago'     => null, 
            ]);

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
        abort_if(! in_array($alquiler->est_alquiler, ['Pendiente_Aprobacion', 'Reservado'], true), 422);

        $alquiler->update(['est_alquiler' => 'Cancelado']);

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

    public function aprobar(Alquiler $alquiler)
    {
        $this->autorizarAlquiler($alquiler);
        abort_if($alquiler->est_alquiler !== 'Pendiente_Aprobacion', 422);

        DB::transaction(function () use ($alquiler) {
            $alquiler->update(['est_alquiler' => 'Reservado']);
            $alquiler->unidadFisica->update(['disponibilidad' => false]);

            Notificacion::create([
                'cod_usuario_not' => $alquiler->cod_usuario_cli,
                'titulo'  => '¡Alquiler confirmed!',
                'mensaje' => 'El vendedor aprobó tu solicitud de ' . $alquiler->unidadFisica->traje->nom_traje . '.',
                'tipo'    => 'exito',
            ]);
        });

        return back()->with('success', 'Alquiler aprobado y unidad bloqueada.');
    }

    public function rechazar(Request $request, Alquiler $alquiler)
    {
        $this->autorizarAlquiler($alquiler);
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

    public function sancionar(Request $request, Alquiler $alquiler)
    {
        // 1. Muro preventivo de seguridad: llama al método de soporte unificado
        $this->autorizarAlquiler($alquiler);

        // 2. Validación de datos sanitizada para evitar tildes cruzadas en la DB
        $data = $request->validate([
            'tipo_sancion'  => ['required', 'in:Retraso,Dano,Perdida,Limpieza'],
            'monto_sancion' => ['required', 'numeric', 'min:1'],
            'descripcion'   => ['required', 'string', 'min:5', 'max:1000'],
        ], [
            'tipo_sancion.in'      => 'El tipo de falta seleccionado no es válido en el catálogo folclórico.',
            'monto_sancion.min'    => 'El monto de la multa debe ser mayor o igual a Bs. 1.',
            'descripcion.required' => 'Debes justificar el motivo o el daño detallado de la prenda.',
            'descripcion.min'      => 'La justificación debe tener al menos 5 caracteres explicativos.'
        ]);

        // 3. Inserción segura mediante Eloquent
        $sancion = SancionAlquiler::create([
            'cod_alquiler_ref' => $alquiler->cod_alquiler,
            'tipo_sancion'     => $data['tipo_sancion'], 
            'monto_sancion'    => $data['monto_sancion'],
            'descripcion'      => $data['descripcion'],
            'pagada'           => false,
        ]);

        $alquiler->update(['est_alquiler' => 'En Mora']);

        Notificacion::create([
            'cod_usuario_not' => $alquiler->cod_usuario_cli,
            'titulo'          => '🚨 Sanción registrada',
            'mensaje'         => 'Se registró una multa de Bs. ' . number_format($sancion->monto_sancion, 0) . ' bajo el concepto de: ' . ($sancion->tipo_sancion === 'Dano' ? 'Daño' : $sancion->tipo_sancion) . '.',
            'tipo'            => 'alerta',
        ]);

        return back()->with('success', '¡Sanción y multa aplicadas correctamente al fraterno!');
    }

    public function devolver(Request $request, Alquiler $alquiler)
    {
        $this->autorizarAlquiler($alquiler);

        $data = $request->validate([
            'fec_retorno_real' => ['required', 'date'],
            'estado_fisico'    => ['required', 'in:Nuevo,Buen Estado,Desgastado,En Reparación'],
            'observaciones'    => ['nullable', 'string', 'max:1000'],
        ], [
            'fec_retorno_real.required' => 'La fecha de retorno real es obligatoria para cerrar el ciclo.',
            'estado_fisico.required'    => 'Debes auditar el estado físico en el que regresa el traje.',
            'estado_fisico.in'          => 'El estado seleccionado no coincide con las categorías del perchero.'
        ]);

        DB::transaction(function () use ($alquiler, $data) {
            $fechaPrevista = Carbon::parse($alquiler->fec_retorno_prev);
            $fechaReal     = Carbon::parse($data['fec_retorno_real']);
            $enMora        = $fechaReal->greaterThan($fechaPrevista);

            $alquiler->update([
                'fec_retorno_real' => $data['fec_retorno_real'],
                'est_alquiler'     => $enMora ? 'En Mora' : 'Devuelto',
            ]);

            if ($alquiler->unidadFisica) {
                $alquiler->unidadFisica->update([
                    'estado_fisico'  => $data['estado_fisico'],
                    'observaciones'  => $data['observaciones'] ?? 'Devuelto conforme.',
                    'disponibilidad' => $data['estado_fisico'] !== 'En Reparación',
                ]);
            }

            Notificacion::create([
                'cod_usuario_not' => $alquiler->cod_usuario_cli,
                'titulo'          => $enMora ? '⚠️ Devolución fuera de plazo' : '↩️ Prenda recibida',
                'mensaje'         => $enMora 
                    ? 'Tu devolución del traje fue registrada pero superó la fecha límite.' 
                    : 'El traje fue recibido conforme. ¡Gracias por confiar en Los Andes!',
                'tipo'            => $enMora ? 'alerta' : 'exito',
            ]);
        });

        return back()->with('success', '¡Devolución e inventario actualizados con éxito en el sistema!');
    }

    /**
     * 🌟 MÉTODO DE SOPORTE ADICIONADO: Centraliza la seguridad operativa del módulo comercial
     */
    private function autorizarAlquiler(Alquiler $alquiler)
    {
        // Bloquea peticiones cruzadas verificando la sucursal/tienda del vendedor autenticado
        abort_unless(
            $alquiler->unidadFisica?->traje?->tienda?->cod_usuario_tie === auth()->id(), 
            403, 
            'No tienes los privilegios comerciales requeridos sobre esta sucursal.'
        );
    }
}