@extends('layouts.app')

@section('title', 'Detalle de Pago')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Detalle de Pago #{{ $pago->id }}</h5>
    </div>
    <div class="card-body">
        
        {{-- Mensajes de éxito o error --}}
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if(session('exito'))
            <div class="alert alert-success">
                {{ session('exito') }}
            </div>
        @endif

        <div class="row mb-3">
            <div class="col-md-6">
                <p><strong>Cliente:</strong> {{ $pago->cliente->nombre ?? 'N/A' }}</p>
                <p><strong>Fecha:</strong> {{ $pago->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Monto:</strong> ${{ number_format($pago->monto, 2) }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Método de Pago:</strong> {{ ucfirst($pago->metodo_pago) }}</p>
                <p><strong>Recibido por:</strong> {{ $pago->recibido_por }}</p>
                <p><strong>Notas:</strong> {{ $pago->notas ?? '-' }}</p>
            </div>
        </div>

        <div class="mt-3">
            <a href="{{ route('pagos.reporte') }}" class="btn btn-secondary">Volver al Reporte</a>
        </div>
    </div>
</div>
@endsection
