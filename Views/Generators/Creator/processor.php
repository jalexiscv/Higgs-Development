<?php

use App\Libraries\Files;

$bootstrap = service("bootstrap");
$f = service("forms", array("lang" => "Nexus."));
//[Request]-------------------------------------------------------------------------------------------------------------
$pathfiles = $f->get_Value("pathfiles");
$cindex = $f->get_Value("cindex");
$cdeny = $f->get_Value("cdeny");
$cform = $f->get_Value("cform");
$cprocessor = $f->get_Value("cprocessor");
$cvalidator = $f->get_Value("cvalidator");
$cbreadcrumb = $f->get_Value("cbreadcrumb");

$files = new Files();
$files->mkDir($pathfiles);
$files->open("{$pathfiles}/index.php", "writeOnly")->write(urldecode($cindex));
@chmod("{$pathfiles}/index.php", 0664);
$files->open("{$pathfiles}/deny.php", "writeOnly")->write(urldecode($cdeny));
@chmod("{$pathfiles}/deny.php", 0664);
$files->open("{$pathfiles}/form.php", "writeOnly")->write(urldecode($cform));
@chmod("{$pathfiles}/form.php", 0664);
$files->open("{$pathfiles}/processor.php", "writeOnly")->write(urldecode($cprocessor));
@chmod("{$pathfiles}/processor.php", 0664);
$files->open("{$pathfiles}/validator.php", "writeOnly")->write(urldecode($cvalidator));
@chmod("{$pathfiles}/validator.php", 0664);
$files->open("{$pathfiles}/breadcrumb.php", "writeOnly")->write(urldecode($cbreadcrumb));
@chmod("{$pathfiles}/breadcrumb.php", 0664);
//[Processing]----------------------------------------------------------------------------------------------------------
$c = $bootstrap->get_Card('success', array(
    'class' => 'card-success',
    'icon' => 'fa-duotone fa-triangle-exclamation',
    'text-class' => 'text-center',
    'title' => lang("Development.creator-success-title"),
    'text' => lang("Development.creator-success-text"),
    'footer-class' => 'text-center',
    'footer-continue' => base_url("/development/generators/list/" . lpk()),
    'voice' => "development/creator-success-message.mp3",
));
echo($c);
?>