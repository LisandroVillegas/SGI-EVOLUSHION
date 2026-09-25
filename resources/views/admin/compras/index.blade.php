@extends('adminlte::page')

@section('content_header')
<nav aria-label="breadcrumb" style="font-size: 18pt">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ url('/admin/compras') }}">Compras</a></li>
    <li class="breadcrumb-item active" aria-current="page">Lista de Compras</li>
  </ol>
</nav>
<hr>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Compras / Ingresos Registrados</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-warning font-weight-bold mr-2 shadow-sm" data-toggle="modal" data-target="#modalCompraRapida">
                        <i class="fas fa-bolt mr-1"></i> Compra Rápida / Egreso
                    </button>
                    <a class="btn btn-primary font-weight-bold shadow-sm" href="{{ url('/admin/compras/create') }}">
                        <i class="fas fa-plus mr-1"></i> Crear Nuevo (Inventariable)
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table id="example1" class="table table-striped table-bordered table-hover table-sm">
                    <thead>
                        <tr>
                            <th style="text-align: center">Nro</th>
                            <th style="text-align: center">Comprobante</th>
                            <th style="text-align: center">Tipo</th>
                            <th style="text-align: center">Detalle / Concepto</th>
                            <th style="text-align: center">Fecha</th>
                            <th style="text-align: center">Total</th>
                            <th style="text-align: center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($compras as $compra)
                            <tr>
                                <td style="text-align: center; vertical-align: middle;">{{ $loop->iteration }}</td>
                                <td style="text-align: center; vertical-align: middle;">{{ $compra->comprobante ?? 'S/N' }}</td>
                                <td style="text-align: center; vertical-align: middle;">
                                    @if (($compra->tipo ?? 'normal') === 'rapida')
                                        <span class="badge badge-warning p-2"><i class="fas fa-bolt mr-1"></i> Rápida</span>
                                    @else
                                        <span class="badge badge-info p-2"><i class="fas fa-boxes mr-1"></i> Inventario</span>
                                    @endif
                                </td>
                                <td style="text-align: left; vertical-align: middle;">
                                    @if (($compra->tipo ?? 'normal') === 'rapida')
                                        <strong>{{ $compra->concepto ?? 'Sin concepto registrado' }}</strong>
                                    @else
                                        @forelse ($compra->detalles as $det)
                                            <small class="d-block">• {{ $det->cantidad }}x {{ $det->producto->nombre ?? 'Producto Eliminado' }}</small>
                                        @empty
                                            <small class="text-muted">Sin detalles de productos</small>
                                        @endforelse
                                    @endif
                                </td>
                                <td style="text-align: center; vertical-align: middle;">{{ $compra->fecha }}</td>
                                <td style="text-align: center; vertical-align: middle;" class="font-weight-bold text-success">
                                    ${{ number_format($compra->total, 0, ',', '.') }}
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <div style="display: flex; justify-content: center; align-items: center; gap: 4px;">
                                        <!-- Botón Ver con texto -->
                                        <a href="{{ url('/admin/compras/'.$compra->id) }}" class="btn btn-info btn-sm font-weight-bold shadow-sm" title="Ver">
                                            <i class="fas fa-eye mr-1"></i> Ver
                                        </a>
                                        
                                        <!-- Botón Eliminar con texto protegido por PIN (.form-secured) -->
                                        <form action="{{ url('/admin/compras/'.$compra->id) }}" method="POST" class="d-inline form-secured" data-secured-message="&iquest;Desea eliminar esta compra?">
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

<!-- Modal Compra Rápida / Egreso Operativo -->
<div class="modal fade" id="modalCompraRapida" tabindex="-1" role="dialog" aria-labelledby="modalCompraRapidaLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title font-weight-bold" id="modalCompraRapidaLabel">
                    <i class="fas fa-bolt mr-1"></i> Registrar Compra Rápida / Egreso
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('compras.storeRapida') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="concepto">¿En qué se gastó el dinero? (Concepto) <span class="text-danger">*</span></label>
                        <input type="text" name="concepto" id="concepto" class="form-control" placeholder="Ej: Bolsas de hielo, servilletas, aseo..." required>
                    </div>

                    <div class="form-group">
                        <label for="total">Monto Gastado ($) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                            </div>
                            <input type="number" step="any" min="0" name="total" id="total" class="form-control" placeholder="0" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="fecha">Fecha <span class="text-danger">*</span></label>
                        <input type="date" name="fecha" id="fecha" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning font-weight-bold">
                        <i class="fas fa-save mr-1"></i> Guardar Egreso
                    </button>
                </div>
            </form>
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
@include('admin.partials.pin-security')

<script>
    $(function () {
        $("#example1").DataTable({
            "pageLength": 10,
            "language": {
                "emptyTable": "No hay información",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ Compras",
                "infoEmpty": "Mostrando 0 a 0 de 0 Compras",
                "infoFiltered": "(Filtrado de _MAX_ total Compras)",
                "lengthMenu": "Mostrar _MENU_ Compras",
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