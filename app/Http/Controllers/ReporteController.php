<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pago;
use Carbon\Carbon;

class ReporteController extends Controller
{
    // --- Reporte Diario de PAGOS ---
    public function diario(Request $request)
    {
        $hoy = $request->input('fecha', now()->toDateString());
        $pagos = Pago::with('renta.cliente')
            ->whereDate('created_at', $hoy)
            ->orderBy('created_at')
            ->get();

        $total = $pagos->sum('monto');

        $porMetodo = $pagos->groupBy('metodo_pago')->map(function ($items) {
            return $items->sum('monto');
        });

        return view('reportes.diario', compact('pagos', 'hoy', 'total', 'porMetodo'));
    }

    // --- Reporte Semanal de PAGOS (protegido) ---
    public function semanal(Request $request)
    {
        if (!$this->checkPassword($request)) {
            return view('reportes.password_form', ['action' => route('reportes.semanal'), 'error' => $request->isMethod('post')]);
        }

        $inicio = now()->startOfWeek();
        $fin = now()->endOfWeek();

        $pagos = Pago::with('renta.cliente')
            ->whereBetween('created_at', [$inicio, $fin])
            ->orderBy('created_at')
            ->get();

        $total = $pagos->sum('monto');

        $porMetodo = $pagos->groupBy('metodo_pago')->map(function ($items) {
            return $items->sum('monto');
        });

        return view('reportes.semanal', compact('pagos', 'inicio', 'fin', 'total', 'porMetodo'));
    }

    // --- Reporte Mensual de PAGOS (protegido) ---
    public function mensual(Request $request)
    {
        if (!$this->checkPassword($request)) {
            return view('reportes.password_form', ['action' => route('reportes.mensual'), 'error' => $request->isMethod('post')]);
        }

        $inicio = now()->startOfMonth();
        $fin = now()->endOfMonth();

        $pagos = Pago::with('renta.cliente')
            ->whereBetween('created_at', [$inicio, $fin])
            ->orderBy('created_at')
            ->get();

        $total = $pagos->sum('monto');

        $porMetodo = $pagos->groupBy('metodo_pago')->map(function ($items) {
            return $items->sum('monto');
        });

        return view('reportes.mensual', compact('pagos', 'inicio', 'fin', 'total', 'porMetodo'));
    }

    // --- Password check helper ---
    protected function checkPassword(Request $request)
    {
        if ($request->isMethod('post')) {
            $password = $request->input('password');
            if ($password === env('REPORTE_PASSWORD')) {
                session(['reporte_access_granted' => true]);
                return true;
            } else {
                return false;
            }
        }
        return session('reporte_access_granted', false);
    }

    // --- Salir de la protección de reportes ---
    public function salirProteccionReporte()
    {
        session()->forget('reporte_access_granted');
        return redirect()->route('calendario.index');
    }
}
