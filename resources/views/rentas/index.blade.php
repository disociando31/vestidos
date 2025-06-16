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
            <table class="table table-striped">
                <thead>
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
                        <td>
                            <a href="{{ route('rentas.mostrar', $renta) }}" class="btn btn-sm btn-info">Ver</a>
                            @if($renta->estado != 'devuelto')
                            <a href="{{ route('rentas.devolver', $renta) }}" class="btn btn-sm btn-success">Devolver</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $rentas->links() }}
    </div>
</div>
@endsection