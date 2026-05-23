<?php

include __DIR__ . '/_shared.php';

$code = "<?php\n";
$code .= get_development_code_copyright(['path' => $g->namespaced . 'breadcrumb.php']);
$code .= "use Higgs\\Frontend\\Bootstrap\\v5_3_3\\Bootstrap as BS5;\n";
$code .= "echo BS5::breadcrumb(['items' => [\n";
$code .= "    ['label' => '{$g->slc_module}', 'href' => '/{$g->slc_module}/'],\n";
$code .= "    ['label' => lang('App.{$g->slc_component}'), 'href' => '/{$g->slc_module}/{$g->slc_component}/home/'.lpk(), 'active' => true],\n";
$code .= "]]);\n";
$code .= '?>';

echo($code);
