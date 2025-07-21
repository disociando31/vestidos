@extends('layouts.app')

@section('title', 'Lista de Clientes')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Clientes Registrados</h5>
        <a href="{{ route('clientes.crear') }}" class="btn btn-primary">Nuevo Cliente</a>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('clientes.index') }}" class="mb-3">
            <div class="input-group">
                <input type="text" name="buscar" class="form-control" placeholder="Buscar cliente..." value="{{ request('buscar') }}">
                <button class="btn btn-primary" type="submit">Buscar</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Registro</th>
                        <th>Días Atraso</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientes as $cliente)
                    <tr>
                        <td>{{ $cliente->nombre }}</td>
                        <td>{{ $cliente->telefono }}</td>
                        <td>{{ $cliente->email ?? 'N/A' }}</td>
                        <td>{{ $cliente->fecha_registro?->format('d/m/Y') ?? 'N/A' }}</td>
                        <td>{{ $cliente->dias_atraso }}</td>
                        <td>
                            <a href="{{ route('clientes.mostrar', $cliente) }}" class="btn btn-sm btn-info">Ver</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">No se encontraron clientes</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $clientes->appends(request()->query())->links() }}
    </div>
</div>
@endsection