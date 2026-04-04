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
    $model = "App\\Modules\\{$ucf_module}\\Models\\{$ucf_module}_{$ucf_component}_{$ucf_options}";
    $path = '/' . $slc_module . '/' . $slc_component . '/' . $slc_options;
    $namespaced = "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\{$ucf_options}\\Creator\\deny.php";
    $plural = "{$slc_module}-{$slc_component}-{$slc_options}-view-all";
    $pathfiles = APPPATH . "Modules/{$ucf_module}/Views/{$ucf_component}/{$ucf_options}/_List";
    $ajax = "/{$slc_module}/{$slc_component}/{$slc_options}/ajax/list?time=\".time()";
} else {
    $model = "App\\Modules\\{$ucf_module}\\Models\\{$ucf_module}_{$ucf_component}";
    $path = '/' . $slc_module . '/' . $slc_component;
    $namespaced = "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\Creator\\deny.php";
    $plural = "{$slc_module}-{$slc_component}-view-all";
    $pathfiles = APPPATH . "Modules/{$ucf_module}/Views/{$ucf_component}/_List";
    $ajax = "/{$slc_module}/{$slc_component}/ajax/list/";
}

$code = "<?php\n";
$code .= get_development_code_copyright(array("path" => $namespaced));
$code .= "use Higgs\\Frontend\\Bootstrap\\v5_3_3\\Bootstrap as BS5;\n";
$code .= "\$continue = \"/{$slc_module}/{$slc_component}/list/\".lpk();\n";
$code .= "if (\$authentication->get_LoggedIn()) {\n";
$code .= "    \$_icon = (string)BS5::icon(['icon' => 'ban', 'style' => 'duotone', 'size' => '2xl']);\n";
$code .= "    \$_body = '<div class=\"text-center py-3\">'.\$_icon.'</div>'\n";
$code .= "           . '<p class=\"text-center pb-2\">'.lang('App.Access-denied-message').'</p>';\n";
$code .= "    \$card = BS5::card([\n";
$code .= "        'header'      => ['title' => lang('App.Access-denied-title'), 'class' => 'bg-danger text-white'],\n";
$code .= "        'htmlContent' => \$_body,\n";
$code .= "        'footer'      => [\n";
$code .= "            'content' => BS5::button(['content' => lang('App.Continue'), 'variant' => 'danger', 'size' => 'sm', 'attributes' => ['href' => \$continue]]),\n";
$code .= "            'class'   => 'd-flex justify-content-end',\n";
$code .= "        ],\n";
$code .= "        'attributes'  => ['class' => 'border-danger shadow-sm'],\n";
$code .= "    ]);\n";
$code .= "} else {\n";
$code .= "    \$_icon = (string)BS5::icon(['icon' => 'lock', 'style' => 'duotone', 'size' => '2xl']);\n";
$code .= "    \$_body = '<div class=\"text-center py-3\">'.\$_icon.'</div>'\n";
$code .= "           . '<p class=\"text-center pb-2\">'.lang('App.login-required-message').'</p>';\n";
$code .= "    \$card = BS5::card([\n";
$code .= "        'header'      => ['title' => lang('App.login-required-title'), 'class' => 'bg-danger text-white'],\n";
$code .= "        'htmlContent' => \$_body,\n";
$code .= "        'footer'      => [\n";
$code .= "            'content' => BS5::button(['content' => lang('App.Continue'), 'variant' => 'danger', 'size' => 'sm', 'attributes' => ['href' => \$continue]]),\n";
$code .= "            'class'   => 'd-flex justify-content-end',\n";
$code .= "        ],\n";
$code .= "        'attributes'  => ['class' => 'border-danger shadow-sm'],\n";
$code .= "    ]);\n";
$code .= "}\n";
$code .= "echo(\$card);\n";
$code .= "?>";
echo($code);
?>