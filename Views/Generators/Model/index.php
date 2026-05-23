<?php

/** @var string $component */

//[vars]----------------------------------------------------------------------------------------------------------------
$data = $parent->get_Array();
$data['permissions'] = ['singular' => 'development-access', 'plural' => null];
$singular = $authentication->has_Permission($data['permissions']['singular']);
$submited = $request->getPost('submited');
$form = $component . '\form';
$validator = $component . '\validator';
$deny = $component . '\deny';
$breadcrumb = $component . '\breadcrumb';
//[build]---------------------------------------------------------------------------------------------------------------
if ($singular) {
    if (!empty($submited)) {
        $json = ['breadcrumb' => view($breadcrumb, $data), 'main' => view($validator, $data), 'right' => ''];
    } else {
        $json = ['breadcrumb' => view($breadcrumb, $data), 'main' => view($form, $data), 'right' => ''];
    }
} else {
    $json = ['breadcrumb' => view($breadcrumb, $data), 'main' => view($deny, $data), 'right' => ''];
}
echo(json_encode($json));
