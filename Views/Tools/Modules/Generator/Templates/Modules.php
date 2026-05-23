<?php

$strings = service('strings');
/** @var string $module */
$ucf_module = $strings->get_Ucfirst($module);
$lc_module = $strings->get_Strtolower($module);

$code = "<?php\n";
$code .= "\n";
$code .= "\n";
$code .= "namespace App\Modules\\$ucf_module\Models;\n";
$code .= "\n";
$code .= "use App\Models\Application_Modules;\n";
$code .= "\n";
$code .= "class {$ucf_module}_Modules extends Application_Modules\n";
$code .= "{\n";
$code .= "\n";
$code .= "}\n";
$code .= "\n";
$code .= "\n";
$code .= "?>\n";
echo($code);
