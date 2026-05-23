<?php

$f = service('forms', ['lang' => 'Development_Modules.']);
/*
* -----------------------------------------------------------------------------
* [Request]
* -----------------------------------------------------------------------------
*/
$f->set_ValidationRule('module', 'trim|required');
$f->set_ValidationRule('reference', 'trim|required');
$f->set_ValidationRule('acronym', 'trim|required');
$f->set_ValidationRule('name', 'trim|required');
$f->set_ValidationRule('status', 'trim|required');
$f->set_ValidationRule('author', 'trim|required');
$f->set_ValidationRule('created_at', 'trim|required');
$f->set_ValidationRule('updated_at', 'trim|required');
$f->set_ValidationRule('deleted_at', 'trim|required');
/*
* -----------------------------------------------------------------------------
* [Validation]
* -----------------------------------------------------------------------------
*/
if ($f->run_Validation()) {
    $c = view($component . '\processor', $parent->get_Array());
} else {
    $errors = $f->validation->listErrors();
    $bootstrap = service('bootstrap');
    $c = $bootstrap->get_Card('access-denied', [
        'class' => 'card-danger',
        'icon' => 'fa-duotone fa-triangle-exclamation',
        'text-class' => 'text-center',
        'text' => lang('App.validator-errors-message'),
        'errors' => $errors,
        'footer-class' => 'text-center',
        'voice' => 'app/validator-errors-message.mp3',
    ]);
    $c .= view($component . '\form', $parent->get_Array());
}
/*
* -----------------------------------------------------------------------------
* [Build]
* -----------------------------------------------------------------------------
*/
echo($c);
