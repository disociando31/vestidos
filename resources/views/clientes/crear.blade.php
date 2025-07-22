@extends('layouts.app')

@section('title', 'Registrar Cliente')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Registrar Nuevo Cliente</h5>
    </div>
    <div class="card-body">
        @if(session('exito'))
            <div class="alert alert-success">{{ session('exito') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('clientes.guardar') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Nombre Completo</label>
                <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" value="{{ old('telefono') }}" required>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('telefono2Field').style.display='block'">+ Teléfono adicional</button>
                </div>
            </div>

            <div class="mb-3" id="telefono2Field" style="display:none">
                <label class="form-label">Segundo Teléfono</label>
                <input type="text" name="telefono2" class="form-control" value="{{ old('telefono2') }}">
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Fecha de Registro</label>
                    <input type="date" name="fecha_registro" class="form-control" value="{{ old('fecha_registro') }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Fecha de Cumpleaños</label>
                <input type="date" name="fecha_cumpleanos" class="form-control" value="{{ old('fecha_cumpleanos') }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Dirección</label>
                <textarea name="direccion" class="form-control" rows="2">{{ old('direccion') }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">Guardar Cliente</button>
        </form>
    </div>
</div>
@endsection
