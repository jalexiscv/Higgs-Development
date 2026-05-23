<?php

$authentication = service('authentication');
$bootstrap = service('bootstrap');
$card = $bootstrap->get_Card('card-view-service', [
    'class' => 'mb-3',
    'title' => lang('App.Module-Development') . '',
    'header-back' => '/',
    'image-class' => 'img-fluid p-3',
    'content' => view("App\\Modules\\Development\\Views\\Ide\Home\\three", []),
]);
echo($card);
