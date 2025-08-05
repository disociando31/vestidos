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
            <div class="row mb-3">
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
            </div>
        @endforeach
    </div>

    <!-- Adicionales -->
    <h5 class="mt-4">Trajes de Caballero / Adicionales</h5>
    <div id="adicionales-container">
        @foreach ($renta->adicionales ?? [] as $index => $adicional)
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
        @endforeach
    </div>

    <button type="button" class="btn btn-outline-success mb-3" id="add-adicional-btn">
        + Añadir adicional
    </button>

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
let adicionalIdx = {{ count($renta->adicionales ?? []) }};

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

document.getElementById('add-adicional-btn').addEventListener('click', () => {
    $('#adicionales-container').append(adicionalRow(adicionalIdx));
    adicionalIdx++;
});

$(document).on('click', '.btn-remove-adicional', function () {
    $(this).closest('.adicional-item').remove();
});
</script>
@endpush
