<?php

$f = service("forms", array("lang" => "Nexus."));
/*
 * -----------------------------------------------------------------------------
 * [Coder]
 * -----------------------------------------------------------------------------
 */
$eid = explode("_", $oid);
$ucf_module = safe_ucfirst($eid[0]);
$ucf_component = safe_ucfirst($eid[1]);
$ucf_options = safe_ucfirst(@$eid[2]);
$slc_module = safe_strtolower($eid[0]);
$slc_component = safe_strtolower($eid[1]);
$slc_options = safe_strtolower(@$eid[2]);

if (count($eid) == 3) {
    $classname = "{$ucf_module}_{$ucf_component}_{$ucf_options}";
} else {
    $classname = "{$ucf_module}_{$ucf_component}";
}

$mkdir = APPPATH . "Modules/{$ucf_module}/Language/es";
$pathfile = APPPATH . "Modules/{$ucf_module}/Language/es/_{$ucf_module}_{$ucf_component}.php";
$code = view($component . '\coders\lang', $parent->get_Array());
/*
 * -----------------------------------------------------------------------------
 * [Requests]
 * -----------------------------------------------------------------------------
 */
$r["uri_save"] = $f->get_Value("uri_save", $pathfile);
$r["code"] = $f->get_Value("code", $code);
$r["date"] = $f->get_Value("date", service("dates")::get_Date());
$r["time"] = $f->get_Value("time", service("dates")::get_Time());
/*
 * -----------------------------------------------------------------------------
 * [Fields]
 * -----------------------------------------------------------------------------
 */
$f->add_HiddenField("pathfile", $pathfile);
$f->add_HiddenField("mkdir", $mkdir);
$f->fields["uri_save"] = $f->get_FieldText("uri_save", array("value" => $r["uri_save"], "readonly" => true));
$f->fields["code"] = $f->get_FieldCode("code", array("value" => $r["code"], "mode" => "php"));
$f->fields["cancel"] = $f->get_Cancel("cancel", array("href" => "/nexus/generators/", "text" => lang("App.Cancel"),
    "type" => "secondary", "proportion" => "col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 padding-right"));
$f->fields["submit"] = $f->get_Submit("submit", array("value" => "Guardar", "proportion" => "col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 padding-left"));
/*
 * -----------------------------------------------------------------------------
 * [Groups]
 * -----------------------------------------------------------------------------
 */
$f->groups["g1"] = $f->get_Group(array("legend" => "", "fields" => ($f->fields["uri_save"])));
$f->groups["g2"] = $f->get_Group(array("legend" => "", "fields" => ($f->fields["code"])));
/*
 * -----------------------------------------------------------------------------
 * [Buttons]
 * -----------------------------------------------------------------------------
 */
$f->groups["gy"] = $f->get_GroupSeparator();
$f->groups["gz"] = $f->get_Buttons(array("fields" => $f->fields["submit"] . $f->fields["cancel"]));
//[card]---------------------------------------------------------------------------------------------------------------
$bootstrap = service("bootstrap");
$card = $bootstrap->get_Card("card-view-service", array(
    "title" => lang("Nexus.generators-lang"),
    "header-back" => "",
    "content" => $f,
));
echo($card);
?>