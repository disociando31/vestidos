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

            <h5 class="mt-4">Productos a Rentar</h5>
            <div id="productos-container"></div>

            <button type="button" id="agregar-producto" class="btn btn-secondary mb-3">Agregar Producto</button>

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
            <div class="row mb-3 producto-item border p-2 rounded">
                <div class="col-md-4">
                    <label class="form-label">Producto</label>
                    <select name="items[${newId}][producto_id]" class="form-control producto-select" required>
                        <option value="">Seleccionar producto...</option>
                        @foreach($productos as $producto)
                        <option 
                            value="{{ $producto->id }}" 
                            data-precio="{{ $producto->precio_renta }}"
                            data-img="{{ optional($producto->imagenPrincipal)->ruta }}"
                            data-nombre="{{ $producto->nombre }}"
                            {{ $producto->estado != 'disponible' ? 'disabled' : '' }}>
                            {{ $producto->nombre }} - ${{ number_format($producto->precio_renta, 2) }}
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
                    <button type="button" class="btn btn-danger remover-producto">X</button>
                </div>
                <div class="col-12 mt-2 imagen-producto"></div>
                <div class="col-12 atributos-container mt-2"></div>
            </div>
        `);

        container.append(newProduct);

        newProduct.find('.producto-select').select2();

        const select = newProduct.find('select');
        const cantidadInput = newProduct.find('.cantidad');
        const subtotalInput = newProduct.find('.subtotal');
        const imagenContainer = newProduct.find('.imagen-producto');
        const atributosContainer = newProduct.find('.atributos-container');

        function calcularSubtotal() {
            const precio = parseFloat(select.find(':selected').data('precio') || 0);
            const cantidad = parseInt(cantidadInput.val() || 0);
            subtotalInput.val('$' + (precio * cantidad).toFixed(2));
        }

        select.on('change', function () {
            atributosContainer.html('');
            imagenContainer.html('');
            calcularSubtotal();

            const img = select.find(':selected').data('img');
            const nombre = select.find(':selected').data('nombre');

            if (img) {
                imagenContainer.html(`<img src="/storage/${img}" alt="${nombre}" class="img-thumbnail" style="max-height:150px;">`);
            }
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
