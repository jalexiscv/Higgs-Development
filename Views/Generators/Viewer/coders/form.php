<?php
use Config\Database;

$strings = service("strings");

$action = "";
$module = "";
$component = "";
$f = service("forms", array("lang" => "Nexus."));
/** request * */
$r["client"] = $f->get_Value("client", strtoupper(uniqid()));
$r["time"] = $f->get_Value("time", service("dates")::get_Time());
/** @var TYPE_NAME $oid */
$id = $oid;
$eid = explode("_", $id);
$ucf_module = safe_ucfirst($eid[0]);
$ucf_component = safe_ucfirst($eid[1]);
$ucf_options = safe_ucfirst(@$eid[2]);
$slc_module = safe_strtolower($eid[0]);
$slc_component = safe_strtolower($eid[1]);
$slc_options = safe_strtolower(@$eid[2]);
$sucf_component = $strings->removePluralEnding($ucf_component);

if (count($eid) == 3) {
    $model = "App\\Modules\\{$ucf_module}\\Models\\{$ucf_module}_{$ucf_component}_{$ucf_options}";
    $path = '/' . $slc_module . '/' . $slc_component . '/' . $slc_options;
    $namespaced = "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\{$ucf_options}\\Editor\\form.php";
    $plural = "{$slc_module}-{$slc_component}-{$slc_options}-view-all";
    $pathfiles = APPPATH . "Modules/{$ucf_module}/Views/{$ucf_component}/{$ucf_options}/View";
    $ajax = "/{$slc_module}/{$slc_component}/{$slc_options}/ajax/list?time=\".time()";
} else {
    $model = "App\\Modules\\{$ucf_module}\\Models\\{$ucf_module}_{$ucf_component}";
    $path = '/' . $slc_module . '/' . $slc_component;
    $namespaced = "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\Editor\\form.php";
    $plural = "{$slc_module}-{$slc_component}-view-all";
    $pathfiles = APPPATH . "Modules/{$ucf_module}/Views/{$ucf_component}/_List";
    $ajax = "/{$slc_module}/{$slc_component}/ajax/list/";
}

$db = Database::connect("default");
$fields = $db->getFieldNames($id);

$code = "<?php\n";
$code .= get_development_code_copyright(array("path" => $namespaced));

$code .= "use Higgs\\Frontend\\Bootstrap\\v5_3_3\\Bootstrap as BS5;\n";
$code .= "//[Inherited from ModuleController]---------------------------------------------------\n";
$code .= "// \$authentication  → service('authentication')  App\\Libraries\\Authentication\n";
$code .= "// \$bootstrap       → service('bootstrap')\n";
$code .= "// \$dates           → service('Dates')           App\\Libraries\\Dates\n";
$code .= "// \$strings         → service('strings')         App\\Libraries\\Strings\n";
$code .= "// \$request         → service('request')\n";
$code .= "// \$server          → service('server')\n";
$code .= "// \$parent          → ModuleController instance  (use \$parent->get_Array() for view data)\n";
$code .= "\$server = service(\"server\");\n";
$code .= "\$f = service(\"forms\",array(\"lang\" => \"{$ucf_module}_{$ucf_component}.\"));\n";

$code .= COMMENT_HR_MODELS;
//$code .= "\$model = model(\"App\\Modules\\{$ucf_module}\\Models\\{$ucf_module}_{$ucf_component}\");\n";

$code .= COMMENT_HR_VARS;
$code .= "\$row= \$model->get{$sucf_component}(\$oid);\n";
foreach ($fields as $field) {
    $code .= "\$r[\"{$field}\"] =\$row[\"$field\"];\n";
}

$code .= "\$back=\$f->get_Value(\"back\",\$server->get_Referer());\n";

$code .= "//[Fields]-----------------------------------------------------------------------------\n";
foreach ($fields as $field) {
    if ($field == "author") {
        $code .= "\$f->add_HiddenField(\"author\",\$r[\"author\"]);\n";
    } else {
        $code .= "\$f->fields[\"{$field}\"] = \$f->get_FieldView(\"{$field}\", array(\"value\" => \$r[\"{$field}\"],\"proportion\"=>\"col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12\"));\n";
    }
}
$code .= "\$f->fields[\"cancel\"]=\$f->get_Cancel(\"cancel\", array(\"href\" =>\$back,\"text\" =>lang(\"App.Cancel\"),\"type\"=>\"secondary\",\"proportion\" =>\"col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 padding-right\"));\n";
$code .= "\$f->fields[\"edit\"] =\$f->get_Button(\"edit\", array(\"href\" =>\"/{$slc_module}/{$slc_component}/edit/\".\$oid,\"text\" =>lang(\"App.Edit\"),\"class\"=>\"btn btn-secondary\",\"proportion\" =>\"col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 padding-right\"));\n";
$code .= COMMENT_HR_GROUPS;
$skipped = ["author", "created_at", "updated_at", "deleted_at"];
$visible_fields = array_values(array_filter($fields, fn($f) => !in_array($f, $skipped)));
$chunks = array_chunk($visible_fields, 3);
$grupo = 0;
foreach ($chunks as $chunk) {
    $grupo++;
    $fields_code = implode('.', array_map(fn($f) => "\$f->fields[\"{$f}\"]", $chunk));
    $code .= "\$f->groups[\"g{$grupo}\"]=\$f->get_Group(array(\"legend\"=>\"\",\"fields\"=>({$fields_code})));\n";
}
$code .= COMMENT_HR_BUTTONS;
$code .= "\$f->groups[\"gy\"] =\$f->get_GroupSeparator();\n";
$code .= "\$f->groups[\"gz\"] = \$f->get_Buttons(array(\"fields\"=>\$f->fields[\"edit\"].\$f->fields[\"cancel\"]));\n";
$code .= COMMENT_HR_BUILD;
$code .= "\$card = BS5::card([\n";
$code .= "    'headerTitle'   => lang(\"{$ucf_module}_{$ucf_component}.view-title\"),\n";
$code .= "    'headerButtons' => [BS5::button(['content' => BS5::icon(['icon' => 'arrow-left', 'style' => 'duotone']), 'variant' => 'secondary', 'size' => 'sm', 'attributes' => ['href' => \$back]])],\n";
$code .= "    'content'       => \$f,\n";
$code .= "]);\n";
$code .= "echo(\$card);\n";
$code .= "?>\n";
echo($code);
?>