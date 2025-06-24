<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Factura #{{ $rental->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .info { margin-bottom: 30px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f2f2f2; }
        .totals { float: right; width: 300px; }
        .footer { margin-top: 50px; font-size: 12px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Tienda de Alquiler de Vestidos</h1>
        <h2>Factura #{{ $rental->id }}</h2>
    </div>
    
    <div class="info">
        <p><strong>Cliente:</strong> {{ $rental->customer->name }}</p>
        <p><strong>Teléfono:</strong> {{ $rental->customer->phone }}</p>
        <p><strong>Fecha de Renta:</strong> {{ $rental->rental_date->format('d/m/Y') }}</p>
        <p><strong>Fecha de Devolución:</strong> {{ $rental->return_date->format('d/m/Y') }}</p>
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
            @foreach($rental->items as $item)
            <tr>
                <td>{{ $item->product->id }}</td>
                <td>{{ $item->product->name }}</td>
                <td>{{ number_format($item->unit_price, 2) }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->subtotal, 2) }}</td>
                <td>{{ number_format($item->iva, 2) }}</td>
                <td>{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="totals">
        <p><strong>Subtotal:</strong> {{ number_format($rental->total_amount - $rental->items->sum('iva'), 2) }}</p>
        <p><strong>IVA:</strong> {{ number_format($rental->items->sum('iva'), 2) }}</p>
        <p><strong>Total:</strong> {{ number_format($rental->total_amount, 2) }}</p>
        <p><strong>Pagado:</strong> {{ number_format($rental->paid_amount, 2) }}</p>
        <p><strong>Saldo:</strong> {{ number_format($rental->balance, 2) }}</p>
    </div>
    
    @if($rental->payments->count() > 0)
    <h3>Pagos realizados</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Método</th>
                <th>Monto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rental->payments as $payment)
            <tr>
                <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $payment->payment_method }}</td>
                <td>{{ number_format($payment->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    
    <div class="footer">
        <p>Gracias por su preferencia</p>
        <p>Tienda de Alquiler de Vestidos - {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>