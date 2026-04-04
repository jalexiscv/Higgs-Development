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

/*
 * -----------------------------------------------------------------------------
 * [Vars]
 * -----------------------------------------------------------------------------
*/
$data = $parent->get_Array();
$data['permissions'] = array('singular' => 'nexus-access', "plural" => null);
$singular = $authentication->has_Permission($data['permissions']['singular']);
$submited = $request->getPost("submited");
$form = $component . '\form';
$breadcrumb = $component . '\breadcrumb';
$validator = $component . '\validator';
$deny = $component . '\deny';
//[build]---------------------------------------------------------------------------------------------------------------
if ($singular) {
    if (!empty($submited)) {
        $json = array('breadcrumb' => view($breadcrumb, $data), 'main' => view($validator, $data), 'right' => "");
    } else {
        $json = array('breadcrumb' => view($breadcrumb, $data), 'main' => view($form, $data), 'right' => "");
    }
} else {
    $json = array('breadcrumb' => view($breadcrumb, $data), 'main' => view($deny, $data), 'right' => "");
}
echo(json_encode($json));
?>