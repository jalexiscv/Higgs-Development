<?php
use Higgs\Frontend\Bootstrap\v5_3_3\Bootstrap as BS5;
$continue = "/development/generators/list/" . lpk();
if ($authentication->get_LoggedIn()) {
    $_icon = (string)BS5::icon(['icon' => 'ban', 'style' => 'duotone', 'size' => '2xl']);
    $_body = '<div class="text-center py-3">'.$_icon.'</div>'
           . '<p class="text-center pb-2">'.lang('App.Access-denied-message').'</p>';
    $card = BS5::card([
        'header'      => ['title' => lang('App.Access-denied-title'), 'class' => 'bg-danger text-white'],
        'htmlContent' => $_body,
        'footer'      => [
            'content' => BS5::button(['content' => lang('App.Continue'), 'variant' => 'danger', 'size' => 'sm', 'attributes' => ['href' => $continue]]),
            'class'   => 'd-flex justify-content-end',
        ],
        'attributes'  => ['class' => 'border-danger shadow-sm'],
    ]);
} else {
    $_icon = (string)BS5::icon(['icon' => 'lock', 'style' => 'duotone', 'size' => '2xl']);
    $_body = '<div class="text-center py-3">'.$_icon.'</div>'
           . '<p class="text-center pb-2">'.lang('App.login-required-message').'</p>';
    $card = BS5::card([
        'header'      => ['title' => lang('App.login-required-title'), 'class' => 'bg-danger text-white'],
        'htmlContent' => $_body,
        'footer'      => [
            'content' => BS5::button(['content' => lang('App.Continue'), 'variant' => 'danger', 'size' => 'sm', 'attributes' => ['href' => $continue]]),
            'class'   => 'd-flex justify-content-end',
        ],
        'attributes'  => ['class' => 'border-danger shadow-sm'],
    ]);
}
echo($card);
?>