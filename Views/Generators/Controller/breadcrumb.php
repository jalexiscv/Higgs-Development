<?php

/** @var string $component */

use Higgs\Frontend\Bootstrap\v5_3_3\Bootstrap as BS5;

$menu = [
    ["href" => "/development/home/" . lpk(), "text" => "Development", "class" => false],
];
echo BS5::breadcrumb(['items' => $menu]);
?>