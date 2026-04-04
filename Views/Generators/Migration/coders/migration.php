<?php

/* @var string $path */

/* @var string $oid */

use Config\Database;

$action = "";
$module = "";
$component = "";
$f = service("forms", array("lang" => "Nexus."));
/** request * */
$r["client"] = $f->get_Value("client", strtoupper(uniqid()));
$r["time"] = $f->get_Value("time", service("dates")::get_Time());
$id = $oid;
$eid = explode("_", $id);
$ucf_module = safe_ucfirst($eid[0]);
$ucf_component = safe_ucfirst($eid[1]);
$ucf_options = safe_ucfirst(@$eid[2]);
$slc_module = safe_strtolower($eid[0]);
$slc_component = safe_strtolower($eid[1]);
$slc_options = safe_strtolower(@$eid[2]);

$migrations = new \App\Libraries\Migrations("frontend", $oid);
$code_migration = $migrations->generate($oid);

$code = "<?php\n";

$code .= get_development_code_copyright(array("path" => $path));
$code .= $code_migration;
$code .= "?>\n";
echo($code);
?>