@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Crear Renta</h2>
    <form action="{{ route('rentas.store') }}" method="POST">
        @csrf

        <!-- Cliente -->
        <div class="mb-3">
            <label for="cliente_id" class="form-label">Cliente</label>
            <select name="cliente_id" id="cliente_id" class="form-select" required>
                @foreach($clientes as $cliente)
                    <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                @endforeach
            </select>
        </div>

        <!-- Producto -->
        <div class="mb-3">
            <label for="producto_id" class="form-label">Producto</label>
            <select name="producto_id" id="producto_id" class="form-select" required>
                @foreach($productos as $producto)
                    <option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
                @endforeach
            </select>
        </div>

        <!-- Fecha Inicio y Fin -->
        <div class="mb-3">
            <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
            <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="fecha_fin" class="form-label">Fecha de Fin</label>
            <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" required>
        </div>

        <!-- Campos Adicionales -->
        <h5 class="mt-4">Adicionales</h5>

        <div class="mb-3">
            <label for="camisa_color" class="form-label">Camisa (Color)</label>
            <input type="text" name="camisa_color" id="camisa_color" class="form-control">
        </div>

        <div class="mb-3">
            <label for="zapatos_color" class="form-label">Zapatos/Tacones (Color)</label>
            <input type="text" name="zapatos_color" id="zapatos_color" class="form-control">
        </div>

        <div class="mb-3">
            <label for="zapatos_talla" class="form-label">Zapatos/Tacones (Talla)</label>
            <input type="text" name="zapatos_talla" id="zapatos_talla" class="form-control">
        </div>

        <div class="mb-3">
            <label for="cartera_color" class="form-label">Cartera (Color)</label>
            <input type="text" name="cartera_color" id="cartera_color" class="form-control">
        </div>

        <div class="mb-3">
            <label for="otro_nombre" class="form-label">Otro Adicional (Nombre)</label>
            <input type="text" name="otro_nombre" id="otro_nombre" class="form-control">
        </div>

        <div class="mb-3">
            <label for="otro_precio" class="form-label">Otro Adicional (Precio)</label>
            <input type="number" step="0.01" name="otro_precio" id="otro_precio" class="form-control">
        </div>

        <!-- Total a calcularse desde el backend -->
        <button type="submit" class="btn btn-primary">Registrar Renta</button>
    </form>
</div>
@endsection
