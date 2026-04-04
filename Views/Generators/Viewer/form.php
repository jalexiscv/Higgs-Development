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

$pathfiles = APPPATH . "Modules/{$ucf_module}/Views/{$ucf_component}/_View";
$data = $parent->get_Array();

$cindex = view($component . '\coders\index', $data);
$cdeny = view($component . '\coders\deny', $data);
$cform = view($component . '\coders\form', $data);
$cprocessor = view($component . '\coders\processor', $data);
$cvalidator = view($component . '\coders\validator', $data);
$cbreadcrumb = view($component . '\coders\breadcrumb', $data);

$code = $cindex . $cdeny . $cform . $cprocessor . $cvalidator;

/*
 * -----------------------------------------------------------------------------
 * [Requests]
 * -----------------------------------------------------------------------------
 */
$r["uri_save"] = $f->get_Value("uri_save", $pathfiles);
$r["code"] = $f->get_Value("code", $code);
$r["date"] = $f->get_Value("date", service("dates")::get_Date());
$r["time"] = $f->get_Value("time", service("dates")::get_Time());
/*
 * -----------------------------------------------------------------------------
 * [Fields]
 * -----------------------------------------------------------------------------
 */
$f->add_HiddenField("pathfiles", $pathfiles);
$f->add_HiddenField("cindex", urlencode($cindex));
$f->add_HiddenField("cdeny", urlencode($cdeny));
$f->add_HiddenField("cform", urlencode($cform));
$f->add_HiddenField("cprocessor", urlencode($cprocessor));
$f->add_HiddenField("cvalidator", urlencode($cvalidator));
$f->add_HiddenField("cbreadcrumb", urlencode($cbreadcrumb));

$f->fields["uri_save"] = $f->get_FieldText("uri_save", array("value" => $r["uri_save"], "readonly" => true));
$f->fields["code"] = $f->get_FieldCode("code", array("value" => $r["code"], "mode" => "php"));
$f->fields["cancel"] = $f->get_Cancel("cancel", array("href" => "/nexus/generators/", "text" => lang("App.Cancel"),
    "type" => "secondary", "proportion" => "col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 padding-right"));
$f->fields["submit"] = $f->get_Submit("submit", array("value" => "Guardar Editor", "proportion" => "col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 padding-left"));
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
    "title" => lang("Nexus.generators-viewers"),
    "header-back" => "",
    "content" => $f,
));
echo($card);
?>