<?php

/** @var string $component */

use Higgs\Frontend\Bootstrap\v5_3_3\Bootstrap as BS5;

$b = service('bootstrap');
$menu = [
    ['href' => '/development/', 'text' => 'Development', 'class' => false],
    ['href' => '/development/home/index/' . lpk(), 'text' => lang('App.Home'), 'class' => 'active'],
];
echo($b->get_Breadcrumb($menu));
