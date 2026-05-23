<?php

include __DIR__ . '/_shared.php';

$namespacedFile = $g->namespaced . "processor.php";
$fields = $g->fields;

$code = "<?php\n";
$code .= get_development_code_copyright(array("path" => $namespacedFile));

$code .= "use Higgs\\Frontend\\Bootstrap\\v5_3_3\\Bootstrap as BS5;\n";
$code .= COMMENT_HR_SERVICES;
$code .= COMMENT_HR_MODELS;
$code .= "\$f = service(\"forms\",array(\"lang\" => \"{$g->ucf_module}_{$g->ucf_component}.\"));\n";
$code .= "\$model = model(\"App\\Modules\\{$g->ucf_module}\\Models\\{$g->ucf_module}_{$g->ucf_component}\");\n";

$code .= COMMENT_HR_VARS;
$code .= "\$d = array(\n";
foreach ($fields as $field) {
    if ($field != "created_at" && $field != "updated_at" && $field != "deleted_at") {
        if ($field == "author") {
            $code .= "    \"{$field}\" => safe_get_user(),\n";
        } else {
            $code .= "    \"{$field}\" => \$f->get_Value(\"{$field}\"),\n";
        }
    }
}
$code .= ");\n";
$code .= "\$row = \$model->find(\$d[\"{$fields[0]}\"]);\n";

$code .= COMMENT_HR_BUILD;
$code .= "if (isset(\$row[\"{$fields[0]}\"])) {\n";
$code .= "    \$_icon = (string)BS5::icon(['icon' => 'circle-check', 'style' => 'duotone', 'size' => '4x']);\n";
$code .= "    \$_body = '<div class=\"text-center py-3\">' . \$_icon . '</div>'\n";
$code .= "        . '<p class=\"text-center pb-2\">' . lang(\"{$g->ucf_module}_{$g->ucf_component}.view-success-message\") . '</p>'\n";
$code .= "        . '<div class=\"text-center pb-3\">' . (string)BS5::button(['content' => lang('App.Continue'), 'variant' => 'success', 'size' => 'md', 'attributes' => ['href' => \$l['back']]]) . '</div>';\n";
$code .= "    \$_content = (string)BS5::col(['attributes' => ['class' => 'text-center'], 'content' => \$_body]);\n";
$code .= "    \$c = BS5::card([\n";
$code .= "        'header' => [\n";
$code .= "            'title' => lang(\"{$g->ucf_module}_{$g->ucf_component}.view-success-title\"),\n";
$code .= "            'class' => 'bg-success border-success text-white'\n";
$code .= "        ],\n";
$code .= "        'content' => [\n";
$code .= "            'htmlContent' => \$_content,\n";
$code .= "            'class' => 'bg-success text-white'\n";
$code .= "        ],\n";
$code .= "        'attributes'  => ['class' => 'border-success shadow-sm'],\n";
$code .= "    ]);\n";
$code .= "} else {\n";
$code .= "    \$_icon = (string)BS5::icon(['icon' => 'triangle-exclamation', 'style' => 'duotone', 'size' => '4x']);\n";
$code .= "    \$_body = '<div class=\"text-center py-3\">' . \$_icon . '</div>'\n";
$code .= "        . '<p class=\"text-center pb-2\">' . lang(\"{$g->ucf_module}_{$g->ucf_component}.view-noexist-message\") . '</p>'\n";
$code .= "        . '<div class=\"text-center pb-3\">' . (string)BS5::button(['content' => lang('App.Continue'), 'variant' => 'warning', 'size' => 'md', 'attributes' => ['href' => \$l['back']]]) . '</div>';\n";
$code .= "    \$_content = (string)BS5::col(['attributes' => ['class' => 'text-center'], 'content' => \$_body]);\n";
$code .= "    \$c = BS5::card([\n";
$code .= "        'header' => [\n";
$code .= "            'title' => lang(\"{$g->ucf_module}_{$g->ucf_component}.view-noexist-title\"),\n";
$code .= "            'class' => 'bg-warning border-warning text-dark'\n";
$code .= "        ],\n";
$code .= "        'content' => [\n";
$code .= "            'htmlContent' => \$_content,\n";
$code .= "            'class' => 'bg-warning text-dark'\n";
$code .= "        ],\n";
$code .= "        'attributes'  => ['class' => 'border-warning shadow-sm'],\n";
$code .= "    ]);\n";
$code .= "}\n";
$code .= "echo(\$c);\n";
$code .= "?>\n";

echo($code);
