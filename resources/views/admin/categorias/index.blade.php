@extends('adminlte::page')

@section('content_header')
<nav aria-label="breadcrumb" style="font-size: 18pt">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ url('/admin/categorias') }}">Categorías</a></li>
    <li class="breadcrumb-item active" aria-current="page">Lista de Categorías</li>
  </ol>
</nav>
<hr>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Categorías Registradas</h3>
                <div class="card-tools">
                    <a class="btn btn-primary" href="{{ url('/admin/categorias/create') }}">Crear Nuevo</a>
                </div>
            </div>
            <div class="card-body">
                <table id="example1" class="table table-striped table-bordered table-hover table-sm">
                    <thead>
                        <tr>
                            <th style="text-align: center">Nro</th>
                            <th style="text-align: center">Nombre</th>
                            <th style="text-align: center">Descripción</th>
                            <th style="text-align: center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categorias as $categoria)
                            <tr>
                                <td style="text-align: center; vertical-align: middle;">{{ $loop->iteration }}</td>
                                <td style="text-align: center; vertical-align: middle;">{{ $categoria->nombre }}</td>
                                <td style="text-align: center; vertical-align: middle;">{{ $categoria->descripcion }}</td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <div style="display: flex; justify-content: center; align-items: center; gap: 4px;">
                                        <!-- Botón Ver con texto -->
                                        <a href="{{ url('/admin/categoria/'.$categoria->id) }}" class="btn btn-info btn-sm font-weight-bold shadow-sm" title="Ver">
                                            <i class="fas fa-eye mr-1"></i> Ver
                                        </a>
                                        
                                        <!-- Botón Editar con texto -->
                                        <a href="{{ url('/admin/categoria/'.$categoria->id.'/edit') }}" class="btn btn-warning btn-sm font-weight-bold shadow-sm" title="Editar">
                                            <i class="fas fa-edit mr-1"></i> Editar
                                        </a>

                                        <!-- Botón Eliminar con texto protegido por PIN (.form-secured) -->
                                        <form action="{{ url('/admin/categoria/'.$categoria->id) }}" method="POST" class="d-inline form-secured" data-secured-message="¿Desea eliminar esta categoría?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm font-weight-bold shadow-sm text-white" title="Eliminar">
                                                <i class="fas fa-trash-alt mr-1"></i> Eliminar
                                            </button>
                                        </form>
                                    </div>
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
    #example1_wrapper .dt-buttons .btn {
        color: #fff;
        border-radius: 4px;
        padding: 5px 15px;
        font-size: 14px;
    }
    .btn-danger { background-color: #dc3545; border: none; }
    .btn-success { background-color: #28a745; border: none; }
    .btn-info { background-color: #17a2b8; border: none; }
    .btn-warning { background-color: #ffc107; color: #212529; border: none; }
    .btn-default { background-color: #6e7176; color: #212529; border: none; }
    </style>
@stop

@section('js')
@include('admin.partials.pin-security')
<script>
    $(function () {
        $("#example1").DataTable({
            "pageLength": 10,
            "language": {
                "emptyTable": "No hay información",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ Categorías",
                "infoEmpty": "Mostrando 0 a 0 de 0 Categorías",
                "infoFiltered": "(Filtrado de _MAX_ total Categorías)",
                "lengthMenu": "Mostrar _MENU_ Categorías",
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
                { text: '<i class="fas fa-copy"></i> COPIAR', extend: 'copy', className: 'btn btn-default' },
                { text: '<i class="fas fa-file-pdf"></i> PDF', extend: 'pdf', className: 'btn btn-danger' },
                { text: '<i class="fas fa-file-csv"></i> CSV', extend: 'csv', className: 'btn btn-info' },
                { text: '<i class="fas fa-file-excel"></i> EXCEL', extend: 'excel', className: 'btn btn-success' },
                { text: '<i class="fas fa-print"></i> IMPRIMIR', extend: 'print', className: 'btn btn-warning' }
            ]
        }).buttons().container().appendTo('#example1_wrapper .row:eq(0)');
    });
</script>
@stop