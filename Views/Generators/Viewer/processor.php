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
$cform = $f->get_Value('cform');
$cprocessor = $f->get_Value('cprocessor');
$cvalidator = $f->get_Value('cvalidator');
$cbreadcrumb = $f->get_Value('cbreadcrumb');

$files = new Files();
$files->mkDir($pathfiles);
try {
    chmod($pathfiles, 0775);
} catch (\Throwable $e) {
    // Ignore permission errors when chmod fails
}

// Archivos a crear con permisos editables
$generatedFiles = [
    "{$pathfiles}/index.php" => urldecode($cindex),
    "{$pathfiles}/deny.php" => urldecode($cdeny),
    "{$pathfiles}/form.php" => urldecode($cform),
    "{$pathfiles}/processor.php" => urldecode($cprocessor),
    "{$pathfiles}/validator.php" => urldecode($cvalidator),
    "{$pathfiles}/breadcrumb.php" => urldecode($cbreadcrumb),
];

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
//[processing]----------------------------------------------------------------------------------------------------------
//$row = $model->find($d["client"]);
if (isset($row['client'])) {
    $c = $bootstrap->get_Card('warning', [
        'class' => 'card-warning',
        'icon' => 'fa-duotone fa-triangle-exclamation',
        'text-class' => 'text-center',
        'title' => lang('Development.viewer-warning-title'),
        'text' => lang('Development.viewer-warning-text'),
        'footer-class' => 'text-center',
        'footer-continue' => base_url('/development/generators/list/' . lpk()),
        'voice' => 'development/viewer-create-warning-message.mp3',
    ]);
} else {
    //$create = $model->insert($d);
    //
    $c = $bootstrap->get_Card('success', [
        'class' => 'card-success',
        'icon' => 'fa-duotone fa-triangle-exclamation',
        'text-class' => 'text-center',
        'title' => lang('Development.viewer-success-title'),
        'text' => lang('Development.viewer-success-text'),
        'footer-class' => 'text-center',
        'footer-continue' => base_url('/development/generators/list/' . lpk()),
        'voice' => 'development/viewer-success-text.mp3',
    ]);
}
echo($c);
