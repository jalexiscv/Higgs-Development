<?php

$f = service("forms", array("lang" => "Modules.modules-"));
/*
* -----------------------------------------------------------------------------
* [Request]
* -----------------------------------------------------------------------------
*/
$f->set_ValidationRule("module", "trim|required");
//$f->set_ValidationRule("time","trim|required");
//$f->set_ValidationRule("author","trim|required");
//$f->set_ValidationRule("created_at","trim|required");
//$f->set_ValidationRule("updated_at","trim|required");
//$f->set_ValidationRule("deleted_at","trim|required");
//[Validation]----------------------------------------------------------------------------------------------------------
if ($f->run_Validation()) {
    $c = view($component . '\processor', $parent->get_Array());
} else {
    $errors = $f->validation->listErrors();
    $errors = $f->validation->listErrors();
    $c = $bootstrap->get_Card('access-denied', array(
        'class' => 'card-danger',
        'icon' => 'fa-duotone fa-triangle-exclamation',
        'text-class' => 'text-center',
        'text' => lang("Customers.create-errors-message"),
        'errors' => $errors,
        'footer-class' => 'text-center',
        'voice' => "cadastre/customers-create-errors-message.mp3",
    ));
    $c .= view($component . '\form', $parent->get_Array());
}
//[Build]---------------------------------------------------------------------------------------------------------------
echo($c);
?>