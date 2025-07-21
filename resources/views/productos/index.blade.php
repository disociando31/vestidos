@extends('layouts.app')

@section('title', 'Lista de Productos')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Inventario de Productos</h5>
        <a href="{{ route('productos.crear') }}" class="btn btn-primary">Nuevo Producto</a>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('productos.index') }}" class="mb-3">
            <div class="input-group">
                <input type="text" name="buscar" class="form-control" placeholder="Buscar producto..." value="{{ request('buscar') }}">
                <button class="btn btn-primary" type="submit">Buscar</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Precio Renta</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productos as $producto)
                    <tr>
                        <td>{{ $producto->codigo }}</td>
                        <td>{{ $producto->nombre }}</td>
                        <td>{{ ucfirst($producto->tipo) }}</td>
                        <td>${{ number_format($producto->precio_renta, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $producto->estado == 'disponible' ? 'success' : ($producto->estado == 'rentado' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($producto->estado) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('productos.mostrar', $producto) }}" class="btn btn-sm btn-info">Ver</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">No se encontraron productos</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $productos->appends(request()->query())->links() }}
    </div>
</div>
@endsection