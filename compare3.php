<?php
$p = file_get_contents('resources/views/admin/productos/index.blade.php');
// Show all CSS and style blocks
preg_match_all('/<style[^>]*>(.*?)<\/style>/s', $p, $m);
echo "=== ALL STYLE BLOCKS IN PRODUCTOS ===\n";
foreach ($m[0] as $i => $block) {
    echo "--- Block $i ---\n";
    echo $block . "\n";
}
echo "\n=== FULL JS SECTION ===\n";
preg_match("/@section\('js'\)(.*?)@stop/s", $p, $m);
echo $m[1] ?? 'NO JS SECTION';

echo "\n\n=== FULL FILE (lines 70-130) ===\n";
$lines = explode("\n", $p);
for ($i = 65; $i < min(130, count($lines)); $i++) {
    echo ($i+1) . ": " . $lines[$i] . "\n";
}
