<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Traje;
use App\Models\InventarioUnidad;
use Barryvdh\DomPDF\Facade\Pdf;

class TrajeImpresionController extends Controller
{
    /**
     * Muestra el panel maestro de etiquetado con el carrusel superior unificado.
     */
    public function panelImpresion($cod_traje)
    {
        $tienda = Auth::user()->tiendas()->first();

        if (!$tienda || !$tienda->est_tie) {
            return redirect()->route('vendedor.dashboard')->with('error', 'Tienda no aprobada.');
        }

        // Redirección preventiva al ID Padre para mantener el estándar unificado de colecciones
        $trajeVerificacion = Traje::withTrashed()->findOrFail($cod_traje);
        if ($trajeVerificacion->cod_traje_padre !== null) {
            return redirect()->route('vendedor.trajes.impresion.panel', $trajeVerificacion->cod_traje_padre);
        }

        // 1. Traemos el traje activo consolidado con su variante de damas y fotos
        $trajeActivo = Traje::where('cod_tienda_traje', $tienda->cod_tienda)
            ->withTrashed()
            ->with([
                'danza',
                'imagenes',
                'unidades' => function ($q) { $q->withTrashed(); },
                'varianteFemenina' => function ($q) { 
                    $q->withTrashed()->with(['unidades' => function ($sq) { $sq->withTrashed(); }, 'imagenes']); 
                }
            ])
            ->findOrFail($cod_traje);

        // 2. Traemos SOLO los trajes principales (whereNull) para alimentar el carrusel sin duplicados
        $todosLosTrajes = Traje::where('cod_tienda_traje', $tienda->cod_tienda)
            ->whereNull('cod_traje_padre') 
            ->withTrashed()
            ->with([
                'imagenes', 
                'danza',
                'unidades' => function ($q) { $q->withTrashed(); },
                'varianteFemenina' => function ($q) { $q->withTrashed()->with('unidades'); }
            ])
            ->latest()
            ->get();

        return view('vendedor.impresiones.panel', compact('trajeActivo', 'todosLosTrajes'));
    }

    /**
     * Genera el lote de PDF predeterminado multiplicando cada unidad por el total de fotos.
     */
    public function descargarPdf(Request $request, $cod_traje)
{
    $request->validate([
        'unidades' => 'required|array|min:1',
    ]);

    // 1. CAPTURAMOS LA TIENDA DIRECTAMENTE DESDE EL VENDEDOR AUTENTICADO
    // Cargamos la tienda del usuario en sesión junto con su diseño de la tabla disenos_tiendas
    $tienda = auth()->user()->tiendas()->with('diseno')->first();

    // Si encuentra la tienda, extrae su nombre real ('nom_tie'). Si no, usa el nombre del usuario como plan de rescate.
    $nombreTienda = $tienda ? $tienda->nom_tie : auth()->user()->name;
    $nombreLimpio = strtolower(trim($nombreTienda));

    // 2. CONFIGURACIÓN DEL COLOR (Prioriza la base de datos, si no hay, usa el Match Failsafe)
    $colorTienda = '#1e293b'; // Gris oscuro por defecto
    if ($tienda && $tienda->diseno && $tienda->diseno->color_primario) {
        $colorTienda = $tienda->diseno->color_primario;
    } else {
        // Asignación manual basada en el nombre de tu tienda si no hay diseño guardado
        $colorTienda = match (true) {
            str_contains($nombreLimpio, 'andes')    => '#10b981', // Verde Andes
            str_contains($nombreLimpio, 'mundi')    => '#3b82f6', // Azul MundiToys
            str_contains($nombreLimpio, 'imperial') => '#eab308', // Dorado Imperial
            default                                 => '#1e293b',
        };
    }

    // 3. CONFIGURACIÓN DEL LOGO (Busca en disenos_tiendas, si no, usa el respaldo físico)
    $logoPath = null;
    if ($tienda && $tienda->diseno && $tienda->diseno->logo_path) {
        $rutaMesaDiseno = storage_path('app/public/' . $tienda->diseno->logo_path);
        if (file_exists($rutaMesaDiseno)) {
            $logoPath = $rutaMesaDiseno;
        }
    }

    // Si el vendedor no subió un logo en su diseño, buscamos el archivo fijo en public/img/logos/
    if (!$logoPath) {
        $logoNombre = match (true) {
            str_contains($nombreLimpio, 'andes')    => 'logo_andes.png',
            str_contains($nombreLimpio, 'mundi')    => 'logo_munditoys.png',
            str_contains($nombreLimpio, 'imperial') => 'logo_imperial.png',
            default                                 => 'default.png',
        };
        
        $rutaFisicaLocal = public_path('img/logos/' . $logoNombre);
        if (file_exists($rutaFisicaLocal)) {
            $logoPath = $rutaFisicaLocal;
        }
    }

    // 4. PROCESADO DE LAS ETIQUETAS DEL LOTE
    $etiquetasFinales = [];
    $unidades = InventarioUnidad::whereIn('cod_unidad', $request->unidades)->get();

    foreach ($unidades as $unidad) {
        $trajeDuenio = Traje::withTrashed()->with(['imagenes', 'danza'])->find($unidad->cod_traje_base);
        $totalFotos = $trajeDuenio->imagenes->count();
        $totalFotos = $totalFotos > 0 ? $totalFotos : 1;

        for ($i = 1; $i <= $totalFotos; $i++) {
            $serialPieza = $unidad->nro_serie_interno . '-P' . $i;

            $etiquetasFinales[] = [
                'traje_nom'    => $trajeDuenio->nom_traje,
                'talla'        => $unidad->talla,
                'serial_pieza' => $serialPieza,
                'pieza_nom'    => 'Pieza / Accesorio ' . $i,
                'tienda_nom'   => $nombreTienda, // Inyecta el nombre real aquí
                'tienda_color' => $colorTienda
            ];
        }
    }

    $pdf = Pdf::loadView('vendedor.impresiones.pdf', compact('etiquetasFinales', 'logoPath'))
              ->setPaper('letter', 'portrait');

    return $pdf->stream('Lote_Etiquetas_Perchero.pdf');
}

    /**
     * Reimprime una pieza o accesorio específico de forma ilimitada (Por pérdida/daño).
     */
    public function reimprimirPieza($id, $pieza)
    {
        $unidad = InventarioUnidad::findOrFail($id);
        $traje = Traje::withTrashed()->with('danza')->findOrFail($unidad->cod_traje_base);

        $serialPieza = $unidad->nro_serie_interno . '-P' . $pieza;

        $etiquetasFinales[] = [
            'traje_nom' => $traje->nom_traje,
            'talla' => $unidad->talla,
            'serial_pieza' => $serialPieza,
            'pieza_nom' => 'Reposición: Pieza ' . $pieza,
        ];

        $pdf = Pdf::loadView('vendedor.impresiones.pdf', compact('etiquetasFinales'))
                  ->setPaper('letter', 'portrait');

        return $pdf->stream('Reposicion_' . $serialPieza . '.pdf');
    }
}