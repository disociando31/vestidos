<?php

namespace App\Http\Controllers;

use App\Models\Renta;
use Barryvdh\DomPDF\Facade\Pdf;

class FacturaController extends Controller
{
    protected function generarPDF(Renta $renta)
    {
        $renta->load(['cliente', 'items.producto', 'pagos']);
        return Pdf::loadView('facturas.mostrar', compact('renta'));
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
