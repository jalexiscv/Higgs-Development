<?php

/** @var string $component */

use Higgs\Frontend\Bootstrap\v5_3_3\Bootstrap as BS5;
$bootstrap = service('bootstrap');
$menu = [
    ['href' => '/development/', 'text' => lang('App.Development'), 'class' => false],
    ['href' => '/development/ui/home/' . lpk(), 'text' => lang('App.UI'), 'class' => 'active'],
];
echo($bootstrap->get_Breadcrumb($menu));
