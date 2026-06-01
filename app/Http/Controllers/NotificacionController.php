<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    public function index()
    {
        $notificaciones = Notificacion::where('cod_usuario_not', auth()->id())
            ->latest('cod_notificacion')
            ->paginate(12);

        return view('notificaciones.index', compact('notificaciones'));
    }

    public function marcarLeida(Notificacion $notificacion)
    {
        abort_unless($notificacion->cod_usuario_not === auth()->id(), 403);

        $notificacion->update(['leido' => true]);

        return back()->with('success', 'Notificacion marcada como leida.');
    }

    public function marcarTodas()
    {
        Notificacion::where('cod_usuario_not', auth()->id())
            ->where('leido', false)
            ->update(['leido' => true]);

        return back()->with('success', 'Todas las notificaciones fueron marcadas como leidas.');
    }

    public function destroy(Notificacion $notificacion)
    {
        abort_unless($notificacion->cod_usuario_not === auth()->id(), 403);

        $notificacion->delete();

        return back()->with('success', 'Notificacion eliminada.');
    }
}
