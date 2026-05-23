<?php

$data = $parent->get_Array();
$data['permissions'] = ['singular' => 'nexus-modules-create', 'plural' => false];
$singular = $authentication->has_Permission($data['permissions']['singular']);
$submited = $request->getPost('submited');
$validator = $component . '\validator';
$breadcrumb = $component . '\breadcrumb';
$form = $component . '\form';
$deny = $component . '\deny';
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
