<?php

include __DIR__ . '/_shared.php';

$namespacedFile = $g->namespaced;

$code = "<?php\n";
$code .= "\n";
$code .= "namespace App\\Modules\\{$g->ucf_module}\\Controllers;\n";
$code .= get_development_code_copyright(array("path" => $namespacedFile));

$code .= "\n";
$code .= "use App\Controllers\ModuleController;\n";
$code .= "\n";
$code .= "class {$g->ucf_component} extends ModuleController {\n";
$code .= "\n";

$code .= "\t//[{$g->ucf_module}/Config/Routes]\n";
$code .= "\t//[{$g->ucf_component}]----------------------------------------------------------------------------------------\n";
$code .= "\t//\"{$g->slc_module}-{$g->slc_component}-home\"=>\"\$views\\{$g->ucf_component}\\Home\\index\",\n";
$code .= "\t//\"{$g->slc_module}-{$g->slc_component}-list\"=>\"\$views\\{$g->ucf_component}\\List\\index\",\n";
$code .= "\t//\"{$g->slc_module}-{$g->slc_component}-view\"=>\"\$views\\{$g->ucf_component}\\View\\index\",\n";
$code .= "\t//\"{$g->slc_module}-{$g->slc_component}-create\"=>\"\$views\\{$g->ucf_component}\\Create\\index\",\n";
$code .= "\t//\"{$g->slc_module}-{$g->slc_component}-edit\"=>\"\$views\\{$g->ucf_component}\\Edit\\index\",\n";
$code .= "\t//\"{$g->slc_module}-{$g->slc_component}-delete\"=>\"\$views\\{$g->ucf_component}\\Delete\\index\",\n";

$code .= "\n";
$code .= "\t//[{$g->ucf_component}]----------------------------------------------------------------------------------------\n";
$code .= "\t//\"{$g->slc_module}-{$g->slc_component}-access\",\n";
$code .= "\t//\"{$g->slc_module}-{$g->slc_component}-view\",\n";
$code .= "\t//\"{$g->slc_module}-{$g->slc_component}-view-all\",\n";
$code .= "\t//\"{$g->slc_module}-{$g->slc_component}-create\",\n";
$code .= "\t//\"{$g->slc_module}-{$g->slc_component}-edit\",\n";
$code .= "\t//\"{$g->slc_module}-{$g->slc_component}-edit-all\",\n";
$code .= "\t//\"{$g->slc_module}-{$g->slc_component}-delete\",\n";
$code .= "\t//\"{$g->slc_module}-{$g->slc_component}-delete-all\",\n";

$code .= "\n";

$code .= "    public function __construct() {\n";
$code .= "       parent::__construct();\n";
$code .= "       \$this->prefix = '{$g->slc_module}-{$g->slc_component}';\n";
$code .= "       \$this->module = 'App\\Modules\\{$g->ucf_module}';\n";
$code .= "       \$this->views = \$this->module . '\\Views';\n";
$code .= "       \$this->viewer = \$this->views . '\\index';\n";
$code .= "       helper(\$this->module.'\\Helpers\\{$g->ucf_module}');\n";
$code .= "    }\n";
$code .= "\n";
$code .= "    public function index() {\n";
$code .= "        \$url = base_url('{$g->slc_module}/{$g->slc_component}/home/' . lpk());\n";
$code .= "        return (redirect()->to(\$url));\n";
$code .= "    }\n";
$code .= "\n";
$code .= "\n";

$code .= "    public function home(string \$rnd) {\n";
$code .= "        \$this->oid = \$rnd;\n";
$code .= "        \$this->prefix = \"{\$this->prefix}-home\";\n";
$code .= "        \$this->component = \$this->views . '\\" . $g->ucf_component . "\Home';\n";
$code .= "        return (view(\$this->viewer, \$this->get_Array()));\n";
$code .= "    }\n";
$code .= "\n";

$code .= "    public function view(string \$oid) {\n";
$code .= "        \$this->oid = \$oid;\n";
$code .= "        \$this->prefix = \"{\$this->prefix}-view\";\n";
$code .= "        \$this->component = \$this->views . '\\" . $g->ucf_component . "\View';\n";
$code .= "        return (view(\$this->viewer, \$this->get_Array()));\n";
$code .= "    }\n";
$code .= "\n";

$code .= "    public function list(string \$rnd) {\n";
$code .= "        \$this->oid = \$rnd;\n";
$code .= "        \$this->prefix = \"{\$this->prefix}-list\";\n";
$code .= "        \$this->component = \$this->views . '\\" . $g->ucf_component . "\List';\n";
$code .= "        return (view(\$this->viewer, \$this->get_Array()));\n";
$code .= "    }\n";
$code .= "\n";

$code .= "    public function create(string \$rnd) {\n";
$code .= "        \$this->oid = \$rnd;\n";
$code .= "        \$this->prefix = \"{\$this->prefix}-create\";\n";
$code .= "        \$this->component = \$this->views . '\\" . $g->ucf_component . "\Create';\n";
$code .= "        return (view(\$this->viewer, \$this->get_Array()));\n";
$code .= "    }\n";
$code .= "\n";

$code .= "    public function edit(string \$oid) {\n";
$code .= "        \$this->oid = \$oid;\n";
$code .= "        \$this->prefix = \"{\$this->prefix}-edit\";\n";
$code .= "        \$this->component = \$this->views . '\\" . $g->ucf_component . "\Edit';\n";
$code .= "        return (view(\$this->viewer, \$this->get_Array()));\n";
$code .= "    }\n";
$code .= "\n";

$code .= "    public function delete(string \$oid) {\n";
$code .= "        \$this->oid = \$oid;\n";
$code .= "        \$this->prefix = \"{\$this->prefix}-delete\";\n";
$code .= "        \$this->component = \$this->views . '\\" . $g->ucf_component . "\Delete';\n";
$code .= "        return (view(\$this->viewer, \$this->get_Array()));\n";
$code .= "    }\n";
$code .= "\n";

$code .= "\n";
$code .= "}\n";
$code .= "?>";

echo($code);
