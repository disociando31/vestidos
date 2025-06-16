@extends('layouts.app')

@section('title', 'Crear Producto')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Registrar Nuevo Producto</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('productos.guardar') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Tipo</label>
                    <select name="tipo" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <option value="traje">Traje Caballero</option>
                        <option value="vestido">Vestido</option>
                        <option value="vestido_15">Vestido 15 Años</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Código</label>
                    <input type="text" name="codigo" class="form-control" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3" required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Precio de Renta</label>
                <input type="number" name="precio_renta" step="0.01" min="0" class="form-control" required>
            </div>

            <div id="atributos-container">
                <!-- Atributos dinámicos se agregarán aquí -->
            </div>

            <button type="button" id="agregar-atributo" class="btn btn-sm btn-secondary mb-3">
                Agregar Atributo
            </button>

            <div class="mb-3">
                <label class="form-label">Imágenes</label>
                <input type="file" name="imagenes[]" class="form-control" multiple accept="image/*">
            </div>

            <button type="submit" class="btn btn-primary">Guardar Producto</button>
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
            <input type="text" name="atributos[nombre][]" class="form-control" placeholder="Nombre del atributo" required>
        </div>
        <div class="col-md-5">
            <input type="text" name="atributos[valor][]" class="form-control" placeholder="Valor" required>
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