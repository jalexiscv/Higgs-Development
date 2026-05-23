<?php

include __DIR__ . '/_shared.php';

$namespacedFile = $g->namespaced . "validator.php";
$fields = $g->fields;

$code = "<?php\n";
$code .= get_development_code_copyright(array("path" => $namespacedFile));

$code .= "\$bootstrap = service('bootstrap');\n";
$code .= "\$f = service(\"forms\",array(\"lang\" => \"{$g->ucf_module}_{$g->ucf_component}.\"));\n";

$code .= "//[Request]-----------------------------------------------------------------------------\n";
foreach ($fields as $field) {
    $code .= "//\$f->set_ValidationRule(\"{$field}\",\"trim|required\");\n";
}
$code .= "//[Validation]-----------------------------------------------------------------------------\n";
$code .= "if (\$f->run_Validation()) {\n";
$code .= "   \$c=view(\$component.'\processor',\$parent->get_Array());\n";
$code .= "}else {\n";
$code .= "\$c =\$bootstrap->get_Card('access-denied', array(\n";
$code .= "    'class'=>'card-danger',\n";
$code .= "    'icon'=>'fa-duotone fa-triangle-exclamation',\n";
$code .= "    'text-class' => 'text-center',\n";
$code .= "    'text' => lang('App.validator-errors-message'),\n";
$code .= "    'errors' => \$f->validation->listErrors(),\n";
$code .= "    'footer-class'=>'text-center',\n";
$code .= "    'voice'=>\"app/validator-errors-message.mp3\",\n";
$code .= "));\n";
$code .= "   \$c.=view(\$component.'\form',\$parent->get_Array());\n";
$code .= "}\n";
$code .= "//[Build]-----------------------------------------------------------------------------\n";
$code .= "echo(\$c);\n";
$code .= "?>\n";

echo($code);
