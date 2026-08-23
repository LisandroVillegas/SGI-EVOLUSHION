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
            <div class="card-body">
                <table id="example1" class="table table-striped table-bordered table-hover table-sm">
                    <thead>
                        <tr>
                            <th style="text-align: center">Nro</th>
                            <th style="text-align: center">Código</th>
                            <th style="text-align: center">Categoría</th>
                            <th style="text-align: center">Nombre</th>
                            <th style="text-align: center">Precio Venta</th>
                            <th style="text-align: center">Stock</th>
                            <th style="text-align: center">Imagen</th>
                            <th style="text-align: center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($productos as $producto)
                            <tr>
                                <td style="text-align: center">{{ $loop->iteration }}</td>
                                <td style="text-align: center">{{ $producto->codigo }}</td>
                                <td style="text-align: center">{{ $producto->categoria->nombre }}</td>
                                <td style="text-align: center">{{ $producto->nombre }}</td>
                                <td style="text-align: center">${{ number_format($producto->precio_venta, 0, ',', '.') }}</td>
                                <td style="text-align: center">{{ $producto->stock }}</td>
                                <td style="text-align: center">
                                    @if($producto->imagen)
                                        <img src="{{ asset('storage/' . $producto->imagen) }}" width="40px" class="img-thumbnail">
                                    @else
                                        <span>Sin imagen</span>
                                    @endif
                                </td>
                                <td style="text-align: center">
                                    <div style="display: flex; justify-content: center; align-items: center; gap: 8px;">
                                        <a href="{{ url('/admin/producto/'.$producto->id) }}" class="btn btn-info"><i class="fas fa-eye"></i> Ver</a>
                                        <a href="{{ url('/admin/producto/'.$producto->id.'/edit') }}" class="btn btn-warning"><i class="fas fa-edit"></i> Editar</a>
                                        <form action="{{ url('/admin/producto/'.$producto->id) }}" id="miformulario{{ $producto->id }}" method="POST" style="margin: 0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" onclick="preguntar{{ $producto->id }}(event)"><i class="fas fa-trash"></i> Eliminar</button>
                                        </form>
                                    </div>
                                    <script>
                                        function preguntar{{ $producto->id }}(event) {
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
                                                    document.getElementById('miformulario{{ $producto->id }}').submit();
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
    /* Fondo transparente y sin borde en el contenedor */
    #example1_wrapper .dt-buttons {
        background-color: transparent;
        box-shadow: none;
        border: none;
        display: flex;
        justify-content: center; /* Centrar los botones */
        gap: 10px; /* Espaciado entre botones */
        margin-bottom: 15px; /* Separar botones de la tabla */
    }

    /* Estilo personalizado para los botones */
    #example1_wrapper .btn {
        color: white; /* Color del texto en blanco */
        border-radius: 4px; /* Bordes redondeados */
        padding: 5px 15px; /* Espaciado interno */
        font-size: 14px; /* TamaÃ±o de fuente */
    }

    /* Colores por tipo de botÃ³n */
    .btn-danger { background-color: #dc3545; border: none; }
    .btn-success { background-color: #28a745; border: none; }
    .btn-info { background-color: #17a2b8; border: none; }
    .btn-warning { background-color: #ffc107; color: #212529; border: none; }
    .btn-default { background-color: #6c757d;  border: none; }

    
</style>


@section('js')
   <script>
     $(function () {
        $("#example1").DataTable({
            "pageLength": 10,
            "language": {
                "emptyTable": "No hay informacion",
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
                    "last": "Ultimo",
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
