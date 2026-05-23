<?php

include __DIR__ . '/_shared.php';

$code = "<?php\n";
$code .= get_development_code_copyright(array("path" => $g->namespaced . "index.php"));
$code .= COMMENT_HR_VARS;
$code .= COMMENT_MODULECONTROLER_VARS;

$code .= "\$data = \$parent->get_Array();\n";
$code .= "\$data['permissions'] = array('singular' => false, \"plural\" =>'{$g->plural}');\n";
$code .= "\$plural = \$authentication->has_Permission(\$data['permissions']['plural']);\n";
$code .= "\$submited = \$request->getPost(\"submited\");\n";

$code .= "\$breadcrumb = \$component . '\breadcrumb';\n";
$code .= "\$validator = \$component . '\\validator';\n";
$code .= "\$table = \$component . '\\grid';\n";
$code .= "\$deny = \$component . '\\deny';\n";

$code .= COMMENT_HR_BUILD;
$code .= "if (\$plural) {\n";
$code .= "\t\tif (!empty(\$submited)) {\n";
$code .= "\t\t\t\t\$json = array('breadcrumb' => view(\$breadcrumb, \$data), 'main' => view(\$validator, \$data), 'right' => \"\");\n";
$code .= "\t\t} else {\n";
$code .= "\t\t\t\t\$json = array('breadcrumb' => view(\$breadcrumb, \$data), 'main' => view(\$table, \$data), 'right' => \"\");\n";
$code .= "\t\t}\n";
$code .= "} else {\n";
$code .= "\t\t\$json = array('breadcrumb' => view(\$breadcrumb, \$data), 'main' => view(\$deny, \$data), 'right' => \"\");\n";
$code .= "}\n";
$code .= "echo(json_encode(\$json));\n";
$code .= "?>\n";
echo($code);
