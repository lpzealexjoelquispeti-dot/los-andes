<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tienda;
use Illuminate\Http\Request;

class AdminTiendaController extends Controller
{
    // Listar solo las tiendas que están esperando aprobación
    public function index()
    {
        $tiendasPendientes = Tienda::where('est_tie', false)
            ->with('vendedor') // Carga los datos del dueño de la tienda
            ->latest()
            ->get();

        return view('admin.tiendas.index', compact('tiendasPendientes'));
    }

    // Método para aprobar la tienda
    public function aprobar($id)
    {
        $tienda = Tienda::findOrFail($id);
        $tienda->est_tie = true;
        $tienda->save();

        // Ahora redirigimos explícitamente a la lista de todas las tiendas pendientes
        return redirect()->route('admin.tiendas.index')->with('success', "¡Proceso completado! La tienda '{$tienda->nom_tie}' ya está activa en el sistema.");
    }

    // Método para rechazar/eliminar (opcional, por si suben cosas indebidas)
    public function rechazar($id)
    {
        $tienda = Tienda::findOrFail($id);
        $nombre = $tienda->nom_tie;
        $tienda->delete();

        // Redirigimos a la lista con un mensaje de alerta
        return redirect()->route('admin.tiendas.index')->with('error', "La solicitud de la tienda '{$nombre}' ha sido rechazada y eliminada.");
    }
    public function show($id)
    {
        // Obtenemos la tienda con toda la información de su dueño
        $tienda = Tienda::with('vendedor')->findOrFail($id);
        
        return view('admin.tiendas.show', compact('tienda'));
    }
}