<?php

include __DIR__ . '/_shared.php';

$fields = $g->fields;

$code = "<?php\n";
$code .= get_development_code_copyright(['path' => $g->namespaced . 'table.php']);

$code .= "\n";
$code .= COMMENT_HR_SERVICES;
$code .= "\$bootstrap = service('bootstrap');\n";
$code .= COMMENT_HR_VARS;
$code .= COMMENT_MODULECONTROLER_VARS;
$code .= "\$back= '/{$g->slc_module}';\n";
$code .= "\$table = \$bootstrap->get_DynamicTable([\n";
$code .= "    'id' => 'table-' . lpk(),\n";
$code .= "    'data-url' => '/{$g->slc_module}/api/{$g->slc_component}/json/list/' . lpk(),\n";
$code .= "    'buttons' => [\n";
$code .= "        'create' => ['icon' =>ICON_ADD,'text'=>lang('App.Create'), 'href' => '/{$g->slc_module}/{$g->slc_component}/create/'.lpk(), 'class' => 'btn-secondary'],\n";
$code .= "    ],\n";
$code .= "    'cols' => [\n";
foreach ($fields as $field) {
    $code .= "        '{$field}' => ['text' => lang('App.{$field}'), 'class' => 'text-center'],\n";
}
$code .= "        'options' => ['text' => lang('App.Options'), 'class' => 'text-center fit px-2'],\n";
$code .= "    ],\n";
$code .= "    'data-page-size' => 10,\n";
$code .= "    'data-side-pagination' => 'server'\n";
$code .= "]);\n";
$code .= COMMENT_HR_BUILD;
$code .= "\$card = \$bootstrap->get_Card('card-view-service', [\n";
$code .= "\t 'title' => lang('{$g->ucf_component}.list-title'),\n";
$code .= "\t 'header-back' => \$back,\n";
$code .= "\t 'alert' => ['icon' => ICON_INFO, 'type' => 'info', 'title' => lang('{$g->ucf_component}.list-title'), 'message' => lang('{$g->ucf_component}.list-description')],\n";
$code .= "\t 'content' => \$table,\n";
$code .= "]);\n";
$code .= "echo(\$card);\n";
$code .= "?>\n";
echo($code);
