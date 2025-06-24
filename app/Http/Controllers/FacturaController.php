<?php

namespace App\Http\Controllers;

use App\Models\Renta;
use Barryvdh\DomPDF\Facade\Pdf;

class FacturaController extends Controller
{
    protected function generarPDF(Renta $renta)
    {
        // Carga relaciones necesarias
        $renta->load(['cliente', 'items.producto', 'pagos']);

        // Generar colección de productos desde los items
        $productos = $renta->items->map(function ($item) {
            return $item->producto;
        });

        return Pdf::loadView('facturas.mostrar', [
            'renta'     => $renta,
            'productos' => $productos, // ← Aquí enviamos los productos ya listos
        ]);
    }

    public function mostrar(Renta $renta)
    {
        return $this->generarPDF($renta)->stream("factura-{$renta->id}.pdf");
    }

    public function descargar(Renta $renta)
    {
        return $this->generarPDF($renta)->download("factura-{$renta->id}.pdf");
    }
}
