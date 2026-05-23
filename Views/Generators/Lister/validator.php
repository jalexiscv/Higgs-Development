<?php

/** @var string $component */

$f = service('forms', ['lang' => 'Nexus.']);
/*
 * -----------------------------------------------------------------------------
 * [Request]
 * -----------------------------------------------------------------------------
*/
$f->set_ValidationRule('pathfiles', 'trim|required');
$f->set_ValidationRule('cindex', 'trim|required');
$f->set_ValidationRule('cdeny', 'trim|required');
//$f->set_ValidationRule("ctable", "trim|required");
//$f->set_ValidationRule("cjson", "trim|required");
$f->set_ValidationRule('cgrid', 'trim|required');

/*
 * -----------------------------------------------------------------------------
 * [Validation]
 * -----------------------------------------------------------------------------
*/
if ($f->run_Validation()) {
    $c = view($component . '\processor', $parent->get_Array());
} else {
    $c = $bootstrap->get_Card('validator', [
        'class' => 'card-danger',
        'icon' => 'fa-duotone fa-triangle-exclamation',
        'text-class' => 'text-center',
        'text' => lang('App.create-errors-message'),
        'errors' => $f->validation->listErrors(),
        'footer-class' => 'text-center',
        'voice' => 'app/form-errors-message.mp3',
    ]);
}
/*
 * -----------------------------------------------------------------------------
 * [Build]
 * -----------------------------------------------------------------------------
*/
echo($c);
