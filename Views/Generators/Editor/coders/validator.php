<?php

include __DIR__ . '/_shared.php';

$fields = $g->fields;
$namespacedFile = $g->namespaced . "validator.php";

$code = "<?php\n";
$code .= get_development_code_copyright(array("path" => $namespacedFile));

$code .= "use Higgs\\Frontend\\Bootstrap\\v5_3_3\\Bootstrap as BS5;\n";
$code .= "\$f = service(\"forms\",array(\"lang\" => \"{$g->ucf_module}_{$g->ucf_component}.\"));\n";

$code .= "//[Request]-----------------------------------------------------------------------------\n";
foreach ($fields as $field) {
    $code .= "//\$f->set_ValidationRule(\"{$field}\",\"trim|required\");\n";
}
$code .= "//[Validation]-----------------------------------------------------------------------------\n";
$code .= "if (\$f->run_Validation()) {\n";
$code .= "    \$c = view(\$component.'\\\\processor', \$parent->get_Array());\n";
$code .= "} else {\n";
$code .= "    \$_icon_col  = BS5::row(['attributes' => ['class' => 'text-center py-3'], 'content' => BS5::icon(['icon' => 'triangle-exclamation', 'style' => 'duotone', 'size' => '2xl'])]);\n";
$code .= "    \$_msg_col   = BS5::row(['attributes' => ['class' => 'text-center pb-2'], 'htmlContent' => lang('App.validator-errors-message')]);\n";
$code .= "    \$_errors_col= BS5::row(['attributes' => ['class' => 'pb-2'], 'htmlContent' => \$f->validation->listErrors()]);\n";
$code .= "    \$_content   = BS5::col(['attributes' => ['class' => 'justify-content-center'], 'htmlContent' => \$_icon_col.\$_msg_col.\$_errors_col]);\n";
$code .= "    \$c = BS5::card([\n";
$code .= "        'headerTitle' => lang('App.validator-errors-title'),\n";
$code .= "        'headerClass' => 'bg-danger text-white',\n";
$code .= "        'content'     => [\"htmlContent\" =>\$_content,],\n";
$code .= "        'attributes'  => ['class' => 'border-danger shadow-sm'],\n";
$code .= "    ]);\n";
$code .= "    \$c .= view(\$component.'\\\\form', \$parent->get_Array());\n";
$code .= "}\n";
$code .= "//[Build]-----------------------------------------------------------------------------\n";
$code .= "echo(\$c);\n";
$code .= "?>\n";
echo($code);
