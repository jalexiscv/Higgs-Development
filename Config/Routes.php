<?php

/**
 * █ -------------------------------------------------------------------------------------------------------------------
 * █ ░FRAMEWORK                                  2023-11-26 00:29:34
 * █ ░█▀▀█ █▀▀█ █▀▀▄ █▀▀ ░█─░█ ─▀─ █▀▀▀ █▀▀▀ █▀▀ [App\Modules\Development\Config\Routes.php]
 * █ ░█─── █──█ █──█ █▀▀ ░█▀▀█ ▀█▀ █─▀█ █─▀█ ▀▀█ Copyright 2023 - CloudEngine S.A.S., Inc. <admin@cgine.com>
 * █ ░█▄▄█ ▀▀▀▀ ▀▀▀─ ▀▀▀ ░█─░█ ▀▀▀ ▀▀▀▀ ▀▀▀▀ ▀▀▀ Para obtener información completa sobre derechos de autor y licencia,
 * █                                             consulte la LICENCIA archivo que se distribuyó con este código fuente.
 * █ -------------------------------------------------------------------------------------------------------------------
 * █ EL SOFTWARE SE PROPORCIONA -TAL CUAL-, SIN GARANTÍA DE NINGÚN TIPO, EXPRESA O
 * █ IMPLÍCITA, INCLUYENDO PERO NO LIMITADO A LAS GARANTÍAS DE COMERCIABILIDAD,
 * █ APTITUD PARA UN PROPÓSITO PARTICULAR Y NO INFRACCIÓN. EN NINGÚN CASO SERÁ
 * █ LOS AUTORES O TITULARES DE LOS DERECHOS DE AUTOR SERÁN RESPONSABLES DE CUALQUIER
 * █ RECLAMO, DAÑOS U OTROS RESPONSABILIDAD, YA SEA EN UNA ACCIÓN DE CONTRATO,
 * █ AGRAVIO O DE OTRO MODO, QUE SURJA DESDE, FUERA O EN RELACIÓN CON EL SOFTWARE
 * █ O EL USO U OTROS NEGOCIACIONES EN EL SOFTWARE.
 * █ -------------------------------------------------------------------------------------------------------------------
 * █ @Author Jose Alexis Correa Valencia <jalexiscv@gmail.com>
 * █ @link https://www.codehiggs.com
 * █ @Version 2.0.0 @since PHP 7, PHP 8
 * █ -------------------------------------------------------------------------------------------------------------------
 * █ Datos recibidos desde el controlador - @ModuleController
 * █ -------------------------------------------------------------------------------------------------------------------
 * █ Ninguno
 * █ -------------------------------------------------------------------------------------------------------------------
 **/

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
