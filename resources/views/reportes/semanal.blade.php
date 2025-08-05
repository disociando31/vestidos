@extends('layouts.app')

<form action="{{ route('reportes.salirProteccion') }}" method="POST" class="mb-3">
    @csrf
    <button class="btn btn-danger">Salir (proteger reporte)</button>
</form>

@section('title', 'Reporte Semanal de Pagos')
@section('content')
<div class="container">
    <h2>Reporte Semanal de Pagos</h2>
    <div>Del <strong>{{ $inicio->format('d/m/Y') }}</strong> al <strong>{{ $fin->format('d/m/Y') }}</strong></div>
    @include('reportes.tabla_pagos', ['pagos' => $pagos])
</div>
@endsection
