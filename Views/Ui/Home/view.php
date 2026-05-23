<?php

$bootstrap = service('bootstrap');

$options = [
    ['href' => '/development/ui/buttons/list/' . lpk(), 'title' => 'Bótones', 'icon' => 'fas fa-tasks'],
    ['href' => '/development/ui/cards/list/' . lpk(), 'title' => 'Tarjetas', 'icon' => 'fas fa-tasks'],
    ['href' => '/development/ui/chatbox/list/' . lpk(), 'title' => 'ChatBox', 'icon' => 'fas fa-tasks'],
    ['href' => '/development/ui/uploaders/list/' . lpk(), 'title' => 'Uploaders', 'icon' => ICON_UPLOAD],
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
