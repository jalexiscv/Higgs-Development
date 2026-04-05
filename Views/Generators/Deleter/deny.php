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

/** @var $permissions array que contiene los permisos que el usuario no posee */
/** @var $authentication \App\Libraries\Authentication */

use Higgs\Frontend\Bootstrap\v5_3_3\Bootstrap as BS5;

$continue = "#";
if ($authentication->get_LoggedIn()) {
    $_icon = (string)BS5::icon(['icon' => 'ban', 'style' => 'duotone', 'size' => '4x']);
    $_body = '<div class="text-center py-3">' . $_icon . '</div>'
        . '<p class="text-center pb-2">' . lang('App.Access-denied-message') . '</p>';
    $_permissions="<p class=\"text-center pb-2\">Permisos requeridos: ".implode(" - ",$permissions)."</p>";
    $_continue = BS5::button(['content' => lang('App.Continue'), 'variant' => 'danger', 'size' => 'md', 'attributes' => ['href' => $continue]]);
    $card = BS5::card([
        'header' => [
            'title' => lang('App.Access-denied-title'),
            'class' => 'bg-danger border-danger text-white'
        ],
        'content' => [
            'htmlContent'=>$_body.$_permissions,
            'class' => 'bg-danger text-white',
        ],
        'footer' => [
            'content' => $_continue,
            'class' => 'bg-danger text-white d-flex justify-content-end',
        ],
        'attributes' => [
            'class' => 'border-danger shadow-sm'
        ],
    ]);
} else {
    $_icon = (string)BS5::icon(['icon' => 'lock', 'style' => 'duotone', 'size' => '4x']);
    $_body = '<div class="text-center py-3">' . $_icon . '</div>'
        . '<p class="text-center pb-2">' . lang('App.login-required-message') . '</p>';
    $_continue = BS5::button(['content' => lang('App.Continue'), 'variant' => 'danger', 'size' => 'md', 'attributes' => ['href' => $continue]]);
    $card = BS5::card([
        'header' => [
            'title' => lang('App.login-required-title'),
            'class' => 'bg-danger text-white'
        ],
        'content' => [
            'htmlContent'=>$_body,
            'class' => 'bg-danger text-white',
        ],
        'footer' => [
            'content' => $_continue,
            'class' => 'bg-danger text-white d-flex justify-content-end',
        ],
        'attributes' => [
            'class' => 'border-danger shadow-sm'
        ],
    ]);
}
echo($card);
?>