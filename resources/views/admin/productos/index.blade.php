@extends('adminlte::page')

@section('content_header')
<nav aria-label="breadcrumb" style="font-size: 18pt">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ url('/admin/productos') }}">Productos</a></li>
    <li class="breadcrumb-item active" aria-current="page">Lista de Productos</li>
  </ol>
</nav>
<hr>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Productos / Cócteles Registrados</h3>
                <div class="card-tools">
                    <a class="btn btn-primary" href="{{ url('/admin/productos/create') }}">Crear Nuevo</a>
                </div>
            </div>
            <div class="card-body p-0 p-md-3">
                <div class="table-responsive">
                <table id="example1" class="table table-striped table-bordered table-hover table-sm">
                    <thead>
                        <tr>
                            <th style="text-align: center">Nro</th>
                            <th style="text-align: center">Código</th>
                            <th style="text-align: center">Categoría</th>
                            <th style="text-align: center">Nombre</th>
                            <th style="text-align: center">Precio Venta</th>
                            <th style="text-align: center">Stock</th>
                            <th style="text-align: center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($productos as $producto)
                            <tr>
                                <td style="text-align: center; vertical-align: middle;">{{ $loop->iteration }}</td>
                                <td style="text-align: center; vertical-align: middle;">{{ $producto->codigo }}</td>
                                <td style="text-align: center; vertical-align: middle;">{{ $producto->categoria->nombre ?? 'Sin categoría' }}</td>
                                <td style="text-align: center; vertical-align: middle;">{{ $producto->nombre }}</td>
                                <td style="text-align: center; vertical-align: middle;">${{ number_format($producto->precio_venta, 0, ',', '.') }}</td>
                                <td style="text-align: center; vertical-align: middle;">{{ $producto->stock }}</td>
                        
                                <td style="text-align: center; vertical-align: middle;">
                                    <div style="display: flex; justify-content: center; align-items: center; gap: 4px;">
                                        <a href="{{ url('/admin/producto/'.$producto->id) }}" class="btn btn-info btn-sm font-weight-bold shadow-sm" title="Ver">
                                            <i class="fas fa-eye"></i><span class="btn-accion-texto ml-1"> Ver</span>
                                        </a>
                                        <a href="{{ url('/admin/producto/'.$producto->id.'/edit') }}" class="btn btn-warning btn-sm font-weight-bold shadow-sm" title="Editar">
                                            <i class="fas fa-edit"></i><span class="btn-accion-texto ml-1"> Editar</span>
                                        </a>
                                        <form action="{{ url('/admin/producto/'.$producto->id) }}" method="POST" class="d-inline form-secured" data-secured-message="&iquest;Desea eliminar este producto?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm font-weight-bold shadow-sm text-white" title="Eliminar">
                                                <i class="fas fa-trash-alt"></i><span class="btn-accion-texto ml-1"> Eliminar</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div><!-- /.table-responsive -->
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
        flex-wrap: wrap;
        justify-content: center;
        gap: 6px;
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

    /* === RESPONSIVE MÓVIL: botones de acción solo muestran ícono === */
    @media (max-width: 575.98px) {
        .btn-accion-texto { display: none; }
        .btn-sm { padding: 4px 8px; }
        #example1_wrapper .dt-buttons .btn { font-size: 12px; padding: 4px 8px; }
        .card-header .card-tools { margin-top: 6px; }
    }
    </style>
@stop

@section('js')
@include('admin.partials.pin-security')
<script>
    $(function () {
        $("#example1").DataTable({
            "pageLength": 10,
            "scrollX": true,
            "language": {
                "emptyTable": "No hay información",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ Productos",
                "infoEmpty": "Mostrando 0 a 0 de 0 Productos",
                "infoFiltered": "(Filtrado de _MAX_ total Productos)",
                "lengthMenu": "Mostrar _MENU_ Productos",
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
            "responsive": false,
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