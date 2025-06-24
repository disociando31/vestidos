@extends('layouts.app')

@section('title', 'Lista de Rentas')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Historial de Rentas</h5>
        <a href="{{ route('rentas.crear') }}" class="btn btn-primary">Nueva Renta</a>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Fecha Renta</th>
                        <th>Fecha Devolución</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rentas as $renta)
                        <tr>
                            <td>{{ $renta->id }}</td>
                            <td>{{ $renta->cliente->nombre }}</td>
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
                            <td class="d-flex gap-1">
                                <a href="{{ route('rentas.mostrar', $renta) }}" class="btn btn-sm btn-info">Ver</a>
                                
                                @if($renta->estado != 'devuelto')
                                    <form action="{{ route('rentas.devolver', $renta) }}" method="POST" onsubmit="return confirm('¿Estás seguro de marcar esta renta como devuelta?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Devolver</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $rentas->links() }}
        </div>
    </div>
</div>
@endsection
