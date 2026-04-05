<?php

use Config\Database;

$action = "";
$module = "";
$component = "";
$f = service("forms", array("lang" => "Nexus."));
/** request * */
$r["client"] = $f->get_Value("client", strtoupper(uniqid()));
$r["time"] = $f->get_Value("time", service("dates")::get_Time());
$id = $oid;
$eid = explode("_", $id);
$ucf_module = safe_ucfirst($eid[0]);
$ucf_component = safe_ucfirst($eid[1]);
$ucf_options = safe_ucfirst(@$eid[2]);
$slc_module = safe_strtolower($eid[0]);
$slc_component = safe_strtolower($eid[1]);
$slc_options = safe_strtolower(@$eid[2]);

if (count($eid) == 3) {
    $namespaced = "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\{$ucf_options}\\Editor\\deny.php";
} else {
    $namespaced = "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\Editor\\deny.php";
}

$db = Database::connect("default");
$fields = $db->getFieldNames($id);


$code = "<?php\n";
$code .= "/** @var \$permissions array que contiene los permisos que el usuario no posee */\n";
$code .= "/** @var \$authentication \\App\\Libraries\\Authentication */\n";
$code .= "\n";
$code .= get_development_code_copyright(array("path" => $namespaced));
$code .= "use Higgs\\Frontend\\Bootstrap\\v5_3_3\\Bootstrap as BS5;\n";
$code .= "\n";
$code .= "\$continue = \"/{$slc_module}/{$slc_component}/list/\".lpk();\n";
$code .= "if (\$authentication->get_LoggedIn()) {\n";
$code .= "    \$_icon = (string)BS5::icon(['icon' => 'ban', 'style' => 'duotone', 'size' => '4x']);\n";
$code .= "    \$_body = '<div class=\"text-center py-3\">' . \$_icon . '</div>'\n";
$code .= "        . '<p class=\"text-center pb-2\">' . lang('App.Access-denied-message') . '</p>';\n";
$code .= "    \$_permissions=\"<p class=\\\"text-center pb-2\\\">Permisos requeridos: \".implode(\" - \",\$permissions).\"</p>\";\n";
$code .= "    \$_continue = BS5::button(['content' => lang('App.Continue'), 'variant' => 'danger', 'size' => 'md', 'attributes' => ['href' => \$continue]]);\n";
$code .= "    \$card = BS5::card([\n";
$code .= "        'header' => [\n";
$code .= "            'title' => lang('App.Access-denied-title'),\n";
$code .= "            'class' => 'bg-danger border-danger text-white'\n";
$code .= "        ],\n";
$code .= "        'content' => [\n";
$code .= "            'htmlContent'=>\$_body.\$_permissions,\n";
$code .= "            'class' => 'bg-danger text-white',\n";
$code .= "        ],\n";
$code .= "        'footer' => [\n";
$code .= "            'content' => \$_continue,\n";
$code .= "            'class' => 'bg-danger text-white d-flex justify-content-end',\n";
$code .= "        ],\n";
$code .= "        'attributes' => [\n";
$code .= "            'class' => 'border-danger shadow-sm'\n";
$code .= "        ],\n";
$code .= "    ]);\n";
$code .= "} else {\n";
$code .= "    \$_icon = (string)BS5::icon(['icon' => 'lock', 'style' => 'duotone', 'size' => '4x']);\n";
$code .= "    \$_body = '<div class=\"text-center py-3\">' . \$_icon . '</div>'\n";
$code .= "        . '<p class=\"text-center pb-2\">' . lang('App.login-required-message') . '</p>';\n";
$code .= "    \$_continue = BS5::button(['content' => lang('App.Continue'), 'variant' => 'danger', 'size' => 'md', 'attributes' => ['href' => \$continue]]);\n";
$code .= "    \$card = BS5::card([\n";
$code .= "        'header' => [\n";
$code .= "            'title' => lang('App.login-required-title'),\n";
$code .= "            'class' => 'bg-danger text-white'\n";
$code .= "        ],\n";
$code .= "        'content' => [\n";
$code .= "            'htmlContent'=>\$_body,\n";
$code .= "            'class' => 'bg-danger text-white',\n";
$code .= "        ],\n";
$code .= "        'footer' => [\n";
$code .= "            'content' => \$_continue,\n";
$code .= "            'class' => 'bg-danger text-white d-flex justify-content-end',\n";
$code .= "        ],\n";
$code .= "        'attributes' => [\n";
$code .= "            'class' => 'border-danger shadow-sm'\n";
$code .= "        ],\n";
$code .= "    ]);\n";
$code .= "}\n";
$code .= "echo(\$card);\n";
$code .= "?>";
echo($code);
?>