<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Danza;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DanzaController extends Controller
{
    public function index()
    {
        $danzas = Danza::withTrashed()->orderBy('nom_danza', 'asc')->get();
        return view('admin.danzas.index', compact('danzas'));
    }

    public function create()
    {
        return view('admin.danzas.create');
    }

    public function store(Request $request)
    {
        $this->validarDanza($request);

        $data = $request->all();
        if ($request->hasFile('imagen_danza')) {
            $data['imagen_danza'] = $request->file('imagen_danza')->store('danzas', 'public');
        }

        Danza::create($data);
        return redirect()->route('admin.danzas.index')->with('success', 'Danza registrada con éxito.');
    }

    public function edit($id)
    {
        $danza = Danza::withTrashed()->findOrFail($id);
        return view('admin.danzas.edit', compact('danza'));
    }

    public function update(Request $request, $id)
    {
        $this->validarDanza($request, $id);

        $danza = Danza::withTrashed()->findOrFail($id);
        $data = $request->all();

        if ($request->hasFile('imagen_danza')) {
            if ($danza->imagen_danza) Storage::disk('public')->delete($danza->imagen_danza);
            $data['imagen_danza'] = $request->file('imagen_danza')->store('danzas', 'public');
        }

        $danza->update($data);
        return redirect()->route('admin.danzas.index')->with('success', 'Danza actualizada correctamente.');
    }

    // Lógica Maestra de Validación con mensajes en español
    private function validarDanza(Request $request, $id = null)
    {
        $nombre = $request->nom_danza;
        $danzaExistente = Danza::withTrashed()->where('nom_danza', $nombre)
            ->when($id, function($q) use ($id) { 
                return $q->where('cod_danza', '!=', $id); 
            })->first();

        if ($danzaExistente) {
            $mensaje = $danzaExistente->trashed() 
                ? "La danza '$nombre' ya existe pero está DADA DE BAJA. Por favor, restáurela."
                : "La danza '$nombre' ya se encuentra ACTIVA.";
            
            $request->validate(['nom_danza' => 'unique:danzas,nom_danza'], ['nom_danza.unique' => $mensaje]);
        }

        return $request->validate([
            'nom_danza' => 'required|string|max:255',
            'clasificacion' => 'required',
            'descripcion' => 'nullable|string',
            'imagen_danza' => 'nullable|image|max:2048'
        ], [
            'nom_danza.required' => 'El nombre es obligatorio.',
            'clasificacion.required' => 'Seleccione una clasificación.'
        ]);
    }

    public function destroy($id)
    {
        Danza::findOrFail($id)->delete();
        return back()->with('info', 'Danza enviada a la papelera.');
    }

    public function restore($id)
    {
        Danza::withTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Danza reactivada.');
    }
}