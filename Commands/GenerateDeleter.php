<?php

namespace App\Modules\Development\Commands;

use Higgs\CLI\BaseCommand;
use Higgs\CLI\CLI;

/**
 * Spark command: development:generate-deleter
 *
 * Generates the Delete/ view set for a given table.
 * Usage: php spark development:generate-deleter <table>
 * Example: php spark development:generate-deleter access_events
 */
class GenerateDeleter extends BaseCommand
{
    protected $group       = 'Development';
    protected $name        = 'development:generate-deleter';
    protected $description = 'Generates the Delete/ view set for a given table (e.g. access_events).';
    protected $usage       = 'development:generate-deleter <table>';
    protected $arguments   = [
        'table' => 'Table name (e.g. access_events or module_component_options)',
    ];

    public function run(array $params): int
    {
        $table = array_shift($params);
        if (empty($table)) {
            CLI::error('Usage: php spark development:generate-deleter <table>');
            return EXIT_ERROR;
        }

        $eid           = explode('_', $table);
        $ucf_module    = ucfirst($eid[0]);
        $ucf_component = ucfirst($eid[1]);
        $ucf_options   = isset($eid[2]) ? ucfirst($eid[2]) : '';
        $slc_module    = strtolower($eid[0]);
        $slc_component = strtolower($eid[1]);
        $slc_options   = isset($eid[2]) ? strtolower($eid[2]) : '';

        $strings       = service('strings');
        $sucf_component = $strings->removePluralEnding($ucf_component);
        $slc_singular   = strtolower($sucf_component);

        if (count($eid) === 3) {
            $pathfiles   = APPPATH . "Modules/{$ucf_module}/Views/{$ucf_component}/{$ucf_options}/Delete";
            $singular    = "{$slc_module}-{$slc_component}-{$slc_options}-delete";
            $plural      = "{$slc_module}-{$slc_component}-{$slc_options}-delete-all";
            $path        = "/{$slc_module}/{$slc_component}/{$slc_options}";
            $ns_prefix   = "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\{$ucf_options}\\Delete";
        } else {
            $pathfiles   = APPPATH . "Modules/{$ucf_module}/Views/{$ucf_component}/Delete";
            $singular    = "{$slc_module}-{$slc_component}-delete";
            $plural      = "{$slc_module}-{$slc_component}-delete-all";
            $path        = "/{$slc_module}/{$slc_component}";
            $ns_prefix   = "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\Delete";
        }

        if (!is_dir($pathfiles)) {
            mkdir($pathfiles, 0755, true);
            CLI::write("Created directory: {$pathfiles}", 'green');
        }

        $files = [
            'index.php'     => $this->buildIndex($ucf_module, $ucf_component, $ns_prefix, $singular, $plural),
            'form.php'      => $this->buildForm($ucf_module, $ucf_component, $sucf_component, $slc_module, $slc_component, $ns_prefix),
            'processor.php' => $this->buildProcessor($ucf_module, $ucf_component, $slc_module, $slc_component, $ns_prefix),
            'validator.php' => $this->buildValidator($ucf_module, $ucf_component, $ns_prefix),
            'breadcrumb.php' => $this->buildBreadcrumb($slc_module, $slc_component, $ns_prefix),
            'deny.php'      => $this->buildDeny($slc_module, $slc_component, $ns_prefix),
        ];

        foreach ($files as $filename => $content) {
            $filepath = $pathfiles . '/' . $filename;
            file_put_contents($filepath, $content);
            CLI::write("  Created: {$filepath}", 'yellow');
        }

        CLI::write('Delete/ view set generated successfully.', 'green');
        return EXIT_SUCCESS;
    }

    // -------------------------------------------------------------------------

    private function buildIndex(string $ucf_module, string $ucf_component, string $ns_prefix, string $singular, string $plural): string
    {
        $namespaced = "{$ns_prefix}\\index.php";
        $c  = "<?php\n";
        $c .= $this->copyright($namespaced);
        $c .= "//[Vars]-----------------------------------------------------------------------------\n";
        $c .= "/** @var \$parent ModuleController */\n";
        $c .= "/** @var \$authentication \App\Libraries\Authentication */\n";
        $c .= "/** @var \$request \Higgs\HTTP\IncomingRequest */\n";
        $c .= "\$data = \$parent->get_Array();\n";
        $c .= "\$data['model']=model(\"App\\\\Modules\\\\{$ucf_module}\\\\Models\\\\{$ucf_module}_{$ucf_component}\");\n";
        $c .= "\$data['permissions'] = array('singular' => '{$singular}', \"plural\" =>'{$plural}');\n";
        $c .= "\$singular = \$authentication->has_Permission(\$data['permissions']['singular']);\n";
        $c .= "\$plural = \$authentication->has_Permission(\$data['permissions']['plural']);\n";
        $c .= "\$author= \$data['model']->getAuthority(\$oid,safe_get_user());\n";
        $c .= "\$authority= (\$singular&&\$author)?true:false;\n";
        $c .= "\$submited = \$request->getPost(\"submited\");\n";
        $c .= "\$breadcrumb = \$component . '\\\\breadcrumb';\n";
        $c .= "\$validator = \$component . '\\\\validator';\n";
        $c .= "\$form = \$component . '\\\\form';\n";
        $c .= "\$deny = \$component . '\\\\deny';\n";
        $c .= "//[Build]-----------------------------------------------------------------------------\n";
        $c .= "if (\$plural||\$authority) {\n";
        $c .= "\t\tif (!empty(\$submited)) {\n";
        $c .= "\t\t\t\t\$json = array('breadcrumb' => view(\$breadcrumb, \$data), 'main' => view(\$validator, \$data), 'right' => \"\");\n";
        $c .= "\t\t} else {\n";
        $c .= "\t\t\t\t\$json = array('breadcrumb' => view(\$breadcrumb, \$data), 'main' => view(\$form, \$data), 'right' => \"\");\n";
        $c .= "\t\t}\n";
        $c .= "} else {\n";
        $c .= "\t\t\$json = array('breadcrumb' => view(\$breadcrumb, \$data), 'main' => view(\$deny, \$data), 'right' => \"\");\n";
        $c .= "}\n";
        $c .= "echo(json_encode(\$json));\n";
        $c .= "?>\n";
        return $c;
    }

    private function buildForm(string $ucf_module, string $ucf_component, string $sucf_component, string $slc_module, string $slc_component, string $ns_prefix): string
    {
        $namespaced = "{$ns_prefix}\\form.php";
        $c  = "<?php\n";
        $c .= $this->copyright($namespaced);
        $c .= "use Higgs\\Frontend\\Bootstrap\\v5_3_3\\Bootstrap as BS5;\n";
        $c .= "//[Inherited from ModuleController]---------------------------------------------------\n";
        $c .= "// \$authentication  → service('authentication')  App\\Libraries\\Authentication\n";
        $c .= "// \$bootstrap       → service('bootstrap')\n";
        $c .= "// \$dates           → service('Dates')           App\\Libraries\\Dates\n";
        $c .= "// \$strings         → service('strings')         App\\Libraries\\Strings\n";
        $c .= "// \$request         → service('request')\n";
        $c .= "// \$server          → service('server')\n";
        $c .= "// \$parent          → ModuleController instance  (use \$parent->get_Array() for view data)\n";
        $c .= "\$server = service(\"server\");\n";
        $c .= "//[Models]-----------------------------------------------------------------------------\n";
        $c .= "//\$model = model(\"App\\\\Modules\\\\{$ucf_module}\\\\Models\\\\{$ucf_module}_{$ucf_component}\");\n";
        $c .= "//[Vars]-----------------------------------------------------------------------------\n";
        $c .= "/** @var \$parent ModuleController */\n";
        $c .= "/** @var \$authentication \App\Libraries\Authentication */\n";
        $c .= "/** @var \$request \Higgs\HTTP\IncomingRequest */\n";
        $c .= "\$f = service(\"forms\",array(\"lang\" => \"{$ucf_module}_{$ucf_component}.\"));\n";
        $c .= "\$r= \$model->get{$sucf_component}(\$oid);\n";
        $c .= "\$name = urldecode(\$r[\"name\"]);\n";
        $c .= "\$message=sprintf(lang(\"{$ucf_module}_{$ucf_component}.delete-message\"),\$name);\n";
        $c .= "\$back=\$f->get_Value(\"back\",\$server->get_Referer());\n";
        $c .= "//[Fields]-----------------------------------------------------------------------------\n";
        $c .= "\$f->add_HiddenField(\"back\",\$back);\n";
        $c .= "\$f->add_HiddenField(\"pkey\", \$oid);\n";
        $c .= "\$f->fields[\"cancel\"] = \$f->get_Cancel(\"cancel\", array(\"href\" => \$back, \"text\" => lang(\"App.Cancel\"), \"type\" => \"secondary\", \"proportion\" => \"col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 padding-right\"));\n";
        $c .= "\$f->fields[\"submit\"] = \$f->get_Submit(\"submit\", array(\"value\" => lang(\"App.Delete\"), \"proportion\" => \"col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 padding-left\"));\n";
        $c .= "//[Groups]-----------------------------------------------------------------------------\n";
        $c .= "\$f->groups[\"gy\"] = \$f->get_GroupSeparator();\n";
        $c .= "//[Buttons]----------------------------------------------------------------------------\n";
        $c .= "\$f->groups[\"gz\"] = \$f->get_Buttons(array(\"fields\" => \$f->fields[\"submit\"] . \$f->fields[\"cancel\"]));\n";
        $c .= "//[Build]-----------------------------------------------------------------------------\n";
        $c .= "\$card = BS5::card([\n";
        $c .= "    'headerTitle'   => sprintf(lang(\"{$ucf_module}_{$ucf_component}.delete-title\"), \$name),\n";
        $c .= "    'headerClass'   => 'bg-danger text-white',\n";
        $c .= "    'headerButtons' => [BS5::button(['content' => BS5::icon(['icon' => 'arrow-left', 'style' => 'duotone']), 'variant' => 'secondary', 'size' => 'sm', 'attributes' => ['href' => \$back]])],\n";
        $c .= "    'content'       => \$f,\n";
        $c .= "    'attributes'    => ['class' => 'border-danger shadow-sm'],\n";
        $c .= "]);\n";
        $c .= "echo(\$card);\n";
        $c .= "?>\n";
        return $c;
    }

    private function buildProcessor(string $ucf_module, string $ucf_component, string $slc_module, string $slc_component, string $ns_prefix): string
    {
        $namespaced = "{$ns_prefix}\\processor.php";
        $c  = "<?php\n";
        $c .= $this->copyright($namespaced);
        $c .= "use Higgs\\Frontend\\Bootstrap\\v5_3_3\\Bootstrap as BS5;\n";
        $c .= "//[Inherited from ModuleController]---------------------------------------------------\n";
        $c .= "// \$authentication  → service('authentication')  App\\Libraries\\Authentication\n";
        $c .= "// \$bootstrap       → service('bootstrap')\n";
        $c .= "// \$dates           → service('Dates')           App\\Libraries\\Dates\n";
        $c .= "// \$strings         → service('strings')         App\\Libraries\\Strings\n";
        $c .= "// \$request         → service('request')\n";
        $c .= "// \$server          → service('server')\n";
        $c .= "// \$parent          → ModuleController instance  (use \$parent->get_Array() for view data)\n";
        $c .= "\$f = service(\"forms\", array(\"lang\" => \"{$ucf_module}_{$ucf_component}.\"));\n";
        $c .= "//\$model = model(\"App\\\\Modules\\\\{$ucf_module}\\\\Models\\\\{$ucf_module}_{$ucf_component}\");\n";
        $c .= "\$pkey= \$f->get_Value(\"pkey\");\n";
        $c .= "\$row = \$model->withDeleted()->find(\$pkey);\n";
        $c .= "/* Vars */\n";
        $c .= "\$l[\"back\"]=\$f->get_Value(\"back\");\n";
        $c .= "\$l[\"edit\"]=\"/{$slc_module}/{$slc_component}/edit/{\$pkey}\";\n";
        $c .= "\$vsuccess=\"{$slc_module}/{$slc_component}-delete-success-message.mp3\";\n";
        $c .= "\$vnoexist=\"{$slc_module}/{$slc_component}-delete-noexist-message.mp3\";\n";
        $c .= "/* Build */\n";
        $c .= "if (!empty(\$row)) {\n";
        $c .= "    \$delete = \$model->delete(\$pkey);\n";
        $c .= "    \$_icon_col = BS5::col(['attributes' => ['class' => 'text-center py-3'], 'content' => BS5::icon(['icon' => 'circle-check', 'style' => 'duotone', 'size' => '2xl'])]);\n";
        $c .= "    \$_msg_col  = BS5::col(['attributes' => ['class' => 'text-center pb-2'], 'content' => lang(\"{$ucf_module}_{$ucf_component}.delete-success-message\")]);\n";
        $c .= "    \$_btn_col  = BS5::col(['attributes' => ['class' => 'text-center pb-3'], 'content' => BS5::button(['content' => lang('App.Continue'), 'variant' => 'success', 'size' => 'sm', 'attributes' => ['href' => \$l['back']]])]);\n";
        $c .= "    \$_content  = BS5::row(['attributes' => ['class' => 'justify-content-center'], 'content' => \$_icon_col.\$_msg_col.\$_btn_col]);\n";
        $c .= "    \$c = BS5::card([\n";
        $c .= "        'headerTitle' => lang(\"{$ucf_module}_{$ucf_component}.delete-success-title\"),\n";
        $c .= "        'headerClass' => 'bg-success text-white',\n";
        $c .= "        'content'     => \$_content,\n";
        $c .= "        'attributes'  => ['class' => 'border-success shadow-sm'],\n";
        $c .= "    ]);\n";
        $c .= "} else {\n";
        $c .= "    \$_icon_col = BS5::col(['attributes' => ['class' => 'text-center py-3'], 'content' => BS5::icon(['icon' => 'triangle-exclamation', 'style' => 'duotone', 'size' => '2xl'])]);\n";
        $c .= "    \$_msg_col  = BS5::col(['attributes' => ['class' => 'text-center pb-2'], 'content' => lang(\"{$ucf_module}_{$ucf_component}.delete-noexist-message\")]);\n";
        $c .= "    \$_btn_col  = BS5::col(['attributes' => ['class' => 'text-center pb-3'], 'content' => BS5::button(['content' => lang('App.Continue'), 'variant' => 'warning', 'size' => 'sm', 'attributes' => ['href' => \$l['back']]])]);\n";
        $c .= "    \$_content  = BS5::row(['attributes' => ['class' => 'justify-content-center'], 'content' => \$_icon_col.\$_msg_col.\$_btn_col]);\n";
        $c .= "    \$c = BS5::card([\n";
        $c .= "        'headerTitle' => lang(\"{$ucf_module}_{$ucf_component}.delete-noexist-title\"),\n";
        $c .= "        'headerClass' => 'bg-warning text-dark',\n";
        $c .= "        'content'     => \$_content,\n";
        $c .= "        'attributes'  => ['class' => 'border-warning shadow-sm'],\n";
        $c .= "    ]);\n";
        $c .= "}\n";
        $c .= "echo(\$c);\n";
        $c .= "\$model->invalidateSearchCache();\n";
        $c .= "?>\n";
        return $c;
    }

    private function buildValidator(string $ucf_module, string $ucf_component, string $ns_prefix): string
    {
        $namespaced = "{$ns_prefix}\\validator.php";
        $c  = "<?php\n";
        $c .= $this->copyright($namespaced);
        $c .= "\$bootstrap = service('bootstrap');\n";
        $c .= "\$f = service(\"forms\",array(\"lang\" => \"{$ucf_module}_{$ucf_component}.\"));\n";
        $c .= "//[Request]-----------------------------------------------------------------------------\n";
        $c .= "\$f->set_ValidationRule(\"pkey\",\"trim|required\");\n";
        $c .= "//[Validation]-----------------------------------------------------------------------------\n";
        $c .= "if (\$f->run_Validation()) {\n";
        $c .= "   \$c=view(\$component.'\\\\processor',\$parent->get_Array());\n";
        $c .= "}else {\n";
        $c .= "   \$errors=\$f->validation->listErrors();\n";
        $c .= "\$errors = \$f->validation->listErrors();\n";
        $c .= "\$c =\$card=\$bootstrap->get_Card('access-denied', array(\n";
        $c .= "    'class'=>'card-danger',\n";
        $c .= "    'icon'=>'fa-duotone fa-triangle-exclamation',\n";
        $c .= "    'text-class' => 'text-center',\n";
        $c .= "    'text' => lang('App.validator-errors-message'),\n";
        $c .= "    'errors' => \$errors,\n";
        $c .= "    'footer-class'=>'text-center',\n";
        $c .= "    'voice'=>\"app/validator-errors-message.mp3\",\n";
        $c .= "));\n";
        $c .= "   \$c.=view(\$component.'\\\\form',\$parent->get_Array());\n";
        $c .= "}\n";
        $c .= "//[Build]-----------------------------------------------------------------------------\n";
        $c .= "echo(\$c);\n";
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
