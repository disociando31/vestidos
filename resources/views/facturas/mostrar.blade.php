<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Factura #{{ $renta->id }}</title>
    <style>
        body { font-family: 'Georgia', serif; color: #111; background-color: #f9f9f9; margin: 0; padding: 0; font-size: 13px; }
        .container { width: 95%; margin: auto; padding: 10px; background-color: #fff; }
        header, footer { text-align: center; margin-bottom: 10px; }
        .logo { height: 50px; }
        .title { font-size: 22px; font-weight: bold; margin: 5px 0; }
        .info, .payment-info { font-size: 13px; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; page-break-inside: avoid; }
        th, td { padding: 4px; border-bottom: 1px solid #ccc; vertical-align: top; }
        th { text-align: left; background-color: #f0f0f0; }
        .right { text-align: right; }
        .totals td { border: none; }
        .highlight { font-weight: bold; }
        .divider { border-top: 1px dashed #000; margin: 20px 0; }
        .small { font-size: 11px; color: #666; }
        .admin-section { font-family: monospace; font-size: 12px; background-color: #f5f5f5; padding: 8px; margin-top: 15px; }
        .manual-note-box {
            border: 1px dashed #999;
            height: 60px;
            margin-top: 10px;
            background-color: #fff;
        }
        .manual-note-label { font-weight: bold; font-size: 12px; margin-top: 8px; }

        @media print {
            body { margin: 0; }
            .container { padding: 0; }
            .manual-note-box { height: 80px; }
        }
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
        <strong>CLIENTE:</strong> {{ $renta->cliente->nombre }}<br>
        Tel: {{ $renta->cliente->telefono }}<br>
        {{ $renta->cliente->direccion ?? '' }}
    </section>

    <table>
        <thead>
            <tr>
                <th>Artículo</th>
                <th>Cant.</th>
                <th>Precio U.</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
        @foreach($renta->items as $item)
            <tr>
                <td>
                    {{ $item->producto->nombre }}
                    @php
                        $atributos = is_string($item->atributos) ? json_decode($item->atributos, true) : $item->atributos;
                    @endphp
                    @if(!empty($atributos))
                        <br><small>
                            @foreach($atributos as $nombre => $valor)
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

    @if(isset($renta->adicionales) && count($renta->adicionales))
    <h4 style="margin-top: 15px;">Adicionales</h4>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Color</th>
                <th>Talla</th>
                <th class="right">Precio</th>
            </tr>
        </thead>
        <tbody>
            @foreach($renta->adicionales as $adicional)
            <tr>
                <td>{{ $adicional['nombre'] ?? '-' }}</td>
                <td>{{ $adicional['color'] ?? '-' }}</td>
                <td>{{ $adicional['talla'] ?? '-' }}</td>
                <td class="right">
                    @if(isset($adicional['precio']) && $adicional['precio'] > 0)
                        ${{ number_format($adicional['precio'], 2) }}
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <table class="totals" style="margin-top: 10px; width: 40%; float: right;">
        <tr><td class="right">Subtotal:</td><td class="right">${{ number_format($renta->monto_total, 2) }}</td></tr>
        <tr><td class="right">Pagado:</td><td class="right">${{ number_format($renta->pagos->sum('monto'), 2) }}</td></tr>
        <tr><td class="right highlight">Saldo:</td><td class="right highlight">${{ number_format($renta->monto_total - $renta->pagos->sum('monto'), 2) }}</td></tr>
    </table>

    <div style="clear: both;"></div>

    <section class="payment-info">
        <strong>Fecha límite:</strong> {{ now()->addDays(4)->format('d/m/Y') }}
    </section>

    <footer>
        <div class="small">¡Gracias por su preferencia!</div>
    </footer>

{{-- ADMINISTRACIÓN --}}
<div class="divider"></div>
<div class="admin-section">
    <h4>FACTURA ADMINISTRATIVA</h4>
    <p><strong>Cliente:</strong> {{ $renta->cliente->nombre }} | <strong>Tel:</strong> {{ $renta->cliente->telefono }}</p>
    <p><strong>Recibió:</strong> {{ $renta->recibido_por }} | <strong>Fechas:</strong> {{ $renta->fecha_renta->format('d/m/Y') }} - {{ $renta->fecha_devolucion->format('d/m/Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cant.</th>
                <th>Precio U.</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($renta->items as $item)
            <tr>
                <td>
                    {{ $item->producto->nombre }}
                    @php
                        $atributos = is_string($item->atributos) ? json_decode($item->atributos, true) : $item->atributos;
                    @endphp
                    @if(!empty($atributos))
                        <br><small>
                            @foreach($atributos as $nombre => $valor)
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

    @if(isset($renta->adicionales) && count($renta->adicionales))
    <h5>Adicionales</h5>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Color</th>
                <th>Talla</th>
                <th class="right">Precio</th>
            </tr>
        </thead>
        <tbody>
            @foreach($renta->adicionales as $adicional)
            <tr>
                <td>{{ $adicional['nombre'] ?? '-' }}</td>
                <td>{{ $adicional['color'] ?? '-' }}</td>
                <td>{{ $adicional['talla'] ?? '-' }}</td>
                <td class="right">
                    @if(isset($adicional['precio']) && $adicional['precio'] > 0)
                        ${{ number_format($adicional['precio'], 2) }}
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- Totales administrativos --}}
    <table style="width: 40%; float: right; margin-top: 10px;">
        <tr><td class="right">Total:</td><td class="right">${{ number_format($renta->monto_total, 2) }}</td></tr>
        <tr><td class="right">Pagado:</td><td class="right">${{ number_format($renta->pagos->sum('monto'), 2) }}</td></tr>
        <tr><td class="right highlight">Saldo:</td><td class="right highlight">${{ number_format($renta->monto_total - $renta->pagos->sum('monto'), 2) }}</td></tr>
    </table>

    <div style="clear: both;"></div>

    <p class="small">Factura generada el {{ now()->format('d/m/Y H:i') }}</p>
    <div class="manual-note-label">Anotaciones internas:</div>
    <div class="manual-note-box"></div>
</div>
</div>
</body>
</html>
