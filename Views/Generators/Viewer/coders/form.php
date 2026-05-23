<?php

include __DIR__ . '/_shared.php';

$strings = service("strings");
$sucf_component = $strings->removePluralEnding($g->ucf_component);
$namespacedFile = $g->namespaced . "form.php";
$fields = $g->fields;

$code = "<?php\n";
$code .= get_development_code_copyright(array("path" => $namespacedFile));

$code .= "use Higgs\\Frontend\\Bootstrap\\v5_3_3\\Bootstrap as BS5;\n";
$code .= COMMENT_HR_SERVICES;
$code .= "\$server = service(\"server\");\n";
$code .= "\$f = service(\"forms\",array(\"lang\" => \"{$g->ucf_module}_{$g->ucf_component}.\"));\n";

$code .= COMMENT_HR_MODELS;
//$code .= "\$model = model(\"App\\Modules\\{$g->ucf_module}\\Models\\{$g->ucf_module}_{$g->ucf_component}\");\n";

$code .= COMMENT_HR_VARS;
$code .= "\$row= \$model->get{$sucf_component}(\$oid);\n";
foreach ($fields as $field) {
    $code .= "\$r[\"{$field}\"] =\$row[\"$field\"];\n";
}

$code .= "\$back=\$f->get_Value(\"back\",\$server->get_Referer());\n";

$code .= COMMENT_HR_FIELDS;
foreach ($fields as $field) {
    if ($field == "author") {
        $code .= "\$f->add_HiddenField(\"author\",\$r[\"author\"]);\n";
    } else {
        $code .= "\$f->fields[\"{$field}\"] = \$f->get_FieldView(\"{$field}\", array(\"value\" => \$r[\"{$field}\"],\"proportion\"=>\"col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12\"));\n";
    }
}
$code .= "\$f->fields[\"cancel\"]=\$f->get_Cancel(\"cancel\", array(\"href\" =>\$back,\"text\" =>lang(\"App.Cancel\"),\"type\"=>\"secondary\",\"proportion\" =>\"col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 padding-right\"));\n";
$code .= "\$f->fields[\"edit\"] =\$f->get_Button(\"edit\", array(\"href\" =>\"/{$g->slc_module}/{$g->slc_component}/edit/\".\$oid,\"icon\"=>ICON_EDIT,\"text\" =>lang(\"App.Edit\"),\"class\"=>\"btn btn-secondary\",\"proportion\" =>\"col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 padding-right\"));\n";
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
$code .= "    'headerTitle'   => lang(\"{$g->ucf_module}_{$g->ucf_component}.view-title\"),\n";
$code .= "    'headerButtons' => [BS5::button(['content' => BS5::icon(['icon' => 'arrow-left', 'style' => 'duotone']), 'variant' => 'secondary', 'size' => 'sm', 'attributes' => ['href' => \$back]])],\n";
$code .= "    'content'       => [\"htmlContent\" =>\$f,]\n";
$code .= "]);\n";
$code .= "echo(\$card);\n";
$code .= "?>\n";

echo($code);
