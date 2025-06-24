<?php

namespace App\Http\Controllers;

use App\Models\Renta;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PagoController extends Controller
{
    public function index()
    {
        $pagos = Pago::with('renta.cliente')->orderByDesc('created_at')->paginate(15);
        return view('pagos.index', compact('pagos'));
    }

    public function reporte(Request $request)
    {
        $query = Pago::with('renta.cliente');

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        $pagos = $query->orderByDesc('created_at')->paginate(15);
        $totalPagos = $query->sum('monto');

        return view('pagos.reporte', compact('pagos', 'totalPagos'));
    }

    public function show(Pago $pago)
    {
        $pago->load('renta.cliente');
        return view('pagos.show', compact('pago'));
    }

    public function store(Request $request, Renta $renta)
    {
        $validator = Validator::make($request->all(), [
            'monto' => 'required|numeric|min:0.01|max:' . ($renta->saldo),
            'metodo_pago' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
            'recibido_por' => 'required|string|max:100'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validado = $validator->validated();

        DB::transaction(function () use ($validado, $renta) {
            Pago::create([
                'renta_id' => $renta->id,
                'monto' => $validado['monto'],
                'metodo_pago' => $validado['metodo_pago'],
                'fecha_pago' => now(),
                'descripcion' => $validado['descripcion'] ?? null,
                'recibido_por' => $validado['recibido_por']
            ]);
            
            $renta->increment('monto_pagado', $validado['monto']);
            $renta->actualizarEstado();
        });
        $renta->refresh();
        return back()->with('exito', 'Abono registrado correctamente.');
    }
}
