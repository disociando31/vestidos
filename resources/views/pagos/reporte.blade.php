@extends('layouts.app')

@section('title', 'Reporte de Pagos')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">📊 Reporte de Pagos</h5>
    </div>
    <div class="card-body">

        <form action="{{ route('pagos.reporte') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label">Desde</label>
                <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Hasta</label>
                <input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}" class="form-control">
            </div>
            <div class="col-md-3 align-self-end">
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="{{ route('pagos.reporte') }}" class="btn btn-secondary">Limpiar</a>
            </div>
        </form>

        <div class="mb-3">
            <strong>Total de pagos:</strong> ${{ number_format($totalPagos, 2) }}
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Monto</th>
                        <th>Método</th>
                        <th>Recibido por</th>
                        <th>Notas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pagos as $pago)
                    <tr>
                        <td>{{ $pago->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $pago->cliente->nombre ?? 'N/A' }}</td>
                        <td>${{ number_format($pago->monto, 2) }}</td>
                        <td>{{ ucfirst($pago->metodo_pago) }}</td>
                        <td>{{ $pago->recibido_por }}</td>
                        <td>{{ $pago->notas ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">No se encontraron pagos en el rango seleccionado.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $pagos->withQueryString()->links() }}
    </div>
</div>
@endsection
