@extends('layouts.app')

@section('title', 'Crear Renta')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Registrar Nueva Renta</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('rentas.guardar') }}" method="POST">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Cliente</label>
                    <select name="cliente_id" class="form-select" required>
                        <option value="">Seleccionar cliente...</option>
                        @foreach($clientes as $cliente)
                        <option value="{{ $cliente->id }}">{{ $cliente->nombre }} - {{ $cliente->telefono }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Recibió</label>
                    <input type="text" name="recibido_por" class="form-control" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Fecha de Renta</label>
                    <input type="date" name="fecha_renta" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Fecha de Devolución</label>
                    <input type="date" name="fecha_devolucion" class="form-control" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Tipo de Gancho</label>
                <input type="text" name="tipo_gancho" class="form-control">
            </div>

            <h5 class="mt-4">Productos a Rentar</h5>
            <div id="productos-container">
                <!-- Productos dinámicos se agregarán aquí -->
            </div>

            <button type="button" id="agregar-producto" class="btn btn-sm btn-secondary mb-3">
                Agregar Producto
            </button>

            <div class="mb-3">
                <label class="form-label">Notas</label>
                <textarea name="notas" class="form-control" rows="2"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Guardar Renta</button>
        </form>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
// Select2 para búsqueda de productos
$(document).ready(function() {
    $('.select-producto').select2({
        placeholder: "Buscar producto...",
        allowClear: true
    });
});

// Contador para IDs únicos
let productoCounter = 0;

document.getElementById('agregar-producto').addEventListener('click', function() {
    const container = document.getElementById('productos-container');
    const newId = productoCounter++;
    
    const newProduct = document.createElement('div');
    newProduct.className = 'row mb-3 producto-item';
    newProduct.innerHTML = `
        <div class="col-md-6">
            <label class="form-label">Producto</label>
            <select name="items[${newId}][producto_id]" class="form-control select-producto" required>
                <option value="">Seleccionar producto...</option>
                @foreach($productos as $producto)
                <option 
                    value="{{ $producto->id }}" 
                    data-precio="{{ $producto->precio_renta }}"
                    data-atributos="{{ $producto->atributos }}"
                    {{ $producto->estado != 'disponible' ? 'disabled' : '' }}>
                    {{ $producto->nombre }} - ${{ number_format($producto->precio_renta, 2) }}
                    {{ $producto->estado != 'disponible' ? '(No disponible)' : '' }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Cantidad</label>
            <input type="number" name="items[${newId}][cantidad]" min="1" class="form-control cantidad" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Subtotal</label>
            <input type="text" class="form-control subtotal" readonly>
        </div>
        <div class="col-md-1 d-flex align-items-end">
            <button type="button" class="btn btn-sm btn-danger remover-producto">X</button>
        </div>
        <div class="col-12 atributos-container mt-2"></div>
    `;
    
    container.appendChild(newProduct);
    
    // Inicializar Select2 para el nuevo select
    $(newProduct.querySelector('.select-producto')).select2();
    
    // Event listeners para cálculos
    const select = newProduct.querySelector('select');
    const cantidadInput = newProduct.querySelector('.cantidad');
    const subtotalInput = newProduct.querySelector('.subtotal');
    const atributosContainer = newProduct.querySelector('.atributos-container');
    
    function calcularSubtotal() {
        const precio = parseFloat(select.selectedOptions[0]?.dataset.precio) || 0;
        const cantidad = parseInt(cantidadInput.value) || 0;
        subtotalInput.value = '$' + (precio * cantidad).toFixed(2);
    }
    
    select.addEventListener('change', function() {
        atributosContainer.innerHTML = '';
        const atributos = JSON.parse(this.selectedOptions[0]?.dataset.atributos || '{}');
        
        for (const [nombre, valor] of Object.entries(atributos)) {
            const attrDiv = document.createElement('div');
            attrDiv.className = 'badge bg-light text-dark me-1 mb-1';
            attrDiv.textContent = `${nombre}: ${valor}`;
            atributosContainer.appendChild(attrDiv);
        }
        
        calcularSubtotal();
    });
    
    cantidadInput.addEventListener('input', calcularSubtotal);
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remover-producto')) {
        e.target.closest('.producto-item').remove();
    }
});
</script>
@endpush
@endsection
