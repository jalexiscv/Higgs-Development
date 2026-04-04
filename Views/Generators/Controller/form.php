<?php


/*
 * -----------------------------------------------------------------------------
 * [Coder]
 * -----------------------------------------------------------------------------
 */

use Config\Database;

$action = "";
$module = "";
$component = "";
$f = service("forms", array("lang" => "Nexus."));
/** request * */
$r["client"] = $f->get_Value("client", strtoupper(uniqid()));
$r["time"] = $f->get_Value("time", service("dates")::get_Time());

$eid = explode("_", $oid);
$ucf_module = safe_ucfirst($eid[0]);
$ucf_component = safe_ucfirst($eid[1]);
$slc_module = safe_strtolower($eid[0]);
$slc_component = safe_strtolower($eid[1]);

$classname = "{$ucf_module}_{$ucf_component}";
$mkdir = APPPATH . "Modules/{$ucf_module}/Controllers";
$pathfile = APPPATH . "Modules/{$ucf_module}/Controllers/_{$ucf_component}.php";
$relative = "Modules/{$ucf_module}/Models/_{$classname}.php";
$namespaced = "App\\Modules\\{$ucf_module}\\Controllers\\_{$ucf_component}.php";

$db = Database::connect("default");
$fields = $db->getFieldNames($oid);

$code = "<?php\n";
$code .= "\n";
$code .= "namespace App\Modules\\{$ucf_module}\\Controllers;\n";
$code .= get_development_code_copyright(array("path" => $namespaced));

$code .= "\n";
$code .= "use App\Controllers\ModuleController;\n";
$code .= "\n";
$code .= "class {$ucf_component} extends ModuleController {\n";
$code .= "\n";


$code .= "\t//[{$ucf_module}/Config/Routes]\n";
//$code .= "\t//[{$ucf_component}]\n";
//$code .= "\t//\$subroutes->add('{$slc_component}', '{$ucf_component}::index');\n";
//$code .= "\t//\$subroutes->add('{$slc_component}/home/(:any)', '{$ucf_component}::home/$1');\n";
//$code .= "\t//\$subroutes->add('{$slc_component}/list/(:any)', '{$ucf_component}::list/$1');\n";
//$code .= "\t//\$subroutes->add('{$slc_component}/create/(:any)', '{$ucf_component}::create/$1');\n";
//$code .= "\t//\$subroutes->add('{$slc_component}/view/(:any)/', '{$ucf_component}::view/$1');\n";
//$code .= "\t//\$subroutes->add('{$slc_component}/edit/(:any)/', '{$ucf_component}::edit/$1');\n";
//$code .= "\t//\$subroutes->add('{$slc_component}/delete/(:any)/', '{$ucf_component}::delete/$1');\n";
//$code .= "\t//\$subroutes->add('api/{$slc_component}/(:any)/(:any)/(:any)', 'Api::{$ucf_component}/$1/$2/$3');\n";
//$code .= "\t//[{$ucf_module}/Views/index]\n";
$code .= "\t//[{$ucf_component}]----------------------------------------------------------------------------------------\n";
$code .= "\t//\"{$slc_module}-{$slc_component}-home\"=>\"\$views\\{$ucf_component}\\Home\\index\",\n";
$code .= "\t//\"{$slc_module}-{$slc_component}-list\"=>\"\$views\\{$ucf_component}\\List\\index\",\n";
$code .= "\t//\"{$slc_module}-{$slc_component}-view\"=>\"\$views\\{$ucf_component}\\View\\index\",\n";
$code .= "\t//\"{$slc_module}-{$slc_component}-create\"=>\"\$views\\{$ucf_component}\\Create\\index\",\n";
$code .= "\t//\"{$slc_module}-{$slc_component}-edit\"=>\"\$views\\{$ucf_component}\\Edit\\index\",\n";
$code .= "\t//\"{$slc_module}-{$slc_component}-delete\"=>\"\$views\\{$ucf_component}\\Delete\\index\",\n";

$code .= "\n";
$code .= "\t//[{$ucf_component}]----------------------------------------------------------------------------------------\n";
$code .= "\t//\t\t\t\t\t\t\"{$slc_module}-{$slc_component}-access\",\n";
$code .= "\t//\t\t\t\t\t\t\"{$slc_module}-{$slc_component}-view\",\n";
$code .= "\t//\t\t\t\t\t\t\"{$slc_module}-{$slc_component}-view-all\",\n";
$code .= "\t//\t\t\t\t\t\t\"{$slc_module}-{$slc_component}-create\",\n";
$code .= "\t//\t\t\t\t\t\t\"{$slc_module}-{$slc_component}-edit\",\n";
$code .= "\t//\t\t\t\t\t\t\"{$slc_module}-{$slc_component}-edit-all\",\n";
$code .= "\t//\t\t\t\t\t\t\"{$slc_module}-{$slc_component}-delete\",\n";
$code .= "\t//\t\t\t\t\t\t\"{$slc_module}-{$slc_component}-delete-all\",\n";

$code .= "\n";

$code .= "    public function __construct() {\n";
$code .= "       parent::__construct();\n";
$code .= "       \$this->prefix = '{$slc_module}-{$slc_component}';\n";
$code .= "       \$this->module = 'App\Modules\\{$ucf_module}';\n";
$code .= "       \$this->views = \$this->module . '\Views';\n";
$code .= "       \$this->viewer = \$this->views . '\index';\n";
$code .= "       helper(\$this->module.'\\Helpers\\{$ucf_module}');\n";
$code .= "    }\n";
$code .= "\n";
$code .= "    public function index() {\n";
$code .= "        \$url = base_url('{$slc_module}/{$slc_component}/home/' . lpk());\n";
$code .= "        return (redirect()->to(\$url));\n";
$code .= "    }\n";
$code .= "\n";
$code .= "\n";

$code .= "    public function home(string \$rnd) {\n";
$code .= "        \$this->oid = \$rnd;\n";
$code .= "        \$this->prefix = \"{\$this->prefix}-home\";\n";
$code .= "        \$this->component = \$this->views . '\\" . $ucf_component . "\Home';\n";
$code .= "        return (view(\$this->viewer, \$this->get_Array()));\n";
$code .= "    }\n";
$code .= "\n";

$code .= "    public function view(string \$oid) {\n";
$code .= "        \$this->oid = \$oid;\n";
$code .= "        \$this->prefix = \"{\$this->prefix}-view\";\n";
$code .= "        \$this->component = \$this->views . '\\" . $ucf_component . "\View';\n";
$code .= "        return (view(\$this->viewer, \$this->get_Array()));\n";
$code .= "    }\n";
$code .= "\n";

$code .= "    public function list(string \$rnd) {\n";
$code .= "        \$this->oid = \$rnd;\n";
$code .= "        \$this->prefix = \"{\$this->prefix}-list\";\n";
$code .= "        \$this->component = \$this->views . '\\" . $ucf_component . "\List';\n";
$code .= "        return (view(\$this->viewer, \$this->get_Array()));\n";
$code .= "    }\n";
$code .= "\n";

$code .= "    public function create(string \$rnd) {\n";
$code .= "        \$this->oid = \$rnd;\n";
$code .= "        \$this->prefix = \"{\$this->prefix}-create\";\n";
$code .= "        \$this->component = \$this->views . '\\" . $ucf_component . "\Create';\n";
$code .= "        return (view(\$this->viewer, \$this->get_Array()));\n";
$code .= "    }\n";
$code .= "\n";

$code .= "    public function edit(string \$oid) {\n";
$code .= "        \$this->oid = \$oid;\n";
$code .= "        \$this->prefix = \"{\$this->prefix}-edit\";\n";
$code .= "        \$this->component = \$this->views . '\\" . $ucf_component . "\Edit';\n";
$code .= "        return (view(\$this->viewer, \$this->get_Array()));\n";
$code .= "    }\n";
$code .= "\n";

$code .= "    public function delete(string \$oid) {\n";
$code .= "        \$this->oid = \$oid;\n";
$code .= "        \$this->prefix = \"{\$this->prefix}-delete\";\n";
$code .= "        \$this->component = \$this->views . '\\" . $ucf_component . "\Delete';\n";
$code .= "        return (view(\$this->viewer, \$this->get_Array()));\n";
$code .= "    }\n";
$code .= "\n";

$code .= "\n";
$code .= "}\n";
$code .= "?>";
$controller = $code;


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
$f->fields["submit"] = $f->get_Submit("submit", array("value" => "Guardar Controlador", "proportion" => "col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 padding-left"));
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
    "title" => lang('Generators.controller-generator'),
    "header-back" => '/development/generators/list/' . lpk(),
    "content" => $f,
));
echo($card);
?>