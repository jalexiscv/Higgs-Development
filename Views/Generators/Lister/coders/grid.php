<?php

include __DIR__ . '/_shared.php';

$fields = $g->fields;
$pk = $fields[0] ?? 'id';

$code = "<?php\n";
$code .= "use Higgs\\Frontend\\Bootstrap\\v5_3_3\\Extras\\DataTable;\n";
$code .= "use Higgs\\Frontend\\Bootstrap\\v5_3_3\\Interface\\Button;\n";
$code .= "use Higgs\\Frontend\\Bootstrap\\v5_3_3\\Interface\\Card;\n";
$code .= "use Higgs\\Frontend\\Bootstrap\\v5_3_3\\Interface\\Alert;\n";
$code .= "\n";
$code .= get_development_code_copyright(['path' => $g->namespaced . 'grid.php']);
$code .= COMMENT_MODULECONTROLER_VARS;
$code .= COMMENT_HR_MODELS;
$code .= "\$m{$g->slc_component} = model('{$g->model}');\n";

$code .= COMMENT_HR_VARS;
$code .= "\$back = \"/{$g->slc_module}\";\n";
$code .= "\$component = '/{$g->slc_module}/{$g->slc_component}';\n\n";
$code .= "// Server-Side Pagination\n";
$code .= "\$currentPage = max(1, (int)(\$request->getVar(\"page\") ?? 1));\n";
$code .= "\$perPage     = (int)(\$request->getVar(\"per_page\") ?? 10);\n";
$code .= "\$offset      = (\$currentPage - 1) * \$perPage;\n";
$code .= "\$search      = !empty(\$request->getVar(\"search\")) ? \$request->getVar(\"search\") : \"\";\n";
$code .= "\$field       = !empty(\$request->getVar(\"field\"))  ? \$request->getVar(\"field\")  : \"\";\n";
$code .= "\$limit       = \$perPage;\n\n";

$code .= "\$fields = array(\n";
foreach ($fields as $field) {
    $code .= "\t\t //\"{$field}\" => lang(\"App.{$field}\"),\n";
}
$code .= ");\n";

$code .= COMMENT_HR_BUILD;
$code .= "\$conditions = array();\n";
$code .= "//\$m{$g->slc_component}->clear_AllCache();\n\n";

$code .= "// Query database with server-side pagination\n";
$code .= "\$rows  = \$m{$g->slc_component}->getCachedSearch(\$conditions, \$limit, \$offset, \"{$pk} DESC\");\n";
$code .= "\$total = \$m{$g->slc_component}->getCountAllResults(\$conditions);\n\n";

$code .= "//[prepare data for DataTable]-----------------------------------------------------------------------------------------\n";
$code .= "\$tableData = [];\n";
$code .= "\$count = \$offset;\n";
$code .= "foreach (\$rows[\"data\"] as \$row) {\n";
$code .= "\t\tif (!empty(\$row[\"{$pk}\"])) {\n";
$code .= "\t\t\t\$count++;\n";
$code .= "\t\t\t//[links]-------------------------------------------------------------------------------------------------------\n";
$code .= "\t\t\t\$hrefView   = \"\$component/view/{\$row[\"{$pk}\"]}\";\n";
$code .= "\t\t\t\$hrefEdit   = \"\$component/edit/{\$row[\"{$pk}\"]}\";\n";
$code .= "\t\t\t\$hrefDelete = \"\$component/delete/{\$row[\"{$pk}\"]}\";\n\n";
$code .= "\t\t\t//[buttons]-----------------------------------------------------------------------------------------------------\n";
$code .= "\t\t\t\$btnView = (new Button([\n";
$code .= "\t\t\t\t\"size\"       => \"sm\",\n";
$code .= "\t\t\t\t\"icon\"       => ICON_VIEW,\n";
$code .= "\t\t\t\t\"variant\"    => \"primary\",\n";
$code .= "\t\t\t\t\"attributes\" => [\"href\" => \$hrefView, \"class\" => \"btn-sm ml-1\", \"title\" => lang(\"App.View\")]\n";
$code .= "\t\t\t]))->render();\n\n";

$code .= "\t\t\t\$btnEdit = (new Button([\n";
$code .= "\t\t\t\t\"size\"       => \"sm\",\n";
$code .= "\t\t\t\t\"icon\"       => ICON_EDIT,\n";
$code .= "\t\t\t\t\"variant\"    => \"warning\",\n";
$code .= "\t\t\t\t\"attributes\" => [\"href\" => \$hrefEdit, \"class\" => \"btn-sm ml-1\", \"title\" => lang(\"App.Edit\")]\n";
$code .= "\t\t\t]))->render();\n\n";

$code .= "\t\t\t\$btnDelete = (new Button([\n";
$code .= "\t\t\t\t\"size\"       => \"sm\",\n";
$code .= "\t\t\t\t\"icon\"       => ICON_DELETE,\n";
$code .= "\t\t\t\t\"variant\"    => \"danger\",\n";
$code .= "\t\t\t\t\"attributes\" => [\"href\" => \$hrefDelete, \"class\" => \"btn-sm ml-1\", \"title\" => lang(\"App.Delete\")]\n";
$code .= "\t\t\t]))->render();\n\n";

$code .= "\t\t\t\$options = \$bootstrap->get_BtnGroup(\"btn-group\", array(\"content\" => \$btnView . \$btnEdit . \$btnDelete));\n\n";

$code .= "\t\t\t//[add row to table data]---------------------------------------------------------------------------------------\n";
$code .= "\t\t\t\$tableData[] = [\n";
$code .= "\t\t\t\t'count' => [\n";
$code .= "\t\t\t\t\t\"value\" => \$count,\n";
$code .= "\t\t\t\t\t\"class\" => \"text-center align-middle\",\n";
$code .= "\t\t\t\t\t\"style\" => \"width: 80px;\"\n";
$code .= "\t\t\t\t],\n";

foreach ($fields as $field) {
    $code .= "\t\t\t\t//'$field' => [\n";
    $code .= "\t\t\t\t//\t\"value\" => \$row['$field'],\n";
    $code .= "\t\t\t\t//\t\"class\" => \"text-left align-middle\"\n";
    $code .= "\t\t\t\t//],\n";
}

$code .= "\t\t\t\t'options' => [\n";
$code .= "\t\t\t\t\t\"value\" => \$options,\n";
$code .= "\t\t\t\t\t\"class\" => \"text-center align-middle text-nowrap\",\n";
$code .= "\t\t\t\t\t\"style\" => \"width: 120px;\"\n";
$code .= "\t\t\t\t]\n";
$code .= "\t\t\t];\n";
$code .= "\t\t}\n";
$code .= "}\n\n";

$code .= "//[configure DataTable]-------------------------------------------------------------------------------------------------\n";
$code .= "\$dataTable = new DataTable([\n";
$code .= "\t\t'id'              => '{$g->slc_component}-datatable',\n";
$code .= "\t\t'columns'         => [\n";
$code .= "\t\t\t'count'   => [\"title\" => \"#\",                      \"class\" => \"text-center align-middle\"],\n";

foreach ($fields as $field) {
    $code .= "\t\t\t//'$field' => [\"title\" => lang(\"App.$field\"), \"class\" => \"text-center align-middle\"],\n";
}

$code .= "\t\t\t'options' => [\"title\" => lang(\"App.Options\"), \"class\" => \"text-center align-middle\"]\n";
$code .= "\t\t],\n";
$code .= "\t\t'data'            => \$tableData,\n";
$code .= "\t\t'searchable'      => true,\n";
$code .= "\t\t'pagination'      => true,\n";
$code .= "\t\t'perPage'         => \$perPage,\n";
$code .= "\t\t'perPageOptions'  => [10, 25, 50, 100, 250, 500],\n";
$code .= "\t\t'tableAttributes' => ['class' => 'table-sm'],\n";
$code .= "\t\t'serverSide'      => true,\n";
$code .= "\t\t'totalRecords'    => \$total,\n";
$code .= "\t\t'currentPage'     => \$currentPage\n";
$code .= "]);\n\n";

$code .= COMMENT_HR_BUILD;
$code .= "\$headerButtons = [];\n\n";
$code .= "\$btnBack = (new Button([\n";
$code .= "\t\t\"size\"       => \"sm\",\n";
$code .= "\t\t\"icon\"       => ICON_BACK,\n";
$code .= "\t\t\"variant\"    => \"secondary\",\n";
$code .= "\t\t\"attributes\" => [\"href\" => \$back, \"class\" => \"ml-1\", \"title\" => lang(\"App.Back\")]\n";
$code .= "]))->render();\n";
$code .= "\$headerButtons[] = \$btnBack;\n\n";

$code .= "\$btnAdd = (new Button([\n";
$code .= "\t\t\"size\"       => \"sm\",\n";
$code .= "\t\t\"icon\"       => ICON_ADD,\n";
$code .= "\t\t\"variant\"    => \"success\",\n";
$code .= "\t\t\"attributes\" => [\"href\" => \"/{$g->slc_module}/{$g->slc_component}/create/\" . lpk(), \"class\" => \"ml-1\", \"title\" => lang(\"App.Add\")]\n";
$code .= "]))->render();\n";
$code .= "\$headerButtons[] = \$btnAdd;\n\n";

$code .= "\$alertContent = '<strong>' . lang('{$g->ucf_module}_{$g->ucf_component}.list-title') . '</strong>'\n";
$code .= "\t\t. '<p class=\"mb-0\">' . lang('{$g->ucf_module}_{$g->ucf_component}.list-description') . '</p>';\n\n";

$code .= "\$alert = (new Alert([\n";
$code .= "\t\t'type'        => 'info',\n";
$code .= "\t\t'icon'        => ICON_INFO,\n";
$code .= "\t\t'htmlContent' => \$alertContent\n";
$code .= "]));\n\n";

$code .= "\$card = (new Card([\n";
$code .= "\t\t'attributes'   => ['class' => 'card-grid shadow-sm'],\n";
$code .= "\t\t'headerTitle'  => lang('{$g->ucf_module}_{$g->ucf_component}.list-title'),\n";
$code .= "\t\t'headerButtons' => \$headerButtons,\n";
$code .= "\t\t'content'      => [\n";
$code .= "\t\t\t'htmlContent' => \$alert->render() . \$dataTable->render(),\n";
$code .= "\t\t\t'class'       => 'p-0'\n";
$code .= "\t\t]\n";
$code .= "]))->render();\n";
$code .= "echo(\$card);\n";
$code .= "?>\n";
echo($code);
