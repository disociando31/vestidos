@extends('layouts.app')

@section('title', 'Panel Principal')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">📊 Panel de Control - Boutique de Vestidos</h1>

    <div class="row">
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Disponibles</h5>
                    <p class="card-text display-4">{{ $productosDisponibles }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title">Rentados</h5>
                    <p class="card-text display-4">{{ $productosRentados }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <h5 class="card-title">Rentas Activas</h5>
                    <p class="card-text display-4">{{ $rentasActivas }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger mb-3">
                <div class="card-body">
                    <h5 class="card-title">Rentas Atrasadas</h5>
                    <p class="card-text display-4">{{ $rentasAtrasadas }}</p>
                </div>
            </div>
        </div>
    </div>

    <a href="{{ route('calendario.index') }}" class="btn btn-primary mt-3">📅 Ir al Calendario</a>
</div>
@endsection
