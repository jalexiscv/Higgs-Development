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
//[Processing]----------------------------------------------------------------------------------------------------------
$c = $bootstrap->get_Card('success', [
    'class' => 'card-success',
    'icon' => 'fa-duotone fa-triangle-exclamation',
    'text-class' => 'text-center',
    'title' => lang('Development.deleter-success-title'),
    'text' => lang('Development.deleter-success-text'),
    'footer-class' => 'text-center',
    'footer-continue' => base_url('/development/generators/list/' . lpk()),
    'voice' => 'development/deleter-success-message.mp3',
]);
echo($c);
