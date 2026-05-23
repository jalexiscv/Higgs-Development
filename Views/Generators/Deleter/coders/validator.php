<?php

include __DIR__ . '/_shared.php';

$code = "<?php\n";
$code .= get_development_code_copyright(['path' => $g->namespaced . 'validator.php']);
$code .= "\$bootstrap = service('bootstrap');\n";
$code .= "\$f = service(\"forms\",array(\"lang\" => \"{$g->ucf_module}_{$g->ucf_component}.\"));\n";
$code .= "//[Request]-----------------------------------------------------------------------------\n";
$code .= "\$f->set_ValidationRule(\"pkey\",\"trim|required\");\n";
$code .= "//[Validation]-----------------------------------------------------------------------------\n";
$code .= "if (\$f->run_Validation()) {\n";
$code .= "   \$c=view(\$component.'\\processor',\$parent->get_Array());\n";
$code .= "}else {\n";
$code .= "   \$errors=\$f->validation->listErrors();\n";
$code .= "\$errors = \$f->validation->listErrors();\n";
$code .= "\$c =\$card=\$bootstrap->get_Card('access-denied', array(\n";
$code .= "    'class'=>'card-danger',\n";
$code .= "    'icon'=>'fa-duotone fa-triangle-exclamation',\n";
$code .= "    'text-class' => 'text-center',\n";
$code .= "    'text' => lang('App.validator-errors-message'),\n";
$code .= "    'errors' => \$errors,\n";
$code .= "    'footer-class'=>'text-center',\n";
$code .= "    'voice'=>\"app/validator-errors-message.mp3\",\n";
$code .= "));\n";
$code .= "   \$c.=view(\$component.'\\form',\$parent->get_Array());\n";
$code .= "}\n";
$code .= "//[Build]-----------------------------------------------------------------------------\n";
$code .= "echo(\$c);\n";
$code .= "?>\n";

echo($code);
