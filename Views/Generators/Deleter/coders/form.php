<?php

include __DIR__ . '/_shared.php';

$strings = service("strings");
$sucf_component = $strings->removePluralEnding($g->ucf_component);

$code = "<?php\n";
$code .= get_development_code_copyright(array("path" => $g->namespaced . "form.php"));
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
$code .= COMMENT_HR_MODELS;
$code .= "//\$model = model(\"App\\Modules\\{$g->ucf_module}\\Models\\{$g->ucf_module}_{$g->ucf_component}\");\n";
$code .= COMMENT_HR_VARS;
$code .= COMMENT_MODULECONTROLER_VARS;
$code .= "\$f = service(\"forms\",array(\"lang\" => \"{$g->ucf_module}_{$g->ucf_component}.\"));\n";
$code .= "\$r= \$model->get{$sucf_component}(\$oid);\n";
$code .= "\$name = urldecode(\$r[\"name\"]);\n";
$code .= "\$message=sprintf(lang(\"{$g->ucf_module}_{$g->ucf_component}.delete-message\"),\$name);\n";
$code .= "\$back=\$f->get_Value(\"back\",\$server->get_Referer());\n";
$code .= COMMENT_HR_FIELDS;
$code .= "\$f->add_HiddenField(\"back\",\$back);\n";
$code .= "\$f->add_HiddenField(\"pkey\", \$oid);\n";
$code .= "\$f->fields[\"cancel\"] = \$f->get_Cancel(\"cancel\", array(\"href\" => \$back, \"text\" => lang(\"App.Cancel\"), \"type\" => \"secondary\", \"proportion\" => \"col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 padding-right\"));\n";
$code .= "\$f->fields[\"submit\"] = \$f->get_Submit(\"submit\", array(\"value\" => lang(\"App.Delete\"), \"proportion\" => \"col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 padding-left\"));\n";
$code .= COMMENT_HR_GROUPS;
$code .= "\$f->groups[\"gy\"] = \$f->get_GroupSeparator();\n";
$code .= COMMENT_HR_BUTTONS;
$code .= "\$f->groups[\"gz\"] = \$f->get_Buttons(array(\"fields\" => \$f->fields[\"submit\"] . \$f->fields[\"cancel\"]));\n";
$code .= COMMENT_HR_BUILD;
$code .= "\$card = BS5::card([\n";
$code .= "    'header' => [\n";
$code .= "        'title' => sprintf(lang(\"{$g->ucf_module}_{$g->ucf_component}.delete-title\"),\$name),\n";
$code .= "        'class' => 'bg-danger text-white mx-0',\n";
$code .= "        'buttons' => [BS5::button(['content' => BS5::icon(['icon' => 'arrow-left', 'style' => 'duotone']), 'variant' => 'secondary', 'size' => 'sm', 'attributes' => ['href' => \$back]])],\n";
$code .= "    ],\n";
$code .= "    'content'       => [\"htmlContent\" =>\$message.\$f,],\n";
$code .= "]);\n";
$code .= "echo(\$card);\n";
$code .= "?>\n";

echo($code);
