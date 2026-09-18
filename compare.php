<?php
// Comparar seccion de CSS y acciones entre turnos y productos

echo "=== CSS SECTION - TURNOS ===\n";
$t = file_get_contents('resources/views/admin/turnos/index.blade.php');
preg_match("/@section\('css'\)(.*?)@stop/s", $t, $m);
echo $m[1] ?? 'NO CSS SECTION';
echo "\n\n";

echo "=== CSS SECTION - PRODUCTOS ===\n";
$p = file_get_contents('resources/views/admin/productos/index.blade.php');
preg_match("/@section\('css'\)(.*?)@stop/s", $p, $m);
echo $m[1] ?? 'NO CSS SECTION';
echo "\n\n";

echo "=== ACTION BUTTONS - TURNOS ===\n";
preg_match('/<td style="text-align: center">\s*<div style="display: flex(.*?)<\/div>\s*<\/td>/s', $t, $m);
echo $m[0] ?? 'NO MATCH';
echo "\n\n";

echo "=== ACTION BUTTONS - PRODUCTOS ===\n";
preg_match('/<td style="text-align: center">\s*<div style="display: flex(.*?)<\/div>/s', $p, $m);
echo $m[0] ?? 'NO MATCH';
echo "\n\n";

echo "=== FULL PRODUCTOS ACTIONS TD ===\n";
// Buscar el ultimo td de la fila de productos (acciones)
preg_match_all('/<td style="text-align: center">(.*?)<\/td>/s', $p, $ms);
foreach ($ms[0] as $td) {
    if (strpos($td, 'btn-danger') !== false) {
        echo $td;
        echo "\n";
    }
}
