<?php

use Config\Database;

$action = "";
$module = "";
$component = "";
$f = service("forms", array("lang" => "Nexus."));
/** request * */
$r["client"] = $f->get_Value("client", strtoupper(uniqid()));
$r["time"] = $f->get_Value("time", service("dates")::get_Time());
$id = $oid;
$eid = explode("_", $id);
$ucf_module = safe_ucfirst($eid[0]);
$ucf_component = safe_ucfirst($eid[1]);
$ucf_options = safe_ucfirst(@$eid[2]);
$slc_module = safe_strtolower($eid[0]);
$slc_component = safe_strtolower($eid[1]);
$slc_options = safe_strtolower(@$eid[2]);

if (count($eid) == 3) {
    $model = "App\\Modules\\{$ucf_module}\\Models\\{$ucf_module}_{$ucf_component}_{$ucf_options}";
    $path = '/' . $slc_module . '/' . $slc_component . '/' . $slc_options;
    $namespaced = "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\{$ucf_options}\\Delete\\index.php";
    $singular = "{$slc_module}-{$slc_component}-{$slc_options}-delete";
    $plural = "{$slc_module}-{$slc_component}-{$slc_options}-delete-all";
    $pathfiles = APPPATH . "Modules/{$ucf_module}/Views/{$ucf_component}/{$ucf_options}/Delete";
    $ajax = "/{$slc_module}/{$slc_component}/{$slc_options}/ajax/list?time=\".time()";
} else {
    $model = "App\\Modules\\{$ucf_module}\\Models\\{$ucf_module}_{$ucf_component}";
    $path = '/' . $slc_module . '/' . $slc_component;
    $namespaced = "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\Delete\\index.php";
    $singular = "{$slc_module}-{$slc_component}-delete";
    $plural = "{$slc_module}-{$slc_component}-delete-all";
    $pathfiles = APPPATH . "Modules/{$ucf_module}/Views/{$ucf_component}/Delete";
    $ajax = "/{$slc_module}/{$slc_component}/ajax/list/";
}

$db = Database::connect("default");
$fields = $db->getFieldNames($id);


$code = "<?php\n";
$code .= get_development_code_copyright(array("path" => $namespaced));

$code .= COMMENT_HR_VARS;
$code .= COMMENT_MODULECONTROLER_VARS;
$code .= "\$data = \$parent->get_Array();\n";
$code .= "\$data['model']=model(\"App\\Modules\\{$ucf_module}\\Models\\{$ucf_module}_{$ucf_component}\");\n";
$code .= "\$data['permissions'] = array('singular' => '{$singular}', \"plural\" =>'{$plural}');\n";
$code .= "\$singular = \$authentication->has_Permission(\$data['permissions']['singular']);\n";
$code .= "\$plural = \$authentication->has_Permission(\$data['permissions']['plural']);\n";
$code .= "\$author= \$data['model']->getAuthority(\$oid,safe_get_user());\n";
$code .= "\$authority= (\$singular&&\$author)?true:false;\n";
$code .= "\$submited = \$request->getPost(\"submited\");\n";
$code .= "\$breadcrumb = \$component . '\breadcrumb';\n";
$code .= "\$validator = \$component . '\\validator';\n";
$code .= "\$form = \$component . '\\form';\n";
$code .= "\$deny = \$component . '\\deny';\n";
$code .= COMMENT_HR_BUILD;
$code .= "if (\$plural||\$authority) {\n";
$code .= "\t\tif (!empty(\$submited)) {\n";
$code .= "\t\t\t\t\$json = array('breadcrumb' => view(\$breadcrumb, \$data), 'main' => view(\$validator, \$data), 'right' => \"\");\n";
$code .= "\t\t} else {\n";
$code .= "\t\t\t\t\$json = array('breadcrumb' => view(\$breadcrumb, \$data), 'main' => view(\$form, \$data), 'right' => \"\");\n";
$code .= "\t\t}\n";
$code .= "} else {\n";
$code .= "\t\t\$json = array('breadcrumb' => view(\$breadcrumb, \$data), 'main' => view(\$deny, \$data), 'right' => \"\");\n";
$code .= "}\n";
$code .= "echo(json_encode(\$json));\n";
$code .= "?>\n";
echo($code);
?>