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
$pathfile = $f->get_Value("pathfile");
$mkdir = $f->get_Value("mkdir");
$relative = $f->get_Value("relative");
$code = $f->get_Value("code");

$files = new Files();
$files->mkDir($mkdir);
$files->open($pathfile, "writeOnly")->write($code);
//[Processing]----------------------------------------------------------------------------------------------------------
if (isset($row["client"])) {
    $c = $bootstrap->get_Card('warning', array(
        'class' => 'card-warning',
        'icon' => 'fa-duotone fa-triangle-exclamation',
        'text-class' => 'text-center',
        'text' => lang("Development.model-warning-title"),
        'footer-class' => 'text-center',
        'footer-continue' => base_url("/development/generators/list/" . lpk()),
        'voice' => "development/model-create-warning-message.mp3",
    ));
} else {
    $c = $bootstrap->get_Card('success', array(
        'class' => 'card-success',
        'icon' => 'fa-duotone fa-triangle-exclamation',
        'text-class' => 'text-center',
        'text' => lang("Development.model--success-title"),
        'footer-class' => 'text-center',
        'footer-continue' => base_url("/development/generators/list/" . lpk()),
        'voice' => "development/model-success-message.mp3",
    ));
}
echo($c);