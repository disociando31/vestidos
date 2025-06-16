<?php

namespace App\Http\Controllers;

use App\Models\Renta;
use Barryvdh\DomPDF\Facade\Pdf; // Importación correcta del facade

class FacturaController extends Controller
{
    public function mostrar(Renta $renta)
    {
        $renta->load(['cliente', 'items.producto', 'pagos']); // Corregido 'pages' a 'pagos'
        
        $pdf = Pdf::loadView('facturas.mostrar', compact('renta'));
        
        return $pdf->stream("factura-{$renta->id}.pdf"); // Corregida la interpolación y comillas
    }

    public function descargar(Renta $renta)
    {
        $renta->load(['cliente', 'items.producto', 'pagos']); // Corregido 'pages' a 'pagos'
        
        $pdf = Pdf::loadView('facturas.mostrar', compact('renta'));
        
        return $pdf->download("factura-{$renta->id}.pdf"); // Corregida la interpolación y comillas
    }

    
    public function imprimir(Renta $renta)
    {
        $renta->load(['cliente', 'items.producto', 'pagos']); // Corregido 'pages' a 'pagos'
        
        $pdf = Pdf::loadView('facturas.mostrar', compact('renta'));
        
        return $pdf->stream("factura-{$renta->id}.pdf"); // Corregida la interpolación y comillas
    }

}
