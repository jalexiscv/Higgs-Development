<?php

use Higgs\Frontend\Bootstrap\v5_3_3\Bootstrap as BS5;

echo BS5::breadcrumb([
    'items' => [
        ['label' => 'Development', 'href' => '/development/'],
        ['label' => lang("App.Home"), 'active' => true],
    ],
])->render();
