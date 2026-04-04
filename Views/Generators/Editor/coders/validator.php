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
    $namespaced = "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\{$ucf_options}\\Editor\\validator.php";
    $plural = "{$slc_module}-{$slc_component}-{$slc_options}-view-all";
    $pathfiles = APPPATH . "Modules/{$ucf_module}/Views/{$ucf_component}/{$ucf_options}/_Editor";
    $ajax = "/{$slc_module}/{$slc_component}/{$slc_options}/ajax/list?time=\".time()";
} else {
    $model = "App\\Modules\\{$ucf_module}\\Models\\{$ucf_module}_{$ucf_component}";
    $path = '/' . $slc_module . '/' . $slc_component;
    $namespaced = "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\Editor\\validator.php";
    $plural = "{$slc_module}-{$slc_component}-view-all";
    $pathfiles = APPPATH . "Modules/{$ucf_module}/Views/{$ucf_component}/_List";
    $ajax = "/{$slc_module}/{$slc_component}/ajax/list/";
}

$db = Database::connect("default");
$fields = $db->getFieldNames($id);

$code = "<?php\n";
$code .= get_development_code_copyright(array("path" => $namespaced));
$code .= "\$bootstrap = service('bootstrap');\n";
$code .= "\$f = service(\"forms\",array(\"lang\" => \"{$ucf_module}_{$ucf_component}.\"));\n";

$code .= "//[Request]-----------------------------------------------------------------------------\n";
foreach ($fields as $field) {
    $code .= "//\$f->set_ValidationRule(\"{$field}\",\"trim|required\");\n";
}
$code .= COMMENT_HR_BUILD;
$code .= "if (\$f->run_Validation()) {\n";
$code .= "   \$c=view(\$component.'\\processor',\$parent->get_Array());\n";
$code .= "}else {\n";
$code .= "\$c =\$bootstrap->get_Card('validator-error', array(\n";
$code .= "    'class'=>'card-danger',\n";
$code .= "    'icon'=>'fa-duotone fa-triangle-exclamation',\n";
$code .= "    'text-class' => 'text-center',\n";
$code .= "    'text' => lang(\"App.validator-errors-message\"),\n";
$code .= "    'errors' => \$f->validation->listErrors(),\n";
$code .= "    'footer-class'=>'text-center',\n";
$code .= "    'voice'=>\"app/validator-errors-message.mp3\",\n";
$code .= "));\n";
$code .= "   \$c.=view(\$component.'\\form',\$parent->get_Array());\n";
$code .= "}\n";
$code .= "echo(\$c);\n";
$code .= "?>\n";
echo($code);
?>