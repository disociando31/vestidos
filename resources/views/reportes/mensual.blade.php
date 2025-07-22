@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Reporte Mensual</h2>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Producto</th>
                <th>Camisa</th>
                <th>Zapatos</th>
                <th>Cartera</th>
                <th>Otro</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rentas as $renta)
                <tr>
                    <td>{{ $renta->fecha_inicio }}</td>
                    <td>{{ $renta->cliente->nombre }}</td>
                    <td>{{ $renta->producto->nombre }}</td>
                    <td>{{ $renta->item->camisa_color ?? '-' }}</td>
                    <td>
                        @if($renta->item->zapatos_color)
                            {{ $renta->item->zapatos_color }} ({{ $renta->item->zapatos_talla }})
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $renta->item->cartera_color ?? '-' }}</td>
                    <td>
                        @if($renta->item->otro_nombre)
                            {{ $renta->item->otro_nombre }} (${{ number_format($renta->item->otro_precio, 0) }})
                        @else
                            -
                        @endif
                    </td>
                    <td>${{ number_format($renta->total, 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
