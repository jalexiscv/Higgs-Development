<?php

use Higgs\Frontend\Bootstrap\v5_3_3\Bootstrap as BS5;

$f = service('forms', ['lang' => 'Nexus.']);

$file = $f->get_Value('file');
$path = $f->get_Value('path');
$code = $f->get_Value('code');
$uri = $f->get_Value('uri');

$files = new \App\Libraries\Files();
$files->mkDir($path, true, 0775);
$files->open($uri, 'writeOnly')->write($code);
try {
    chmod($uri, 0664);
} catch (\Throwable $e) {
}

$_icon = (string)BS5::icon(['icon' => 'circle-check', 'style' => 'duotone', 'size' => '4x']);
$_body = '<div class="text-center py-3">' . $_icon . '</div>'
    . '<p class="text-center pb-2">' . lang('Development.lang-success-text') . '</p>'
    . '<div class="text-center pb-3">' . (string)BS5::button(['content' => lang('App.Continue'), 'variant' => 'success', 'size' => 'md', 'attributes' => ['href' => base_url('/development/generators/list/' . lpk())]]) . '</div>';
$_content = (string)BS5::col(['attributes' => ['class' => 'text-center'], 'htmlContent' => $_body]);
$c = BS5::card([
    'header' => [
        'title' => lang('Development.lang-success-title'),
        'class' => 'bg-success border-success text-white',
    ],
    'content' => [
        'htmlContent' => $_content,
        'class' => 'bg-success text-white',
    ],
    'attributes' => ['class' => 'border-success shadow-sm'],
]);

echo($c);
