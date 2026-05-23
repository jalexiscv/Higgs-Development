<?php

$bootstrap = service('bootstrap');
$authentication = service('authentication');
$request = service('request');
$strings = service('strings');
$module = $request->uri->getSegment(1);
$component = $request->uri->getSegment(2);
$method = $request->uri->getSegment(3);
$objeto = $request->uri->getSegment(4);

$card = $bootstrap->get_Card(
    'card-error404',
    [
        'class' => 'card-danger',
        'title' => "Error 404 - Page not found - {$component}/{$method}/{$objeto}",
        'icon' => 'fas fa-exclamation-triangle',
        'content' => lang('App.Error404') . "<br><br>Prefijo: {$prefix}",
        'footer-continue' => "/{$module}",
        'voice' => 'app/error404.mp3',
    ]
);
echo($card);
