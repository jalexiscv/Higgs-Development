<?php

/** @var string $component */
/** @var string $view */

$processor = $component . '\Create\processor';
$form = $component . '\Create\form';

$f = service('forms', ['lang' => 'Social.posts-']);

/** Request * */
$f->set_ValidationRule('post', 'trim|required');
$f->set_ValidationRule('title', 'trim|required');
$f->set_ValidationRule('content', 'trim|required');

if ($f->run_Validation()) {
    $c = view($processor);
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
    $c .= view($form);
}
echo($c);
