<?php

/* @var object $authentication */
/* @var object $request */
/* @var object $parent */
/* @var string $component */
//[Vars] ---------------------------------------------------------------------------------------------------------------
$data = $parent->get_Array();
$data['permissions'] = ['singular' => 'nexus-access', 'plural' => null];
$singular = $authentication->has_Permission($data['permissions']['singular']);
$submited = $request->getPost('submited');
$form = $component . '\form';
$breadcrumb = $component . '\breadcrumb';
$validator = $component . '\validator';
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
