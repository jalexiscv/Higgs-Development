<?php

$bootstrap = service('bootstrap');

$options = [
    ['href' => '/development/tools/modules/generator/' . lpk(), 'title' => 'Generador de módulos', 'icon' => 'fas fa-tasks'],
    ['href' => '/development/tools/texttophp/generator/' . lpk(), 'title' => 'Texto a PHP', 'icon' => 'fas fa-tasks'],
    ['href' => '/development/tools/poeditor/generator/' . lpk(), 'title' => 'PoEditor', 'icon' => 'fas fa-tasks'],
];

$shortcuts = '<div class="row  row-cols-xxl-4 row-cols-xl-3 row-cols-lg-2 row-cols-md-1 row-cols-1  text-center shortcuts">';
foreach ($options as $option) {
    $shortcuts .= '<div class="col mb-3">';
    $shortcuts .= $bootstrap->get_Card('option-' . lpk(), [
        'title' => $option['title'],
        'icon' => $option['icon'],
        'footer-continue' => $option['href'],
    ]);
    $shortcuts .= '</div>';
}
$shortcuts .= '</div>';

$card = $bootstrap->get_Card('card-tools', [
    'title' => lang('App.Tools'),
    'header-back' => '/development/home/' . lpk(),
    'content' => $shortcuts,
]);
echo($card);
