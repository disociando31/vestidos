<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Factura #{{ $renta->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .info { margin-bottom: 30px; }
        .logo { max-width: 150px; margin-bottom: 10px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f2f2f2; }
        .totals { float: right; width: 300px; margin-top: 20px; }
        .footer { margin-top: 50px; font-size: 10px; text-align: center; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        @if(file_exists(public_path('images/logo.png')))
            <img src="{{ public_path('images/logo.png') }}" class="logo">
        @endif
        <h1>Alquiler de Vestidos Elegance</h1>
        <h2>Factura #{{ $renta->id }}</h2>
    </div>
    
    <div class="info">
        <p><strong>Cliente:</strong> {{ $renta->cliente->nombre }}</p>
        <p><strong>Teléfono:</strong> {{ $renta->cliente->telefono }}</p>
        <p><strong>Fecha de Renta:</strong> {{ $renta->fecha_renta->format('d/m/Y') }}</p>
        <p><strong>Fecha de Devolución:</strong> {{ $renta->fecha_devolucion->format('d/m/Y') }}</p>
        <p><strong>Atendió:</strong> {{ $renta->recibido_por }}</p>
    </div>
    
    <table class="table">
        <thead>
            <tr>
                <th>Código</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
                <th>IVA</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($renta->items as $item)
            <tr>
                <td>{{ $item->producto->codigo }}</td>
                <td>
                    {{ $item->producto->nombre }}
                    @if($item->atributos)
                        <br><small>
                        @foreach($item->atributos as $nombre => $valor)
                            {{ $nombre }}: {{ $valor }}@if(!$loop->last), @endif
                        @endforeach
                        </small>
                    @endif
                </td>
                <td>${{ number_format($item->precio_unitario, 2) }}</td>
                <td>{{ $item->cantidad }}</td>
                <td>${{ number_format($item->subtotal, 2) }}</td>
                <td>${{ number_format($item->iva, 2) }}</td>
                <td>${{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="totals">
        <p><strong>Subtotal:</strong> ${{ number_format($renta->monto_total - $renta->items->sum('iva'), 2) }}</p>
        <p><strong>IVA (16%):</strong> ${{ number_format($renta->items->sum('iva'), 2) }}</p>
        <p><strong>Total:</strong> ${{ number_format($renta->monto_total, 2) }}</p>
        <p><strong>Pagado:</strong> ${{ number_format($renta->monto_pagado, 2) }}</p>
        <p><strong>Saldo:</strong> ${{ number_format($renta->saldo, 2) }}</p>
    </div>
    
    @if($renta->pagos->count() > 0)
    <h3>Pagos realizados</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Método</th>
                <th>Monto</th>
                <th>Recibió</th>
            </tr>
        </thead>
        <tbody>
            @foreach($renta->pagos as $pago)
            <tr>
                <td>{{ $pago->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ ucfirst($pago->metodo_pago) }}</td>
                <td>${{ number_format($pago->monto, 2) }}</td>
                <td>{{ $pago->recibido_por }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    
    <div class="footer">
        <p>Gracias por su preferencia</p>
        <p>Tienda de Alquiler de Vestidos - {{ now()->format('d/m/Y H:i') }}</p>
        <p>Estado de la renta: {{ ucfirst($renta->estado) }}</p>
    </div>
</body>
</html>