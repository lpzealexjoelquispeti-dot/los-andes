<?php

namespace App\Http\Controllers;

use App\Models\Tienda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; 
use App\Models\DisenoTienda;
class TiendaController extends Controller
{
    // Mostrar el formulario para crear la tienda
    public function create()
    {
        // Verificar si el vendedor ya tiene una tienda registrada
        $tiendaExistente = Tienda::where('cod_usuario_tie', Auth::id())->first();

        if ($tiendaExistente) {
            // Si ya tiene, lo mandamos a ver su tienda (crearemos esta vista después)
            return redirect()->route('vendedor.tienda.create')->with('status', 'Ya tienes una tienda registrada.');
        }

        return view('vendedor.tienda.create');
    }

    // Guardar los datos de la tienda en la base de datos
    public function store(Request $request)
    {
        $request->validate([
            // Mínimo 3 letras, máximo 100, solo letras, números y espacios
            'nom_tie' => [
    'required', 
    'string', 
    'min:3', 
    'max:100', 
    // Explicación:
    // (?=.*[a-zA-ZñÑáéíóúÁÉÍÓÚ]) -> Asegura que haya al menos una letra en cualquier posición.
    // [a-zA-Z0-9\sñÑáéíóúÁÉÍÓÚ\-\'"&]+ -> Permite el resto de caracteres permitidos.
    'regex:/^(?=.*[a-zA-ZñÑáéíóúÁÉÍÓÚ])[a-zA-Z0-9\sñÑáéíóúÁÉÍÓÚ\-\'\"& ]+$/u'
],
            // Mínimo 10 caracteres (para evitar que pongan solo "calle")
            'dir_tie' => ['required', 'string', 'min:10', 'max:255'],
            
            // Regex estricto: Empieza con 6 o 7 y le siguen exactamente 7 números más (8 en total)
            'tel_tie' => ['required', 'string', 'regex:/^[67][0-9]{7}$/'], 
            'foto_ref' => ['required', 'file', 'mimes:jpg,jpeg', 'max:2048'],
            'latitud' => ['required', 'numeric'],
            'longitud' => ['required', 'numeric'],
        ], [
            // Mensajes hiper-claros en español
            'nom_tie.required' => 'Debes darle un nombre a tu tienda.',
            'nom_tie.min' => 'El nombre es muy corto. Mínimo 3 caracteres.',
            'nom_tie.max' => 'El nombre es demasiado largo. Máximo 100 caracteres.',
            'nom_tie.regex' => 'El nombre contiene símbolos extraños no permitidos.',
            
            'dir_tie.required' => 'Necesitamos saber dónde está tu local.',
            'dir_tie.min' => 'Sé más específico con tu dirección (ej. Calle, Número, Esquina).',
            
            'tel_tie.required' => 'El WhatsApp es obligatorio para tus clientes.',
            'tel_tie.regex' => 'Debe ser un número de celular válido en Bolivia (8 dígitos, empezando con 6 o 7).',
            'foto_ref.required' => 'Debes subir una foto de referencia de tu local.',
            'foto_ref.mimes' => 'La imagen DEBE ser estrictamente en formato .JPG',
            'foto_ref.max' => 'La foto es muy pesada. El máximo es 2MB.',
            'latitud.required' => 'Debes marcar la ubicación exacta de tu tienda en el mapa.',
        ]);
        $rutaFoto = $request->file('foto_ref')->store('tiendas', 'public');
        Tienda::create([
            'nom_tie' => $request->nom_tie,
            'dir_tie' => $request->dir_tie,
            'tel_tie' => $request->tel_tie,
            'foto_ref' => $rutaFoto, // Guardamos la ruta
            'latitud' => $request->latitud,
            'longitud' => $request->longitud,
            'est_tie' => false,
            'cod_usuario_tie' => Auth::id(),
        ]);

        return redirect()->route('vendedor.dashboard')->with('success', '¡Tu tienda fue registrada! Está en revisión por el SuperAdmin.');
    }
    // El cerebro del menú: decide si mostrar la tienda o mandarlo a crearla
    public function index()
    {
        $tienda = Tienda::where('cod_usuario_tie', Auth::id())->first();

        // Si no tiene tienda, lo obligamos a ir al formulario de creación
        if (!$tienda) {
            return redirect()->route('vendedor.tienda.create');
        }

        // Si ya tiene tienda, le mostramos su perfil
        return view('vendedor.tienda.show', compact('tienda'));
    }
    public function edit($id)
{
    $tienda = Tienda::findOrFail($id);
    
    // Seguridad: Solo el dueño puede editar su propia tienda
    if ($tienda->cod_usuario_tie !== Auth::id()) {
        abort(403);
    }

    return view('vendedor.tienda.edit', compact('tienda'));
}

public function update(Request $request, $id)
{
    $tienda = Tienda::findOrFail($id);

    $request->validate([
        'nom_tie' => ['required', 'string', 'min:3', 'max:100'],
        'dir_tie' => ['required', 'string', 'min:10', 'max:255'],
        'tel_tie' => ['required', 'string', 'regex:/^[67][0-9]{7}$/'],
        'foto_ref' => ['nullable', 'file', 'mimes:jpg,jpeg', 'max:2048'], // Ahora es opcional
        'latitud' => ['required', 'numeric'],
        'longitud' => ['required', 'numeric'],
    ]);

    // Actualizar datos básicos
    $tienda->nom_tie = $request->nom_tie;
    $tienda->dir_tie = $request->dir_tie;
    $tienda->tel_tie = $request->tel_tie;
    $tienda->latitud = $request->latitud;
    $tienda->longitud = $request->longitud;

    // Lógica de Foto: Si sube una nueva, borrar la vieja y guardar la nueva
    if ($request->hasFile('foto_ref')) {
        Storage::disk('public')->delete($tienda->foto_ref);
        $tienda->foto_ref = $request->file('foto_ref')->store('tiendas', 'public');
    }

    $tienda->save();

    return redirect()->route('vendedor.tienda.index')->with('success', '¡Datos actualizados con éxito!');
}
public function diseno()
    {
        // Buscamos la tienda del vendedor logueado
        $tienda = Tienda::where('cod_usuario_tie', Auth::id())->first();

        // Si la tienda no tiene diseño aún, lo creamos con valores por defecto
        $diseno = DisenoTienda::firstOrCreate(
            ['cod_tienda_dis' => $tienda->cod_tienda],
            [
                'color_primario' => '#ce1212',
                'color_fondo' => '#f3f4f6',
                'tipografia' => 'font-sans'
            ]
        );

        return view('vendedor.tienda.diseno', compact('tienda', 'diseno'));
    }

    public function storeDiseno(Request $request)
    {
        $tienda = Tienda::where('cod_usuario_tie', Auth::id())->first();
        $diseno = $tienda->diseno;

        // --- EL BLOQUE DE VALIDACIÓN ---
        $request->validate([
            // WhatsApp: Solo números, sin letras ni símbolos (puedes ajustar el largo)
            'link_whatsapp' => 'nullable|numeric|digits_between:8,15',
            
            // Facebook: Debe ser una URL y contener "facebook.com"
            'link_facebook' => [
                'nullable', 
                'url', 
                'regex:/^(https?:\/\/)?(www\.)?facebook\.com\/[a-zA-Z0-9(\.\?)?]/i'
            ],
            
            // Fotos: Solo formatos específicos y máximo 2MB (2048 KB)
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'banner' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            
            // Otros campos
            'color_primario' => 'required|string|size:7',
            'color_fondo' => 'required|string|size:7',
            'slogan' => 'nullable|string|max:100',
        ], [
            // Mensajes personalizados para que el vendedor entienda el error
            'link_whatsapp.numeric' => 'El WhatsApp solo debe contener números.',
            'link_facebook.regex' => 'El enlace debe ser una URL válida de Facebook.',
            'logo.mimes' => 'El logo debe ser una imagen JPG o PNG.',
            'banner.max' => 'La portada no debe pesar más de 2MB.',
        ]);

        // --- LÓGICA DE GUARDADO ---
        $datos = $request->only(['color_primario', 'color_fondo', 'slogan', 'link_facebook', 'link_whatsapp', 'horario_tie']);

        if ($request->hasFile('logo')) {
            $datos['logo_path'] = $request->file('logo')->store('tiendas/logos', 'public');
        }

        if ($request->hasFile('banner')) {
            $datos['banner_path'] = $request->file('banner')->store('tiendas/banners', 'public');
        }

        $diseno->update($datos);

        return back()->with('success', '¡Identidad visual actualizada correctamente!');
    }
}