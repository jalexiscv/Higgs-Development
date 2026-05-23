<?php

//[vars]----------------------------------------------------------------------------------------------------------------
$data = $parent->get_Array();
$data['permissions'] = ['singular' => 'security-access'];
$singular = $authentication->has_Permission($data['permissions']['singular']);
$submited = $request->getPost('submited');
$breadcrumb = 'App\Modules\Development\Views\E404\breadcrumb';
$home = 'App\Modules\Development\Views\E404\view';
//[build]---------------------------------------------------------------------------------------------------------------
$json = [
    'breadcrumb' => view($breadcrumb, $data),
    'main' => view($home, $data),
    'right' => '',
];
echo(json_encode($json));
