<?php

namespace App\Http\Controllers;

use App\Models\Renta;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PagoController extends Controller
{
    public function index(Request $request)
{
    $pagos = Pago::with(['renta.cliente'])
        ->orderByDesc('created_at')
        ->paginate(15);

    return view('pagos.index', compact('pagos'));
}
public function reporte(Request $request)
{
    $query = Pago::with(['renta.cliente']);

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
    $pago->load('cliente');
    return view('pagos.show', compact('pago'));
}
    public function store(Request $request, Renta $renta)
    {
        $validado = $request->validate([
            'cantidad' => 'required|numeric|min:1|max:' . ($renta->monto_total - $renta->monto_pagado),
            'metodo_pago' => 'required|string',
            'notas' => 'nullable|string',
            'recibido_por' => 'required|string|max:100'
        ]);
        
        DB::transaction(function () use ($validado, $renta) {
            $pago = Pago::create([
                'renta_id' => $renta->id,
                'monto' => $validado['cantidad'],
                'metodo_pago' => $validado['metodo_pago'],
                'notas' => $validado['notas'] ?? null,
                'recibido_por' => $validado['recibido_por']
            ]);
            
            $renta->increment('monto_pagado', $validado['cantidad']);
            
            if ($renta->monto_pagado >= $renta->monto_total) {
                $renta->estado = 'pagado';
                $renta->save();
            }
        });
        
        return back()->with('exito', 'Pago registrado exitosamente');
    }
}