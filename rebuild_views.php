<?php
// CSS base compartido (igual que turnos)
$cssBase = '
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

    /* Forzar tamano correcto en botones de acciones de tabla */
    #example1 .btn.btn-sm {
        padding: .25rem .5rem !important;
        font-size: .875rem !important;
        line-height: 1.5 !important;
        display: inline-block !important;
    }

    .btn-danger { background-color: #dc3545; border: none; }
    .btn-success { background-color: #28a745; border: none; }
    .btn-info    { background-color: #17a2b8; border: none; }
    .btn-warning { background-color: #ffc107; color: #212529; border: none; }
    .btn-default { background-color: #6c757d; border: none; }
    </style>';

// JS DataTables base compartido
function dtInit($modelName) {
    return '    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    $(function () {
        $("#example1").DataTable({
            "pageLength": 10,
            "language": {
                "emptyTable": "No hay informacion",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ ' . $modelName . '",
                "infoEmpty": "Mostrando 0 a 0 de 0 ' . $modelName . '",
                "infoFiltered": "(Filtrado de _MAX_ total ' . $modelName . ')",
                "lengthMenu": "Mostrar _MENU_ ' . $modelName . '",
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
                { text: \'<i class="fas fa-copy"></i> COPIAR\', extend: \'copy\', className: \'btn btn-secondary\' },
                { text: \'<i class="fas fa-file-pdf"></i> PDF\', extend: \'pdf\', className: \'btn btn-danger\' },
                { text: \'<i class="fas fa-file-csv"></i> CSV\', extend: \'csv\', className: \'btn btn-info\' },
                { text: \'<i class="fas fa-file-excel"></i> EXCEL\', extend: \'excel\', className: \'btn btn-success\' },
                { text: \'<i class="fas fa-print"></i> IMPRIMIR\', extend: \'print\', className: \'btn btn-warning\' }
            ]
        }).buttons().container().appendTo(\'#example1_wrapper .row:eq(0)\');
    });
    </script>';
}

// ---- Funcion que aplica los cambios al CSS y JS de cada blade ----
function fixBlade($file, $modelName, $cssBase) {
    $c = file_get_contents($file);
    if (!$c) { echo "[ERR] No se pudo leer $file\n"; return; }

    // Reemplazar @section('css') ... @stop con el nuevo CSS
    $c = preg_replace(
        "/@section\('css'\).*?@stop/s",
        "@section('css')\n" . $cssBase . "\n@stop",
        $c
    );

    // Asegurar SweetAlert2 CDN en JS section y remover @stop extra del css que quedara solo
    // Agregar CDN justo despues del include si no existe
    if (strpos($c, 'sweetalert2') === false) {
        $c = str_replace(
            "@include('admin.partials.pin-security')",
            "@include('admin.partials.pin-security')\n    <script src=\"https://cdn.jsdelivr.net/npm/sweetalert2@11\"></script>",
            $c
        );
    }

    // Quitar width:1% del th Acciones (que puede forzar columna muy estrecha)
    $c = str_replace(
        '<th style="text-align: center; width: 1%; white-space: nowrap;">Acciones</th>',
        '<th style="text-align: center">Acciones</th>',
        $c
    );

    file_put_contents($file, $c);
    echo "[OK] $file\n";
}

$cssBase = str_replace("'", "\\'", $cssBase); // escapar comillas simples para PHP heredoc
global $cssBase;

fixBlade('resources/views/admin/categorias/index.blade.php', 'Categorias', $cssBase);
fixBlade('resources/views/admin/productos/index.blade.php',  'Productos',  $cssBase);
fixBlade('resources/views/admin/compras/index.blade.php',    'Compras',    $cssBase);
fixBlade('resources/views/admin/ventas/index.blade.php',     'Ventas',     $cssBase);

echo "\n[DONE]\n";
