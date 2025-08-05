@extends('layouts.app')
@section('title', 'Reporte Diario de Pagos')

@section('content')
    <h3>Pagos recibidos el {{ \Carbon\Carbon::parse($hoy)->format('d/m/Y') }}</h3>
    @include('reportes.tabla_pagos', ['pagos' => $pagos])
@endsection
