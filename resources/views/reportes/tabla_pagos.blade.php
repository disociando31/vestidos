{{-- resources/views/reportes/tabla_pagos.blade.php --}}
<table class="table table-bordered table-striped">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>Fecha/Hora</th>
            <th>Cliente</th>
            <th>Renta</th>
            <th>Monto</th>
            <th>Método</th>
            <th>Recibido por</th>
            <th>Notas</th>
        </tr>
    </thead>
    <tbody>
        @php $i = 1; $granTotal = 0; @endphp
        @forelse($pagos as $pago)
            @php $granTotal += $pago->monto; @endphp
            <tr>
                <td>{{ $i++ }}</td>
                <td>{{ $pago->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $pago->renta->cliente->nombre ?? '-' }}</td>
                <td>#{{ $pago->renta->id }}</td>
                <td>${{ number_format($pago->monto, 2) }}</td>
                <td>{{ ucfirst($pago->metodo_pago) }}</td>
                <td>{{ $pago->recibido_por }}</td>
                <td>{{ $pago->notas }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center text-muted">No hay pagos registrados.</td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4" class="text-end">Total recibido:</th>
            <th colspan="4">${{ number_format($granTotal, 2) }}</th>
        </tr>
    </tfoot>
</table>
