<?php

namespace App\Modules\Development\Commands;

use Higgs\CLI\BaseCommand;
use Higgs\CLI\CLI;

/**
 * Spark command: development:generate-controller
 *
 * Generates the Controllers/_{Component}.php controller file for a given table.
 * Usage: php spark development:generate-controller <table>
 * Example: php spark development:generate-controller access_events
 */
class GenerateController extends BaseCommand
{
    protected $group       = 'Development';
    protected $name        = 'development:generate-controller';
    protected $description = 'Generates the Controllers/_{Component}.php controller file for a given table.';
    protected $usage       = 'development:generate-controller <table>';
    protected $arguments   = [
        'table' => 'Table name (e.g. access_events or module_component_options)',
    ];

    public function run(array $params): int
    {
        $table = array_shift($params);
        if (empty($table)) {
            CLI::error('Usage: php spark development:generate-controller <table>');
            return EXIT_ERROR;
        }

        $eid           = explode('_', $table);
        $ucf_module    = ucfirst($eid[0]);
        $ucf_component = ucfirst($eid[1]);
        $slc_module    = strtolower($eid[0]);
        $slc_component = strtolower($eid[1]);

        $classname   = "{$ucf_module}_{$ucf_component}";
        $controllersDir = APPPATH . "Modules/{$ucf_module}/Controllers";
        $pathfile       = "{$controllersDir}/_{$ucf_component}.php";
        $namespaced     = "App\\Modules\\{$ucf_module}\\Controllers\\_{$ucf_component}.php";

        if (!is_dir($controllersDir)) {
            mkdir($controllersDir, 0755, true);
            CLI::write("Created directory: {$controllersDir}", 'green');
        }

        $content = $this->buildController($ucf_module, $ucf_component, $slc_module, $slc_component, $namespaced);

        file_put_contents($pathfile, $content);
        CLI::write("  Created: {$pathfile}", 'yellow');
        CLI::write('Controller file generated successfully.', 'green');
        return EXIT_SUCCESS;
    }

    // -------------------------------------------------------------------------

    private function buildController(string $ucf_module, string $ucf_component, string $slc_module, string $slc_component, string $namespaced): string
    {
        $c  = "<?php\n";
        $c .= "\n";
        $c .= "namespace App\\Modules\\{$ucf_module}\\Controllers;\n";
        $c .= $this->copyright($namespaced);
        $c .= "\n";
        $c .= "use App\\Controllers\\ModuleController;\n";
        $c .= "\n";
        $c .= "class {$ucf_component} extends ModuleController {\n";
        $c .= "\n";

        // Route hints
        $c .= "\t//[{$ucf_module}/Config/Routes]\n";
        $c .= "\t//[{$ucf_component}]----------------------------------------------------------------------------------------\n";
        $c .= "\t//\"{$slc_module}-{$slc_component}-home\"=>\"\$views\\\\{$ucf_component}\\\\Home\\\\index\",\n";
        $c .= "\t//\"{$slc_module}-{$slc_component}-list\"=>\"\$views\\\\{$ucf_component}\\\\List\\\\index\",\n";
        $c .= "\t//\"{$slc_module}-{$slc_component}-view\"=>\"\$views\\\\{$ucf_component}\\\\View\\\\index\",\n";
        $c .= "\t//\"{$slc_module}-{$slc_component}-create\"=>\"\$views\\\\{$ucf_component}\\\\Create\\\\index\",\n";
        $c .= "\t//\"{$slc_module}-{$slc_component}-edit\"=>\"\$views\\\\{$ucf_component}\\\\Edit\\\\index\",\n";
        $c .= "\t//\"{$slc_module}-{$slc_component}-delete\"=>\"\$views\\\\{$ucf_component}\\\\Delete\\\\index\",\n";

        // Permission hints
        $c .= "\n";
        $c .= "\t//[{$ucf_component}]----------------------------------------------------------------------------------------\n";
        $c .= "\t//\t\t\t\t\t\t\"{$slc_module}-{$slc_component}-access\",\n";
        $c .= "\t//\t\t\t\t\t\t\"{$slc_module}-{$slc_component}-view\",\n";
        $c .= "\t//\t\t\t\t\t\t\"{$slc_module}-{$slc_component}-view-all\",\n";
        $c .= "\t//\t\t\t\t\t\t\"{$slc_module}-{$slc_component}-create\",\n";
        $c .= "\t//\t\t\t\t\t\t\"{$slc_module}-{$slc_component}-edit\",\n";
        $c .= "\t//\t\t\t\t\t\t\"{$slc_module}-{$slc_component}-edit-all\",\n";
        $c .= "\t//\t\t\t\t\t\t\"{$slc_module}-{$slc_component}-delete\",\n";
        $c .= "\t//\t\t\t\t\t\t\"{$slc_module}-{$slc_component}-delete-all\",\n";
        $c .= "\n";

        // __construct
        $c .= "    public function __construct() {\n";
        $c .= "       parent::__construct();\n";
        $c .= "       \$this->prefix = '{$slc_module}-{$slc_component}';\n";
        $c .= "       \$this->module = 'App\\\\Modules\\\\{$ucf_module}';\n";
        $c .= "       \$this->views = \$this->module . '\\\\Views';\n";
        $c .= "       \$this->viewer = \$this->views . '\\\\index';\n";
        $c .= "       helper(\$this->module.'\\\\Helpers\\\\{$ucf_module}');\n";
        $c .= "    }\n";
        $c .= "\n";

        // index
        $c .= "    public function index() {\n";
        $c .= "        \$url = base_url('{$slc_module}/{$slc_component}/home/' . lpk());\n";
        $c .= "        return (redirect()->to(\$url));\n";
        $c .= "    }\n";
        $c .= "\n";

        // home
        $c .= "    public function home(string \$rnd) {\n";
        $c .= "        \$this->oid = \$rnd;\n";
        $c .= "        \$this->prefix = \"{\$this->prefix}-home\";\n";
        $c .= "        \$this->component = \$this->views . '\\\\{$ucf_component}\\\\Home';\n";
        $c .= "        return (view(\$this->viewer, \$this->get_Array()));\n";
        $c .= "    }\n";
        $c .= "\n";

        // view
        $c .= "    public function view(string \$oid) {\n";
        $c .= "        \$this->oid = \$oid;\n";
        $c .= "        \$this->prefix = \"{\$this->prefix}-view\";\n";
        $c .= "        \$this->component = \$this->views . '\\\\{$ucf_component}\\\\View';\n";
        $c .= "        return (view(\$this->viewer, \$this->get_Array()));\n";
        $c .= "    }\n";
        $c .= "\n";

        // list
        $c .= "    public function list(string \$rnd) {\n";
        $c .= "        \$this->oid = \$rnd;\n";
        $c .= "        \$this->prefix = \"{\$this->prefix}-list\";\n";
        $c .= "        \$this->component = \$this->views . '\\\\{$ucf_component}\\\\List';\n";
        $c .= "        return (view(\$this->viewer, \$this->get_Array()));\n";
        $c .= "    }\n";
        $c .= "\n";

        // create
        $c .= "    public function create(string \$rnd) {\n";
        $c .= "        \$this->oid = \$rnd;\n";
        $c .= "        \$this->prefix = \"{\$this->prefix}-create\";\n";
        $c .= "        \$this->component = \$this->views . '\\\\{$ucf_component}\\\\Create';\n";
        $c .= "        return (view(\$this->viewer, \$this->get_Array()));\n";
        $c .= "    }\n";
        $c .= "\n";

        // edit
        $c .= "    public function edit(string \$oid) {\n";
        $c .= "        \$this->oid = \$oid;\n";
        $c .= "        \$this->prefix = \"{\$this->prefix}-edit\";\n";
        $c .= "        \$this->component = \$this->views . '\\\\{$ucf_component}\\\\Edit';\n";
        $c .= "        return (view(\$this->viewer, \$this->get_Array()));\n";
        $c .= "    }\n";
        $c .= "\n";

        // delete
        $c .= "    public function delete(string \$oid) {\n";
        $c .= "        \$this->oid = \$oid;\n";
        $c .= "        \$this->prefix = \"{\$this->prefix}-delete\";\n";
        $c .= "        \$this->component = \$this->views . '\\\\{$ucf_component}\\\\Delete';\n";
        $c .= "        return (view(\$this->viewer, \$this->get_Array()));\n";
        $c .= "    }\n";
        $c .= "\n";

        $c .= "}\n";
        $c .= "?>";
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
