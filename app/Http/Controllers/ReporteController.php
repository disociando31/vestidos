<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Renta;

class ReporteController extends Controller
{
    public function diario()
    {
        $hoy = now()->toDateString();
        $rentas = Renta::with(['cliente', 'items.producto'])->whereDate('fecha_renta', $hoy)->get();
        return view('reportes.diario', compact('rentas', 'hoy'));
    }

    public function semanal()
    {
        $inicio = now()->startOfWeek();
        $fin = now()->endOfWeek();
        $rentas = Renta::with(['cliente', 'items.producto'])->whereBetween('fecha_renta', [$inicio, $fin])->get();
        return view('reportes.semanal', compact('rentas', 'inicio', 'fin'));
    }

    public function mensual()
    {
        $inicio = now()->startOfMonth();
        $fin = now()->endOfMonth();
        $rentas = Renta::with(['cliente', 'items.producto'])->whereBetween('fecha_renta', [$inicio, $fin])->get();
        return view('reportes.mensual', compact('rentas', 'inicio', 'fin'));
    }
}
