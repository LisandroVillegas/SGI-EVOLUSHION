<?php
$files = [
    'turnos' => 'resources/views/admin/turnos/index.blade.php',
    'productos' => 'resources/views/admin/productos/index.blade.php',
    'categorias' => 'resources/views/admin/categorias/index.blade.php',
    'compras' => 'resources/views/admin/compras/index.blade.php',
];

foreach ($files as $name => $file) {
    $c = file_get_contents($file);
    echo "=== $name ===\n";
    // Table class
    preg_match('/<table[^>]*id="example1"[^>]*>/', $c, $m);
    echo "TABLE: " . ($m[0] ?? 'NO TABLE') . "\n";
    // SweetAlert2 include
    echo "SweetAlert2 CDN: " . (strpos($c, 'sweetalert2') !== false ? 'YES' : 'NO') . "\n";
    // @stop after css
    preg_match("/@section\('css'\)(.*?)@stop/s", $c, $m);
    echo "@stop after css: " . (!empty($m[0]) ? 'YES' : 'NO (MISSING!)') . "\n";
    echo "\n";
}
