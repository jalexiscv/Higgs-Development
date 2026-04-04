<?php

//[vars]----------------------------------------------------------------------------------------------------------------
$data = $parent->get_Array();
$data['permissions'] = array('singular' => "development-access");
$singular = $authentication->has_Permission($data['permissions']['singular']);
$submited = $request->getPost("submited");
$breadcrumb = $component . '\breadcrumb';
$validator = $component . '\validator';
$home = $component . '\view';
$deny = $component . '\deny';
//[build]---------------------------------------------------------------------------------------------------------------
if ($singular) {
    $json = array(
        'breadcrumb' => view($breadcrumb, $data),
        'main' => view($home, $data),
        'right' => "",
    );
} else {
    $json = array(
        'breadcrumb' => view($breadcrumb, $data),
        'main' => view($deny, $data),
        'right' => "",
    );
}
echo(json_encode($json));
?>