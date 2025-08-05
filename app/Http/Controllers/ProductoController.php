<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\AtributoProducto;
use App\Models\ImagenProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Validator;

class ProductoController extends Controller
{
    use ValidatesRequests;

    public function index(Request $request)
    {
    $productos = Producto::with(['atributos', 'imagenes'])
        ->when($request->filled('buscar'), function ($query) use ($request) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                ->orWhere('codigo', 'like', "%{$buscar}%");
            });
        })
        ->orderBy('tipo')
        ->orderBy('nombre')
        ->paginate(20);

    return view('productos.index', compact('productos'));
    }

    public function create()
    {
        return view('productos.crear');
    }
    public function show(Producto $producto)
{
    // Carga las relaciones necesarias (atributos e imágenes)
    $producto->load(['atributos', 'imagenes']);

    return view('productos.mostrar', compact('producto'));
}

public function store(Request $request)
{ 
    $validator = Validator::make($request->all(), [
        'tipo' => 'required|in:traje,vestido,vestido_15,bodas,primeras_comuniones',
        'nombre' => 'required|string|max:255',
        'descripcion' => 'required|string',
        'precio_renta' => 'required|numeric|min:0',
        'atributo_nombre' => 'required|array',
        'atributo_valor' => 'required|array',
        'imagenes' => 'nullable|array',
        'imagenes.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
    ]);

    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }

    $validated = $validator->validated();

    DB::beginTransaction();

    try {
        // Generar código automático único
        $prefijo = match ($validated['tipo']) {
            'traje' => 'TRA',
            'vestido' => 'VES',
            'vestido_15' => 'V15',
            'bodas' => 'BOD',
            'primeras_comuniones' => 'PCO',
        };

        // Obtener el último código
        $ultimoCodigo = Producto::where('tipo', $validated['tipo'])
            ->whereNotNull('codigo')
            ->orderBy('id', 'desc')
            ->value('codigo');

        $numero = 1;

        if ($ultimoCodigo && preg_match('/\d+$/', $ultimoCodigo, $coincidencias)) {
            $numero = intval($coincidencias[0]) + 1;
        }

        $codigo = $prefijo . str_pad($numero, 3, '0', STR_PAD_LEFT);

        // Crear producto
        $producto = Producto::create([
            'tipo' => $validated['tipo'],
            'nombre' => $validated['nombre'],
            'codigo' => $codigo,
            'descripcion' => $validated['descripcion'],
            'precio_renta' => $validated['precio_renta'],
            'estado' => 'disponible',
            'rentado' => 0,
            'fecha_disponible' => now()
        ]);

        // Guardar atributos
        foreach ($validated['atributo_nombre'] as $index => $nombre) {
            AtributoProducto::create([
                'producto_id' => $producto->id,
                'nombre' => $nombre,
                'valor' => $validated['atributo_valor'][$index] ?? ''
            ]);
        }

        // Guardar imágenes
        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $imagen) {
                $ruta = $imagen->store('imagenes_productos', 'public');

                ImagenProducto::create([
                    'producto_id' => $producto->id,
                    'ruta' => $ruta,
                    'es_principal' => false
                ]);
            }
        }

        DB::commit();

        return redirect()->route('productos.mostrar', $producto)
            ->with('exito', 'Producto creado exitosamente con código: ' . $codigo);

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Error al crear el producto: ' . $e->getMessage())->withInput();
    }
}
public function edit(Producto $producto)
{
    $producto->load(['atributos', 'imagenes']);
    return view('productos.editar', compact('producto'));
}
public function update(Request $request, Producto $producto)
{
    $validator = Validator::make($request->all(), [
        'tipo' => 'required|in:traje,vestido,vestido_15,bodas,primeras_comuniones',
        'nombre' => 'required|string|max:255',
        'descripcion' => 'required|string',
        'precio_renta' => 'required|numeric|min:0',
        'atributo_nombre' => 'required|array',
        'atributo_valor' => 'required|array',
        'imagenes' => 'nullable|array',
        'imagenes.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
    ]);

    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }

    $validated = $validator->validated();

    DB::beginTransaction();

    try {
        // Actualizar campos básicos
        $producto->update([
            'tipo' => $validated['tipo'],
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'],
            'precio_renta' => $validated['precio_renta']
        ]);

        // Eliminar y recrear atributos
        $producto->atributos()->delete();

        foreach ($validated['atributo_nombre'] as $index => $nombre) {
            AtributoProducto::create([
                'producto_id' => $producto->id,
                'nombre' => $nombre,
                'valor' => $validated['atributo_valor'][$index] ?? ''
            ]);
        }

        // Subir nuevas imágenes si hay
        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $imagen) {
                $ruta = $imagen->store('imagenes_productos', 'public');

                ImagenProducto::create([
                    'producto_id' => $producto->id,
                    'ruta' => $ruta,
                    'es_principal' => false
                ]);
            }
        }

        DB::commit();

        return redirect()->route('productos.mostrar', $producto)
            ->with('exito', 'Producto actualizado correctamente.');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Error al actualizar el producto: ' . $e->getMessage())->withInput();
    }
}

}