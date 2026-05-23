<?php

/** @var $authentication \App\Libraries\Authentication */
/** @var $parent \App\Controllers\ModuleController */
/** @var $request \CodeIgniter\HTTP\RequestInterface */
/** @var $component string */

$data = $parent->get_Array();
$data['permissions'] = array('singular' => 'nexus-access', "plural" => null);
$singular = $authentication->has_Permission($data['permissions']['singular']);
$submited = $request->getPost("submited");
$form = $component . '\form';
$validator = $component . '\validator';
$deny = $component . '\deny';
$breadcrumb = $component . '\breadcrumb';
//[build]---------------------------------------------------------------------------------------------------------------
if ($singular) {
    if (!empty($submited)) {
        $json = array('breadcrumb' => view($breadcrumb, $data), 'main' => view($validator, $data), 'right' => "");
    } else {
        $json = array('breadcrumb' => view($breadcrumb, $data), 'main' => view($form, $data), 'right' => "");
    }
} else {
    $json = array('breadcrumb' => view($breadcrumb, $data), 'main' => view($deny, $data), 'right' => "");
}
echo(json_encode($json));
?>