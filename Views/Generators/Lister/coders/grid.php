<?php
/*
 * Copyright (c) 2021. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

use App\Libraries\Bootstrap;
use App\Libraries\Files;
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
    $namespaced = "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\{$ucf_options}\\List\\table.php";
    $plural = "{$slc_module}-{$slc_component}-{$slc_options}-view-all";
    $pathfiles = APPPATH . "Modules/{$ucf_module}/Views/{$ucf_component}/{$ucf_options}/_List";
    $ajax = "/{$slc_module}/{$slc_component}/{$slc_options}/ajax/list?time=\".time()";
} else {
    $model = "App\\Modules\\{$ucf_module}\\Models\\{$ucf_module}_{$ucf_component}";
    $path = '/' . $slc_module . '/' . $slc_component;
    $namespaced = "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\List\\table.php";
    $plural = "{$slc_module}-{$slc_component}-view-all";
    $pathfiles = APPPATH . "Modules/{$ucf_module}/Views/{$ucf_component}/_List";
    $ajax = "/{$slc_module}/{$slc_component}/ajax/list/";
}

$db = Database::connect("default");
$fields = $db->getFieldNames($id);

$code = "<?php\n";
$code .= get_development_code_copyright(array("path" => $namespaced));
$code .= COMMENT_MODULECONTROLER_VARS;
$code .= COMMENT_HR_MODELS;
$code .= "\$m{$slc_component} = model('{$model}');\n";

$code .= COMMENT_HR_VARS;
$code .= "\$back= \"/{$slc_module}\";\n";
$code .= "\$offset = !empty(\$request->getVar(\"offset\")) ? \$request->getVar(\"offset\") : 0;\n";
$code .= "\$search = !empty(\$request->getVar(\"search\")) ? \$request->getVar(\"search\") : \"\";\n";
$code .= "\$field = !empty(\$request->getVar(\"field\")) ? \$request->getVar(\"field\") : \"\";\n";
$code .= "\$limit = !empty(\$request->getVar(\"limit\")) ? \$request->getVar(\"limit\") : 10;\n";

$code .= "\$fields = array(\n";
foreach ($fields as $field) {
    $code .= "\t\t //\"{$field}\" => lang(\"App.{$field}\"),\n";
}
$code .= ");\n";

$code .= "//[build]--------------------------------------------------------------------------------------------------------------\n";
$code .= "\$conditions = array();\n";
$code .= "//\$m{$slc_component}->clear_AllCache();\n";


$code .= "\$rows = \$m{$slc_component}->getCachedSearch(\$conditions,\$limit, \$offset,\"{$fields["0"]} DESC\");\n";
$code .= "\$total = \$m{$slc_component}->getCountAllResults(\$conditions);\n";
$code .= "//echo(safe_dump(\$rows['sql']));\n";
$code .= "//[build]--------------------------------------------------------------------------------------------------------------\n";
$code .= "\$bgrid = \$bootstrap->get_Grid();\n";
$code .= "\$bgrid->set_Total(\$total);\n";
$code .= "\$bgrid->set_Limit(\$limit);\n";
$code .= "\$bgrid->set_Offset(\$offset);\n";
$code .= "\$bgrid->set_Class(\"P-0 m-0\");\n";
$code .= "\$bgrid->set_Limits(array(10, 20, 40, 80, 160, 320, 640, 1200, 2400));\n";
$code .= "\$bgrid->set_Headers(array(\n";

$code .= "\t\tarray(\"content\" => \"#\", \"class\" => \"text-center\talign-middle\"),\n";
foreach ($fields as $field) {
    $code .= "\t\t //array(\"content\" => lang(\"App.{$field}\"), \"class\" => \"text-center\talign-middle\"),\n";
}
$code .= "\t\tarray(\"content\" => lang(\"App.Options\"), \"class\" => \"text-center\talign-middle\"),\n";

$code .= "));\n";
$code .= "\$bgrid->set_Search(array(\"search\" => \$search, \"field\" => \$field, \"fields\" => \$fields,));\n";
$code .= "\$component = '/{$slc_module}/{$slc_component}';\n";
$code .= "\$count = \$offset;\n";
$code .= "foreach (\$rows[\"data\"] as \$row) {\n";
$code .= "\t\tif(!empty(\$row[\"{$fields["0"]}\"])){\n";
$code .= "\t\t\t\t\$count++;\n";
$code .= "\t\t\t\t//[links]-------------------------------------------------------------------------------------------------------\n";
$code .= "\t\t\t\t\$hrefView=\"\$component/view/{\$row[\"{$fields["0"]}\"]}\";\n";
$code .= "\t\t\t\t\$hrefEdit=\"\$component/edit/{\$row[\"{$fields["0"]}\"]}\";\n";
$code .= "\t\t\t\t\$hrefDelete=\"\$component/delete/{\$row[\"{$fields["0"]}\"]}\";\n";
$code .= "\t\t\t\t//[buttons]-----------------------------------------------------------------------------------------------------\n";
$code .= "\t\t\t\t\$btnView = \$bootstrap->get_Link(\"btn-view\", array(\"size\" => \"sm\",\"icon\" => ICON_VIEW,\"title\" => lang(\"App.View\"),\"href\" =>\$hrefView,\"class\" => \"btn-primary ml-1\",));\n";
$code .= "\t\t\t\t\$btnEdit = \$bootstrap->get_Link(\"btn-edit\", array(\"size\" => \"sm\",\"icon\" => ICON_EDIT,\"title\" => lang(\"App.Edit\"),\"href\" =>\$hrefEdit,\"class\" => \"btn-warning ml-1\",));\n";
$code .= "\t\t\t\t\$btnDelete = \$bootstrap->get_Link(\"btn-delete\", array(\"size\" => \"sm\",\"icon\" => ICON_DELETE,\"title\" => lang(\"App.Delete\"),\"href\" =>\$hrefDelete,\"class\" => \"btn-danger ml-1\",));\n";
$code .= "\t\t\t\t\$options = \$bootstrap->get_BtnGroup(\"btn-group\", array(\"content\" => \$btnView.\$btnEdit.\$btnDelete));\n";
$code .= "\t\t\t\t//[etc]---------------------------------------------------------------------------------------------------------\n";

$code .= "\t\t\t\t\$bgrid->add_Row(\n";
$code .= "\t\t\t\t\t\tarray(\n";
$code .= "\t\t\t\t\t\t\t\tarray(\"content\" => \$count, \"class\" => \"text-center align-middle\"),\n";
foreach ($fields as $field) {
    $code .= "\t\t\t\t\t\t\t\t //array(\"content\" => \$row['{$field}'], \"class\" => \"text-left align-middle\"),\n";
}
$code .= "\t\t\t\t\t\t\t\tarray(\"content\" => \$options, \"class\" => \"text-center align-middle\"),\n";

$code .= "\t\t\t\t\t\t)\n";
$code .= "\t\t\t\t);\n";
$code .= "\t\t}\n";
$code .= "}\n";
$code .= COMMENT_HR_BUILD;
$code .= "\$card = \$bootstrap->get_Card2(\"card-grid\", array(\n";
$code .= "\t\t\"header-title\" =>lang('{$ucf_component}.list-title'),\n";
$code .= "\t\t\"header-back\" => \$back,\n";
$code .= "\t\t\"header-add\"=>\"/{$slc_module}/{$slc_component}/create/\" . lpk(),\n";
$code .= "\t\t\"alert\" => array(\"icon\" => ICON_INFO, \"type\" => \"info\", \"title\" => lang('{$ucf_component}.list-title'), \"message\" => lang('{$ucf_component}.list-description')),\n";
$code .= "\t\t\"content\" => \$bgrid,\n";
$code .= "));\n";
$code .= "echo(\$card);\n";
$code .= "?>\n";
echo($code);
?>