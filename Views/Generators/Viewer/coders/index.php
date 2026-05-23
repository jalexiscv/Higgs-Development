<?php

include __DIR__ . '/_shared.php';

$singular = "{$g->slc_module}-{$g->slc_component}-view";
$plural = "{$g->slc_module}-{$g->slc_component}-view-all";
$namespacedFile = $g->namespaced . 'index.php';

$code = "<?php\n";
$code .= get_development_code_copyright(['path' => $namespacedFile]);
$code .= COMMENT_HR_VARS;
$code .= COMMENT_MODULECONTROLER_VARS;
$code .= "\$data = \$parent->get_Array();\n";
$code .= "\$data['model']=model('App\\Modules\\{$g->ucf_module}\\Models\\{$g->ucf_module}_{$g->ucf_component}');\n";
$code .= "\$data['permissions'] = ['singular' => '{$singular}', 'plural' =>'{$plural}'];\n";
$code .= "\$singular = \$authentication->has_Permission(\$data['permissions']['singular']);\n";
$code .= "\$plural = \$authentication->has_Permission(\$data['permissions']['plural']);\n";
$code .= "\$author= \$data['model']->getAuthority(\$oid,safe_get_user());\n";
$code .= "\$authority= (\$singular&&\$author)?true:false;\n";
$code .= "\$submited = \$request->getPost('submited');\n";
$code .= "\$breadcrumb = \$component . '\breadcrumb';\n";
$code .= "\$validator = \$component . '\\validator';\n";
$code .= "\$form = \$component . '\\form';\n";
$code .= "\$deny = \$component . '\\deny';\n";
$code .= COMMENT_HR_BUILD;
$code .= "if (\$plural||\$authority) {\n";
$code .= "\t\tif (!empty(\$submited)) {\n";
$code .= "\t\t\t\t\$json = ['breadcrumb' => view(\$breadcrumb, \$data), 'main' => view(\$validator, \$data), 'right' => ''];\n";
$code .= "\t\t} else {\n";
$code .= "\t\t\t\t\$json = ['breadcrumb' => view(\$breadcrumb, \$data), 'main' => view(\$form, \$data), 'right' => ''];\n";
$code .= "\t\t}\n";
$code .= "} else {\n";
$code .= "\t\t\$json = ['breadcrumb' => view(\$breadcrumb, \$data), 'main' => view(\$deny, \$data), 'right' => ''];\n";
$code .= "}\n";
$code .= "echo(json_encode(\$json));\n";
$code .= "?>\n";

echo($code);
