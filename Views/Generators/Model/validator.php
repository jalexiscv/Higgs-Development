<?php
$bootstrap = service("bootstrap");
$f = service("forms", array("lang" => "Nexus."));
//[Request]-------------------------------------------------------------------------------------------------------------
$f->set_ValidationRule("pathfile", "trim|required");
$f->set_ValidationRule("mkdir", "trim|required");
$f->set_ValidationRule("relative", "trim|required");
$f->set_ValidationRule("uri_save", "trim|required");
$f->set_ValidationRule("code", "trim|required");
//[Validation]----------------------------------------------------------------------------------------------------------
if ($f->run_Validation()) {
    $c = view($component . '\processor', $parent->get_Array());
} else {
    $c = $bootstrap->get_Card('validator', array(
        'class' => 'card-danger',
        'icon' => 'fa-duotone fa-triangle-exclamation',
        'text-class' => 'text-center',
        'text' => lang("App.create-errors-message"),
        'errors' => $errors,
        'footer-class' => 'text-center',
        'voice' => "app/form-errors-message.mp3",
    ));
}
//[Build]---------------------------------------------------------------------------------------------------------------
echo($c);
?>