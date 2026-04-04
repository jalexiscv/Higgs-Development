<?php

/** @var string $component */

use Config\Database;

$action = "";

$f = service("forms", array("lang" => "Nexus."));
/** request * */
$r["client"] = $f->get_Value("client", strtoupper(uniqid()));
$r["time"] = $f->get_Value("time", service("dates")::get_Time());

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
$mkdir = APPPATH . "Modules/{$ucf_module}/Models";
$pathfile = APPPATH . "Modules/{$ucf_module}/Models/_{$classname}.php";
$relative = "Modules/{$ucf_module}/Models/_{$classname}.php";

$db = Database::connect("default");
$fields = $db->getFieldNames($oid);
$datas = $db->getFieldData($oid);

$code = "";
$code .= "<?php\n";
$code .= "\n";
$code .= "namespace App\\Modules\\{$ucf_module}\\Models;\n";
$code .= "\n";
$code .= "use App\\Models\\CachedModel5;\n";
$code .= "use Config\\Database;\n";
$code .= "\n";

$code .= "/**\n";
$code .= "* {$classname} Model\n";
$code .= "*\n";
$code .= "* Modelo generado automáticamente usando CachedModel5.\n";
$code .= "*\n";
$code .= "* @Copilot Siempre que en el código se cree una variable llamada \$m{$slc_component}, esta deberá ser igualada a  model('App\\Modules\\{$ucf_module}\\Models\\{$classname}');\n";
$code .= "* @Instruction \$m{$slc_component} = model('App\\Modules\\{$ucf_module}\\Models\\{$classname}');\n";
$code .= "* @method insert(array \$data, bool \$returnID = true, bool \$protect = true): int|string\n";
$code .= "* @method update(string \$id, array \$data): bool\n";
$code .= "* @method delete(string \$id, bool \$purge = false): bool\n";
$code .= "* @method getCached(mixed \$id): array|object|null\n";
$code .= "* @method getCachedFirst(array \$conditions, string \$orderBy = 'created_at DESC'): array|object|null\n";
$code .= "* @method getCachedSearch(array \$conditions = [], int \$limit = 10, int \$offset = 0, string \$orderBy = '', int \$page = 1): array\n";
$code .= "* @method getCachedCustomQuery(callable \$queryBuilder, string \$cacheKeySuffix, ?int \$ttl = null): array\n";
$code .= "* @method withTags(array \$tags): static\n";
$code .= "* @method invalidateTag(string \$tag): int\n";
$code .= "* @method invalidateSearchCache(): void\n";
$code .= "* @method getSelectDataCached(?string \$labelColumn = 'name', int \$ttl = 3600): array\n";
$code .= "* @package App\\Modules\\{$ucf_module}\\Models\n";
$code .= "* @version 2.1.0\n";
$code .= "* @see CachedModel5\n";
$code .= "*/\n";


$code .= "class {$classname} extends CachedModel5\n";
$code .= "\t {\n";
$code .= "\t\t protected \$table = \"{$oid}\";\n";
$code .= "\t\t protected \$primaryKey = \"{$fields["0"]}\";\n";
$code .= "\t\t protected \$returnType = \"array\";\n";
$code .= "\t\t protected \$useSoftDeletes = true;\n";
$code .= "\t\t protected \$allowedFields = [\n";
foreach ($fields as $field) {
    $code .= "\t\t\t \"{$field}\",\n";
}
$code .= "\t\t ];\n";
//$code .= "protected \$beforeFind = ['_exec_BeforeFind'];\n";
//$code .= "protected \$afterFind = ['_exec_FindCache'];\n";
//$code .= "protected \$afterInsert = ['_exec_UpdateCache'];\n";
//$code .= "protected \$afterUpdate = ['_exec_UpdateCache'];\n";
//$code .= "protected \$afterDelete = ['_exec_DeleteCache'];\n";
$code .= "\t\t protected \$useTimestamps = true;\n";
$code .= "\t\t protected \$createdField = \"created_at\";\n";
$code .= "\t\t protected \$updatedField = \"updated_at\";\n";
$code .= "\t\t protected \$deletedField = \"deleted_at\";\n";
$code .= "\t\t protected \$validationRules = [];\n";
$code .= "\t\t protected \$validationMessages = [];\n";
$code .= "\t\t protected \$skipValidation = false;\n";
$code .= "\t\t protected \$DBGroup = \"authentication\";//default\n";
$code .= "\t\t protected \$version = '1.0.1';\n";
$code .= "\t\t protected \$cache_time = 60;\n";
$code .= "\t\t protected array \$cacheTags = ['table:{$oid}'];\n";
//$code .= "protected \$cache;\n";
$code .= view($component . '\Methods\__construct', array());
//$code .= view($component . '\Methods\get_CountAllResults', array("primary" => $fields["0"], "fields" => $fields));
$code .= view($component . '\Methods\exec_Migrate', array('module' => $ucf_module));
$code .= view($component . '\Methods\getAuthority', array());
$code .= view($component . '\Methods\getList', array("primary" => $fields["0"], "fields" => $fields));
$code .= view($component . '\Methods\getSelectData', array());
//$code .= view($component . '\Methods\get_TableExist', array());
//$code .= view($component . '\Methods\get_Total', array());
$code .= view($component . '\Methods\get_Row', array("primary" => $fields["0"]));
//$code .= view($component . '\Methods\is_CacheValid', array());
//$code .= view($component . '\Methods\get_CacheKey', array());
//$code .= view($component . '\Methods\get_CachedItem', array());
//$code .= view($component . '\Methods\_exec_BeforeFind', array());
//$code .= view($component . '\Methods\_exec_FindCache', array());
//$code .= view($component . '\Methods\_exec_UpdateCache', array());
//$code .= view($component . '\Methods\_exec_DeleteCache', array());
$code .= "}\n";
$code .= "\n";
$code .= "?>\n";

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
$f->add_HiddenField("relative", $relative);
$f->fields["uri_save"] = $f->get_FieldText("uri_save", array("value" => $r["uri_save"], "readonly" => true));
$f->fields["code"] = $f->get_FieldCode("code", array("value" => $r["code"], "mode" => "php"));
$f->fields["cancel"] = $f->get_Cancel("cancel", array("href" => "/nexus/generators/", "text" => lang("App.Cancel"),
    "type" => "secondary", "proportion" => "col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 padding-right"));
$f->fields["submit"] = $f->get_Submit("submit", array("value" => lang("App.Create"), "proportion" => "col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 padding-left"));
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
//[Build]---------------------------------------------------------------------------------------------------------------
$bootstrap = service("bootstrap");
$card = $bootstrap->get_Card("card-view-service", array(
    "title" => lang('Generators.model-generator'),
    "header-back" => '/development/generators/list/' . lpk(),
    "content" => $f,
));
echo($card);
?>