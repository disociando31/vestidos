@extends('layouts.app')

@section('title', 'Crear Producto')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Registrar Nuevo Producto</h5>
    </div>
    <div class="card-body">
        @if ($errors->any())
    <div class="alert alert-danger">
        <strong>¡Error!</strong>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@if (session('exito'))
    <div class="alert alert-success">
        {{ session('exito') }}
    </div>
@endif
        <form action="{{ route('productos.guardar') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            {{-- Tipo de producto --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Tipo</label>
                    <select name="tipo" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="traje">Traje Caballero</option>
                        <option value="vestido">Vestido</option>
                        <option value="vestido_15">Vestido 15 Años</option>
                        <option value="bodas">Bodas</option>
                        <option value="primeras_comuniones">Primeras Comuniones</option>
                    </select>
                </div>
            </div>

            {{-- Nombre --}}
            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>

            {{-- Descripción --}}
            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3" required></textarea>
            </div>

            {{-- Precio de renta --}}
            <div class="mb-3">
                <label class="form-label">Precio de Renta</label>
                <input type="number" name="precio_renta" step="0.01" min="0" class="form-control" required>
            </div>

            {{-- Atributos dinámicos --}}
            <div id="atributos-container" class="mb-3">
                <!-- Atributos dinámicos se agregarán aquí -->
            </div>

            <button type="button" id="agregar-atributo" class="btn btn-sm btn-secondary mb-3">
                Agregar Atributo
            </button>

            {{-- Imágenes --}}
            <div class="mb-3">
                <label class="form-label">Imágenes</label>
                <input type="file" name="imagenes[]" class="form-control" multiple accept="image/*">
            </div>

            {{-- Botón Guardar --}}
            <div class="text-end">
                <button type="submit" class="btn btn-primary">Guardar Producto</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('agregar-atributo').addEventListener('click', function() {
    const container = document.getElementById('atributos-container');
    const newAttr = document.createElement('div');
    newAttr.className = 'row mb-2 atributo';
    newAttr.innerHTML = `
        <div class="col-md-5">
            <input type="text" name="atributo_nombre[]" class="form-control" placeholder="Nombre del atributo" required>
        </div>
        <div class="col-md-5">
            <input type="text" name="atributo_valor[]" class="form-control" placeholder="Valor" required>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-sm btn-danger remover-atributo">X</button>
        </div>
    `;
    container.appendChild(newAttr);
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remover-atributo')) {
        e.target.closest('.atributo').remove();
    }
});
</script>
@endpush
@endsection
