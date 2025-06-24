@extends('layouts.app')

@section('title', 'Reporte Mensual')

@section('content')
    <h3>Reporte Mensual ({{ $inicio->format('d/m/Y') }} - {{ $fin->format('d/m/Y') }})</h3>
    @include('reportes._tabla', ['rentas' => $rentas])
@endsection
