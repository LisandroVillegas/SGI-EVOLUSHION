@extends('adminlte::page')

@section('title', 'Configuración del Sistema')

@section('content_header')
    <h1>Configuración del Sistema</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Seguridad y PIN de Autorización</h3>
            </div>
            <form action="{{ route('configuracion.updatePin') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="pin_actual">PIN Actual</label>
                        <input type="password" name="pin_actual" id="pin_actual" class="form-control @error('pin_actual') is-invalid @enderror" maxlength="4" placeholder="****" required>
                        @error('pin_actual')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="pin_nuevo">Nuevo PIN (4 dígitos numéricos)</label>
                        <input type="password" name="pin_nuevo" id="pin_nuevo" class="form-control @error('pin_nuevo') is-invalid @enderror" maxlength="4" placeholder="****" required>
                        @error('pin_nuevo')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-key"></i> Actualizar PIN
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop