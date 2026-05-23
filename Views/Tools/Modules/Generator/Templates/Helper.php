<?php

$strings = service('strings');
/** @var string $module */
$ucf_module = $strings->get_Ucfirst($module);
$lc_module = $strings->get_Strtolower($module);

$code = "<?php\n";
$code .= "\n";
$code .= "if (!function_exists(\"generate_{$lc_module}_permissions\")) {\n";
$code .= "\n";
$code .= "\t\t/**\n";
$code .= "\t\t * Permite registrar los permisos asociados al modulo, tecnicamente su\n";
$code .= "\t\t * ejecucion regenera los permisos asignables definidos por el modulo DISA\n";
$code .= "\t\t */\n";
$code .= "\t\tfunction generate_{$lc_module}_permissions():void\n";
$code .= "\t\t{\n";
$code .= "\t\t\t\t\$permissions = array(\n";
$code .= "\t\t\t\t\t\t\"{$lc_module}-access\",\n";
$code .= "\t\t\t\t);\n";
$code .= "\t\t\t\tgenerate_permissions(\$permissions, \"{$lc_module}\");\n";
$code .= "\t\t}\n";
$code .= "\n";
$code .= "}\n";
$code .= "\n";
$code .= "if (!function_exists(\"get_{$lc_module}_sidebar\")) {\n";
$code .= "\t\tfunction get_{$lc_module}_sidebar(\$active_url = false):string\n";
$code .= "\t\t{\n";
$code .= "\t\t\t\t\$bootstrap = service(\"bootstrap\");\n";
$code .= "\t\t\t\t\$lpk = safe_strtolower(pk());\n";
$code .= "\t\t\t\t\$options = array(\n";
$code .= "\t\t\t\t\t\t\"home\" => array(\"text\" => lang(\"App.Home\"), \"href\" => \"/{$lc_module}/\", \"svg\" => \"home.svg\"),\n";
$code .= "\t\t\t\t\t\t\"settings\" => array(\"text\" => lang(\"App.Settings\"), \"href\" => \"/{$lc_module}/settings/home/\" . lpk(), \"icon\" => ICON_TOOLS, \"permission\" => \"{$lc_module}-access\"),\n";
$code .= "\t\t\t\t);\n";
$code .= "\t\t\t\t\$o = get_application_custom_sidebar(\$options, \$active_url);\n";
$code .= "\t\t\t\t\$return = \$bootstrap->get_NavPills(\$o, \$active_url);\n";
$code .= "\t\t\t\treturn (\$return);\n";
$code .= "\t\t}\n";
$code .= "\t}\n";
$code .= "\n";
$code .= "?>\n";
echo($code);
