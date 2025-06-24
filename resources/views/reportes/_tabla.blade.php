@if($rentas->count())
    <table class="table table-bordered">
        <thead>
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
            @foreach ($rentas as $renta)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $renta->cliente->nombre }}</td>
                    <td>
                        @foreach($renta->productos as $producto)
                            {{ $producto->nombre }}<br>
                        @endforeach
                    </td>
                    <td>{{ $renta->fecha_evento }}</td>
                    <td>{{ $renta->regreso_sucursal }}</td>
                    <td>${{ number_format($renta->total, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <div class="alert alert-info">No hay rentas registradas para este período.</div>
@endif
