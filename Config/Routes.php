<?php

use Config\Services;

// [services] ----------------------------------------------------------------------------------------------------------
$platform = service('platform');

// [vars] --------------------------------------------------------------------------------------------------------------
if ($platform->getCandidate(__FILE__)) {
    $routes = $routes ?? Services::routes(true);
    $module = 'development';
    $namespace = 'App\Modules\Development\Controllers';
    $authorized = $platform->getAuthorizedModule($module);
    $routes->group($module, ['namespace' => $namespace], function ($subroutes) use ($authorized) {
        if ($authorized === 'authorized') {
            $subroutes->add('/', 'Development::index');
            $subroutes->add('/home', 'Development::index');
            $subroutes->add('home/(:any)', 'Development::home/$1');
            $subroutes->add('(:any)/(:any)/(:any)', 'Router::route/$1/$2/$3');
        } else {
            $subroutes->add('(:any)', 'Development::denied');
        }
    });
}
