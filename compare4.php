<?php
$files = [
    'categorias' => 'resources/views/admin/categorias/index.blade.php',
    'productos' => 'resources/views/admin/productos/index.blade.php',
    'compras' => 'resources/views/admin/compras/index.blade.php',
];
foreach ($files as $name => $file) {
    $c = file_get_contents($file);
    $lines = explode("\n", $c);
    echo "=== $name - LINEAS TOTALES: " . count($lines) . " ===\n";
    // Buscar scripts por fila (preguntar/miformulario)
    echo "Old 'preguntar' scripts: " . (strpos($c, 'preguntar') !== false ? 'YES (OLD SCRIPTS STILL THERE!)' : 'NO') . "\n";
    echo "Old 'miformulario': " . (strpos($c, 'miformulario') !== false ? 'YES (OLD FORMS STILL THERE!)' : 'NO') . "\n";
    echo "SweetAlert CDN: " . (strpos($c, 'sweetalert2') !== false ? 'YES' : 'NO') . "\n";
    // Imprimir todo el archivo si es pequenio
    echo "--- FULL FILE ---\n";
    echo $c . "\n";
    echo "=== END $name ===\n\n";
}
