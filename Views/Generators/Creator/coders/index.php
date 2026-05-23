<?php

include __DIR__ . '/_shared.php';

$singular = "{$g->slc_module}-{$g->slc_component}-create";
if ($g->has_options) {
    $namespacedFile = $g->namespaced . "index.php";
    $path = '/' . $g->slc_module . '/' . $g->slc_component . '/' . $g->slc_options;
    $ajax = "/{$g->slc_module}/{$g->slc_component}/{$g->slc_options}/ajax/list?time=\".time()";
} else {
    $namespacedFile = $g->namespaced . "index.php";
    $path = '/' . $g->slc_module . '/' . $g->slc_component;
    $ajax = "/{$g->slc_module}/{$g->slc_component}/ajax/list/";
}

$code = "<?php\n";
$code .= get_development_code_copyright(array("path" => $namespacedFile));
$code .= COMMENT_HR_VARS;
$code .= COMMENT_MODULECONTROLER_VARS;
$code .= "\$data = \$parent->get_Array();\n";
$code .= "\$data['permissions'] = array('singular' => '{$singular}', \"plural\" =>false);\n";
$code .= "\$singular = \$authentication->has_Permission(\$data['permissions']['singular']);\n";
$code .= "\$submited = \$request->getPost(\"submited\");\n";
$code .= "\$validator = \$component . '\\validator';\n";
$code .= "\$breadcrumb = \$component . '\breadcrumb';\n";
$code .= "\$form = \$component . '\\form';\n";
$code .= "\$deny = \$component . '\\deny';\n";
$code .= COMMENT_HR_BUILD;
$code .= "if (\$singular) {\n";
$code .= "\t\tif (!empty(\$submited)){\n";
$code .= "\t\t\t\t\$json = array(\n";
$code .= "\t\t\t\t\t 'breadcrumb' => view(\$breadcrumb, \$data),\n";
$code .= "\t\t\t\t\t 'main' => view(\$validator, \$data),\n ";
$code .= "\t\t\t\t\t 'right' => \"\",\n";
$code .= "\t\t\t\t\t 'main_template' =>'c8c4',\n";
$code .= "\t\t\t\t );\n";
$code .= "\t\t} else {\n";
$code .= "\t\t\t\t\$json = array(\n";
$code .= "\t\t\t\t\t 'breadcrumb' => view(\$breadcrumb, \$data),\n ";
$code .= "\t\t\t\t\t 'main' => view(\$form, \$data),\n ";
$code .= "\t\t\t\t\t 'right' => \"\",\n";
$code .= "\t\t\t\t\t 'main_template' =>'c8c4',\n";
$code .= "\t\t\t\t );\n";
$code .= "\t\t}\n";
$code .= "} else {\n";
$code .= "\t\t\t\t\$json = array(\n";
$code .= "\t\t\t\t\t 'breadcrumb' => view(\$breadcrumb, \$data),\n ";
$code .= "\t\t\t\t\t 'main' => view(\$deny, \$data),\n ";
$code .= "\t\t\t\t\t 'right' => \"\",\n";
$code .= "\t\t\t\t\t 'main_template' =>'c8c4',\n";
$code .= "\t\t\t\t );\n";
$code .= "}\n";
$code .= "echo(json_encode(\$json));\n";
$code .= "?>\n";
echo($code);
