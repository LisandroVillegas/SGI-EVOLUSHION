@extends('adminlte::page')



@section('content_header')
<nav aria-label="breadcrumb" style="font-size: 18pt">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ url('/admin/categorias') }}">Categorias</a></li>
    <li class="breadcrumb-item active" aria-current="page">Lista de Categorias</li>
  </ol>
</nav>
<hr>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
                <div class="card card-outline card-primary">
                  <div class="card-header">
                    <h3 class="card-title">Categorias Registrados</h3>

                    <div class="card-tools">
                      <a  class="btn btn-primary" href="{{ url('/admin/categorias/create') }}">Crear Nuevo</a>
                    </div>
                    <!-- /.card-tools -->
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body" style="box-sizing: border-box; display: block;">

                    <table id="example1" class="table table-striped table-bordered table-hover table-sm">
    <thead>
        <tr>
            <th style="text-align: center">Nro</th>
            <th style="text-align: center">Nombre</th>
            <th style="text-align: center">Descripcion</th>
            <th style="text-align: center">Acciones</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($categorias as $categoria)
            <tr>
                <td style="text-align: center">{{ $loop->iteration }}</td>
                <td style="text-align: center">{{ $categoria->nombre }}</td>
                <td style="text-align: center">{{ $categoria->descripcion }}</td>

                <td style="text-align: center">

                    <div style="display: flex; justify-content: center; align-items: center; gap: 8px;">

                        {{-- BOTÓN VER --}}
                        <a href="{{ url('/admin/categoria/'.$categoria->id) }}"
                           class="btn btn-info">
                            <i class="fas fa-eye"></i> Ver
                        </a>

                        {{-- BOTÓN EDITAR --}}
                        <a href="{{ url('/admin/categoria/'.$categoria->id.'/edit') }}"
                           class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>

                        {{-- BOTÓN ELIMINAR --}}
                        <form action="{{ url('/admin/categoria/'.$categoria->id) }}"
                              id="miformulario{{ $categoria->id }}"
                              method="POST"
                              style="margin: 0;">

                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="btn btn-danger"
                                    onclick="preguntar{{ $categoria->id }}(event)">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>

                        </form>

                    </div>

                    <script>
                        function preguntar{{ $categoria->id }}(event) {

                            event.preventDefault();

                            Swal.fire({
                                title: "¿Desea eliminar este registro?",
                                text: "",
                                icon: "question",
                                showCancelButton: true,
                                confirmButtonColor: "#3085d6",
                                cancelButtonColor: "#d33",
                                confirmButtonText: "Si, eliminar",
                                denyButtonText: "No, cancelar"
                            }).then((result) => {

                                if (result.isConfirmed) {

                                    document.getElementById(
                                        'miformulario{{ $categoria->id }}'
                                    ).submit();

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
                  <!-- /.card-body -->
                </div>
                <!-- /.card -->
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
        color: #fff; /* Color del texto en blanco */
        border-radius: 4px; /* Bordes redondeados */
        padding: 5px 15px; /* Espaciado interno */
        font-size: 14px; /* TamaÃ±o de fuente */
    }

    /* Colores por tipo de botÃ³n */
    .btn-danger { background-color: #dc3545; border: none; }
    .btn-success { background-color: #28a745; border: none; }
    .btn-info { background-color: #17a2b8; border: none; }
    .btn-warning { background-color: #ffc107; color: #212529; border: none; }
    .btn-default { background-color: #6e7176; color: #212529; border: none; }

    
</style>

@stop

@section('js')
   <script>
     $(function () {
        $("#example1").DataTable({
            "pageLength": 10,
            "language": {
                "emptyTable": "No hay informacion",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ Categorias",
                "infoEmpty": "Mostrando 0 a 0 de 0 Categorias",
                "infoFiltered": "(Filtrado de _MAX_ total Categorias)",
                "lengthMenu": "Mostrar _MENU_ Categorias",
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
