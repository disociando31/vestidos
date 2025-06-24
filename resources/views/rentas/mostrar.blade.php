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
                <p><strong>Atendió:</strong> {{ $renta->recibido_por }}</p>
                <p><strong>Total:</strong> ${{ number_format($renta->monto_total, 2) }}</p>
                <p><strong>Saldo:</strong> ${{ number_format($renta->saldo, 2) }}</p>
            </div>
        </div>

        <h5 class="mb-3">Productos Rentados</h5>
        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
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
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('pagos.store', $renta) }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label" for="cantidad">Cantidad</label>
                            <input type="number" name="cantidad" id="cantidad" min="0.01" step="0.01"
                                max="{{ $renta->saldo }}" value="{{ old('cantidad') }}"
                                class="form-control @error('cantidad') is-invalid @enderror" required>
                            @error('cantidad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label" for="metodo_pago">Método de Pago</label>
                            <select name="metodo_pago" id="metodo_pago" class="form-select @error('metodo_pago') is-invalid @enderror" required>
                                <option value="" disabled selected>Selecciona un método</option>
                                <option value="efectivo" {{ old('metodo_pago') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                <option value="tarjeta" {{ old('metodo_pago') == 'tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                                <option value="transferencia" {{ old('metodo_pago') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                            </select>
                            @error('metodo_pago')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="recibido_por">Recibido por</label>
                            <input type="text" name="recibido_por" id="recibido_por" value="{{ old('recibido_por') }}"
                                class="form-control @error('recibido_por') is-invalid @enderror" required>
                            @error('recibido_por')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Registrar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endif

        @if($renta->pagos->count() > 0)
        <h5 class="mb-3">Historial de Pagos</h5>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
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

<div class="mt-4 d-flex gap-2">
    <a href="{{ route('facturas.mostrar', $renta) }}" class="btn btn-info" target="_blank">
        Generar Factura PDF
    </a>

    @if($renta->estado != 'devuelto')
        <form action="{{ route('rentas.devolver', $renta) }}" method="POST" onsubmit="return confirm('¿Estás seguro de marcar esta renta como devuelta?')" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-warning">Marcar como Devuelta</button>
        </form>
    @endif

    <a href="{{ route('rentas.index') }}" class="btn btn-secondary">Volver</a>
</div>

@endsection
