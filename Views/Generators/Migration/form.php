<?php
/** @var string $component */
/** @var object $parent */
/** @var string $oid Representa el nombre de la tabla */
$bootstrap = service("bootstrap");
$f = service("forms", array("lang" => "Generators."));
//[vars]----------------------------------------------------------------------------------------------------------------
$data = $parent->get_Array();
$eid = explode("_", $oid);
$ucf_module = safe_ucfirst($eid[0]);
$ucf_component = safe_ucfirst($eid[1]);
$ucf_options = safe_ucfirst(@$eid[2]);
$slc_module = safe_strtolower($eid[0]);
$slc_component = safe_strtolower($eid[1]);
$slc_options = safe_strtolower(@$eid[2]);
$classname = count($eid) == 3 ? "{$ucf_module}_{$ucf_component}_{$ucf_options}" : "{$ucf_module}_{$ucf_component}";
$timestamp = date('Y-m-d_His');
$path = APPPATH . "Modules/{$ucf_module}/Database/Migrations/";
$file = "{$timestamp}_{$ucf_module}_{$ucf_component}.php";
$uri = $path . $file;
$data["path"] = $file;
$back = "";
//[raw]-----------------------------------------------------------------------------------------------------------------
$code = view($component . '\coders\migration', $data);
$r["uri"] = $f->get_Value("uri", $uri);
$r["code"] = $f->get_Value("code", $code);
$r["date"] = $f->get_Value("date", service("dates")::get_Date());
$r["time"] = $f->get_Value("time", service("dates")::get_Time());
//[fields]--------------------------------------------------------------------------------------------------------------
$f->add_HiddenField("file", $file);
$f->add_HiddenField("path", $path);
$f->fields["uri"] = $f->get_FieldText("uri", array("value" => $r["uri"], "readonly" => true));
$f->fields["code"] = $f->get_FieldCode("code", array("value" => $r["code"], "mode" => "php"));
$f->fields["cancel"] = $f->get_Cancel("cancel", array("href" => "/nexus/generators/", "text" => lang("App.Cancel"), "type" => "secondary", "proportion" => "col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 padding-right"));
$f->fields["submit"] = $f->get_Submit("submit", array("value" => "Guardar", "proportion" => "col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 padding-left"));
//[Groups]--------------------------------------------------------------------------------------------------------------
$f->groups["g1"] = $f->get_Group(array("legend" => "", "fields" => ($f->fields["uri"])));
$f->groups["g2"] = $f->get_Group(array("legend" => "", "fields" => ($f->fields["code"])));
//[buttons]-------------------------------------------------------------------------------------------------------------
$f->groups["gy"] = $f->get_GroupSeparator();
$f->groups["gz"] = $f->get_Buttons(array("fields" => $f->fields["submit"] . $f->fields["cancel"]));
//[Build]---------------------------------------------------------------------------------------------------------------
$card = $bootstrap->get_Card("create", array(
    "title" => lang("Generators.migrations-generator-title"),
    "content" => $f,
    "header-back" => $back
));
echo($card);
?>