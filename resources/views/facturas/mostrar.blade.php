<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Factura #{{ $renta->id }}</title>
    <style>
        body { font-family: 'Georgia', serif; color: #111; background-color: #f9f9f9; margin: 0; padding: 0; }
        .container { width: 90%; margin: auto; padding: 20px; background-color: #fff; }
        header, footer { text-align: center; margin-bottom: 20px; }
        .logo { height: 60px; }
        .title { font-size: 28px; font-weight: bold; margin-top: 0; }
        .info, .payment-info { font-size: 14px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 8px 5px; border-bottom: 1px solid #ccc; }
        th { text-align: left; background-color: #f0f0f0; }
        .right { text-align: right; }
        .totals td { border: none; }
        .highlight { font-size: 16px; font-weight: bold; }
        .divider { border-top: 2px solid #000; margin: 40px 0; }
        .small { font-size: 12px; color: #666; }
        .admin-section { font-family: 'Courier New', monospace; font-size: 12px; background-color: #f5f5f5; padding: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <img src="{{ public_path('img/logo1.png') }}" alt="Logo" class="logo">
            <div class="title">FACTURA</div>
            <div class="small">Factura No. {{ $renta->id }} - {{ $renta->created_at->format('d/m/Y') }}</div>
        </header>

        <section class="info">
            <strong>CLIENTE:</strong><br>
            {{ $renta->cliente->nombre }}<br>
            {{ $renta->cliente->telefono }}<br>
            {{ $renta->cliente->direccion ?? '' }}
        </section>

        <table>
            <thead>
                <tr>
                    <th>Artículo</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th class="right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($renta->items as $item)
                <tr>
                    <td>
                        {{ $item->producto->nombre }}
                        @if($item->atributos)
                            <br><small>
                            @foreach($item->atributos as $nombre => $valor)
                                {{ $nombre }}: {{ $valor }}{{ !$loop->last ? ', ' : '' }}
                            @endforeach
                            </small>
                        @endif
                    </td>
                    <td>{{ $item->cantidad }}</td>
                    <td>${{ number_format($item->precio_unitario, 2) }}</td>
                    <td class="right">${{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals" style="margin-top: 10px; width: 40%; float: right;">
            <tr><td class="right">Subtotal:</td><td class="right">${{ number_format($renta->monto_total, 2) }}</td></tr>
            <tr><td class="right">Pagado:</td><td class="right">${{ number_format($renta->monto_pagado, 2) }}</td></tr>
            <tr><td class="right highlight">Saldo:</td><td class="right highlight">${{ number_format($renta->saldo, 2) }}</td></tr>
        </table>

        <div style="clear: both;"></div>

        <section class="payment-info" style="margin-top: 30px;">
            <strong> Fecha límite: {{ now()->addDays(10)->format('d/m/Y') }}</strong><br>
        </section>

        <footer>
            <div class="small">¡Muchas gracias por su preferencia!</div>
        </footer>

        {{-- ADMINISTRACIÓN --}}
        <div class="divider"></div>
        <div class="admin-section">
            <h3>FACTURA ADMINISTRATIVA</h3>
            <p><strong>Cliente:</strong> {{ $renta->cliente->nombre }} | <strong>Tel.:</strong> {{ $renta->cliente->telefono }}</p>
            <p><strong>Recibió:</strong> {{ $renta->recibido_por }}</p>
            <p><strong>Fechas:</strong> Renta {{ $renta->fecha_renta->format('d/m/Y') }} - Devolución {{ $renta->fecha_devolucion->format('d/m/Y') }}</p>

            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Atributos</th>
                        <th>Cant.</th>
                        <th>Precio U.</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($renta->items as $item)
                    <tr>
                        <td>{{ $item->producto->nombre }}</td>
                        <td>
                            @foreach($item->atributos as $nombre => $valor)
                                {{ $nombre }}: {{ $valor }}{{ !$loop->last ? ', ' : '' }}
                            @endforeach
                        </td>
                        <td>{{ $item->cantidad }}</td>
                        <td>${{ number_format($item->precio_unitario, 2) }}</td>
                        <td>${{ number_format($item->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <p class="small">Factura generada el {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>
</body>
</html>
