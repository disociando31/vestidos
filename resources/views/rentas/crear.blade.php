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
    <!-- Botón para trajes -->
<button type="button" class="btn btn-outline-primary mb-3" id="add-traje-btn">
    + Agregar Traje de Caballero
</button>
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
let adicionalIdx = 0;
function adicionalRow(idx) {
    return `
    <div class="row mb-2 adicional-item" data-idx="${idx}">
        <div class="col-md-2">
            <select name="adicionales[${idx}][tipo]" class="form-select adicional-tipo" required>
                <option value="camisa">Camisa</option>
                <option value="zapatos">Zapatos</option>
                <option value="corbata">Corbata</option>
                <option value="cartera">Cartera</option>
                <option value="otro">Otro</option>
            </select>
        </div>
        <div class="col-md-2">
            <input type="text" name="adicionales[${idx}][color]" class="form-control" placeholder="Color">
        </div>
        <div class="col-md-2">
            <input type="text" name="adicionales[${idx}][talla]" class="form-control" placeholder="Talla">
        </div>
        <div class="col-md-3">
            <input type="text" name="adicionales[${idx}][nombre]" class="form-control adicional-nombre" placeholder="Nombre (solo 'Otro')" style="display:none;">
        </div>
        <div class="col-md-2">
            <input type="number" name="adicionales[${idx}][precio]" class="form-control" placeholder="Precio" min="0" step="0.01" required>
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-danger btn-remove-adicional">&times;</button>
        </div>
    </div>
    `;
}

function addAdicionalRow() {
    $('#adicionales-container').append(adicionalRow(adicionalIdx));
    adicionalIdx++;
}

addAdicionalRow();

$('#add-adicional-btn').click(function() {
    addAdicionalRow();
});

$('#adicionales-container').on('click', '.btn-remove-adicional', function() {
    $(this).closest('.adicional-item').remove();
});

// Mostrar campo nombre SOLO cuando seleccionas "otro"
$('#adicionales-container').on('change', '.adicional-tipo', function() {
    const tipo = $(this).val();
    const row = $(this).closest('.adicional-item');
    if(tipo === 'otro') {
        row.find('.adicional-nombre').show();
    } else {
        row.find('.adicional-nombre').hide();
    }
});
</script>
@endpush
@push('scripts')
<script>
const productos = @json($productos->values());
let prodIdx = 0;

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
     // Activar select2 en el nuevo select
    $(`select[name="items[${prodIdx}][producto_id]"]`).select2({
        placeholder: "Seleccionar producto...",
        width: '100%'
        });
    prodIdx++;
}

document.getElementById('add-product-btn').addEventListener('click', addProductoRow);
document.addEventListener('DOMContentLoaded', () => addProductoRow());

// Remove row
document.getElementById('productos-container').addEventListener('click', function(e) {
    if (e.target.classList.contains('btn-remove')) {
        e.target.closest('.producto-item').remove();
    }
});

// Show image and status on change
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


</script>
@endpush
@push('scripts')
<script>
    $(document).ready(function() {
        $('#cliente_id').select2({
            placeholder: "Selecciona un cliente...",
            width: '100%'
        });
    });
</script>
<script>
let trajeIdx = 0;

function generarComponenteAdicional(nombre, color = '', talla = '', precio = '') {
    return `
        <div class="row mb-2 adicional-componente">
            <div class="col-md-3">
                <input type="text" name="adicionales[${trajeIdx}][nombre]" class="form-control" value="${nombre}" readonly>
            </div>
            <div class="col-md-3">
                <input type="text" name="adicionales[${trajeIdx}][color]" class="form-control" placeholder="Color" value="${color}">
            </div>
            <div class="col-md-3">
                <input type="text" name="adicionales[${trajeIdx}][talla]" class="form-control" placeholder="Talla" value="${talla}">
            </div>
            <div class="col-md-2">
                <input type="number" name="adicionales[${trajeIdx}][precio]" class="form-control" placeholder="$ Precio" step="0.01" value="${precio}">
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
        'Chaqueta',
        'Camisa',
        'Pantalón',
        'Zapatos'
    ];

    piezas.forEach(pieza => {
        const temp = document.createElement('div');
        temp.innerHTML = generarComponenteAdicional(`Traje ${Math.floor(trajeIdx / 4) + 1} - ${pieza}`);
        container.appendChild(temp.firstElementChild);
        trajeIdx++;
    });
});

document.getElementById('trajes-container').addEventListener('click', function(e) {
    if (e.target.classList.contains('btn-remove-componente')) {
        e.target.closest('.adicional-componente').remove();
    }
});
</script>
@endpush

