@extends('layouts.app')

@section('title', 'Editar Renta')

@section('content')
<h1 class="mb-4">Editar Renta #{{ $renta->id }}</h1>

<form action="{{ route('rentas.actualizar', $renta->id) }}" method="POST">
    @csrf
    @method('PUT')

    <!-- Cliente -->
    <div class="mb-3">
        <label for="cliente_id" class="form-label">Cliente</label>
        <select name="cliente_id" id="cliente_id" class="form-select" required>
            @foreach($clientes as $cliente)
                <option value="{{ $cliente->id }}" {{ $renta->cliente_id == $cliente->id ? 'selected' : '' }}>{{ $cliente->nombre }}</option>
            @endforeach
        </select>
    </div>

    <!-- Fechas -->
    <div class="row">
        <div class="mb-3 col-md-6">
            <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
            <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" value="{{ $renta->fecha_renta->toDateString() }}" required>
        </div>
        <div class="mb-3 col-md-6">
            <label for="fecha_devolucion" class="form-label">Fecha de Devolución</label>
            <input type="date" name="fecha_devolucion" id="fecha_devolucion" class="form-control" value="{{ $renta->fecha_devolucion->toDateString() }}" required>
        </div>
    </div>

    <!-- Productos existentes -->
    <h5>Productos Rentados</h5>
    <div id="productos-container">
        @foreach ($renta->items as $index => $item)
            <div class="row mb-3 producto-item" data-idx="{{ $index }}">
                <div class="col-md-7">
                    <label class="form-label">Producto</label>
                    <select name="items[{{ $index }}][producto_id]" class="form-select" required>
                        @foreach ($productos as $producto)
                            <option value="{{ $producto->id }}" {{ $producto->id == $item->producto_id ? 'selected' : '' }}>{{ $producto->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Cantidad</label>
                    <input type="number" name="items[{{ $index }}][cantidad]" class="form-control" value="{{ $item->cantidad }}" min="1" required>
                </div>
                <div class="col-md-1 text-end align-self-end">
                    <button type="button" class="btn btn-danger btn-sm btn-remove-producto" style="margin-bottom:8px;">&times;</button>
                </div>
            </div>
        @endforeach
    </div>
    <button type="button" id="add-product-btn" class="btn btn-outline-success mb-3">
        + Añadir otro producto
    </button>

    <!-- Trajes y adicionales -->
    <h5 class="mt-4">Trajes de Caballero / Traje Niño / Adicionales</h5>
    <div class="mb-3 d-flex gap-2">
        <button type="button" class="btn btn-outline-primary" id="add-traje-btn">
            + Agregar Traje de Caballero
        </button>
        <button type="button" class="btn btn-outline-info" id="add-traje-nino-btn">
            + Agregar Traje de Niño
        </button>
        <button type="button" class="btn btn-outline-success" id="add-adicional-btn">
            + Añadir adicional
        </button>
    </div>
    <div id="adicionales-container">
        @foreach ($renta->adicionales ?? [] as $index => $adicional)
            @if(isset($adicional['nombre']) && strtolower($adicional['nombre']) == 'traje niño')
                <!-- Traje Niño -->
                <div class="row mb-2 adicional-componente traje-nino-row" data-idx="{{ $index }}">
                    <div class="col-md-3">
                        <input type="text" name="adicionales[{{ $index }}][nombre]" class="form-control" value="Traje Niño" readonly>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="adicionales[{{ $index }}][color]" class="form-control" placeholder="Color" value="{{ $adicional['color'] ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <select name="adicionales[{{ $index }}][talla]" class="form-select talla-nino-select" required>
                            <option value="">Seleccionar talla</option>
                            <option value="1" @if(($adicional['talla'] ?? '')=='1') selected @endif>1-4</option>
                            <option value="6" @if(($adicional['talla'] ?? '')=='6') selected @endif>6-10</option>
                            <option value="12" @if(($adicional['talla'] ?? '')=='12') selected @endif>12-16</option>
                            <option value="18" @if(($adicional['talla'] ?? '')=='18') selected @endif>18-30</option>
                            <option value="32" @if(($adicional['talla'] ?? '')=='32') selected @endif>32-54</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="adicionales[{{ $index }}][precio]" class="form-control precio-nino" placeholder="$ Precio" readonly value="{{ $adicional['precio'] ?? '' }}">
                    </div>
                    <div class="col-md-1 text-end">
                        <button type="button" class="btn btn-danger btn-sm btn-remove-componente">&times;</button>
                    </div>
                </div>
            @elseif(isset($adicional['nombre']) && in_array($adicional['nombre'], ['chaqueta','Camisa','Pantalón','Corbata']))
                <!-- Traje Caballero pieza -->
                <div class="row mb-2 adicional-componente" data-idx="{{ $index }}">
                    <div class="col-md-3">
                        <input type="text" name="adicionales[{{ $index }}][nombre]" class="form-control" value="{{ $adicional['nombre'] ?? '' }}" readonly>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="adicionales[{{ $index }}][color]" class="form-control" placeholder="Color" value="{{ $adicional['color'] ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="adicionales[{{ $index }}][talla]" class="form-control" placeholder="Talla" value="{{ $adicional['talla'] ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="adicionales[{{ $index }}][precio]" class="form-control" placeholder="Precio" step="0.01" value="{{ $adicional['precio'] ?? '' }}">
                    </div>
                    <div class="col-md-1 text-end">
                        <button type="button" class="btn btn-danger btn-sm btn-remove-componente">&times;</button>
                    </div>
                </div>
            @else
                <!-- Otro adicional -->
                <div class="row mb-2 adicional-item" data-idx="{{ $index }}">
                    <div class="col-md-3">
                        <input type="text" name="adicionales[{{ $index }}][nombre]" class="form-control" placeholder="Nombre" value="{{ $adicional['nombre'] ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="adicionales[{{ $index }}][color]" class="form-control" placeholder="Color" value="{{ $adicional['color'] ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="adicionales[{{ $index }}][talla]" class="form-control" placeholder="Talla" value="{{ $adicional['talla'] ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="adicionales[{{ $index }}][precio]" class="form-control" placeholder="Precio" step="0.01" value="{{ $adicional['precio'] ?? '' }}">
                    </div>
                    <div class="col-md-1 text-end">
                        <button type="button" class="btn btn-danger btn-sm btn-remove-adicional">&times;</button>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <div class="mb-3">
        <label for="recibido_por" class="form-label">Recibido por</label>
        <input type="text" name="recibido_por" id="recibido_por" class="form-control" value="{{ $renta->recibido_por }}">
    </div>

    <div class="text-end">
        <button type="submit" class="btn btn-primary">Actualizar Renta</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
const productos = @json($productos->values());
let prodIdx = {{ count($renta->items) }};
let adicionalIdx = {{ count($renta->adicionales ?? []) }};

// Añadir otro producto/vestido
function productoRow(idx) {
    let options = productos.map(prod => `<option value="${prod.id}">${prod.nombre}</option>`).join('');
    return `
    <div class="row mb-3 producto-item" data-idx="${idx}">
        <div class="col-md-7">
            <label class="form-label">Producto</label>
            <select name="items[${idx}][producto_id]" class="form-select" required>
                <option value="" selected disabled>Seleccionar producto...</option>
                ${options}
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Cantidad</label>
            <input type="number" name="items[${idx}][cantidad]" class="form-control" value="1" min="1" required>
        </div>
        <div class="col-md-1 text-end align-self-end">
            <button type="button" class="btn btn-danger btn-sm btn-remove-producto" style="margin-bottom:8px;">&times;</button>
        </div>
    </div>
    `;
}
document.getElementById('add-product-btn').addEventListener('click', function() {
    $('#productos-container').append(productoRow(prodIdx));
    prodIdx++;
});
$(document).on('click', '.btn-remove-producto', function () {
    $(this).closest('.producto-item').remove();
});

// Añadir Traje de Caballero (4 piezas)
function generarComponenteAdicional(nombre, color = '', talla = '', precio = '') {
    return `
        <div class="row mb-2 adicional-componente" data-idx="${adicionalIdx}">
            <div class="col-md-3">
                <input type="text" name="adicionales[${adicionalIdx}][nombre]" class="form-control" value="${nombre}" readonly>
            </div>
            <div class="col-md-3">
                <input type="text" name="adicionales[${adicionalIdx}][color]" class="form-control" placeholder="Color" value="${color}">
            </div>
            <div class="col-md-3">
                <input type="text" name="adicionales[${adicionalIdx}][talla]" class="form-control" placeholder="Talla" value="${talla}">
            </div>
            <div class="col-md-2">
                <input type="number" name="adicionales[${adicionalIdx}][precio]" class="form-control" placeholder="Precio" step="0.01" value="${precio}">
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-danger btn-sm btn-remove-componente">&times;</button>
            </div>
        </div>
    `;
}
document.getElementById('add-traje-btn').addEventListener('click', () => {
    const piezas = [
        { nombre: 'chaqueta', precio: 25000 },
        { nombre: 'Camisa', precio: 15000 },
        { nombre: 'Pantalón', precio: 20000 },
        { nombre: 'Corbata', precio: 10000 }
    ];
    piezas.forEach(pieza => {
        $('#adicionales-container').append(generarComponenteAdicional(pieza.nombre, '', '', pieza.precio));
        adicionalIdx++;
    });
});

// Añadir Traje Niño
document.getElementById('add-traje-nino-btn').addEventListener('click', () => {
    const idx = adicionalIdx;
    const html = `
        <div class="row mb-2 adicional-componente traje-nino-row" data-idx="${idx}">
            <div class="col-md-3">
                <input type="text" name="adicionales[${idx}][nombre]" class="form-control" value="Traje Niño" readonly>
            </div>
            <div class="col-md-3">
                <input type="text" name="adicionales[${idx}][color]" class="form-control" placeholder="Color">
            </div>
            <div class="col-md-3">
                <select name="adicionales[${idx}][talla]" class="form-select talla-nino-select" required>
                    <option value="">Seleccionar talla</option>
                    <option value="1">1-4</option>
                    <option value="6">6-10</option>
                    <option value="12">12-16</option>
                    <option value="18">18-30</option>
                    <option value="32">32-54</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" name="adicionales[${idx}][precio]" class="form-control precio-nino" readonly placeholder="$ Precio">
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-danger btn-sm btn-remove-componente">&times;</button>
            </div>
        </div>
    `;
    $('#adicionales-container').append(html);
    adicionalIdx++;
});

// Precio Traje Niño según talla
$('#adicionales-container').on('change', '.talla-nino-select', function () {
    const row = $(this).closest('.traje-nino-row');
    const precioInput = row.find('.precio-nino');
    const talla = parseInt($(this).val());

    let precio = 0;
    if (talla >= 1 && talla <= 4) precio = 50000;
    else if (talla >= 6 && talla <= 10) precio = 55000;
    else if (talla >= 12 && talla <= 16) precio = 60000;
    else if (talla >= 18 && talla <= 30) precio = 65000;
    else if (talla >= 32 && talla <= 54) precio = 70000;

    precioInput.val(precio);
});

// Añadir otro adicional personalizado
function adicionalRow(idx) {
    return `
        <div class="row mb-2 adicional-item" data-idx="${idx}">
            <div class="col-md-3">
                <input type="text" name="adicionales[${idx}][nombre]" class="form-control" placeholder="Nombre">
            </div>
            <div class="col-md-2">
                <input type="text" name="adicionales[${idx}][color]" class="form-control" placeholder="Color">
            </div>
            <div class="col-md-2">
                <input type="text" name="adicionales[${idx}][talla]" class="form-control" placeholder="Talla">
            </div>
            <div class="col-md-2">
                <input type="number" name="adicionales[${idx}][precio]" class="form-control" placeholder="Precio" step="0.01">
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-danger btn-sm btn-remove-adicional">&times;</button>
            </div>
        </div>
    `;
}
document.getElementById('add-adicional-btn').addEventListener('click', function() {
    $('#adicionales-container').append(adicionalRow(adicionalIdx));
    adicionalIdx++;
});

// Eliminar botones
$('#adicionales-container').on('click', '.btn-remove-componente, .btn-remove-adicional', function () {
    $(this).closest('.row').remove();
});
</script>
@endpush
