@extends('layouts.app')

@section('title', 'Editar Cliente')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Editar Cliente: {{ $cliente->nombre }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('clientes.actualizar', $cliente) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label class="form-label">Nombre Completo</label>
                <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $cliente->nombre) }}" required>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $cliente->telefono) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $cliente->email) }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Dirección</label>
                <textarea name="direccion" class="form-control" rows="2">{{ old('direccion', $cliente->direccion) }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Fecha de Registro</label>
                <input type="date" name="fecha_registro" class="form-control" 
                    value="{{ old('fecha_registro', optional($cliente->fecha_registro)->format('Y-m-d')) }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Días de Atraso</label>
                <input type="number" name="dias_atraso" class="form-control" 
                    value="{{ old('dias_atraso', $cliente->dias_atraso) }}" min="0">
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Actualizar Cliente</button>
                <a href="{{ route('clientes.mostrar', $cliente) }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection