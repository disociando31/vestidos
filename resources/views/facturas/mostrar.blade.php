<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Factura #{{ $renta->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        .header, .footer { text-align: center; margin-bottom: 20px; }
        .logo { max-width: 140px; margin-bottom: 5px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th, .table td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        .table th { background-color: #f5f5f5; }
        .totals { margin-top: 15px; width: 40%; float: right; }
        .totals td { padding: 4px 8px; }
        .small { font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        @if(file_exists(public_path('images/logo.png')))
            <img src="{{ public_path('images/logo.png') }}" class="logo">
        @endif
        <h2>Alquiler de Vestidos Elegance</h2>
        <h3>Factura #{{ $renta->id }}</h3>
    </div>

    <p><strong>Cliente:</strong> {{ $renta->cliente->nombre }} | <strong>Tel:</strong> {{ $renta->cliente->telefono }}</p>
    <p><strong>Renta:</strong> {{ $renta->fecha_renta->format('d/m/Y') }} - <strong>Devolución:</strong> {{ $renta->fecha_devolucion->format('d/m/Y') }}</p>
    <p><strong>Recibido por:</strong> {{ $renta->recibido_por }}</p>

    <table class="table">
        <thead>
            <tr>
                <th>Código</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Cant</th>
                <th>Subtotal</th>
                <th>IVA</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($renta->items as $item)
                <tr>
                    <td>{{ $item->producto->codigo }}</td>
                    <td>{{ $item->producto->nombre }}
                        @if($item->atributos)
                            <small><br>
                                @foreach($item->atributos as $nombre => $valor)
                                    {{ $nombre }}: {{ $valor }}{{ !$loop->last ? ', ' : '' }}
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

    <table class="totals">
        <tr><td><strong>Subtotal:</strong></td><td>${{ number_format($renta->monto_total - $renta->items->sum('iva'), 2) }}</td></tr>
        <tr><td><strong>IVA (16%):</strong></td><td>${{ number_format($renta->items->sum('iva'), 2) }}</td></tr>
        <tr><td><strong>Total:</strong></td><td>${{ number_format($renta->monto_total, 2) }}</td></tr>
        <tr><td><strong>Pagado:</strong></td><td>${{ number_format($renta->monto_pagado, 2) }}</td></tr>
        <tr><td><strong>Saldo:</strong></td><td>${{ number_format($renta->saldo, 2) }}</td></tr>
    </table>

    @if($renta->pagos->count())
        <h4>Pagos realizados</h4>
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

    <div class="footer small">
        <p>Gracias por su preferencia | {{ now()->format('d/m/Y H:i') }}</p>
        <p>Estado: {{ ucfirst($renta->estado) }}</p>
    </div>
</body>
</html>
