<?php

/*
 * -----------------------------------------------------------------------------
 *  ╔═╗╔╗╔╔═╗╔═╗╦╔╗ ╦  ╔═╗
 *  ╠═╣║║║╚═╗╚═╗║╠╩╗║  ║╣  [FRAMEWORK]
 *  ╩ ╩╝╚╝╚═╝╚═╝╩╚═╝╩═╝╚═╝
 * -----------------------------------------------------------------------------
 * Copyright 2021 - Higgs Bigdata S.A.S., Inc. <admin@Higgs.com>
 * Este archivo es parte de Higgs Bigdata Framework 7.1
 * Para obtener información completa sobre derechos de autor y licencia, consulte
 * la LICENCIA archivo que se distribuyó con este código fuente.
 * -----------------------------------------------------------------------------
 * EL SOFTWARE SE PROPORCIONA -TAL CUAL-, SIN GARANTÍA DE NINGÚN TIPO, EXPRESA O
 * IMPLÍCITA, INCLUYENDO PERO NO LIMITADO A LAS GARANTÍAS DE COMERCIABILIDAD,
 * APTITUD PARA UN PROPÓSITO PARTICULAR Y NO INFRACCIÓN. EN NINGÚN CASO SERÁ
 * LOS AUTORES O TITULARES DE LOS DERECHOS DE AUTOR SERÁN RESPONSABLES DE CUALQUIER
 * RECLAMO, DAÑOS U OTROS RESPONSABILIDAD, YA SEA EN UNA ACCIÓN DE CONTRATO,
 * AGRAVIO O DE OTRO MODO, QUE SURJA DESDE, FUERA O EN RELACIÓN CON EL SOFTWARE
 * O EL USO U OTROS NEGOCIACIONES EN EL SOFTWARE.
 * -----------------------------------------------------------------------------
 * @Author Jose Alexis Correa Valencia <jalexiscv@gmail.com>
 * @link https://www.Higgs.com
 * @Version 1.5.0
 * @since PHP 7, PHP 8
 * -----------------------------------------------------------------------------
 * Datos recibidos desde el controlador - @ModuleController
 * -----------------------------------------------------------------------------
 * @Authentication
 * @request
 * @dates
 * @view
 * @oid
 * @component
 * @views
 * @prefix
 * @parent
 * -----------------------------------------------------------------------------
 */

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
