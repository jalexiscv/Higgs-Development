<?php
/*
 * Copyright (c) 2021-2021. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

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
    $namespaced = "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\{$ucf_options}\\Editor\\processor.php";
    $plural = "{$slc_module}-{$slc_component}-{$slc_options}-view-all";
    $pathfiles = APPPATH . "Modules/{$ucf_module}/Views/{$ucf_component}/{$ucf_options}/_Editor";
    $ajax = "/{$slc_module}/{$slc_component}/{$slc_options}/ajax/list?time=\".time()";
} else {
    $model = "App\\Modules\\{$ucf_module}\\Models\\{$ucf_module}_{$ucf_component}";
    $path = '/' . $slc_module . '/' . $slc_component;
    $namespaced = "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\Editor\\processor.php";
    $plural = "{$slc_module}-{$slc_component}-view-all";
    $pathfiles = APPPATH . "Modules/{$ucf_module}/Views/{$ucf_component}/_List";
    $ajax = "/{$slc_module}/{$slc_component}/ajax/list/";
}

$db = Database::connect("default");
$fields = $db->getFieldNames($id);

$code = "<?php\n";
$code .= get_development_code_copyright(array("path" => $namespaced));

$code .= "use Higgs\\Frontend\\Bootstrap\\v5_3_3\\Bootstrap as BS5;\n";
$code .= "//[Inherited from ModuleController]---------------------------------------------------\n";
$code .= "// \$authentication  → service('authentication')  App\\Libraries\\Authentication\n";
$code .= "// \$bootstrap       → service('bootstrap')\n";
$code .= "// \$dates           → service('Dates')           App\\Libraries\\Dates\n";
$code .= "// \$strings         → service('strings')         App\\Libraries\\Strings\n";
$code .= "// \$request         → service('request')\n";
$code .= "// \$server          → service('server')\n";
$code .= "// \$parent          → ModuleController instance  (use \$parent->get_Array() for view data)\n";
$code .= "//[Models]-----------------------------------------------------------------------------\n";
$code .= "\$f = service(\"forms\",array(\"lang\" => \"{$ucf_module}_{$ucf_component}.\"));\n";
$code .= "\$model = model(\"App\\Modules\\{$ucf_module}\\Models\\{$ucf_module}_{$ucf_component}\");\n";

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
$code .= "//[Elements]-----------------------------------------------------------------------------\n";
$code .= "\$row = \$model->find(\$d[\"{$fields[0]}\"]);\n";
$code .= "\$l[\"back\"]=\$f->get_Value(\"back\");\n";
$code .= "\$l[\"edit\"]=\"/{$slc_module}/{$slc_component}/edit/{\$d[\"{$fields[0]}\"]}\";\n";
$code .= "\$asuccess = \"{$slc_module}/{$slc_component}-edit-success-message.mp3\";\n";
$code .= "\$anoexist = \"{$slc_module}/{$slc_component}-edit-noexist-message.mp3\";\n";
$code .= COMMENT_HR_BUILD;
$code .= "if (is_array(\$row)) {\n";
$code .= "    \$edit = \$model->update(\$d['{$fields[0]}'],\$d);\n";
$code .= "    \$_icon = (string)BS5::icon(['icon' => 'circle-check', 'style' => 'duotone', 'size' => '4x']);\n";
$code .= "    \$_body = '<div class=\"text-center py-3\">' . \$_icon . '</div>'\n";
$code .= "        . '<p class=\"text-center pb-2\">' . lang(\"{$ucf_module}_{$ucf_component}.edit-success-message\") . '</p>'\n";
$code .= "        . '<div class=\"text-center pb-3\">' . (string)BS5::button(['content' => lang('App.Continue'), 'variant' => 'success', 'size' => 'md', 'attributes' => ['href' => \$l['back']]]) . '</div>';\n";
$code .= "    \$_content = (string)BS5::col(['attributes' => ['class' => 'text-center'], 'content' => \$_body]);\n";
$code .= "    \$c = BS5::card([\n";
$code .= "        'header' => [\n";
$code .= "            'title' => lang(\"{$ucf_module}_{$ucf_component}.edit-success-title\"),\n";
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
$code .= "        . '<p class=\"text-center pb-2\">' . sprintf(lang(\"{$ucf_module}_{$ucf_component}.edit-noexist-message\"), \$d['{$fields[0]}']) . '</p>'\n";
$code .= "        . '<div class=\"text-center pb-3\">' . (string)BS5::button(['content' => lang('App.Continue'), 'variant' => 'warning', 'size' => 'md', 'attributes' => ['href' => \$l['back']]]) . '</div>';\n";
$code .= "    \$_content = (string)BS5::col(['attributes' => ['class' => 'text-center'], 'content' => \$_body]);\n";
$code .= "    \$c = BS5::card([\n";
$code .= "        'header' => [\n";
$code .= "            'title' => lang(\"{$ucf_module}_{$ucf_component}.edit-noexist-title\"),\n";
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
$code .= "\$model->invalidateSearchCache();\n";
$code .= "?>\n";
echo($code);
?>