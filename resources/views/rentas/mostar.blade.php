@extends('layouts.app')

@section('title', 'Detalle de Renta')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Renta #{{ $renta->id }}</h5>
        <span class="badge bg-{{ 
            $renta->estado == 'pendiente' ? 'secondary' : 
            ($renta->estado == 'parcial' ? 'warning' : 
            ($renta->estado == 'pagado' ? 'success' : 
            ($renta->estado == 'devuelto' ? 'info' : 'danger'))) 
        }}">
            {{ ucfirst($renta->estado) }}
        </span>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <p><strong>Cliente:</strong> {{ $renta->cliente->nombre }}</p>
                <p><strong>Teléfono:</strong> {{ $renta->cliente->telefono }}</p>
                <p><strong>Fecha Renta:</strong> {{ $renta->fecha_renta->format('d/m/Y') }}</p>
                <p><strong>Fecha Devolución:</strong> {{ $renta->fecha_devolucion->format('d/m/Y') }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Tipo de Gancho:</strong> {{ $renta->tipo_gancho ?? 'N/A' }}</p>
                <p><strong>Atendió:</strong> {{ $renta->recibido_por }}</p>
                <p><strong>Total:</strong> ${{ number_format($renta->monto_total, 2) }}</p>
                <p><strong>Saldo:</strong> ${{ number_format($renta->saldo, 2) }}</p>
            </div>
        </div>

        <h5 class="mb-3">Productos Rentados</h5>
        <div class="table-responsive mb-4">
            <table class="table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Atributos</th>
                        <th>Precio Unitario</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th>IVA</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($renta->items as $item)
                    <tr>
                        <td>{{ $item->producto->nombre }}</td>
                        <td>
                            @foreach($item->atributos as $nombre => $valor)
                            <span class="badge bg-light text-dark">{{ $nombre }}: {{ $valor }}</span>
                            @endforeach
                        </td>
                        <td>${{ number_format($item->precio_unitario, 2) }}</td>
                        <td>{{ $item->cantidad }}</td>
                        <td>${{ number_format($item->subtotal, 2) }}</td>
                        <td>${{ number_format($item->iva, 2) }}</td>
                        <td>${{ number_format($item->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($renta->estado != 'devuelto')
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Registrar Pago</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('pagos.store', $renta) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Cantidad</label>
                            <input type="number" name="cantidad" min="0.01" step="0.01" 
                                max="{{ $renta->saldo }}" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Método de Pago</label>
                            <select name="metodo_pago" class="form-select" required>
                                <option value="efectivo">Efectivo</option>
                                <option value="tarjeta">Tarjeta</option>
                                <option value="transferencia">Transferencia</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Recibido por</label>
                            <input type="text" name="recibido_por" class="form-control" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">Registrar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endif

        @if($renta->pagos->count() > 0)
        <h5 class="mb-3">Historial de Pagos</h5>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Método</th>
                        <th>Cantidad</th>
                        <th>Recibió</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($renta->pagos as $pago)
                    <tr>
                        <td>{{ $pago->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ ucfirst($pago->metodo_pago) }}</td>
                        <td>${{ number_format($pago->monto, 2) }}</td>
                        <td>{{ $pago->recibido_por }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <div class="mt-4">
            <a href="{{ route('facturas.mostrar', $renta) }}" class="btn btn-info" target="_blank">
                Generar Factura PDF
            </a>
            <a href="{{ route('rentas.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </div>
</div>
@endsection