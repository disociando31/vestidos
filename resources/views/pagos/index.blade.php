@extends('layouts.app')

@section('title', 'Pagos Registrados')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">📑 Lista de Pagos</h5>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Volver al Panel</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Renta</th>
                        <th>Monto</th>
                        <th>Método</th>
                        <th>Recibido por</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pagos as $pago)
                    <tr>
                        <td>{{ $pago->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $pago->renta->cliente->nombre }}</td>
                        <td>#{{ $pago->renta->id }}</td>
                        <td>${{ number_format($pago->monto, 2) }}</td>
                        <td>{{ ucfirst($pago->metodo_pago) }}</td>
                        <td>{{ $pago->recibido_por }}</td>
                        <td>
                            <a href="{{ route('pagos.show', $pago) }}" class="btn btn-sm btn-info">Ver</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $pagos->links() }}
    </div>
</div>
@endsection
