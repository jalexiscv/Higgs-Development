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


$code .= "\n";
$code .= COMMENT_HR_SERVICES;
$code .= "\$bootstrap = service(\"bootstrap\");\n";
$code .= COMMENT_HR_VARS;
$code .= COMMENT_MODULECONTROLER_VARS;
$code .= "\$back= \"/{$slc_module}\";\n";
$code .= "\$table = \$bootstrap->get_DynamicTable(array(\n";
$code .= "    'id' => 'table-' . lpk(),\n";
$code .= "    'data-url' => '/{$slc_module}/api/{$slc_component}/json/list/' . lpk(),\n";
$code .= "    'buttons' => array(\n";
$code .= "        'create' => array('icon' =>ICON_ADD,'text'=>lang('App.Create'), 'href' => '/{$slc_module}/{$slc_component}/create/'.lpk(), 'class' => 'btn-secondary'),\n";
$code .= "    ),\n";
$code .= "    'cols' => array(\n";
foreach ($fields as $field) {
    $code .= "        '{$field}' => array('text' => lang('App.{$field}'), 'class' => 'text-center'),\n";
}
$code .= "        'options' => array('text' => lang('App.Options'), 'class' => 'text-center fit px-2'),\n";
$code .= "    ),\n";
$code .= "    'data-page-size' => 10,\n";
$code .= "    'data-side-pagination' => 'server'\n";
$code .= "));\n";
$code .= COMMENT_HR_BUILD;
$code .= "\$card = \$bootstrap->get_Card(\"card-view-service\", array(\n";
$code .= "\t \"title\" => lang('{$ucf_component}.list-title'),\n";
$code .= "\t \"header-back\" => \$back,\n";
$code .= "\t \"alert\" => array(\"icon\" => ICON_INFO, \"type\" => \"info\", \"title\" => lang('{$ucf_component}.list-title'), \"message\" => lang('{$ucf_component}.list-description')),\n";
$code .= "\t \"content\" => \$table,\n";
$code .= "));\n";
$code .= "echo(\$card);\n";
$code .= "?>\n";
echo($code);
?>