<?php

namespace App\Http\Controllers;

use App\Models\Renta;
use Illuminate\Http\Request;

class CalendarioController extends Controller
{
    public function index()
    {
        $hoy = now()->toDateString();
        $semanaInicio = now()->startOfWeek();
        $semanaFin = now()->endOfWeek();
        $mesInicio = now()->startOfMonth();
        $mesFin = now()->endOfMonth();

        // Solo cuenta rentas activas (NO devueltas)
        $rentasHoy = Renta::where('estado', '!=', 'devuelto')
            ->whereDate('fecha_renta', $hoy)
            ->count();

        $rentasSemana = Renta::where('estado', '!=', 'devuelto')
            ->whereBetween('fecha_renta', [$semanaInicio, $semanaFin])
            ->count();

        $rentasMes = Renta::where('estado', '!=', 'devuelto')
            ->whereBetween('fecha_renta', [$mesInicio, $mesFin])
            ->count();

        $rentasAtrasadas = Renta::where('estado', 'atrasado')->count();

        return view('calendario.index', compact(
            'rentasHoy', 'rentasSemana', 'rentasMes', 'rentasAtrasadas'
        ));
    }

    public function eventos(Request $request)
    {
        $inicio = $request->input('start');
        $fin = $request->input('end');

        $rentas = Renta::where(function ($query) use ($inicio, $fin) {
                $query->whereBetween('fecha_renta', [$inicio, $fin])
                    ->orWhereBetween('fecha_devolucion', [$inicio, $fin]);
            })
            ->with(['cliente', 'items.producto']);

        // ✅ Filtrar rentas devueltas si viene el parámetro ocultarDevueltos
        if ($request->boolean('ocultarDevueltos')) {
            $rentas = $rentas->where('estado', '!=', 'devuelto');
        }

        $rentas = $rentas->get();

        // 🔸 Actualiza el estado de cada renta ANTES de mostrarla en el calendario
        foreach ($rentas as $renta) {
            $renta->actualizarEstado();
        }

        $eventos = [];

        foreach ($rentas as $renta) {
            $color = match ($renta->estado) {
                'pendiente' => '#6c757d', // gris
                'parcial'   => '#ffc107', // amarillo
                'abonado'   => '#ffc107', // amarillo (si usas 'abonado')
                'pagado'    => '#28a745', // verde
                'devuelto'  => '#0dcaf0', // azul claro
                'atrasado'  => '#dc3545', // rojo
                default     => '#007bff'  // azul por defecto
            };

            $eventos[] = [
                'title' => $renta->cliente->nombre . ' - ' . optional($renta->items->first()?->producto)->nombre,
                'start' => $renta->fecha_renta->format('Y-m-d'),
                'end' => $renta->fecha_devolucion->copy()->addDay()->format('Y-m-d'),
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
