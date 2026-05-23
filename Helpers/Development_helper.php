<?php

if (!function_exists('generate_development_permissions')) {

    /**
     * Permite registrar los permisos asociados al modulo, tecnicamente su
     * ejecucion regenera los permisos asignables definidos por el modulo DISA
     */
    function generate_development_permissions()
    {
        $permissions = [
            'development-access',
        ];
        generate_permissions($permissions, 'cadastre');
    }

}

if (!function_exists('get_development_sidebar')) {
    function get_development_sidebar($active_url = false)
    {
        $bootstrap = service('bootstrap');
        $lpk = safe_strtolower(pk());
        $options = [
            'home' => ['text' => lang('App.Home'), 'href' => '/development/', 'svg' => 'home.svg'],
            'customers' => ['text' => lang('App.Customers'), 'href' => '/development/customers/list/' . lpk(), 'icon' => ICON_CUSTOMERS, 'permission' => 'development-access'],
            'generators' => ['text' => lang('App.Generators'), 'href' => '/development/generators/list/' . lpk(), 'icon' => ICON_GENERATORS, 'permission' => 'development-access'],
            'ui' => ['text' => lang('App.UI'), 'href' => '/development/ui/home/' . lpk(), 'icon' => ICON_TOOLS, 'permission' => 'development-access'],
            'tools' => ['text' => lang('App.Tools'), 'href' => '/development/tools/home/' . lpk(), 'icon' => ICON_TOOLS, 'permission' => 'development-access'],
            'ide' => ['text' => lang('App.IDE'), 'href' => '/development/ide/home/' . lpk(), 'icon' => ICON_TOOLS, 'permission' => 'development-access'],
        ];
        $o = get_application_custom_sidebar($options, $active_url);
        $return = $bootstrap->get_NavPillsGamma($o, $active_url);
        return ($return);
    }
}

if (!function_exists('get_development_code_copyright')) {
    function get_development_code_copyright(array $args)
    {
        $path = $args['path'];
        $author = 'Jose Alexis Correa Valencia <jalexiscv@gmail.com>';
        $date = date('Y-m-d H:i:s');
        $c = "\n/**";
        $c .= "\n* █ ---------------------------------------------------------------------------------------------------------------------";
        $c .= "\n* █ ░FRAMEWORK                                  {$date}";
        $c .= "\n* █ ░█▀▀█ █▀▀█ █▀▀▄ █▀▀ ░█─░█ ─▀─ █▀▀▀ █▀▀▀ █▀▀ [{$path}]";
        $c .= "\n* █ ░█─── █──█ █──█ █▀▀ ░█▀▀█ ▀█▀ █─▀█ █─▀█ ▀▀█ Copyright 2023 - CloudEngine S.A.S., Inc. <admin@cgine.com>";
        $c .= "\n* █ ░█▄▄█ ▀▀▀▀ ▀▀▀─ ▀▀▀ ░█─░█ ▀▀▀ ▀▀▀▀ ▀▀▀▀ ▀▀▀ Para obtener información completa sobre derechos de autor y licencia,";
        $c .= "\n* █                                             consulte la LICENCIA archivo que se distribuyó con este código fuente.";
        $c .= "\n* █ ---------------------------------------------------------------------------------------------------------------------";
        $c .= "\n* █ EL SOFTWARE SE PROPORCIONA -TAL CUAL-, SIN GARANTÍA DE NINGÚN TIPO, EXPRESA O";
        $c .= "\n* █ IMPLÍCITA, INCLUYENDO PERO NO LIMITADO A LAS GARANTÍAS DE COMERCIABILIDAD,";
        $c .= "\n* █ APTITUD PARA UN PROPÓSITO PARTICULAR Y NO INFRACCIÓN. EN NINGÚN CASO SERÁ";
        $c .= "\n* █ LOS AUTORES O TITULARES DE LOS DERECHOS DE AUTOR SERÁN RESPONSABLES DE CUALQUIER";
        $c .= "\n* █ RECLAMO, DAÑOS U OTROS RESPONSABILIDAD, YA SEA EN UNA ACCIÓN DE CONTRATO,";
        $c .= "\n* █ AGRAVIO O DE OTRO MODO, QUE SURJA DESDE, FUERA O EN RELACIÓN CON EL SOFTWARE";
        $c .= "\n* █ O EL USO U OTROS NEGOCIACIONES EN EL SOFTWARE.";
        $c .= "\n* █ ---------------------------------------------------------------------------------------------------------------------";
        $c .= "\n* █ @Author {$author}";
        $c .= "\n* █ @link https://www.higgs.com.co";
        $c .= "\n* █ @Version 1.5.1 @since PHP 8,PHP 9";
        $c .= "\n* █ ---------------------------------------------------------------------------------------------------------------------";
        $c .= "\n**/\n";
        return ($c);
    }
}
