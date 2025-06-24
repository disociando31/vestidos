@extends('layouts.app')

@section('title', 'Reporte Semanal')

@section('content')
    <h3>Reporte Semanal ({{ $inicio->format('d/m/Y') }} - {{ $fin->format('d/m/Y') }})</h3>
    @include('reportes._tabla', ['rentas' => $rentas])
@endsection
