@extends('layouts.app')

@section('title', 'Detalle de Cliente')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Cliente: {{ $cliente->nombre }}</h5>
        <span class="badge bg-{{ $cliente->dias_atraso > 0 ? 'danger' : 'success' }}">
            {{ $cliente->dias_atraso }} días de atraso
        </span>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <p><strong>Teléfono:</strong> {{ $cliente->telefono }}</p>
                <p><strong>Email:</strong> {{ $cliente->email ?? 'N/A' }}</p>
                <p><strong>Dirección:</strong> {{ $cliente->direccion ?? 'N/A' }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Fecha de Registro:</strong> {{ $cliente->fecha_registro?->format('d/m/Y') ?? 'N/A' }}</p>
                <p><strong>Total Rentas:</strong> {{ $cliente->rentas->count() }}</p>
            </div>
        </div>

        <h5 class="mb-3">Historial de Rentas</h5>
        @if($cliente->rentas->count() > 0)
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha Renta</th>
                        <th>Fecha Devolución</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cliente->rentas as $renta)
                    <tr>
                        <td>{{ $renta->id }}</td>
                        <td>{{ $renta->fecha_renta->format('d/m/Y') }}</td>
                        <td>{{ $renta->fecha_devolucion->format('d/m/Y') }}</td>
                        <td>${{ number_format($renta->monto_total, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ 
                                $renta->estado == 'pendiente' ? 'secondary' : 
                                ($renta->estado == 'parcial' ? 'warning' : 
                                ($renta->estado == 'pagado' ? 'success' : 
                                ($renta->estado == 'devuelto' ? 'info' : 'danger'))) 
                            }}">
                                {{ ucfirst($renta->estado) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('rentas.mostrar', $renta) }}" class="btn btn-sm btn-info">Ver</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-muted">Este cliente no tiene rentas registradas</p>
        @endif

        <div class="mt-4">
            <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </div>
</div>
@endsection