<?php

/** @var string $component */

use App\Libraries\Files;

$bootstrap = service('bootstrap');
$f = service('forms', ['lang' => 'Nexus.']);
$model = model("App\Models\Application_Clients");
/*
 * -----------------------------------------------------------------------------
 * [Request]
 * -----------------------------------------------------------------------------
*/
$pathfiles = $f->get_Value('pathfiles');
$cindex = $f->get_Value('cindex');
$cdeny = $f->get_Value('cdeny');
//$ctable = $f->get_Value("ctable");
//$cjson = $f->get_Value("cjson");
$cgrid = $f->get_Value('cgrid');
$cbreadcrumb = $f->get_Value('cbreadcrumb');

$files = new Files();
$files->mkDir($pathfiles);

// Archivos a crear con permisos editables
$generatedFiles = [
    "{$pathfiles}/index.php" => urldecode($cindex),
    "{$pathfiles}/deny.php" => urldecode($cdeny),
    "{$pathfiles}/grid.php" => urldecode($cgrid),
    "{$pathfiles}/breadcrumb.php" => urldecode($cbreadcrumb),
];

// Asignar permisos al directorio
try {
    chmod($pathfiles, 0775);
} catch (\Throwable $e) {
    // Ignore permission errors when chmod fails
}

// Escribir archivos y asignar permisos de escritura
foreach ($generatedFiles as $filepath => $content) {
    $files->open($filepath, 'writeOnly')->write($content);
    try {
        chmod($filepath, 0664);
    } catch (\Throwable $e) {
        // Ignore permission errors when chmod fails
    }
}
//$c = ("<b>Archivo creado</b>: {$relative}");
/*
 * -----------------------------------------------------------------------------
 * [Processing]
 * -----------------------------------------------------------------------------
*/
//$row = $model->find($d["client"]);
if (isset($row['client'])) {
    $c = $bootstrap->get_Card('warning', [
        'class' => 'card-warning',
        'icon' => 'fa-duotone fa-triangle-exclamation',
        'text-class' => 'text-center',
        'title' => lang('Development.lister-warning-title'),
        'text' => lang('Development.lister-warning-text'),
        'footer-class' => 'text-center',
        'footer-continue' => base_url('/development/generators/list/' . lpk()),
        'voice' => 'development/lister-create-warning-message.mp3',
    ]);
} else {
    //$create = $model->insert($d);
    //
    $c = $bootstrap->get_Card('success', [
        'class' => 'card-success',
        'icon' => 'fa-duotone fa-triangle-exclamation',
        'text-class' => 'text-center',
        'title' => lang('Development.lister-success-title'),
        'text' => lang('Development.lister-success-text'),
        'footer-class' => 'text-center',
        'footer-continue' => base_url('/development/generators/list/' . lpk()),
        'voice' => 'development/lister-success-message.mp3',
    ]);
}
echo($c);
