@extends('layouts.app')

@section('title', 'Reporte Diario')

@section('content')
    <h3>Reporte Diario ({{ $hoy }})</h3>
    @include('reportes._tabla', ['rentas' => $rentas])
@endsection
