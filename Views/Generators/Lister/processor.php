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

$bootstrap = service("bootstrap");
$f = service("forms", array("lang" => "Nexus."));
$model = model("App\Models\Application_Clients");
/*
 * -----------------------------------------------------------------------------
 * [Request]
 * -----------------------------------------------------------------------------
*/
$pathfiles = $f->get_Value("pathfiles");
$cindex = $f->get_Value("cindex");
$cdeny = $f->get_Value("cdeny");
//$ctable = $f->get_Value("ctable");
//$cjson = $f->get_Value("cjson");
$cgrid = $f->get_Value("cgrid");
$cbreadcrumb = $f->get_Value("cbreadcrumb");


$files = new Files();
$files->mkDir($pathfiles);
$files->open("{$pathfiles}/index.php", "writeOnly")->write(urldecode($cindex));
$files->open("{$pathfiles}/deny.php", "writeOnly")->write(urldecode($cdeny));
$files->open("{$pathfiles}/grid.php", "writeOnly")->write(urldecode($cgrid));
//$files->open("{$pathfiles}/table.php", "writeOnly")->write(urldecode($ctable));
//$files->open("{$pathfiles}/json.php", "writeOnly")->write(urldecode($cjson));
$files->open("{$pathfiles}/breadcrumb.php", "writeOnly")->write(urldecode($cbreadcrumb));
//$c = ("<b>Archivo creado</b>: {$relative}");
/*
 * -----------------------------------------------------------------------------
 * [Processing]
 * -----------------------------------------------------------------------------
*/
//$row = $model->find($d["client"]);
if (isset($row["client"])) {
    $c = $bootstrap->get_Card('warning', array(
        'class' => 'card-warning',
        'icon' => 'fa-duotone fa-triangle-exclamation',
        'text-class' => 'text-center',
        "title" => lang("Development.lister-warning-title"),
        'text' => lang("Development.lister-warning-text"),
        'footer-class' => 'text-center',
        'footer-continue' => base_url("/development/generators/list/" . lpk()),
        'voice' => "development/lister-create-warning-message.mp3",
    ));
} else {
    //$create = $model->insert($d);
    //
    $c = $bootstrap->get_Card('success', array(
        'class' => 'card-success',
        'icon' => 'fa-duotone fa-triangle-exclamation',
        'text-class' => 'text-center',
        "title" => lang("Development.lister-success-title"),
        'text' => lang("Development.lister-success-text"),
        'footer-class' => 'text-center',
        'footer-continue' => base_url("/development/generators/list/" . lpk()),
        'voice' => "development/lister-success-message.mp3",
    ));
}
echo($c);
?>