@extends('layouts.app')

@section('title', 'Protegido')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card mt-5">
            <div class="card-header">
                <h5>Acceso restringido</h5>
            </div>
            <div class="card-body">
                @if(isset($error) && $error)
                    <div class="alert alert-danger">Contraseña incorrecta, intenta de nuevo.</div>
                @endif
                <form method="POST" action="{{ $action }}">
                    @csrf
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" name="password" id="password" class="form-control" required autofocus>
                    </div>
                    <button type="submit" class="btn btn-primary">Ingresar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
