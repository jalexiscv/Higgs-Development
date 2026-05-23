<?php

/*
 * **
 *  ** █ ---------------------------------------------------------------------------------------------------------------------
 *  ** █ ░FRAMEWORK                                  2023-12-01 23:19:27
 *  ** █ ░█▀▀█ █▀▀█ █▀▀▄ █▀▀ ░█─░█ ─▀─ █▀▀▀ █▀▀▀ █▀▀ [App\Modules\Account\Views\Processes\Creator\deny.php]
 *  ** █ ░█─── █──█ █──█ █▀▀ ░█▀▀█ ▀█▀ █─▀█ █─▀█ ▀▀█ Copyright 2023 - CloudEngine S.A.S., Inc. <admin@cgine.com>
 *  ** █ ░█▄▄█ ▀▀▀▀ ▀▀▀─ ▀▀▀ ░█─░█ ▀▀▀ ▀▀▀▀ ▀▀▀▀ ▀▀▀ Para obtener información completa sobre derechos de autor y licencia,
 *  ** █                                             consulte la LICENCIA archivo que se distribuyó con este código fuente.
 *  ** █ ---------------------------------------------------------------------------------------------------------------------
 *  ** █ EL SOFTWARE SE PROPORCIONA -TAL CUAL-, SIN GARANTÍA DE NINGÚN TIPO, EXPRESA O
 *  ** █ IMPLÍCITA, INCLUYENDO PERO NO LIMITADO A LAS GARANTÍAS DE COMERCIABILIDAD,
 *  ** █ APTITUD PARA UN PROPÓSITO PARTICULAR Y NO INFRACCIÓN. EN NINGÚN CASO SERÁ
 *  ** █ LOS AUTORES O TITULARES DE LOS DERECHOS DE AUTOR SERÁN RESPONSABLES DE CUALQUIER
 *  ** █ RECLAMO, DAÑOS U OTROS RESPONSABILIDAD, YA SEA EN UNA ACCIÓN DE CONTRATO,
 *  ** █ AGRAVIO O DE OTRO MODO, QUE SURJA DESDE, FUERA O EN RELACIÓN CON EL SOFTWARE
 *  ** █ O EL USO U OTROS NEGOCIACIONES EN EL SOFTWARE.
 *  ** █ ---------------------------------------------------------------------------------------------------------------------
 *  ** █ @Author Jose Alexis Correa Valencia <jalexiscv@gmail.com>
 *  ** █ @link https://www.codehiggs.com
 *  ** █ @Version 1.5.0 @since PHP 7, PHP 8
 *  ** █ ---------------------------------------------------------------------------------------------------------------------
 *  ** █ Datos recibidos desde el controlador - @ModuleController
 *  ** █ ---------------------------------------------------------------------------------------------------------------------
 *  ** █ @authentication, @request, @dates, @parent, @component, @view, @oid, @views, @prefix
 *  ** █ ---------------------------------------------------------------------------------------------------------------------
 *  **
 */
/** @var string $module */
$strings = service('strings');
$ucf_module = $strings->get_Ucfirst($module);
$lc_module = $strings->get_Strtolower($module);

$code = "<?php\n";
$code .= "\n";
$code .= "namespace App\Modules\\$ucf_module\Controllers;\n";
$code .= "\n";
$code .= "use Higgs\API\ResponseTrait;\n";
$code .= "use Higgs\RESTful\ResourceController;\n";
$code .= "\n";
$code .= "/**\n";
$code .= "* Api\n";
$code .= "*/\n";
$code .= "class Api extends ResourceController\n";
$code .= "{\n";
$code .= "\t\tuse ResponseTrait;\n";
$code .= "\n";
$code .= "\t\tpublic \$namespace;\n";
$code .= "\t\tprotected \$prefix;\n";
$code .= "\t\tprotected \$module;\n";
$code .= "\t\tprotected \$views;\n";
$code .= "\t\tprotected \$viewer;\n";
$code .= "\t\tprotected \$component;\n";
$code .= "\n";
$code .= "\t\tpublic function __construct()\n";
$code .= "\t\t{\n";
$code .= "\t\t\t\t//header(\"Content-Type: text/json\");\n";
$code .= "\t\t\t\t  \$this->prefix = 'web-api';\n";
$code .= "\t\t\t\t  \$this->module = 'App\Modules\\$ucf_module';\n";
$code .= "\t\t\t\t  \$this->views = \$this->module . '\Views';\n";
$code .= "\t\t\t\t  \$this->viewer = \$this->views . '\index';\n";
$code .= "\t\t\t\t  \$this->component = \$this->views . '\Api';\n";
$code .= "\t\t\t    \thelper(\$this->module . '\Helpers\\$ucf_module');\n";
$code .= "\t\t}\n";
$code .= "\n";
$code .= "\t\t// all users\n";
$code .= "\t\tpublic function index()\n";
$code .= "\t\t{\n";
$code .= "\t\t\t\t\$data = array(\"message\" => \"Api Online!\");\n";
$code .= "\t\t\t\treturn \$this->respond(\$data);\n";
$code .= "\t\t}\n";
$code .= "\n";
$code .= "\n";
$code .= "\t\tpublic function test(string \$format, string \$option, string \$oid)\n";
$code .= "\t\t{\n";
$code .= "\t\t\t\t//header(\"Content-Type: text/json\");\n";
$code .= "\t\t\t\theader(\"Content-Type: text/html\");\n";
$code .= "\t\t\t\t\$data = array(\n";
$code .= "\t\t\t\t\t\t\"oid\" => \$oid\n";
$code .= "\t\t\t\t);\n";
$code .= "\t\t\t\tif (\$format == \"json\") {\n";
$code .= "\t\t\t\t\t\tif (\$option == 'list') {\n";
$code .= "\t\t\t\t\t\t\t\techo(view('App\Modules\\$ucf_module\Views\\test\List\json', \$data));\n";
$code .= "\t\t\t\t\t\t}\n";
$code .= "\t\t\t\t} else {\n";
$code .= "\t\t\t\t\t\treturn (\$this->failNotFound(lang(\"$ucf_module.Api-breaches-no-option\")));\n";
$code .= "\t\t\t\t}\n";
$code .= "\t\t}\n";
$code .= "}\n";
echo($code);
