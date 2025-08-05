<?php

namespace App\Http\Controllers;

use App\Models\Renta;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\ItemRenta;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RentaController extends Controller
{
    public function index(Request $request)
    {
        $query = Renta::with(['cliente', 'items.producto']);

        if ($request->filled('cliente')) {
            $query->whereHas('cliente', function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->cliente . '%');
            });
        }

        if ($request->filled('fecha')) {
            $query->whereDate('fecha_renta', $request->fecha);
        }

        if ($request->filled('producto')) {
            $query->whereHas('items.producto', function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->producto . '%');
            });
        }

        $rentas = $query->orderBy('fecha_renta', 'desc')->paginate(20);

        return view('rentas.index', compact('rentas'));
    }

public function create()
{
    $clientes = Cliente::orderBy('nombre')->get();

    // Traer productos con imágenes principales y atributos
    $productos = Producto::with('imagenPrincipal')->get()->filter(function ($producto) {
        return $producto->estaDisponible() || $producto->estado === 'rentado';
    });

    // Agregar img_url para cada producto
    $productos = $productos->map(function ($producto) {
        $ruta = $producto->imagenPrincipal?->ruta;
        if ($ruta && \Storage::disk('public')->exists($ruta)) {
            $producto->img_url = asset('storage/' . $ruta);
        } else {
            $producto->img_url = asset('images/sin_imagen.jpg'); // Cambia esta ruta si tu "sin_imagen" está en otra carpeta
        }
        return $producto;
    });

    return view('rentas.crear', compact('clientes', 'productos'));
}


public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'cliente_id' => 'required|exists:clientes,id',
        'fecha_inicio' => 'required|date',
        'fecha_devolucion' => 'required|date|after:fecha_inicio',
        'items' => 'required|array|min:1',
        'items.*.producto_id' => 'required|exists:productos,id',
        'items.*.cantidad' => 'required|integer|min:1',
        'notas' => 'nullable|string',
        'recibido_por' => 'nullable|string|max:100',
        'abono_inicial' => 'nullable|numeric|min:0',
        // No valides aquí los adicionales, porque puede ser un array asociativo
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }
    $validado = $validator->validated();

    // ⬇️ Recupera los adicionales del request
    $adicionales = $request->input('adicionales', []);

    DB::beginTransaction();

    try {
        $renta = Renta::create([
            'cliente_id' => $validado['cliente_id'],
            'fecha_renta' => $validado['fecha_inicio'],
            'fecha_devolucion' => $validado['fecha_devolucion'],
            'monto_total' => 0,
            'monto_pagado' => 0,
            'estado' => 'pendiente',
            'notas' => $validado['notas'] ?? null,
            'recibido_por' => $validado['recibido_por'] ?? null,
            // No guardes aquí adicionales, lo hacemos más abajo
        ]);

        $total = 0;

        foreach ($validado['items'] as $item) {
            $producto = Producto::findOrFail($item['producto_id']);
            $subtotal = $producto->precio_renta * $item['cantidad'];
            $atributos = $producto->atributos->pluck('valor', 'nombre')->toArray();
            $totalAtributos = array_sum(array_map('floatval', $atributos));
            $totalItem = $subtotal + $totalAtributos;

            ItemRenta::create([
                'renta_id' => $renta->id,
                'producto_id' => $producto->id,
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $producto->precio_renta,
                'subtotal' => $subtotal,
                'descuento' => 0,
                'total' => $totalItem,
                'atributos' => json_encode($atributos),
            ]);

            $total += $totalItem;
            $producto->marcarComoRentado($validado['fecha_devolucion']);
        }

        // Procesa los adicionales: suma sus precios y guárdalos como JSON
        $totalAdicionales = 0;
        $adicionalesClean = [];

        foreach ($adicionales as $adicional) {
            if (!empty($adicional['nombre']) && isset($adicional['precio'])) {
                $precio = floatval($adicional['precio']);
                $totalAdicionales += $precio;
                $adicionalesClean[] = [
                    'nombre' => $adicional['nombre'],
                    'color' => $adicional['color'] ?? null,
                    'talla' => $adicional['talla'] ?? null,
                    'precio' => $precio
                ];
            }
        }

        // Suma el total de adicionales al total general
        $total += $totalAdicionales;

        // Guarda los adicionales en la columna JSON
        $renta->adicionales = $adicionalesClean;
        $renta->monto_total = $total;
        $renta->save();

        // Registrar abono inicial si hay
        $abono = $validado['abono_inicial'] ?? 0;
        if ($abono > 0) {
            Pago::create([
                'renta_id' => $renta->id,
                'monto' => $abono,
                //'fecha_pago' => now(), // sólo si tienes esta columna
                'metodo_pago' => 'efectivo',
                'notas' => 'Abono inicial',
                'recibido_por' => $validado['recibido_por']
            ]);

            $renta->increment('monto_pagado', $abono);
            $renta->actualizarEstado();
        }

        DB::commit();
        return redirect()->route('rentas.mostrar', $renta)->with('exito', 'Renta creada correctamente.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Error al crear la renta: ' . $e->getMessage());
    }
}

    public function show(Renta $renta)
    {
        $renta->load(['cliente', 'items.producto', 'pagos']);
        return view('rentas.mostrar', compact('renta'));
    }

    public function devolver(Renta $renta)
    {
        if ($renta->saldo > 0) {
            return back()->with('error', 'No se puede devolver la renta porque aún tiene un saldo pendiente.');
        }

        DB::beginTransaction();

        try {
            foreach ($renta->items as $item) {
                $producto = $item->producto;
                $producto->estado = 'disponible';
                $producto->save();
            }

            $renta->estado = 'devuelto';
            $renta->save();

            DB::commit();
            return back()->with('exito', 'Productos marcados como devueltos');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar la devolución: ' . $e->getMessage());
        }
    }
}
