<?php
/*
 * Copyright (c) 2021-2021. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
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
    $namespaced = "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\{$ucf_options}\\Creator\\validator.php";
    $plural = "{$slc_module}-{$slc_component}-{$slc_options}-view-all";
    $pathfiles = APPPATH . "Modules/{$ucf_module}/Views/{$ucf_component}/{$ucf_options}/_Creator";
    $ajax = "/{$slc_module}/{$slc_component}/{$slc_options}/ajax/list?time=\".time()";
} else {
    $model = "App\\Modules\\{$ucf_module}\\Models\\{$ucf_module}_{$ucf_component}";
    $path = '/' . $slc_module . '/' . $slc_component;
    $namespaced = "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\Creator\\validator.php";
    $plural = "{$slc_module}-{$slc_component}-view-all";
    $pathfiles = APPPATH . "Modules/{$ucf_module}/Views/{$ucf_component}/_List";
    $ajax = "/{$slc_module}/{$slc_component}/ajax/list/";
}

$db = Database::connect("default");
$fields = $db->getFieldNames($id);

$code = "<?php\n";
$code .= get_development_code_copyright(array("path" => $namespaced));

$code .= "use Higgs\\Frontend\\Bootstrap\\v5_3_3\\Bootstrap as BS5;\n";
$code .= "\$f = service(\"forms\",array(\"lang\" => \"{$ucf_module}_{$ucf_component}.\"));\n";

$code .= "//[Request]-----------------------------------------------------------------------------\n";
foreach ($fields as $field) {
    $code .= "//\$f->set_ValidationRule(\"{$field}\",\"trim|required\");\n";
}
$code .= "//[Validation]-----------------------------------------------------------------------------\n";
$code .= "if (\$f->run_Validation()) {\n";
$code .= "    \$c = view(\$component.'\\\\processor', \$parent->get_Array());\n";
$code .= "} else {\n";
$code .= "    \$_icon_col  = BS5::col(['attributes' => ['class' => 'text-center py-3'], 'content' => BS5::icon(['icon' => 'triangle-exclamation', 'style' => 'duotone', 'size' => '2xl'])]);\n";
$code .= "    \$_msg_col   = BS5::col(['attributes' => ['class' => 'text-center pb-2'], 'content' => lang('App.validator-errors-message')]);\n";
$code .= "    \$_errors_col= BS5::col(['attributes' => ['class' => 'pb-2'], 'content' => \$f->validation->listErrors()]);\n";
$code .= "    \$_content   = BS5::row(['attributes' => ['class' => 'justify-content-center'], 'content' => \$_icon_col.\$_msg_col.\$_errors_col]);\n";
$code .= "    \$c = BS5::card([\n";
$code .= "        'headerTitle' => lang('App.validator-errors-title'),\n";
$code .= "        'headerClass' => 'bg-danger text-white',\n";
$code .= "        'content'     => \$_content,\n";
$code .= "        'attributes'  => ['class' => 'border-danger shadow-sm'],\n";
$code .= "    ]);\n";
$code .= "    \$c .= view(\$component.'\\\\form', \$parent->get_Array());\n";
$code .= "}\n";
$code .= "//[Build]-----------------------------------------------------------------------------\n";
$code .= "echo(\$c);\n";
$code .= "?>\n";
echo($code);
?>