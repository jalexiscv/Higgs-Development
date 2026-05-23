<?php

/** @var $permissions array que contiene los permisos que el usuario no posee */
/** @var $authentication \App\Libraries\Authentication */

use Higgs\Frontend\Bootstrap\v5_3_3\Bootstrap as BS5;

$continue = '/development/generators/list/' . lpk();
if ($authentication->get_LoggedIn()) {
    $_icon = (string)BS5::icon(['icon' => 'ban', 'style' => 'duotone', 'size' => '4x']);
    $_body = '<div class="text-center py-3">' . $_icon . '</div>'
        . '<p class="text-center pb-2">' . lang('App.Access-denied-message') . '</p>';
    $_permissions = '<p class="text-center pb-2">Permisos requeridos: '.implode(' - ', $permissions).'</p>';
    $_continue = BS5::button(['content' => lang('App.Continue'), 'variant' => 'danger', 'size' => 'md', 'attributes' => ['href' => $continue]]);
    $card = BS5::card([
        'header' => [
            'title' => lang('App.Access-denied-title'),
            'class' => 'bg-danger border-danger text-white',
        ],
        'content' => [
            'htmlContent' => $_body.$_permissions,
            'class' => 'bg-danger text-white',
        ],
        'footer' => [
            'content' => $_continue,
            'class' => 'bg-danger text-white d-flex justify-content-end',
        ],
        'attributes' => [
            'class' => 'border-danger shadow-sm',
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
            'class' => 'bg-danger text-white',
        ],
        'content' => [
            'htmlContent' => $_body,
            'class' => 'bg-danger text-white',
        ],
        'footer' => [
            'content' => $_continue,
            'class' => 'bg-danger text-white d-flex justify-content-end',
        ],
        'attributes' => [
            'class' => 'border-danger shadow-sm',
        ],
    ]);
}
echo($card);
