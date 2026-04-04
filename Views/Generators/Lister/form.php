<?php
/**
 * @var string $oid Cadena de objeto recibido generalmente objeto/dato a visualizar trasferido desde el ModuleController
 * @var string $view Cadena que se pasa a la vista definida en viewer para su evaluación
 * @var string $views Uri completa hasta las vistas de modulo .
 * @var string $viewer URI completa hasta la vista encargada de evaluar cada view solicitado .
 * @var string $component URI completa hasta el componente solicitado .
 * @var string $authentication el servicio de autenticación desde el ModuleController
 * @var string $dates el servicio de fechas desde el ModuleController
 * @var string $strings el servicio de cadenas desde el ModuleController
 * @var string $request el servicio de solicitud desde el ModuleController
 * @var object $parent representa al ModuleController
 * @var object $bootstrap el servicio de diseño desde el ModuleController
 **/
//[vars]----------------------------------------------------------------------------------------------------------------
$f = service("forms", array("lang" => "Development_Tools."));
//[coder]---------------------------------------------------------------------------------------------------------------
$eid = explode("_", $oid);
$ucf_module = safe_ucfirst($eid[0]);
$ucf_component = safe_ucfirst(@$eid[1]);
$ucf_options = safe_ucfirst(@$eid[2]);
$slc_module = safe_strtolower($eid[0]);
$slc_component = safe_strtolower($eid[1]);
$slc_options = safe_strtolower(@$eid[2]);
$pathfiles = APPPATH . "Modules/$ucf_module/Views/$ucf_component/_List";
$data = $parent->get_Array();
$cindex = view($component . '\coders\index', $data);
$cdeny = view($component . '\coders\deny', $data);
$cgrid = view($component . '\coders\grid', $data);
$cbreadcrumb = view($component . '\coders\breadcrumb', $data);
$code = $cindex . $cdeny . $cgrid . $cbreadcrumb;
//[requests]------------------------------------------------------------------------------------------------------------
$r["uri_save"] = $f->get_Value("uri_save", $pathfiles);
$r["code"] = $f->get_Value("code", $code);
$r["date"] = $f->get_Value("date", service("dates")::get_Date());
$r["time"] = $f->get_Value("time", service("dates")::get_Time());
//[fields]--------------------------------------------------------------------------------------------------------------
$f->add_HiddenField("pathfiles", $pathfiles);
$f->add_HiddenField("cindex", urlencode($cindex));
$f->add_HiddenField("cdeny", urlencode($cdeny));
$f->add_HiddenField("cgrid", urlencode($cgrid));
$f->add_HiddenField("cbreadcrumb", urlencode($cbreadcrumb));
$f->fields["uri_save"] = $f->get_FieldText("uri_save", array("value" => $r["uri_save"], "readonly" => true));
$f->fields["code"] = $f->get_FieldCode("code", array("value" => $r["code"], "mode" => "php"));
$f->fields["cancel"] = $f->get_Cancel("cancel", array("href" => "/nexus/generators/", "text" => lang("App.Cancel")));
$f->fields["submit"] = $f->get_Submit("submit", array("value" => lang("App.Save")));
//[groups]--------------------------------------------------------------------------------------------------------------
$f->groups["g1"] = $f->get_Group(array("legend" => "", "fields" => ($f->fields["uri_save"])));
$f->groups["g2"] = $f->get_Group(array("legend" => "", "fields" => ($f->fields["code"])));
//[buttons]-------------------------------------------------------------------------------------------------------------
$f->groups["gy"] = $f->get_GroupSeparator();
$f->groups["gz"] = $f->get_Buttons(array("fields" => $f->fields["submit"] . $f->fields["cancel"]));
//[build]---------------------------------------------------------------------------------------------------------------
$bootstrap = service("bootstrap");
$card = $bootstrap->get_Card("card-view-service", array("title" => lang("Development_Tools.generators-lister"), "header-back" => "", "content" => $f,));
echo($card);
?>