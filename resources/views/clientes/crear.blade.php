@extends('layouts.app')

@section('title', 'Registrar Cliente')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Registrar Nuevo Cliente</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('clientes.guardar') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Nombre Completo</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Dirección</label>
                <textarea name="direccion" class="form-control" rows="2"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Fecha de Registro</label>
                <input type="date" name="fecha_registro" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">Guardar Cliente</button>
        </form>
    </div>
</div>
@endsection