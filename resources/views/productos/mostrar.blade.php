@extends('layouts.app')

@section('title', 'Detalle de Producto')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Detalle de Producto: {{ $producto->nombre }}</h5>
        <span class="badge bg-{{ $producto->estado == 'disponible' ? 'success' : ($producto->estado == 'rentado' ? 'warning' : 'secondary') }}">
            {{ ucfirst($producto->estado) }}
        </span>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Código:</strong> {{ $producto->codigo }}</p>
                <p><strong>Tipo:</strong> {{ ucfirst($producto->tipo) }}</p>
                <p><strong>Precio Renta:</strong> ${{ number_format($producto->precio_renta, 2) }}</p>
                
                <h5 class="mt-4">Atributos</h5>
                <ul>
                    @foreach($producto->atributos as $atributo)
                    <li><strong>{{ $atributo->nombre }}:</strong> {{ $atributo->valor }}</li>
                    @endforeach
                </ul>
                
                <p><strong>Descripción:</strong></p>
                <p>{{ $producto->descripcion }}</p>
            </div>
            
            <div class="col-md-6">
                <h5>Imágenes</h5>
                <div class="row">
                    @forelse($producto->imagenes as $imagen)
                    <div class="col-md-6 mb-3">
                        <img src="{{ asset('storage/' . $imagen->ruta) }}" class="img-fluid img-thumbnail">
                    </div>
                    @empty
                    <div class="col-12">
                        <p class="text-muted">No hay imágenes registradas</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <a href="{{ route('productos.index') }}" class="btn btn-secondary">Volver</a>
    </div>
</div>
@endsection