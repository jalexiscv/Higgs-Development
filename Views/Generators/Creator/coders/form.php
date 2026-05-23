<?php

include __DIR__ . '/_shared.php';

$namespacedFile = $g->namespaced . "form.php";
$fields = $g->fields;

$code = "<?php\n";
$code .= get_development_code_copyright(array("path" => $namespacedFile));

$code .= "use Higgs\\Frontend\\Bootstrap\\v5_3_3\\Bootstrap as BS5;\n";
$code .= COMMENT_HR_SERVICES;
$code .= "\$b = service(\"bootstrap\");\n";
$code .= "\$f = service(\"forms\",array(\"lang\" => \"{$g->ucf_module}_{$g->ucf_component}.\"));\n";
$code .= "\$server = service(\"server\");\n";
$code .= COMMENT_HR_MODELS;
$code .= "//\$model = model(\"App\\Modules\\{$g->ucf_module}\\Models\\{$g->ucf_module}_{$g->ucf_component}\");\n";

$code .= COMMENT_HR_VARS;
$code .= COMMENT_MODULECONTROLER_VARS;
$code .= "\$back=\$server->get_Referer();\n";
$code .= "\$r[\"back\"] = \$f->get_Value(\"back\",\$back);\n";
foreach ($fields as $field) {
    if ($field == "author") {
        $code .= "\$r[\"{$field}\"] = \$f->get_Value(\"{$field}\",safe_get_user());\n";
    } else if ($field == "date") {
        $code .= "\$r[\"{$field}\"] = \$f->get_Value(\"{$field}\",service(\"dates\")::get_Date());\n";
    } else if ($field == "time") {
        $code .= "\$r[\"{$field}\"] = \$f->get_Value(\"{$field}\",service(\"dates\")::get_Time());\n";
    } else {
        $code .= "\$r[\"{$field}\"] = \$f->get_Value(\"{$field}\");\n";
    }
}


$code .= COMMENT_HR_FIELDS;
$code .= "\$f->add_HiddenField(\"back\",\$r[\"back\"]);\n";
foreach ($fields as $field) {
    if ($field == "author") {
        $code .= "\$f->add_HiddenField(\"author\",\$r[\"author\"]);\n";
    } else {
        $code .= "\$f->fields[\"{$field}\"] = \$f->get_FieldText(\"{$field}\", array(\"value\" => \$r[\"{$field}\"],\"proportion\"=>\"col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12\"));\n";
    }
}
$code .= "\$f->fields[\"cancel\"]=\$f->get_Cancel(\"cancel\", array(\"href\" =>\$r[\"back\"],\"text\" =>lang(\"App.Cancel\"),\"type\"=>\"secondary\",\"proportion\" =>\"col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 padding-right\"));\n";
$code .= "\$f->fields[\"submit\"] =\$f->get_Submit(\"submit\", array(\"value\" =>lang(\"App.Create\"),\"proportion\" =>\"col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 padding-left\"));\n";


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
$code .= "\$f->groups[\"gz\"] = \$f->get_Buttons(array(\"fields\"=>\$f->fields[\"submit\"].\$f->fields[\"cancel\"]));\n";

$code .= COMMENT_HR_BUILD;
$code .= "\$card = BS5::card([\n";
$code .= "    'headerTitle'   => lang(\"{$g->ucf_module}_{$g->ucf_component}.create-title\"),\n";
$code .= "    'headerButtons' => [BS5::button(['content' => BS5::icon(['icon' => 'arrow-left', 'style' => 'duotone']), 'variant' => 'secondary', 'size' => 'sm', 'attributes' => ['href' => \$back]])],\n";
$code .= "    'content'       => [\"htmlContent\" =>\$f,],\n";
$code .= "]);\n";
$code .= "echo(\$card);\n";
$code .= "?>\n";

echo($code);
