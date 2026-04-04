<?php
$bootstrap = service("bootstrap");
$f = service("forms", array("lang" => "Development_Modules."));
//[Requests]------------------------------------------------------------------------------------------------------------
$r["module"] = $f->get_Value("module", "");
$back = "/development/" . lpk();
//[Fields]--------------------------------------------------------------------------------------------------------------
$f->fields["module"] = $f->get_FieldText("module", array("value" => $r["module"], "proportion" => "col-12"));
$f->fields["cancel"] = $f->get_Cancel("cancel", array("href" => $back, "text" => lang("App.Cancel"), "type" => "secondary", "proportion" => "col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 padding-right"));
$f->fields["submit"] = $f->get_Submit("submit", array("value" => lang("App.Create"), "proportion" => "col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 padding-left"));
//[Groups]--------------------------------------------------------------------------------------------------------------
$f->groups["g3"] = $f->get_Group(array("legend" => "", "fields" => ($f->fields["module"])));
//[Buttons]-------------------------------------------------------------------------------------------------------------
$f->groups["gy"] = $f->get_GroupSeparator();
$f->groups["gz"] = $f->get_Buttons(array("fields" => $f->fields["submit"] . $f->fields["cancel"]));
//[Build]---------------------------------------------------------------------------------------------------------------
$card = $bootstrap->get_Card("card-view-service", array(
    "title" => "Generador de Modulos",
    "header-back" => "/development/",
    "content" => $f,
));
echo($card);
?>