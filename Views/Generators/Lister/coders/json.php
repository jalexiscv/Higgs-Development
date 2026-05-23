<?php

include __DIR__ . '/_shared.php';

$fields = $g->fields;

$code = "<?php\n";
$code .= get_development_code_copyright(['path' => $g->namespaced . 'json.php']);
$code .= "//[Inherited from ModuleController]---------------------------------------------------\n";
$code .= "// \$authentication  → service('authentication')  App\\Libraries\\Authentication\n";
$code .= "// \$bootstrap       → service('bootstrap')\n";
$code .= "// \$dates           → service('Dates')           App\\Libraries\\Dates\n";
$code .= "// \$strings         → service('strings')         App\\Libraries\\Strings\n";
$code .= "// \$request         → service('request')\n";
$code .= "// \$server          → service('server')\n";
$code .= "// \$parent          → ModuleController instance  (use \$parent->get_Array() for view data)\n";

$code .= "//[Models]---------------------------------------------------------------------------------------------------------------\n";
$code .= "\$model = model('{$g->model}');\n";

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
$code .= "\$component = '/{$g->slc_module}/{$g->slc_component}';\n";
$code .= "foreach (\$list as \$item) {\n";
$code .= "\t//[Buttons]---------------------------------------------------------------------------------------------------------\n";
$code .= "\t\$viewer = \"{\$component}/view/{\$item[\"{$fields['0']}\"]}\";\n";
$code .= "\t\$editor = \"{\$component}/edit/{\$item[\"{$fields['0']}\"]}\";\n";
$code .= "\t\$deleter = \"{\$component}/delete/{\$item[\"{$fields['0']}\"]}\";\n";
$code .= "\t\$lviewer = \$bootstrap::get_Link('view', array('href' => \$viewer, 'icon' => ICON_VIEW, 'text' => lang(\"App.View\"), 'class' => 'btn-primary'));\n";
$code .= "\t\$leditor = \$bootstrap::get_Link('edit', array('href' => \$editor, 'icon' => ICON_EDIT, 'text' => lang(\"App.Edit\"), 'class' => 'btn-secondary'));\n";
$code .= "\t\$ldeleter = \$bootstrap::get_Link('delete', array('href' => \$deleter, 'icon' =>ICON_DELETE, 'text' => lang(\"App.Delete\"), 'class' => 'btn-danger'));\n";
$code .= "\t\$options = \$bootstrap::get_BtnGroup('options', array('content'=>array(\$lviewer, \$leditor, \$ldeleter)));\n";
$code .= "\t//[Fields]----------------------------------------------------------------------------------------------------------\n";
foreach ($fields as $field) {
    if (($field == 'title') || ($field == 'description')) {
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
