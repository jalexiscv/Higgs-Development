<?php

/** @var string $component */

use Higgs\Frontend\Bootstrap\v5_3_3\Bootstrap as BS5;

$b = service('bootstrap');
$menu = [
    ['href' => '/development/home/' . lpk(), 'text' => 'Development', 'class' => false],
];
echo($b->get_Breadcrumb($menu));
