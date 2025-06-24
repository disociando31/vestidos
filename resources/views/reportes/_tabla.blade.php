<table class="table table-bordered table-striped">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>Cliente</th>
            <th>Producto</th>
            <th>Fecha Evento</th>
            <th>Fecha Regreso</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @php $i = 1; $granTotal = 0; @endphp
        @forelse($rentas as $renta)
            @foreach($renta->items as $item)
                @php
                    $subtotal = $item->cantidad * $item->precio_unitario;
                    $granTotal += $subtotal;
                @endphp
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ $renta->cliente->nombre }}</td>
                    <td>{{ $item->producto->nombre }}</td>
                    <td>{{ \Carbon\Carbon::parse($renta->fecha_renta)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($renta->fecha_devolucion)->format('d/m/Y') }}</td>
                    <td>${{ number_format($subtotal, 2) }}</td>
                </tr>
            @endforeach
        @empty
            <tr>
                <td colspan="6" class="text-center">No hay rentas registradas en este período.</td>
            </tr>
        @endforelse
    </tbody>
    @if($granTotal > 0)
    <tfoot>
        <tr>
            <th colspan="5" class="text-end">Total General:</th>
            <th>${{ number_format($granTotal, 2) }}</th>
        </tr>
    </tfoot>
    @endif
</table>
