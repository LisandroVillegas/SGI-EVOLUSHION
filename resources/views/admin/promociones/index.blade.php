@extends('adminlte::page')

@section('title', 'Gestión de Promociones')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-tags text-primary"></i> Gestión de Promociones</h1>
        <a href="{{ route('promociones.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Promoción
        </a>
    </div>
@stop

@section('content')
    @if (session('mensaje'))
        <script>
            Swal.fire({
                icon: "{{ session('icono', 'success') }}",
                title: "{{ session('mensaje') }}",
                showConfirmButton: false,
                timer: 2500
            });
        </script>
    @endif

    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Promociones Registradas</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover text-center align-middle">
                    <thead class="thead-dark">
                        <tr>
                            <th style="width: 50px;">N°</th>
                            <th>Nombre</th>
                            <th>Aplica a</th>
                            <th>Cant. Mínima</th>
                            <th>Descuento</th>
                            <th style="width: 220px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($promociones as $promo)
                            <tr>
                                <td class="align-middle">{{ $loop->iteration }}</td>
                                <td class="align-middle"><strong>{{ $promo->nombre }}</strong></td>
                                <td class="align-middle">
                                    @if($promo->producto)
                                        <!-- Si la promoción es para un producto específico (Ej: Nevera) -->
                                        <span class="badge badge-info p-2">
                                            <i class="fas fa-box"></i> {{ $promo->producto->nombre }}
                                        </span>
                                        <span class="badge badge-secondary p-2 ml-1">
                                            <i class="fas fa-snowflake"></i> {{ $promo->categoria->nombre }}
                                        </span>
                                    @else
                                        <!-- Si la promoción aplica a toda la categoría (Ej: Cócteles) -->
                                        <span class="badge badge-primary p-2">
                                            <i class="fas fa-tags"></i> {{ $promo->categoria->nombre }}
                                        </span>
                                    @endif
                                </td>
                                <td class="align-middle">{{ $promo->cantidad_minima }} unidades</td>
                                <td class="align-middle">${{ number_format($promo->descuento, 0, ',', '.') }}</td>
                                <td class="align-middle">
                                    <!-- Botón Editar -->
                                    <a href="{{ route('promociones.edit', $promo->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>

                                    <!-- Botón Eliminar con middleware verify.pin -->
                                    <form action="{{ route('promociones.destroy', $promo->id) }}" method="POST" style="display:inline-block;" class="form-eliminar">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="pin" class="input-pin-hidden">
                                        <button type="button" class="btn btn-sm btn-danger btn-eliminar-promo">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-muted">No hay promociones registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    $('.btn-eliminar-promo').click(function(e) {
        e.preventDefault();
        let form = $(this).closest('form');

        Swal.fire({
            title: '¿Desea eliminar esta promoción?',
            text: 'Ingrese el PIN de seguridad de 4 dígitos:',
            input: 'password',
            inputAttributes: {
                maxlength: 4,
                autofocus: 'autofocus',
                style: 'text-align: center; font-size: 1.2rem; width: 80%; margin: 10px auto;'
            },
            showCancelButton: true,
            icon: 'warning',
            confirmButtonText: 'Sí, continuar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6c757d',
            preConfirm: (pin) => {
                if (!pin || pin.length < 4) {
                    Swal.showValidationMessage('Debe ingresar el PIN de 4 dígitos');
                }
                return pin;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                form.find('.input-pin-hidden').val(result.value);
                form.submit();
            }
        });
    });
</script>
@stop