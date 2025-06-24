@extends('layouts.app')

@section('title', 'Reporte Diario')

@section('content')
    <h3>Reporte Diario ({{ \Carbon\Carbon::parse($hoy)->format('d/m/Y') }})</h3>
    @include('reportes._tabla', ['rentas' => $rentas])
@endsection
