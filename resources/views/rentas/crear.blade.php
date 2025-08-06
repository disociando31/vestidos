@extends('layouts.app')

@section('title', 'Crear Renta')

@section('content')
<h1 class="mb-4">Crear Nueva Renta</h1>

<form action="{{ route('rentas.store') }}" method="POST">
    @csrf

    <!-- Cliente -->
    <div class="mb-3">
        <label for="cliente_id" class="form-label">Cliente</label>
        <select name="cliente_id" id="cliente_id" class="form-select" required>
            <option value="" disabled selected>Selecciona un cliente</option>
            @foreach($clientes as $cliente)
                <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
            @endforeach
        </select>
    </div>

    <!-- Fechas -->
    <div class="row">
        <div class="mb-3 col-md-6">
            <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
            <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" required>
        </div>
        <div class="mb-3 col-md-6">
            <label for="fecha_devolucion" class="form-label">Fecha de Devolución</label>
            <input type="date" name="fecha_devolucion" id="fecha_devolucion" class="form-control" required>
        </div>
    </div>

    <!-- Productos -->
    <h5 class="mt-4">Productos</h5>
    <div id="productos-container"></div>
    <button type="button" id="add-product-btn" class="btn btn-outline-success mb-3">
        + Añadir otro producto
    </button>

    <!-- Botones para trajes -->
    <div class="mb-3 d-flex gap-2">
        <button type="button" class="btn btn-outline-primary" id="add-traje-btn">
            + Agregar Traje de Caballero
        </button>
        <button type="button" class="btn btn-outline-info" id="add-traje-nino-btn">
            + Agregar Traje de Niño
        </button>
    </div>
    <!-- Contenedor dinámico para trajes -->
    <div id="trajes-container"></div>

    <!-- Adicionales -->
    <h5 class="mt-4">Adicionales</h5>
    <div id="adicionales-container"></div>
    <button type="button" class="btn btn-outline-success mb-3" id="add-adicional-btn">
        + Añadir adicional
    </button>

    <!-- Recibido por -->
    <div class="mb-3">
        <label for="recibido_por" class="form-label">Recibido por</label>
        <input type="text" name="recibido_por" id="recibido_por" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary mt-3">Registrar Renta</button>
</form>
@endsection

@push('scripts')
<script>
const productos = @json($productos->values());
let prodIdx = 0;
let adicionalIdx = 0; // ÚNICO índice para TODO adicional, traje, etc.

// --- PRODUCTOS ---
function productoRow(idx) {
    let options = productos.map(prod => {
        let img = prod.img_url;
        let estado = prod.estado;
        let fecha = prod.fecha_disponible ? prod.fecha_disponible.substring(0, 10) : '';
        let disponible = estado === 'disponible' ? 'Disponible' :
                        (estado === 'rentado' ? `Rentado hasta ${fecha}` : 'No disponible');
        return `<option value="${prod.id}" data-img="${img}" data-estado="${estado}" data-fecha="${fecha}">
            ${prod.nombre} (${disponible})
        </option>`;
    }).join('');
    return `
    <div class="producto-item row align-items-end mb-3" data-idx="${idx}">
        <div class="col-md-7">
            <label class="form-label">Producto</label>
            <select name="items[${idx}][producto_id]" class="form-select producto-select" required>
                <option value="" selected disabled>Seleccionar producto...</option>
                ${options}
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Cantidad</label>
            <input type="number" name="items[${idx}][cantidad]" class="form-control" value="1" min="1" required>
        </div>
        <div class="col-md-4" id="preview-${idx}">
        </div>
        <div class="col-md-1 text-end">
            <button type="button" class="btn btn-danger btn-remove" style="display:${idx === 0 ? 'none' : 'inline-block'}">×</button>
        </div>
    </div>
    `;
}

function addProductoRow() {
    const container = document.getElementById('productos-container');
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = productoRow(prodIdx);
    container.appendChild(tempDiv.firstElementChild);
    $(`select[name="items[${prodIdx}][producto_id]"]`).select2({
        placeholder: "Seleccionar producto...",
        width: '100%'
    });
    prodIdx++;
}

document.getElementById('add-product-btn').addEventListener('click', addProductoRow);
document.addEventListener('DOMContentLoaded', () => addProductoRow());

document.getElementById('productos-container').addEventListener('click', function(e) {
    if (e.target.classList.contains('btn-remove')) {
        e.target.closest('.producto-item').remove();
    }
});

$(document).on('change', '.producto-select', function () {
    const idx = $(this).closest('.producto-item').data('idx');
    const selected = this.selectedOptions[0];
    const img = selected.dataset.img;
    const estado = selected.dataset.estado;
    const fecha = selected.dataset.fecha;

    let estadoHtml = '';
    if (estado === 'disponible') {
        estadoHtml = `<span class="badge bg-success">Disponible</span>`;
    } else if (estado === 'rentado') {
        estadoHtml = `<span class="badge bg-warning">Rentado hasta ${fecha}</span>`;
    } else {
        estadoHtml = `<span class="badge bg-secondary">No disponible</span>`;
    }

    $(`#preview-${idx}`).html(`
        <img src="${img}" alt="Preview" class="img-thumbnail mb-1 w-100" style="max-height:300px; object-fit:contain;">
        <div>${estadoHtml}</div>
    `);
});

// --- CLIENTES SELECT2 ---
$(document).ready(function() {
    $('#cliente_id').select2({
        placeholder: "Selecciona un cliente...",
        width: '100%'
    });
});

// --- ADICIONALES DINÁMICOS ---
function adicionalRow(idx) {
    return `
        <div class="row mb-2 adicional-item" data-idx="${idx}">
            <div class="col-md-4">
                <select name="adicionales[${idx}][tipo]" class="form-select adicional-tipo" required>
                    <option value="" selected disabled>Seleccionar tipo...</option>
                    <option value="cinturon">Cinturón</option>
                    <option value="pajarita">Pajarita</option>
                    <option value="chaleco">Chaleco</option>
                    <option value="otro">Otro</option>
                </select>
            </div>
            <div class="col-md-3 adicional-nombre" style="display:none;">
                <input type="text" name="adicionales[${idx}][nombre]" class="form-control" placeholder="Nombre adicional">
            </div>
            <div class="col-md-2">
                <input type="text" name="adicionales[${idx}][color]" class="form-control" placeholder="Color">
            </div>
            <div class="col-md-2">
                <input type="number" name="adicionales[${idx}][precio]" class="form-control" placeholder="$ Precio" step="0.01">
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-danger btn-sm btn-remove-adicional">×</button>
            </div>
        </div>
    `;
}
function addAdicionalRow() {
    $('#adicionales-container').append(adicionalRow(adicionalIdx));
    adicionalIdx++;
}
$('#add-adicional-btn').click(function() {
    addAdicionalRow();
});
$('#adicionales-container').on('click', '.btn-remove-adicional', function() {
    $(this).closest('.adicional-item').remove();
});
$('#adicionales-container').on('change', '.adicional-tipo', function() {
    const tipo = $(this).val();
    const row = $(this).closest('.adicional-item');
    if(tipo === 'otro') {
        row.find('.adicional-nombre').show();
    } else {
        row.find('.adicional-nombre').hide();
    }
});
$(document).ready(function() {
    addAdicionalRow();
});

// --- TRAJE CABALLERO ---
function generarComponenteAdicional(nombre, color = '', talla = '', precio = '', idx) {
    return `
        <div class="row mb-2 adicional-componente">
            <div class="col-md-3">
                <input type="text" name="adicionales[${idx}][nombre]" class="form-control" value="${nombre}" readonly>
            </div>
            <div class="col-md-3">
                <input type="text" name="adicionales[${idx}][color]" class="form-control" placeholder="Color" value="${color}">
            </div>
            <div class="col-md-3">
                <input type="text" name="adicionales[${idx}][talla]" class="form-control" placeholder="Talla" value="${talla}">
            </div>
            <div class="col-md-2">
                <input type="number" name="adicionales[${idx}][precio]" class="form-control" placeholder="$ Precio" step="0.01" value="${precio}">
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-danger btn-sm btn-remove-componente">×</button>
            </div>
        </div>
    `;
}
document.getElementById('add-traje-btn').addEventListener('click', () => {
    const container = document.getElementById('trajes-container');
    const piezas = [
        { nombre: 'chaqueta', precio: 25000 },
        { nombre: 'Camisa', precio: 15000 },
        { nombre: 'Pantalón', precio: 20000 },
        { nombre: 'Corbata', precio: 10000 }
    ];
    piezas.forEach(pieza => {
        const temp = document.createElement('div');
        temp.innerHTML = generarComponenteAdicional(pieza.nombre, '', '', pieza.precio, adicionalIdx);
        container.appendChild(temp.firstElementChild);
        adicionalIdx++;
    });
});

// --- TRAJE NIÑO (solo un adicional, no piezas) ---
document.getElementById('add-traje-nino-btn').addEventListener('click', () => {
    const container = document.getElementById('trajes-container');
    const idx = adicionalIdx;
    const temp = document.createElement('div');
    temp.innerHTML = `
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
                <button type="button" class="btn btn-danger btn-sm btn-remove-componente">×</button>
            </div>
        </div>
    `;
    container.appendChild(temp.firstElementChild);
    adicionalIdx++;
});

// Asignar precio automático al seleccionar talla niño
document.getElementById('trajes-container').addEventListener('change', function (e) {
    if (e.target.classList.contains('talla-nino-select')) {
        const row = e.target.closest('.traje-nino-row');
        const precioInput = row.querySelector('.precio-nino');
        const talla = parseInt(e.target.value);

        let precio = 0;
        if (talla >= 1 && talla <= 4) precio = 50000;
        else if (talla >= 6 && talla <= 10) precio = 55000;
        else if (talla >= 12 && talla <= 16) precio = 60000;
        else if (talla >= 18 && talla <= 30) precio = 65000;
        else if (talla >= 32 && talla <= 54) precio = 70000;

        precioInput.value = precio;
    }
});

// Botón eliminar componente traje
document.getElementById('trajes-container').addEventListener('click', function(e) {
    if (e.target.classList.contains('btn-remove-componente')) {
        e.target.closest('.adicional-componente').remove();
    }
});
</script>
@endpush
