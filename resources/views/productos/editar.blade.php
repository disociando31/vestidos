@extends('layouts.app')

@section('title', 'Editar Producto')

@section('content')
<div class="container">
    <h2>Editar Producto</h2>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('productos.actualizar', $producto->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Tipo --}}
        <div class="mb-3">
            <label for="tipo" class="form-label">Tipo</label>
            <select name="tipo" id="tipo" class="form-select" required>
                @foreach(['traje' => 'Traje', 'vestido' => 'Vestido', 'vestido_15' => 'Vestido de 15 años', 'bodas' => 'Bodas', 'primeras_comuniones' => 'Primeras Comuniones'] as $value => $label)
                    <option value="{{ $value }}" {{ $producto->tipo === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        {{-- Nombre --}}
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" name="nombre" id="nombre" class="form-control"
                   value="{{ old('nombre', $producto->nombre) }}" required>
        </div>

        {{-- Descripción --}}
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea name="descripcion" id="descripcion" rows="3" class="form-control" required>{{ old('descripcion', $producto->descripcion) }}</textarea>
        </div>

        {{-- Precio --}}
        <div class="mb-3">
            <label for="precio_renta" class="form-label">Precio de Renta</label>
            <input type="number" name="precio_renta" id="precio_renta" class="form-control"
                   value="{{ old('precio_renta', $producto->precio_renta) }}" step="0.01" min="0" required>
        </div>

        {{-- Atributos --}}
        <div class="mb-3">
            <label class="form-label">Atributos</label>
            <div id="atributos">
                @foreach($producto->atributos as $index => $atributo)
                    <div class="row mb-2 atributo-item">
                        <div class="col">
                            <input type="text" name="atributo_nombre[]" class="form-control" placeholder="Nombre"
                                   value="{{ old("atributo_nombre.$index", $atributo->nombre) }}" required>
                        </div>
                        <div class="col">
                            <input type="text" name="atributo_valor[]" class="form-control" placeholder="Valor"
                                   value="{{ old("atributo_valor.$index", $atributo->valor) }}" required>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-danger btn-remove-atributo">&times;</button>
                        </div>
                    </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-agregar-atributo">+ Agregar Atributo</button>
        </div>

        {{-- Imágenes actuales --}}
        <div class="mb-3">
            <label class="form-label">Imágenes actuales</label>
            <div class="d-flex flex-wrap gap-2">
                @foreach($producto->imagenes as $imagen)
                    <img src="{{ asset('storage/' . $imagen->ruta) }}" alt="Imagen" class="img-thumbnail" style="max-height: 100px;">
                @endforeach
            </div>
        </div>

        {{-- Nuevas imágenes --}}
        <div class="mb-3">
            <label for="imagenes" class="form-label">Agregar nuevas imágenes</label>
            <input type="file" name="imagenes[]" id="imagenes" class="form-control" multiple accept="image/*">
        </div>

        {{-- Botones --}}
        <button type="submit" class="btn btn-primary">Actualizar Producto</button>
        <a href="{{ route('productos.mostrar', $producto) }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

{{-- Script para atributos dinámicos --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const container = document.getElementById('atributos');
        const addBtn = document.getElementById('btn-agregar-atributo');

        addBtn.addEventListener('click', () => {
            const div = document.createElement('div');
            div.className = 'row mb-2 atributo-item';
            div.innerHTML = `
                <div class="col">
                    <input type="text" name="atributo_nombre[]" class="form-control" placeholder="Nombre" required>
                </div>
                <div class="col">
                    <input type="text" name="atributo_valor[]" class="form-control" placeholder="Valor" required>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-danger btn-remove-atributo">&times;</button>
                </div>
            `;
            container.appendChild(div);
        });

        container.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-remove-atributo')) {
                e.target.closest('.atributo-item').remove();
            }
        });
    });
</script>
@endsection
