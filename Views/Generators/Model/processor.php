<?php

use App\Libraries\Files;

$bootstrap = service("bootstrap");
$f = service("forms", array("lang" => "Nexus."));
$model = model("App\Models\Application_Clients");
//[Request]-------------------------------------------------------------------------------------------------------------
$pathfile = $f->get_Value("pathfile");
$mkdir = $f->get_Value("mkdir");
$relative = $f->get_Value("relative");
$code = $f->get_Value("code");
$files = new Files();
$files->mkDir($mkdir);
$files->open($pathfile, "writeOnly")->write($code);
@chmod($pathfile, 0664);
//[Processing]----------------------------------------------------------------------------------------------------------
if (isset($row["client"])) {
    $c = $bootstrap->get_Card('warning', array(
        'class' => 'card-warning',
        'icon' => 'fa-duotone fa-triangle-exclamation',
        'text-class' => 'text-center',
        'text' => lang("Development.model-warning-title"),
        'footer-class' => 'text-center',
        'footer-continue' => base_url("/development/generators/list/" . lpk()),
        'voice' => "development/model-create-warning-message.mp3",
    ));
} else {
    $c = $bootstrap->get_Card('success', array(
        'class' => 'card-success',
        'icon' => 'fa-duotone fa-triangle-exclamation',
        'text-class' => 'text-center',
        'text' => lang("Development.model--success-title"),
        'footer-class' => 'text-center',
        'footer-continue' => base_url("/development/generators/list/" . lpk()),
        'voice' => "development/model-success-message.mp3",
    ));
}
echo($c);
?>