<?php

use Higgs\Frontend\Bootstrap\v5_3_3\Bootstrap as BS5;
use Higgs\Html\Html;

$authentication = service("authentication");

// Card principal del módulo
echo BS5::card([
    'image'           => '/themes/assets/images/header/development.png',
    'imageAttributes' => ['class' => 'img-fluid p-3'],
    'imagePosition'   => 'top',
    'headerTitle'     => lang("App.Module-Development"),
    'headerButtons'   => [
        BS5::button([
            'content'    => BS5::icon(['icon' => 'angle-left']),
            'variant'    => 'secondary',
            'size'       => 'sm',
            'outline'    => true,
            'attributes' => ['href' => '/'],
        ]),
    ],
    'content'    => lang("Cadastre.intro-1"),
    'attributes' => ['class' => 'mb-3'],
])->render();

// Panel de accesos directos (solo para usuarios autorizados)
if ($authentication->get_LoggedIn() && $authentication->has_Permission("SECURITY-ACCESS")) {
    $shortcuts = BS5::shortcuts([
        'items' => [
            [
                'href' => '/development/tools/modules/generator/' . lpk(),
                'icon' => ICON_TOOLS,
                'title' => 'Módulos',
                'subtitle' => 'Generador'
            ],
            [
                'href' => '/development/tools/texttophp/generator/' . lpk(),
                'icon' => ICON_TOOLS,
                'title' => 'Texto a PHP',
                'subtitle' => 'Convertidor'
            ],
            [
                'href' => '/development/tools/poeditor/generator/' . lpk(),
                'icon' => ICON_TOOLS,
                'title' => 'PoEditor',
                'subtitle' => 'Traductor'
            ],
            [
                'href' => '/development/webpack/home/' . lpk(),
                'icon' => ICON_EXE,
                'title' => 'WebPack',
                'subtitle' => 'Empaquetador'
            ],
        ],
    ]);
    echo($shortcuts->render());
}
?>