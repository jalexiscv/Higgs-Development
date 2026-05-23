<?php

namespace App\Modules\Development\Commands;

use Config\Database;
use Higgs\CLI\BaseCommand;
use Higgs\CLI\CLI;

/**
 * Spark command: development:generate-lister
 *
 * Generates the _List/ view set for a given table.
 * Usage: php spark development:generate-lister <table>
 * Example: php spark development:generate-lister access_events
 */
class GenerateLister extends BaseCommand
{
    protected $group       = 'Development';
    protected $name        = 'development:generate-lister';
    protected $description = 'Generates the _List/ view set for a given table (e.g. access_events).';
    protected $usage       = 'development:generate-lister <table>';
    protected $arguments   = [
        'table' => 'Table name (e.g. access_events or module_component_options)',
    ];

    public function run(array $params): int
    {
        $table = array_shift($params);
        if (empty($table)) {
            CLI::error('Usage: php spark development:generate-lister <table>');
            return EXIT_ERROR;
        }

        $eid           = explode('_', $table);
        $ucf_module    = ucfirst($eid[0]);
        $ucf_component = ucfirst($eid[1]);
        $ucf_options   = isset($eid[2]) ? ucfirst($eid[2]) : '';
        $slc_module    = strtolower($eid[0]);
        $slc_component = strtolower($eid[1]);
        $slc_options   = isset($eid[2]) ? strtolower($eid[2]) : '';

        // Get DB field names
        $db     = Database::connect('default');
        $fields = $db->getFieldNames($table);
        if (empty($fields)) {
            CLI::error("Table '{$table}' not found or has no fields.");
            return EXIT_ERROR;
        }
        $primary = $fields[0];

        if (count($eid) === 3) {
            $pathfiles = APPPATH . "Modules/{$ucf_module}/Views/{$ucf_component}/{$ucf_options}/_List";
            $plural    = "{$slc_module}-{$slc_component}-{$slc_options}-view-all";
            $ns_prefix = "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\{$ucf_options}\\List";
            $model_ns  = "App\\\\Modules\\\\{$ucf_module}\\\\Models\\\\{$ucf_module}_{$ucf_component}_{$ucf_options}";
        } else {
            $pathfiles = APPPATH . "Modules/{$ucf_module}/Views/{$ucf_component}/_List";
            $plural    = "{$slc_module}-{$slc_component}-view-all";
            $ns_prefix = "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\List";
            $model_ns  = "App\\\\Modules\\\\{$ucf_module}\\\\Models\\\\{$ucf_module}_{$ucf_component}";
        }

        if (!is_dir($pathfiles)) {
            mkdir($pathfiles, 0755, true);
            CLI::write("Created directory: {$pathfiles}", 'green');
        }

        $files = [
            'index.php'     => $this->buildIndex($ucf_module, $ucf_component, $ns_prefix, $plural),
            'grid.php'      => $this->buildGrid($ucf_module, $ucf_component, $ucf_options, $slc_module, $slc_component, $ns_prefix, $model_ns, $fields, $primary),
            'json.php'      => $this->buildJson($ucf_module, $ucf_component, $slc_module, $slc_component, $ns_prefix, $model_ns, $fields, $primary),
            'breadcrumb.php' => $this->buildBreadcrumb($slc_module, $slc_component, $ns_prefix),
            'deny.php'      => $this->buildDeny($slc_module, $slc_component, $ns_prefix),
        ];

        foreach ($files as $filename => $content) {
            $filepath = $pathfiles . '/' . $filename;
            file_put_contents($filepath, $content);
            CLI::write("  Created: {$filepath}", 'yellow');
        }

        CLI::write('_List/ view set generated successfully.', 'green');
        return EXIT_SUCCESS;
    }

    // -------------------------------------------------------------------------

    private function buildIndex(string $ucf_module, string $ucf_component, string $ns_prefix, string $plural): string
    {
        $namespaced = "{$ns_prefix}\\index.php";
        $c  = "<?php\n";
        $c .= $this->copyright($namespaced);
        $c .= "//[Vars]-----------------------------------------------------------------------------\n";
        $c .= "/** @var \$parent ModuleController */\n";
        $c .= "/** @var \$authentication \App\Libraries\Authentication */\n";
        $c .= "/** @var \$request \Higgs\HTTP\IncomingRequest */\n";
        $c .= "\$data = \$parent->get_Array();\n";
        $c .= "\$data['permissions'] = array('singular' => false, \"plural\" =>'{$plural}');\n";
        $c .= "\$plural = \$authentication->has_Permission(\$data['permissions']['plural']);\n";
        $c .= "\$submited = \$request->getPost(\"submited\");\n";
        $c .= "\$breadcrumb = \$component . '\\\\breadcrumb';\n";
        $c .= "\$validator = \$component . '\\\\validator';\n";
        $c .= "\$table = \$component . '\\\\grid';\n";
        $c .= "\$deny = \$component . '\\\\deny';\n";
        $c .= "//[Build]-----------------------------------------------------------------------------\n";
        $c .= "if (\$plural) {\n";
        $c .= "\t\tif (!empty(\$submited)) {\n";
        $c .= "\t\t\t\t\$json = array('breadcrumb' => view(\$breadcrumb, \$data), 'main' => view(\$validator, \$data), 'right' => \"\");\n";
        $c .= "\t\t} else {\n";
        $c .= "\t\t\t\t\$json = array('breadcrumb' => view(\$breadcrumb, \$data), 'main' => view(\$table, \$data), 'right' => \"\");\n";
        $c .= "\t\t}\n";
        $c .= "} else {\n";
        $c .= "\t\t\$json = array('breadcrumb' => view(\$breadcrumb, \$data), 'main' => view(\$deny, \$data), 'right' => \"\");\n";
        $c .= "}\n";
        $c .= "echo(json_encode(\$json));\n";
        $c .= "?>\n";
        return $c;
    }

    private function buildGrid(string $ucf_module, string $ucf_component, string $ucf_options, string $slc_module, string $slc_component, string $ns_prefix, string $model_ns, array $fields, string $primary): string
    {
        $namespaced = "{$ns_prefix}\\grid.php";
        $c  = "<?php\n";
        $c .= $this->copyright($namespaced);
        $c .= "/** @var \$parent ModuleController */\n";
        $c .= "/** @var \$authentication \App\Libraries\Authentication */\n";
        $c .= "/** @var \$request \Higgs\HTTP\IncomingRequest */\n";
        $c .= "//[Models]-----------------------------------------------------------------------------\n";
        $c .= "\$m{$slc_component} = model('{$model_ns}');\n";
        $c .= "//[Vars]-----------------------------------------------------------------------------\n";
        $c .= "\$back= \"/{$slc_module}\";\n";
        $c .= "\$offset = !empty(\$request->getVar(\"offset\")) ? \$request->getVar(\"offset\") : 0;\n";
        $c .= "\$search = !empty(\$request->getVar(\"search\")) ? \$request->getVar(\"search\") : \"\";\n";
        $c .= "\$field = !empty(\$request->getVar(\"field\")) ? \$request->getVar(\"field\") : \"\";\n";
        $c .= "\$limit = !empty(\$request->getVar(\"limit\")) ? \$request->getVar(\"limit\") : 10;\n";
        $c .= "\$fields = array(\n";
        foreach ($fields as $field) {
            $c .= "\t\t //\"{$field}\" => lang(\"App.{$field}\"),\n";
        }
        $c .= ");\n";
        $c .= "//[Build]--------------------------------------------------------------------------------------------------------------\n";
        $c .= "\$conditions = array();\n";
        $c .= "//\$m{$slc_component}->clear_AllCache();\n";
        $c .= "\$rows = \$m{$slc_component}->getCachedSearch(\$conditions,\$limit, \$offset,\"{$primary} DESC\");\n";
        $c .= "\$total = \$m{$slc_component}->getCountAllResults(\$conditions);\n";
        $c .= "//echo(safe_dump(\$rows['sql']));\n";
        $c .= "//[Build]--------------------------------------------------------------------------------------------------------------\n";
        $c .= "\$bgrid = \$bootstrap->get_Grid();\n";
        $c .= "\$bgrid->set_Total(\$total);\n";
        $c .= "\$bgrid->set_Limit(\$limit);\n";
        $c .= "\$bgrid->set_Offset(\$offset);\n";
        $c .= "\$bgrid->set_Class(\"P-0 m-0\");\n";
        $c .= "\$bgrid->set_Limits(array(10, 20, 40, 80, 160, 320, 640, 1200, 2400));\n";
        $c .= "\$bgrid->set_Headers(array(\n";
        $c .= "\t\tarray(\"content\" => \"#\", \"class\" => \"text-center\talign-middle\"),\n";
        foreach ($fields as $field) {
            $c .= "\t\t //array(\"content\" => lang(\"App.{$field}\"), \"class\" => \"text-center\talign-middle\"),\n";
        }
        $c .= "\t\tarray(\"content\" => lang(\"App.Options\"), \"class\" => \"text-center\talign-middle\"),\n";
        $c .= "));\n";
        $c .= "\$bgrid->set_Search(array(\"search\" => \$search, \"field\" => \$field, \"fields\" => \$fields,));\n";
        $c .= "\$component = '/{$slc_module}/{$slc_component}';\n";
        $c .= "\$count = \$offset;\n";
        $c .= "foreach (\$rows[\"data\"] as \$row) {\n";
        $c .= "\t\tif(!empty(\$row[\"{$primary}\"])){\n";
        $c .= "\t\t\t\t\$count++;\n";
        $c .= "\t\t\t\t//[links]-------------------------------------------------------------------------------------------------------\n";
        $c .= "\t\t\t\t\$hrefView=\"\$component/view/{\$row[\"{$primary}\"]}\";\n";
        $c .= "\t\t\t\t\$hrefEdit=\"\$component/edit/{\$row[\"{$primary}\"]}\";\n";
        $c .= "\t\t\t\t\$hrefDelete=\"\$component/delete/{\$row[\"{$primary}\"]}\";\n";
        $c .= "\t\t\t\t//[buttons]-----------------------------------------------------------------------------------------------------\n";
        $c .= "\t\t\t\t\$btnView = \$bootstrap->get_Link(\"btn-view\", array(\"size\" => \"sm\",\"icon\" => ICON_VIEW,\"title\" => lang(\"App.View\"),\"href\" =>\$hrefView,\"class\" => \"btn-primary ml-1\",));\n";
        $c .= "\t\t\t\t\$btnEdit = \$bootstrap->get_Link(\"btn-edit\", array(\"size\" => \"sm\",\"icon\" => ICON_EDIT,\"title\" => lang(\"App.Edit\"),\"href\" =>\$hrefEdit,\"class\" => \"btn-warning ml-1\",));\n";
        $c .= "\t\t\t\t\$btnDelete = \$bootstrap->get_Link(\"btn-delete\", array(\"size\" => \"sm\",\"icon\" => ICON_DELETE,\"title\" => lang(\"App.Delete\"),\"href\" =>\$hrefDelete,\"class\" => \"btn-danger ml-1\",));\n";
        $c .= "\t\t\t\t\$options = \$bootstrap->get_BtnGroup(\"btn-group\", array(\"content\" => \$btnView.\$btnEdit.\$btnDelete));\n";
        $c .= "\t\t\t\t//[etc]---------------------------------------------------------------------------------------------------------\n";
        $c .= "\t\t\t\t\$bgrid->add_Row(\n";
        $c .= "\t\t\t\t\t\tarray(\n";
        $c .= "\t\t\t\t\t\t\t\tarray(\"content\" => \$count, \"class\" => \"text-center align-middle\"),\n";
        foreach ($fields as $field) {
            $c .= "\t\t\t\t\t\t\t\t //array(\"content\" => \$row['{$field}'], \"class\" => \"text-left align-middle\"),\n";
        }
        $c .= "\t\t\t\t\t\t\t\tarray(\"content\" => \$options, \"class\" => \"text-center align-middle\"),\n";
        $c .= "\t\t\t\t\t\t)\n";
        $c .= "\t\t\t\t);\n";
        $c .= "\t\t}\n";
        $c .= "}\n";
        $c .= "//[Build]-----------------------------------------------------------------------------\n";
        $c .= "\$card = \$bootstrap->get_Card2(\"card-grid\", array(\n";
        $c .= "\t\t\"header-title\" =>lang('{$ucf_component}.list-title'),\n";
        $c .= "\t\t\"header-back\" => \$back,\n";
        $c .= "\t\t\"header-add\"=>\"/{$slc_module}/{$slc_component}/create/\" . lpk(),\n";
        $c .= "\t\t\"alert\" => array(\"icon\" => ICON_INFO, \"type\" => \"info\", \"title\" => lang('{$ucf_component}.list-title'), \"message\" => lang('{$ucf_component}.list-description')),\n";
        $c .= "\t\t\"content\" => \$bgrid,\n";
        $c .= "));\n";
        $c .= "echo(\$card);\n";
        $c .= "?>\n";
        return $c;
    }

    private function buildJson(string $ucf_module, string $ucf_component, string $slc_module, string $slc_component, string $ns_prefix, string $model_ns, array $fields, string $primary): string
    {
        $namespaced = "{$ns_prefix}\\json.php";
        $c  = "<?php\n";
        $c .= $this->copyright($namespaced);
        $c .= "//[Inherited from ModuleController]---------------------------------------------------\n";
        $c .= "// \$authentication  → service('authentication')  App\\Libraries\\Authentication\n";
        $c .= "// \$bootstrap       → service('bootstrap')\n";
        $c .= "// \$dates           → service('Dates')           App\\Libraries\\Dates\n";
        $c .= "// \$strings         → service('strings')         App\\Libraries\\Strings\n";
        $c .= "// \$request         → service('request')\n";
        $c .= "// \$server          → service('server')\n";
        $c .= "// \$parent          → ModuleController instance  (use \$parent->get_Array() for view data)\n";
        $c .= "//[Models]---------------------------------------------------------------------------------------------------------------\n";
        $c .= "\$model = model('{$model_ns}');\n";
        $c .= "//[Requests]------------------------------------------------------------------------------------------------------------\n";
        $c .= "\$columns = \$request->getGet(\"columns\");\n";
        $c .= "\$offset = \$request->getGet(\"offset\");\n";
        $c .= "\$search = \$request->getGet(\"search\");\n";
        $c .= "\$draw = empty(\$request->getGet(\"draw\")) ? 1 : \$request->getGet(\"draw\");\n";
        $c .= "\$limit = empty(\$request->getGet(\"limit\")) ? 10 : \$request->getGet(\"limit\");\n";
        $c .= "//[Query]---------------------------------------------------------------------------------------------------------------\n";
        $c .= "\$list = \$model->getList(\$limit, \$offset, \$search);\n";
        $c .= "\$recordsTotal = \$model->get_Total(\$search);\n";
        $c .= "//\$sql=\$model->getLastQuery()->getQuery();\n";
        $c .= "//[Asignations]---------------------------------------------------------------------------------------------------------\n";
        $c .= "\$data = array();\n";
        $c .= "\$component = '/{$slc_module}/{$slc_component}';\n";
        $c .= "foreach (\$list as \$item) {\n";
        $c .= "\t//[Buttons]---------------------------------------------------------------------------------------------------------\n";
        $c .= "\t\$viewer = \"{\$component}/view/{\$item[\"{$primary}\"]}\";\n";
        $c .= "\t\$editor = \"{\$component}/edit/{\$item[\"{$primary}\"]}\";\n";
        $c .= "\t\$deleter = \"{\$component}/delete/{\$item[\"{$primary}\"]}\";\n";
        $c .= "\t\$lviewer = \$bootstrap::get_Link('view', array('href' => \$viewer, 'icon' => ICON_VIEW, 'text' => lang(\"App.View\"), 'class' => 'btn-primary'));\n";
        $c .= "\t\$leditor = \$bootstrap::get_Link('edit', array('href' => \$editor, 'icon' => ICON_EDIT, 'text' => lang(\"App.Edit\"), 'class' => 'btn-secondary'));\n";
        $c .= "\t\$ldeleter = \$bootstrap::get_Link('delete', array('href' => \$deleter, 'icon' =>ICON_DELETE, 'text' => lang(\"App.Delete\"), 'class' => 'btn-danger'));\n";
        $c .= "\t\$options = \$bootstrap::get_BtnGroup('options', array('content'=>array(\$lviewer, \$leditor, \$ldeleter)));\n";
        $c .= "\t//[Fields]----------------------------------------------------------------------------------------------------------\n";
        foreach ($fields as $field) {
            if (in_array($field, ['title', 'description'])) {
                $c .= "\t\$row[\"{$field}\"] =\$strings->get_URLDecode(\$item[\"{$field}\"]);\n";
            } else {
                $c .= "\t\$row[\"{$field}\"] =\$item[\"{$field}\"];\n";
            }
        }
        $c .= "\t\$row[\"options\"] = \$options;\n";
        $c .= "\t//[Push]------------------------------------------------------------------------------------------------------------\n";
        $c .= "\tarray_push(\$data, \$row);\n";
        $c .= "}\n";
        $c .= "//[Build]---------------------------------------------------------------------------------------------------------------\n";
        $c .= "\$json[\"draw\"] = \$draw;\n";
        $c .= "\$json[\"columns\"] = \$columns;\n";
        $c .= "\$json[\"offset\"] = \$offset;\n";
        $c .= "\$json[\"search\"] = \$search;\n";
        $c .= "\$json[\"limit\"] = \$limit;\n";
        $c .= "//\$json[\"sql\"] = \$sql;\n";
        $c .= "\$json[\"total\"] = \$recordsTotal;\n";
        $c .= "\$json[\"data\"] = \$data;\n";
        $c .= "echo(json_encode(\$json));\n";
        $c .= "?>\n";
        return $c;
    }

    private function buildBreadcrumb(string $slc_module, string $slc_component, string $ns_prefix): string
    {
        $namespaced = "{$ns_prefix}\\breadcrumb.php";
        $c  = "<?php\n";
        $c .= $this->copyright($namespaced);
        $c .= "use Higgs\\Frontend\\Bootstrap\\v5_3_3\\Bootstrap as BS5;\n";
        $c .= "echo BS5::breadcrumb(['items' => [\n";
        $c .= "    ['label' => '{$slc_module}', 'href' => '/{$slc_module}/'],\n";
        $c .= "    ['label' => lang('App.{$slc_component}'), 'href' => '/{$slc_module}/{$slc_component}/home/'.lpk(), 'active' => true],\n";
        $c .= "]]);\n";
        $c .= '?>';
        return $c;
    }

    private function buildDeny(string $slc_module, string $slc_component, string $ns_prefix): string
    {
        $namespaced = "{$ns_prefix}\\deny.php";
        $c  = "<?php\n";
        $c .= $this->copyright($namespaced);
        $c .= "use Higgs\\Frontend\\Bootstrap\\v5_3_3\\Bootstrap as BS5;\n";
        $c .= "\$continue = \"/{$slc_module}/{$slc_component}/list/\".lpk();\n";
        $c .= "if (\$authentication->get_LoggedIn()) {\n";
        $c .= "    \$_icon_col = BS5::col(['attributes' => ['class' => 'text-center py-3'], 'content' => BS5::icon(['icon' => 'ban', 'style' => 'duotone', 'size' => '2xl'])]);\n";
        $c .= "    \$_msg_col  = BS5::col(['attributes' => ['class' => 'text-center pb-2'], 'content' => lang('App.Access-denied-message')]);\n";
        $c .= "    \$_btn_col  = BS5::col(['attributes' => ['class' => 'text-center pb-3'], 'content' => BS5::button(['content' => lang('App.Continue'), 'variant' => 'danger', 'size' => 'sm', 'attributes' => ['href' => \$continue]])]);\n";
        $c .= "    \$_content  = BS5::row(['attributes' => ['class' => 'justify-content-center'], 'content' => \$_icon_col.\$_msg_col.\$_btn_col]);\n";
        $c .= "    \$card = BS5::card(['headerTitle' => lang('App.Access-denied-title'), 'headerClass' => 'bg-danger text-white', 'content' => \$_content, 'attributes' => ['class' => 'border-danger shadow-sm']]);\n";
        $c .= "} else {\n";
        $c .= "    \$_icon_col = BS5::col(['attributes' => ['class' => 'text-center py-3'], 'content' => BS5::icon(['icon' => 'lock', 'style' => 'duotone', 'size' => '2xl'])]);\n";
        $c .= "    \$_msg_col  = BS5::col(['attributes' => ['class' => 'text-center pb-2'], 'content' => lang('App.login-required-message')]);\n";
        $c .= "    \$_btn_col  = BS5::col(['attributes' => ['class' => 'text-center pb-3'], 'content' => BS5::button(['content' => lang('App.Continue'), 'variant' => 'danger', 'size' => 'sm', 'attributes' => ['href' => \$continue]])]);\n";
        $c .= "    \$_content  = BS5::row(['attributes' => ['class' => 'justify-content-center'], 'content' => \$_icon_col.\$_msg_col.\$_btn_col]);\n";
        $c .= "    \$card = BS5::card(['headerTitle' => lang('App.login-required-title'), 'headerClass' => 'bg-danger text-white', 'content' => \$_content, 'attributes' => ['class' => 'border-danger shadow-sm']]);\n";
        $c .= "}\n";
        $c .= "echo(\$card);\n";
        $c .= '?>';
        return $c;
    }

    // -------------------------------------------------------------------------

    private function copyright(string $path): string
    {
        $author = 'Jose Alexis Correa Valencia <jalexiscv@gmail.com>';
        $date   = date('Y-m-d H:i:s');
        $c  = "\n/**\n";
        $c .= "* █ ---------------------------------------------------------------------------------------------------------------------\n";
        $c .= "* █ ░FRAMEWORK                                  {$date}\n";
        $c .= "* █ ░█▀▀█ █▀▀█ █▀▀▄ █▀▀ ░█─░█ ─▀─ █▀▀▀ █▀▀▀ █▀▀ [{$path}]\n";
        $c .= "* █ ░█─── █──█ █──█ █▀▀ ░█▀▀█ ▀█▀ █─▀█ █─▀█ ▀▀█ Copyright 2023 - CloudEngine S.A.S., Inc. <admin@cgine.com>\n";
        $c .= "* █ ░█▄▄█ ▀▀▀▀ ▀▀▀─ ▀▀▀ ░█─░█ ▀▀▀ ▀▀▀▀ ▀▀▀▀ ▀▀▀ Para obtener información completa sobre derechos de autor y licencia,\n";
        $c .= "* █                                             consulte la LICENCIA archivo que se distribuyó con este código fuente.\n";
        $c .= "* █ ---------------------------------------------------------------------------------------------------------------------\n";
        $c .= "* █ EL SOFTWARE SE PROPORCIONA -TAL CUAL-, SIN GARANTÍA DE NINGÚN TIPO, EXPRESA O\n";
        $c .= "* █ IMPLÍCITA, INCLUYENDO PERO NO LIMITADO A LAS GARANTÍAS DE COMERCIABILIDAD,\n";
        $c .= "* █ APTITUD PARA UN PROPÓSITO PARTICULAR Y NO INFRACCIÓN. EN NINGÚN CASO SERÁ\n";
        $c .= "* █ LOS AUTORES O TITULARES DE LOS DERECHOS DE AUTOR SERÁN RESPONSABLES DE CUALQUIER\n";
        $c .= "* █ RECLAMO, DAÑOS U OTROS RESPONSABILIDAD, YA SEA EN UNA ACCIÓN DE CONTRATO,\n";
        $c .= "* █ AGRAVIO O DE OTRO MODO, QUE SURJA DESDE, FUERA O EN RELACIÓN CON EL SOFTWARE\n";
        $c .= "* █ O EL USO U OTROS NEGOCIACIONES EN EL SOFTWARE.\n";
        $c .= "* █ ---------------------------------------------------------------------------------------------------------------------\n";
        $c .= "* █ @Author {$author}\n";
        $c .= "* █ @link https://www.higgs.com.co\n";
        $c .= "* █ @Version 1.5.1 @since PHP 8,PHP 9\n";
        $c .= "* █ ---------------------------------------------------------------------------------------------------------------------\n";
        $c .= "**/\n";
        return $c;
    }
}
