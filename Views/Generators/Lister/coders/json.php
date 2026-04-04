<?php
/*
 * Copyright (c) 2021. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

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
    $namespaced = "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\{$ucf_options}\\List\\json.php";
    $plural = "{$slc_module}-{$slc_component}-{$slc_options}-view-all";
    $pathfiles = APPPATH . "Modules/{$ucf_module}/Views/{$ucf_component}/{$ucf_options}/_List";
    $ajax = "/{$slc_module}/{$slc_component}/{$slc_options}/ajax/list?time=\".time()";
} else {
    $model = "App\\Modules\\{$ucf_module}\\Models\\{$ucf_module}_{$ucf_component}";
    $path = '/' . $slc_module . '/' . $slc_component;
    $namespaced = "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\List\\json.php";
    $plural = "{$slc_module}-{$slc_component}-view-all";
    $pathfiles = APPPATH . "Modules/{$ucf_module}/Views/{$ucf_component}/_List";
    $ajax = "/{$slc_module}/{$slc_component}/ajax/list/";
}

$db = Database::connect("default");
$fields = $db->getFieldNames($id);

$code = "<?php\n";
$code .= get_development_code_copyright(array("path" => $namespaced));
$code .= "//[Inherited from ModuleController]---------------------------------------------------\n";
$code .= "// \$authentication  → service('authentication')  App\\Libraries\\Authentication\n";
$code .= "// \$bootstrap       → service('bootstrap')\n";
$code .= "// \$dates           → service('Dates')           App\\Libraries\\Dates\n";
$code .= "// \$strings         → service('strings')         App\\Libraries\\Strings\n";
$code .= "// \$request         → service('request')\n";
$code .= "// \$server          → service('server')\n";
$code .= "// \$parent          → ModuleController instance  (use \$parent->get_Array() for view data)\n";

$code .= "//[Models]---------------------------------------------------------------------------------------------------------------\n";
$code .= "\$model = model('{$model}');\n";

$code .= "//[Requests]------------------------------------------------------------------------------------------------------------\n";
$code .= "\$columns = \$request->getGet(\"columns\");\n";
$code .= "\$offset = \$request->getGet(\"offset\");\n";
$code .= "\$search = \$request->getGet(\"search\");\n";
$code .= "\$draw = empty(\$request->getGet(\"draw\")) ? 1 : \$request->getGet(\"draw\");\n";
$code .= "\$limit = empty(\$request->getGet(\"limit\")) ? 10 : \$request->getGet(\"limit\");\n";

$code .= "//[Query]---------------------------------------------------------------------------------------------------------------\n";
$code .= "\$list = \$model->getList(\$limit, \$offset, \$search);\n";
$code .= "\$recordsTotal = \$model->get_Total(\$search);\n";
$code .= "//\$sql=\$model->getLastQuery()->getQuery();\n";

$code .= "//[Asignations]---------------------------------------------------------------------------------------------------------\n";
$code .= "\$data = array();\n";
$code .= "\$component = '/{$slc_module}/{$slc_component}';\n";
$code .= "foreach (\$list as \$item) {\n";
$code .= "\t//[Buttons]---------------------------------------------------------------------------------------------------------\n";
$code .= "\t\$viewer = \"{\$component}/view/{\$item[\"{$fields["0"]}\"]}\";\n";
$code .= "\t\$editor = \"{\$component}/edit/{\$item[\"{$fields["0"]}\"]}\";\n";
$code .= "\t\$deleter = \"{\$component}/delete/{\$item[\"{$fields["0"]}\"]}\";\n";
$code .= "\t\$lviewer = \$bootstrap::get_Link('view', array('href' => \$viewer, 'icon' => ICON_VIEW, 'text' => lang(\"App.View\"), 'class' => 'btn-primary'));\n";
$code .= "\t\$leditor = \$bootstrap::get_Link('edit', array('href' => \$editor, 'icon' => ICON_EDIT, 'text' => lang(\"App.Edit\"), 'class' => 'btn-secondary'));\n";
$code .= "\t\$ldeleter = \$bootstrap::get_Link('delete', array('href' => \$deleter, 'icon' =>ICON_DELETE, 'text' => lang(\"App.Delete\"), 'class' => 'btn-danger'));\n";
$code .= "\t\$options = \$bootstrap::get_BtnGroup('options', array('content'=>array(\$lviewer, \$leditor, \$ldeleter)));\n";
$code .= "\t//[Fields]----------------------------------------------------------------------------------------------------------\n";
foreach ($fields as $field) {
    if (($field == "title") || ($field == "description")) {
        $code .= "\t\$row[\"{$field}\"] =\$strings->get_URLDecode(\$item[\"{$field}\"]);\n";
    } else {
        $code .= "\t\$row[\"{$field}\"] =\$item[\"{$field}\"];\n";
    }
}
$code .= "\t\$row[\"options\"] = \$options;\n";
$code .= "\t//[Push]------------------------------------------------------------------------------------------------------------\n";
$code .= "\tarray_push(\$data, \$row);\n";
$code .= "}\n";
$code .= "//[Build]---------------------------------------------------------------------------------------------------------------\n";
$code .= "\$json[\"draw\"] = \$draw;\n";
$code .= "\$json[\"columns\"] = \$columns;\n";
$code .= "\$json[\"offset\"] = \$offset;\n";
$code .= "\$json[\"search\"] = \$search;\n";
$code .= "\$json[\"limit\"] = \$limit;\n";
$code .= "//\$json[\"sql\"] = \$sql;\n";
$code .= "\$json[\"total\"] = \$recordsTotal;\n";
$code .= "\$json[\"data\"] = \$data;\n";
$code .= "echo(json_encode(\$json));\n";
$code .= "?>\n\n\n\n";

echo($code);
?>