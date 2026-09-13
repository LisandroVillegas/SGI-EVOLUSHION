@extends('adminlte::page')

@section('content_header')
<nav aria-label="breadcrumb" style="font-size: 18pt">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ url('/admin/turnos') }}">Turnos</a></li>
    <li class="breadcrumb-item active" aria-current="page">Lista de Turnos</li>
  </ol>
</nav>
<hr>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Turnos / Cajas Registradas</h3>
                <div class="card-tools d-flex gap-2">
                    @php
                        $miTurnoActivo = $turnos->where('user_id', Auth::id())->where('estado', 'abierto')->first();
                    @endphp

                    @if($miTurnoActivo)
                        <a class="btn btn-warning mr-2" href="{{ url('/admin/turnos/'.$miTurnoActivo->id.'/edit') }}">
                            <i class="fas fa-lock"></i> Cerrar Mi Turno
                        </a>
                    @else
                        <a class="btn btn-primary" href="{{ url('/admin/turnos/create') }}">
                            <i class="fas fa-plus"></i> Abrir Nuevo Turno
                        </a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <table id="example1" class="table table-striped table-bordered table-hover table-sm">
                    <thead>
                        <tr>
                            <th style="text-align: center">Nro</th>
                            <th style="text-align: center">Usuario</th>
                            <th style="text-align: center">Fecha Inicio</th>
                            <th style="text-align: center">Base Caja</th>
                            <th style="text-align: center">Estado</th>
                            <th style="text-align: center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($turnos as $turno)
                            <tr>
                                <td style="text-align: center">{{ $loop->iteration }}</td>
                                <td style="text-align: center">{{ $turno->user->name }}</td>
                                <td style="text-align: center">{{ $turno->fecha_inicio }}</td>
                                <td style="text-align: center">${{ number_format($turno->base_caja, 0, ',', '.') }}</td>
                                <td style="text-align: center">

                                    @if($turno->estado == 'abierto')
                                        <span class="badge badge-primary">Abierto</span>
                                    @elseif(abs((float)$turno->total_descuadre_dinero) < 0.01)
                                        <span class="badge badge-success">Cerrado OK</span>
                                    @else
                                        <span class="badge badge-warning">Cerrado c/ Descuadre</span>
                                    @endif

                                </td>
                                <td style="text-align: center">
                                    <div style="display: flex; justify-content: center; align-items: center; gap: 8px;">
                                        <a href="{{ url('/admin/turnos/'.$turno->id) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> Ver</a>
                                        <form action="{{ url('/admin/turnos/'.$turno->id) }}" id="miformulario{{ $turno->id }}" method="POST" style="margin: 0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="preguntar{{ $turno->id }}(event)"><i class="fas fa-trash"></i> Eliminar</button>
                                        </form>
                                    </div>
                                    <script>
                                        function preguntar{{ $turno->id }}(event) {
                                            event.preventDefault();
                                            Swal.fire({
                                                title: "¿Desea eliminar este registro?",
                                                icon: "question",
                                                showCancelButton: true,
                                                confirmButtonColor: "#3085d6",
                                                cancelButtonColor: "#d33",
                                                confirmButtonText: "Sí, eliminar"
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    document.getElementById('miformulario{{ $turno->id }}').submit();
                                                }
                                            });
                                        }
                                    </script> 
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    <style>
    #example1_wrapper .dt-buttons {
        background-color: transparent;
        box-shadow: none;
        border: none;
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-bottom: 15px;
    }

    #example1_wrapper .btn {
        color: white;
        border-radius: 4px;
        padding: 5px 15px;
        font-size: 14px;
    }

    .btn-danger { background-color: #dc3545; border: none; }
    .btn-success { background-color: #28a745; border: none; }
    .btn-info { background-color: #17a2b8; border: none; }
    .btn-warning { background-color: #ffc107; color: #212529; border: none; }
    .btn-default { background-color: #6c757d; border: none; }
    </style>
@stop

@section('js')
    <script>
     $(function () {
        $("#example1").DataTable({
            "pageLength": 10,
            "language": {
                "emptyTable": "No hay información",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ Turnos",
                "infoEmpty": "Mostrando 0 a 0 de 0 Turnos",
                "infoFiltered": "(Filtrado de _MAX_ total Turnos)",
                "lengthMenu": "Mostrar _MENU_ Turnos",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscador:",
                "zeroRecords": "Sin resultados encontrados",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            buttons: [
                { text: '<i class="fas fa-copy"></i> COPIAR', extend: 'copy', className: 'btn btn-secondary' },
                { text: '<i class="fas fa-file-pdf"></i> PDF', extend: 'pdf', className: 'btn btn-danger' },
                { text: '<i class="fas fa-file-csv"></i> CSV', extend: 'csv', className: 'btn btn-info' },
                { text: '<i class="fas fa-file-excel"></i> EXCEL', extend: 'excel', className: 'btn btn-success' },
                { text: '<i class="fas fa-print"></i> IMPRIMIR', extend: 'print', className: 'btn btn-warning' }
            ]
        }).buttons().container().appendTo('#example1_wrapper .row:eq(0)');
    });
    </script>
@stop