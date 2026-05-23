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
$code .= "/** @var string \$parent */\n";
$code .= "/** @var string \$views */\n";
$code .= "/** @var string \$prefix */\n";
$code .= "/** @var object \$parent */\n";
$code .= "\$server = service('server');\n";
$code .= "\$benchmark = service('timer');\n";
$code .= "\$benchmark->start('time');\n";
$code .= "\$version = round((\$server->get_DirectorySize(APPPATH . 'Modules/$ucf_module') / 102400), 6);\n";
$code .= "\$data = \$parent->get_Array();\n";
$code .= "\$rviews = array(\n";
$code .= "\t\t\"default\" => \"{\$views}\E404\index\",\n";
$code .= "\t\t\"$lc_module-denied\" => \"{\$views}\Denied\index\",\n";
$code .= "\t\t\"$lc_module-home\" => \"{\$views}\Home\index\",\n";
$code .= "\t\t//[others]------------------------------------------------------------------------------------------\n";
$code .= ");\n";
$code .= "\$uri = !isset(\$rviews[\$prefix]) ? \$rviews[\"default\"] : \$rviews[\$prefix];\n";
$code .= "\$json = view(\$uri, \$data);\n";
$code .= "//[build]---------------------------------------------------------------------------------------------------------------\n";
$code .= "\$assign = array();\n";
$code .= "\$assign['theme'] = \"Higgs\";\n";
$code .= "\$assign['main_template'] = safe_json(\$json, 'main_template', 'c8c4');\n";
$code .= "\$assign['breadcrumb'] = safe_json(\$json, 'breadcrumb');\n";
$code .= "\$assign['main'] = safe_json(\$json, 'main');\n";
$code .= "\$assign['left'] = get_{$lc_module}_sidebar();\n";
$code .= "\$assign['right'] = safe_json(\$json, 'right') . get_application_copyright();\n";
$code .= "\$assign['logo_portrait'] = get_logo(\"logo_portrait\");\n";
$code .= "\$assign['logo_landscape'] = get_logo(\"logo_landscape\");\n";
$code .= "\$assign['logo_portrait_light'] = get_logo(\"logo_portrait_light\");\n";
$code .= "\$assign['logo_landscape_light'] = get_logo(\"logo_landscape_light\");\n";
$code .= "\$assign['type'] = safe_json(\$json, 'type');\n";
$code .= "\$assign['canonical'] = safe_json(\$json, 'canonical');\n";
$code .= "\$assign['title'] = safe_json(\$json, 'title');\n";
$code .= "\$assign['description'] = safe_json(\$json, 'description');\n";
$code .= "\$assign['categories'] = safe_json(\$json, 'categories');\n";
$code .= "\$assign['featureds'] = safe_json(\$json, 'featureds');\n";
$code .= "\$assign['sponsoreds'] = safe_json(\$json, 'sponsoreds');\n";
$code .= "\$assign['articles'] = safe_json(\$json, 'articles');\n";
$code .= "\$assign['themostseens'] = safe_json(\$json, 'themostseens');\n";
$code .= "\$assign['article'] = safe_json(\$json, 'article');\n";
$code .= "\$assign['next'] = safe_json(\$json, 'next');\n";
$code .= "\$assign['previus'] = safe_json(\$json, 'previus');\n";
$code .= "\$assign['messenger'] = true;\n";
$code .= "\$assign['messenger_users'] = \"\";\n";
$code .= "\$benchmark->stop('time');\n";
$code .= "\$assign['benchmark'] = \$benchmark->getElapsedTime('time', 4);\n";
$code .= "\$assign['modals'] =safe_module_modal();\n";
$code .= "\$assign['version'] = \$version;\n";
$code .= "//[print]---------------------------------------------------------------------------------------------------------------\n";
$code .= "\$template = view(\"App\Views\Themes\Higgs\index\", \$assign);\n";
$code .= "echo(\$template);\n";
$code .= "?>\n";
echo $code;
