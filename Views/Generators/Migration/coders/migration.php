<?php

include __DIR__ . '/_shared.php';

$migrations = new \App\Libraries\Migrations('frontend', $oid);
$code_migration = $migrations->generate($oid);

$code = "<?php\n";
$code .= get_development_code_copyright(['path' => $g->namespaced]);
$code .= $code_migration;
$code .= "?>\n";
echo($code);
