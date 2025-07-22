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
        $productos = Producto::where('estado', 'disponible')->with('imagenPrincipal')->get();

        return view('rentas.crear', compact('clientes', 'productos'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cliente_id' => 'nullable|exists:clientes,id',
            'nuevo_cliente_nombre' => 'nullable|required_without:cliente_id|string|max:255',
            'nuevo_cliente_telefono' => 'nullable|string|max:20',
            'telefono_alterno' => 'nullable|string|max:20',

            'fecha_renta' => 'required|date',
            'fecha_devolucion' => 'required|date|after:fecha_renta',
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|integer|min:1',
            'notas' => 'nullable|string',
            'recibido_por' => 'required|string|max:100',
            'abono_inicial' => 'nullable|numeric|min:0',

            // Campos adicionales
            'camisa_color' => 'nullable|string|max:100',
            'zapatos_color' => 'nullable|string|max:100',
            'zapatos_talla' => 'nullable|string|max:20',
            'cartera_color' => 'nullable|string|max:100',
            'otro_nombre' => 'nullable|string|max:100',
            'otro_precio' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validado = $validator->validated();

        // Crear cliente si es nuevo
        if (!empty($validado['cliente_id'])) {
            $cliente_id = $validado['cliente_id'];
        } else {
            $nuevoCliente = Cliente::create([
                'nombre' => $validado['nuevo_cliente_nombre'],
                'telefono' => $validado['nuevo_cliente_telefono'] ?? null,
                'telefono_alterno' => $validado['telefono_alterno'] ?? null,
                'fecha_registro' => now()
            ]);
            $cliente_id = $nuevoCliente->id;
        }

        DB::beginTransaction();

        try {
            $renta = Renta::create([
                'cliente_id' => $cliente_id,
                'fecha_renta' => $validado['fecha_renta'],
                'fecha_devolucion' => $validado['fecha_devolucion'],
                'monto_total' => 0,
                'monto_pagado' => 0,
                'estado' => 'pendiente',
                'notas' => $validado['notas'] ?? null,
                'recibido_por' => $validado['recibido_por'],

                // Guardar campos adicionales
                'camisa_color' => $validado['camisa_color'] ?? null,
                'zapatos_color' => $validado['zapatos_color'] ?? null,
                'zapatos_talla' => $validado['zapatos_talla'] ?? null,
                'cartera_color' => $validado['cartera_color'] ?? null,
                'otro_nombre' => $validado['otro_nombre'] ?? null,
                'otro_precio' => $validado['otro_precio'] ?? 0,
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
                    'atributos' => json_encode($atributos)
                ]);

                $total += $totalItem;
                $producto->marcarComoRentado();
            }

            // Agregar el precio del objeto adicional si se proporcionó
            if (!empty($validado['otro_precio'])) {
                $total += floatval($validado['otro_precio']);
            }

            $renta->monto_total = $total;
            $renta->save();

            // Registrar abono inicial
            $abono = $validado['abono_inicial'] ?? 0;
            if ($abono > 0) {
                Pago::create([
                    'renta_id' => $renta->id,
                    'monto' => $abono,
                    'fecha_pago' => now(),
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

    public function eventos()
    {
        $rentas = Renta::where('estado', '!=', 'devuelto')->with('cliente')->get();

        $eventos = $rentas->map(function ($renta) {
            return [
                'id' => $renta->id,
                'title' => $renta->cliente->nombre,
                'start' => $renta->fecha_renta,
                'end' => $renta->fecha_devolucion,
                'color' => $this->getColorPorEstado($renta->estado),
                'url' => route('rentas.show', $renta),
            ];
        });

        return response()->json($eventos);
    }

    protected function getColorPorEstado($estado)
    {
        return match ($estado) {
            'pendiente' => '#ffc107',
            'abonado' => '#17a2b8',
            'pagado' => '#28a745',
            default => '#6c757d',
        };
    }
}


