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

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function () {
    let productoCounter = 0;

    $('#agregar-producto').on('click', function() {
        const container = $('#productos-container');
        const newId = productoCounter++;

        const newProduct = $(`
            <div class="row mb-3 producto-item">
                <div class="col-md-6">
                    <label class="form-label">Producto</label>
                    <select name="items[${newId}][producto_id]" class="form-control producto-select" required>
                        <option value="">Seleccionar producto...</option>
                        @foreach($productos as $producto)
                        <option 
                            value="{{ $producto->id }}" 
                            data-precio="{{ $producto->precio_renta }}"
                            data-atributos='@json($producto->atributos->pluck("valor", "nombre"))'
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
            </div>
        `);

        container.append(newProduct);

        // Inicializar Select2 en este nuevo select
        newProduct.find('.producto-select').select2();

        const select = newProduct.find('select');
        const cantidadInput = newProduct.find('.cantidad');
        const subtotalInput = newProduct.find('.subtotal');
        const atributosContainer = newProduct.find('.atributos-container');

        function calcularSubtotal() {
            const precio = parseFloat(select.find(':selected').data('precio') || 0);
            const cantidad = parseInt(cantidadInput.val() || 0);
            subtotalInput.val('$' + (precio * cantidad).toFixed(2));
        }

        select.on('change', function () {
            atributosContainer.html('');
            let atributos = {};

            try {
                atributos = select.find(':selected').data('atributos') || {};
            } catch (e) {
                console.error("Error al obtener atributos: ", e);
            }

            $.each(atributos, function(nombre, valor) {
                atributosContainer.append(`<div class="badge bg-light text-dark me-1 mb-1">${nombre}: ${valor}</div>`);
            });

            calcularSubtotal();
        });

        cantidadInput.on('input', calcularSubtotal);
    });

    $(document).on('click', '.remover-producto', function () {
        $(this).closest('.producto-item').remove();
    });
});
</script>
@endpush
@endsection