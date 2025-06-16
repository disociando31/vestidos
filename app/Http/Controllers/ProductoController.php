<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\AtributoProducto;
use App\Models\ImagenProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::with(['atributos', 'imagenes'])
            ->orderBy('tipo')
            ->orderBy('nombre')
            ->paginate(20);
            
        return view('productos.index', compact('productos'));
    }
    
    public function crear()
    {
        return view('productos.crear');
    }
    
    public function guardar(Request $request)
    {
        $validado = $request->validate([
            'tipo' => 'required|in:traje,vestido,vestido_15',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'precio_renta' => 'required|numeric|min:0',
            'atributos' => 'required|array',
            'imagenes' => 'nullable|array',
            'imagenes.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        
        DB::beginTransaction();
        
        try {
            $producto = Producto::create([
                'tipo' => $validado['tipo'],
                'nombre' => $validado['nombre'],
                'descripcion' => $validado['descripcion'],
                'precio_renta' => $validado['precio_renta'],
                'estado' => 'disponible'
            ]);
            
            foreach ($validado['atributos'] as $nombre => $valor) {
                AtributoProducto::create([
                    'producto_id' => $producto->id,
                    'nombre' => $nombre,
                    'valor' => $valor
                ]);
            }
            
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
                ->with('exito', 'Producto creado exitosamente');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear el producto: ' . $e->getMessage());
        }
    }
    
    public function mostrar(Producto $producto)
    {
        $producto->load(['atributos', 'imagenes']);
        return view('productos.mostrar', compact('producto'));
    }
}