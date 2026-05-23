<?php

use Higgs\Frontend\Bootstrap\v5_3_3\Bootstrap as BS5;

$f = service("forms", array("lang" => "Nexus."));

$f->set_ValidationRule("pathfile", "trim|required");
$f->set_ValidationRule("mkdir", "trim|required");
$f->set_ValidationRule("uri_save", "trim|required");
$f->set_ValidationRule("code", "trim|required");

if ($f->run_Validation()) {
    $c = view($component . '\processor', $parent->get_Array());
} else {
    $_icon_col  = BS5::row(['attributes' => ['class' => 'text-center py-3'], 'content' => BS5::icon(['icon' => 'triangle-exclamation', 'style' => 'duotone', 'size' => '2xl'])]);
    $_msg_col   = BS5::row(['attributes' => ['class' => 'text-center pb-2'], 'htmlContent' => lang('App.validator-errors-message')]);
    $_errors_col= BS5::row(['attributes' => ['class' => 'pb-2'], 'htmlContent' => $f->validation->listErrors()]);
    $_content   = BS5::col(['attributes' => ['class' => 'justify-content-center'], 'htmlContent' => $_icon_col.$_msg_col.$_errors_col]);
    $c = BS5::card([
        'headerTitle' => lang('App.validator-errors-title'),
        'headerClass' => 'bg-danger text-white',
        'content'     => ["htmlContent" => $_content,],
        'attributes'  => ['class' => 'border-danger shadow-sm'],
    ]);
}

echo($c);
