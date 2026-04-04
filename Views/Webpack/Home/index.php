<?php

//[vars]----------------------------------------------------------------------------------------------------------------
/** @var object $parent */
/** @var string $component */
/** @var string $view */
/** @var object $authentication */
/** @var object $request */
$data = $parent->get_Array();
$data['permissions'] = array('singular' => "development-access");
$singular = $authentication->has_Permission($data['permissions']['singular']);
$submited = $request->getPost("submited");
$breadcrumb = $component . '\breadcrumb';
$validator = $component . '\validator';
$home = $component . '\view';
$deny = $component . '\deny';
//[build]---------------------------------------------------------------------------------------------------------------
$json = array(
    'breadcrumb' => false,
    'main' => view($home, $data),
    'right' => "",
    'main_template' => "c12p1",
);
echo(json_encode($json));
?>