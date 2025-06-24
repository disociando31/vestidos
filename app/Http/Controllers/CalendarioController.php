<?php

namespace App\Http\Controllers;

use App\Models\Renta;
use Illuminate\Http\Request;

class CalendarioController extends Controller
{
    public function index()
    {
        return view('calendario.index');
    }
    
    public function eventos(Request $request)
    {
        $inicio = $request->input('start');
        $fin = $request->input('end');
        
        $rentas = Renta::whereBetween('fecha_renta', [$inicio, $fin])
            ->orWhereBetween('fecha_devolucion', [$inicio, $fin])
            ->with(['cliente', 'items.producto'])
            ->get();
            
        $eventos = [];
        
        foreach ($rentas as $renta) {
            $color = match ($renta->estado) {
                'pendiente' => '#6c757d', // gris
                'parcial'   => '#ffc107', // amarillo
                'pagado'    => '#28a745', // verde
                'devuelto'  => '#0dcaf0', // azul claro
                'atrasado'  => '#dc3545', // rojo
                default     => '#007bff'  // azul por defecto
            };

            $eventos[] = [
                'title' => $renta->cliente->nombre . ' - ' . $renta->items->first()->producto->nombre,
                'start' => $renta->fecha_renta->format('Y-m-d'),
                'end' => $renta->fecha_devolucion->modify('+1 day')->format('Y-m-d'),
                'color' => $color,
                'extendedProps' => [
                    'renta_id' => $renta->id,
                    'cliente' => $renta->cliente->nombre,
                    'productos' => $renta->items->map(function ($item) {
                        return $item->producto->nombre . ' (' . $item->cantidad . ')';
                    })->implode(', '),
                    'estado' => $renta->estado,
                    'total' => number_format($renta->monto_total, 2),
                    'pagado' => number_format($renta->monto_pagado, 2),
                    'saldo' => number_format($renta->saldo, 2)
                ]
            ];
        }
        
        return response()->json($eventos);
    }
}
