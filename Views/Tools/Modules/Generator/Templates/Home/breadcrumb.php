<?php
/** @var string $module */
$strings = service("strings");
$ucf_module = $strings->get_Ucfirst($module);
$lc_module = $strings->get_Strtolower($module);
$time = date("Y-m-d H:i:s");
$year = date("Y");

$code = "<?php\n";
$code .= "\n";
$code .= "/**\n";
$code .= "* █ ---------------------------------------------------------------------------------------------------------------------\n";
$code .= "* █ ░FRAMEWORK\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t{$time}\n";
$code .= "* █ ░█▀▀█ █▀▀█ █▀▀▄ █▀▀ ░█─░█ ─▀─ █▀▀▀ █▀▀▀ █▀▀ [App\Modules\\$ucf_module\Home\breadcrumb.php]\n";
$code .= "* █ ░█─── █──█ █──█ █▀▀ ░█▀▀█ ▀█▀ █─▀█ █─▀█ ▀▀█ Copyright $year - CloudEngine S.A.S., Inc. <admin@cgine.com>\n";
$code .= "* █ ░█▄▄█ ▀▀▀▀ ▀▀▀─ ▀▀▀ ░█─░█ ▀▀▀ ▀▀▀▀ ▀▀▀▀ ▀▀▀ Para obtener información completa sobre derechos de autor y licencia,\n";
$code .= "* █\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t consulte la LICENCIA archivo que se distribuyó con este código fuente.\n";
$code .= "* █ ---------------------------------------------------------------------------------------------------------------------\n";
$code .= "* █ EL SOFTWARE SE PROPORCIONA -TAL CUAL-, SIN GARANTÍA DE NINGÚN TIPO, EXPRESA O\n";
$code .= "* █ IMPLÍCITA, INCLUYENDO PERO NO LIMITADO A LAS GARANTÍAS DE COMERCIABILIDAD,\n";
$code .= "* █ APTITUD PARA UN PROPÓSITO PARTICULAR Y NO INFRACCIÓN. EN NINGÚN CASO SERÁ\n";
$code .= "* █ LOS AUTORES O TITULARES DE LOS DERECHOS DE AUTOR SERÁN RESPONSABLES DE CUALQUIER\n";
$code .= "* █ RECLAMO, DAÑOS U OTROS RESPONSABILIDAD, YA SEA EN UNA ACCIÓN DE CONTRATO,\n";
$code .= "* █ AGRAVIO O DE OTRO MODO, QUE SURJA DESDE, FUERA O EN RELACIÓN CON EL SOFTWARE\n";
$code .= "* █ O EL USO U OTROS NEGOCIACIONES EN EL SOFTWARE.\n";
$code .= "* █ ---------------------------------------------------------------------------------------------------------------------\n";
$code .= "* █ @Author Jose Alexis Correa Valencia <jalexiscv@gmail.com>\n";
$code .= "* █ @link https://www.codehiggs.com\n";
$code .= "* █ @Version 1.5.0 @since PHP 7, PHP 8\n";
$code .= "* █ ---------------------------------------------------------------------------------------------------------------------\n";
$code .= "* █ Datos recibidos desde el controlador - @ModuleController\n";
$code .= "* █ ---------------------------------------------------------------------------------------------------------------------\n";
$code .= "* █ @authentication, @request, @dates, @parent, @component, @view, @oid, @views, @prefix\n";
$code .= "* █ ---------------------------------------------------------------------------------------------------------------------\n";
$code .= "**/\n";
$code .= "\$b = service(\"bootstrap\");\n";
$code .= "\$menu = array(\n";
$code .= "\t\tarray(\"href\" => \"/$lc_module/\", \"text\" => \"$ucf_module\", \"class\" => false),\n";
$code .= "\t\tarray(\"href\" => \"/$lc_module/home/index/\" . lpk(), \"text\" => lang(\"App.Home\"), \"class\" => \"active\"),\n";
$code .= ");\n";
$code .= "echo(\$b->get_Breadcrumb(\$menu));\n";
$code .= "?>\n";
echo($code);
?>