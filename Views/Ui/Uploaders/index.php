<?php

/** @var $authentication \App\Libraries\Authentication */
/** @var $parent \App\Controllers\ModuleController */
/** @var $request \CodeIgniter\HTTP\RequestInterface */
/** @var string $component */

$data = $parent->get_Array();
$data['model'] = model("App\Modules\Sie\Models\Sie_Modules");
$data['permissions'] = ['singular' => 'DEVELOPMENT-ACCESS', 'plural' => 'DEVELOPMENT-ACCESS'];
$singular = $authentication->has_Permission($data['permissions']['singular']);
$plural = $authentication->has_Permission($data['permissions']['plural']);
$author = $data['model']->getAuthority($oid, safe_get_user());
$authority = ($singular && $author) ? true : false;
$submited = $request->getPost('submited');
$breadcrumb = $component . '\breadcrumb';
$validator = $component . '\validator';
$form = $component . '\form';
$deny = $component . '\deny';
//[build]---------------------------------------------------------------------------------------------------------------
if ($plural || $authority) {
    if (!empty($submited)) {
        $json = ['breadcrumb' => view($breadcrumb, $data), 'main' => view($validator, $data), 'right' => ''];
    } else {
        $json = ['breadcrumb' => view($breadcrumb, $data), 'main' => view($form, $data), 'right' => ''];
    }
} else {
    $json = ['breadcrumb' => view($breadcrumb, $data), 'main' => view($deny, $data), 'right' => ''];
}
echo(json_encode($json));
