{{--
    PARCIAL REUTILIZABLE: _datatables.blade.php
    Uso: @include('admin.partials._datatables', ['tableId' => 'miTabla', 'entidad' => 'Productos'])
    Parámetros:
        - $tableId   (string)  : ID del <table>. Default: 'example1'
        - $entidad   (string)  : Nombre de entidad para textos. Default: 'Registros'
        - $conBotones (bool)   : Muestra botones de exportación (Copiar, PDF, CSV, Excel, Imprimir).
                                 true  → módulos de reporte/financieros (Ventas, Compras, Turnos).
                                 false → módulos configurativos/administrativos (Categorías, Productos, Promociones).
                                 Default: true
--}}
@php
    $tableId    = $tableId    ?? 'example1';
    $entidad    = $entidad    ?? 'Registros';
    $conBotones = $conBotones ?? true;
@endphp

@push('css')
@if($conBotones)
<style>


    #{{ $tableId }}_wrapper .dt-buttons {
        background-color: transparent;
        box-shadow: none;
        border: none;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 6px;
        margin-bottom: 15px;
    }
    #{{ $tableId }}_wrapper .dt-buttons .btn {
        color: #fff;
        border-radius: 4px;
        padding: 5px 15px;
        font-size: 13px;
    }
    #{{ $tableId }}_wrapper .dt-buttons .btn-dt-default { background-color: #6e7176 !important; color: #fff !important; }
    @media (max-width: 575.98px) {
        .btn-accion-texto { display: none !important; }
        .btn-sm { padding: 4px 8px; }
        #{{ $tableId }}_wrapper .dt-buttons .btn { font-size: 11px; padding: 4px 8px; }
    }
</style>
@else
<style>
    @media (max-width: 575.98px) {
        .btn-accion-texto { display: none !important; }
        .btn-sm { padding: 4px 8px; }
    }
</style>
@endif
@endpush

@push('js')
<script>
$(function () {
    var dtConfig = {
        "pageLength": 10,
        "scrollX": true,
        "responsive": false,
        "lengthChange": true,
        "autoWidth": false,
        "order": [],
        "language": {
            "emptyTable"    : "No hay {{ strtolower($entidad) }} registrados aún.",
            "info"          : "Mostrando _START_ a _END_ de _TOTAL_ {{ strtolower($entidad) }}",
            "infoEmpty"     : "Mostrando 0 a 0 de 0 {{ strtolower($entidad) }}",
            "infoFiltered"  : "(filtrado de _MAX_ {{ strtolower($entidad) }})",
            "lengthMenu"    : "Mostrar _MENU_ {{ strtolower($entidad) }}",
            "loadingRecords": "Cargando...",
            "processing"    : "Procesando...",
            "search"        : "Buscar:",
            "zeroRecords"   : "Sin resultados encontrados",
            "paginate": {
                "first": "Primero", "last": "Último",
                "next": "Siguiente", "previous": "Anterior"
            }
        }
        @if($conBotones)
        ,
        dom: 'Bfrtip',
        buttons: [
            { text: '<i class="fas fa-copy"></i> COPIAR',    extend: 'copy',  className: 'btn btn-dt-default' },
            { text: '<i class="fas fa-file-pdf"></i> PDF',   extend: 'pdf',   className: 'btn btn-danger' },
            { text: '<i class="fas fa-file-csv"></i> CSV',   extend: 'csv',   className: 'btn btn-info' },
            { text: '<i class="fas fa-file-excel"></i> XLS', extend: 'excel', className: 'btn btn-success' },
            { text: '<i class="fas fa-print"></i> IMPRIMIR', extend: 'print', className: 'btn btn-warning' }
        ]
        @endif
    };

    var table = $("#{{ $tableId }}").DataTable(dtConfig);

    @if($conBotones)
    table.buttons().container().appendTo('#{{ $tableId }}_wrapper .row:eq(0)');
    @endif
});
</script>
@endpush
