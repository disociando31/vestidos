@extends('layouts.app')

@section('title', 'Lista de Clientes')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Clientes Registrados</h5>
        <a href="{{ route('clientes.crear') }}" class="btn btn-primary">Nuevo Cliente</a>
    </div>
    <div class="card-body">
        <form method="GET" class="mb-3">
            <input type="text" name="busqueda" class="form-control" placeholder="Buscar por nombre, teléfono o email..." value="{{ request('busqueda') }}">
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
                    @foreach($clientes as $cliente)
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
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $clientes->withQueryString()->links() }}
    </div>
</div>
@endsection