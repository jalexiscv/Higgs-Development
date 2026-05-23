<?php

/** @var string $component */

use Higgs\Frontend\Bootstrap\v5_3_3\Bootstrap as BS5;

$b = service('bootstrap');
$menu = [
    ['href' => '/security/', 'text' => 'Security', 'class' => 'active'],
];
echo($b->get_Breadcrumb($menu));
