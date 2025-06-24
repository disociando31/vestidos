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
    public function index()
    {
        $rentas = Renta::with(['cliente', 'items.producto'])
            ->orderBy('fecha_renta', 'desc')
            ->paginate(20);

        return view('rentas.index', compact('rentas'));
    }

    public function create()
    {
        $clientes = Cliente::orderBy('nombre')->get();
        $productos = Producto::disponibles()->with(['atributos', 'imagenes'])->get();

        return view('rentas.crear', compact('clientes', 'productos'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cliente_id' => 'required|exists:clientes,id',
            'fecha_renta' => 'required|date',
            'fecha_devolucion' => 'required|date|after:fecha_renta',
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|integer|min:1',
            'notas' => 'nullable|string',
            'recibido_por' => 'required|string|max:100',
            'abono_inicial' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validado = $validator->validated();

        DB::beginTransaction();

        try {
            $renta = Renta::create([
                'cliente_id' => $validado['cliente_id'],
                'fecha_renta' => $validado['fecha_renta'],
                'fecha_devolucion' => $validado['fecha_devolucion'],
                'monto_total' => 0, // se calculará abajo
                'monto_pagado' => 0,
                'estado' => 'pendiente',
                'notas' => $validado['notas'] ?? null,
                'recibido_por' => $validado['recibido_por']
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

            $renta->monto_total = $total;
            $renta->save();

            // Registrar abono inicial si se ingresó
            $abono = $validado['abono_inicial'] ?? 0;
            if ($abono > 0) {
                Pago::create([
                    'renta_id' => $renta->id,
                    'monto' => $abono,
                    'fecha_pago' => now(),
                    'metodo_pago' => 'efectivo', // o seleccionable en el formulario
                    'observaciones' => 'Abono inicial'
                ]);

                $renta->actualizarEstado();
            }

            DB::commit();

            return redirect()->route('rentas.mostrar', $renta)->with('exito', 'Renta creada exitosamente');
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
