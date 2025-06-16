<?php

namespace App\Http\Controllers;

use App\Models\Renta;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\ItemRenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RentaController extends Controller
{
    public function index()
    {
        $rentas = Renta::with(['cliente', 'items.producto'])
            ->orderBy('fecha_renta', 'desc')
            ->paginate(20);
            
        return view('rentas.index', compact('rentas'));
    }
    
    public function crear()
    {
        $clientes = Cliente::orderBy('nombre')->get();
        $productos = Producto::disponibles()->with(['atributos', 'imagenes'])->get();
        
        return view('rentas.crear', compact('clientes', 'productos'));
    }
    
    public function guardar(Request $request)
    {
        $validado = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'fecha_renta' => 'required|date',
            'fecha_devolucion' => 'required|date|after:fecha_renta',
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|integer|min:1',
            'notas' => 'nullable|string',
            'tipo_gancho' => 'nullable|string',
            'recibido_por' => 'required|string|max:100'
        ]);
        
        DB::beginTransaction();
        
        try {
            $renta = Renta::create([
                'cliente_id' => $validado['cliente_id'],
                'fecha_renta' => $validado['fecha_renta'],
                'fecha_devolucion' => $validado['fecha_devolucion'],
                'monto_total' => 0,
                'monto_pagado' => 0,
                'estado' => 'pendiente',
                'notas' => $validado['notas'] ?? null,
                'tipo_gancho' => $validado['tipo_gancho'] ?? null,
                'recibido_por' => $validado['recibido_por']
            ]);
            
            $total = 0;
            
            foreach ($validado['items'] as $item) {
                $producto = Producto::find($item['producto_id']);
                
                $subtotal = $producto->precio_renta * $item['cantidad'];
                $iva = $subtotal * 0.16;
                $totalItem = $subtotal + $iva;
                
                ItemRenta::create([
                    'renta_id' => $renta->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $producto->precio_renta,
                    'subtotal' => $subtotal,
                    'iva' => $iva,
                    'descuento' => 0,
                    'total' => $totalItem,
                    'atributos' => $producto->atributos->pluck('valor', 'nombre')
                ]);
                
                $total += $totalItem;
                $producto->estado = 'rentado';
                $producto->save();
            }
            
            $renta->monto_total = $total;
            $renta->save();
            
            DB::commit();
            
            return redirect()->route('rentas.mostrar', $renta)
                ->with('exito', 'Renta creada exitosamente');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear la renta: ' . $e->getMessage());
        }
    }
    
    public function mostrar(Renta $renta)
    {
        $renta->load(['cliente', 'items.producto', 'pagos']);
        return view('rentas.mostrar', compact('renta'));
    }
    
    public function devolver(Renta $renta)
    {
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
            return back()->with('error', 'Error al registrar la devolución');
        }
    }
}