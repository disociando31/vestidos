@extends('layouts.app')

@section('title', 'Detalle de Renta')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Detalle de Renta #{{ $renta->id }}</h5>
        <div>
            <a href="{{ route('rentas.index') }}" class="btn btn-secondary btn-sm">Volver al listado</a>
            <a href="{{ route('facturas.mostrar', $renta) }}" target="_blank" class="btn btn-outline-primary btn-sm ms-2">
                <i class="fas fa-print"></i> Imprimir Factura
            </a>
        </div>
    </div>

    <div class="card-body">
        <h6>Cliente:</h6>
        <p><strong>{{ $renta->cliente->nombre }}</strong> ({{ $renta->cliente->telefono }})</p>

        <h6>Fechas:</h6>
        <p>
            <strong>Desde:</strong> {{ \Carbon\Carbon::parse($renta->fecha_renta)->format('d/m/Y') }} <br>
            <strong>Hasta:</strong> {{ \Carbon\Carbon::parse($renta->fecha_devolucion)->format('d/m/Y') }}
        </p>

        <h6>Notas:</h6>
        <p>{{ $renta->notas ?? 'Sin notas adicionales.' }}</p>

        <h6>Estado:</h6>
        <p>
            <span class="badge 
                @if($renta->estado == 'pendiente') bg-warning 
                @elseif($renta->estado == 'pagado') bg-success 
                @elseif($renta->estado == 'devuelto') bg-primary 
                @endif
            ">
                {{ ucfirst($renta->estado) }}
            </span>
        </p>

        <h6>Productos Rentados:</h6>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Total</th>
                        <th>Atributos</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($renta->items as $item)
                        <tr>
                            <td>
                                {{ $item->producto->nombre }}
                                @if($item->producto->imagenPrincipal)
                                    <br>
                                    <img src="{{ asset('storage/' . $item->producto->imagenPrincipal->ruta) }}" alt="{{ $item->producto->nombre }}" class="img-thumbnail mt-1" style="max-width: 100px;">
                                @endif
                            </td>
                            <td>{{ $item->cantidad }}</td>
                            <td>${{ number_format($item->precio_unitario, 2) }}</td>
                            <td>${{ number_format($item->total, 2) }}</td>
                            <td>
        @php
    $atributosArray = [];

    if (is_string($item->atributos)) {
        $atributosArray = json_decode($item->atributos, true) ?? [];
    } elseif (is_array($item->atributos)) {
        $atributosArray = $item->atributos;
    }
@endphp

@if(!empty($atributosArray) && is_iterable($atributosArray))
    <ul class="mb-0">
        @foreach($atributosArray as $nombre => $valor)
            <li><strong>{{ $nombre }}:</strong> {{ $valor }}</li>
        @endforeach
    </ul>
@else
    <span class="text-muted">Sin atributos</span>
@endif

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if(!empty($renta->adicionales))
    <h6 class="mt-4">Trajes de Caballero / Adicionales</h6>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Color</th>
                    <th>Talla</th>
                    <th>Precio</th>
                </tr>
            </thead>
            <tbody>
                @foreach($renta->adicionales as $adicional)
                    <tr>
                        <td>{{ $adicional['nombre'] ?? $adicional['tipo'] ?? '-' }}</td>
                        <td>{{ $adicional['color'] ?? '-' }}</td>
                        <td>{{ $adicional['talla'] ?? '-' }}</td>
                        <td>
                            @if(isset($adicional['precio']) && $adicional['precio'] > 0)
                                ${{ number_format($adicional['precio'], 2) }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif


        {{-- Formulario para abonar --}}
        <form action="{{ route('pagos.store', $renta) }}" method="POST" class="mt-3">
            @csrf
            <input type="hidden" name="renta_id" value="{{ $renta->id }}">

            <div class="mb-2">
                <label for="monto" class="form-label">Abonar monto ($)</label>
                <input type="number" name="monto" id="monto" class="form-control" step="0.01" min="0.01" required max="{{ $renta->saldo }}">
            </div>

            <div class="mb-2">
                <label for="metodo_pago" class="form-label">Método de pago</label>
                <select name="metodo_pago" id="metodo_pago" class="form-select" required>
                    <option value="">Seleccionar...</option>
                    <option value="efectivo">Efectivo</option>
                    <option value="transferencia">Transferencia</option>
                    <option value="tarjeta">Tarjeta</option>
                    <option value="otro">Otro</option>
                </select>
            </div>

            <div class="mb-2">
                <label for="recibido_por" class="form-label">Recibido por</label>
                <input type="text" name="recibido_por" id="recibido_por" class="form-control" required>
            </div>

            <div class="mb-2">
                <label for="notas" class="form-label">Notas del pago (opcional)</label>
                <input type="text" name="notas" id="notas" class="form-control">
            </div>

            <button type="submit" class="btn btn-success">Registrar Abono</button>
        </form>

        {{-- Pagos realizados --}}
        <h6 class="mt-4">Pagos:</h6>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Método</th>
                        <th>Monto</th>
                        <th>Recibido Por</th>
                        <th>Notas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($renta->pagos as $pago)
                        <tr>
                            <td>{{ $pago->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ ucfirst($pago->metodo_pago) }}</td>
                            <td>${{ number_format($pago->monto, 2) }}</td>
                            <td>{{ $pago->recibido_por }}</td>
                            <td>{{ $pago->notas ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <h6 class="mt-3">Resumen de Pagos:</h6>
        <p>
            <strong>Total:</strong> ${{ number_format($renta->monto_total, 2) }} <br>
            <strong>Pagado:</strong> ${{ number_format($renta->monto_pagado, 2) }} <br>
            <strong>Pendiente:</strong> ${{ number_format($renta->saldo, 2) }}
        </p>

        @if($renta->estado !== 'devuelto')
            <form action="{{ route('rentas.devolver', $renta) }}" method="POST" onsubmit="return confirm('¿Confirmar devolución de productos?');">
                @csrf
                <button type="submit" class="btn btn-primary">Registrar Devolución</button>
            </form>
        @endif
    </div>
</div>
@endsection