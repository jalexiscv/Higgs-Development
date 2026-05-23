<?php

/**
 * █ Genera las vistas Create/ (index, form, processor, validator, breadcrumb, deny)
 * █ Equivalente al generador web Development/Generators/Creator
 * █ Uso: php spark development:generate-creator <tabla> [--database=nombre_bd] [--force]
 **/

namespace App\Modules\Development\Commands;

use Config\Database;
use Higgs\CLI\BaseCommand;
use Higgs\CLI\CLI;

class GenerateCreator extends BaseCommand
{
    protected $group       = 'Development';
    protected $name        = 'development:generate-creator';
    protected $description = 'Genera las vistas Create/ (index, form, processor, validator, breadcrumb, deny) desde una tabla de BD';
    protected $usage       = 'development:generate-creator <tabla> [--database=nombre_bd] [--force]';
    protected $arguments   = [
        'tabla' => 'Nombre de la tabla BD (ej: access_events, sie_pensums_prerequisites)',
    ];
    protected $options     = [
        '--database' => 'Base de datos a conectar (default: la configurada en .env)',
        '--force'    => 'Sobreescribir archivos existentes sin confirmar',
    ];

    public function run(array $params): int
    {
        CLI::write('============================================', 'green');
        CLI::write('  Development: Generate Creator', 'yellow');
        CLI::write('============================================', 'green');
        CLI::newLine();

        $table    = $params[0] ?? null;
        $database = null;
        $force    = false;

        foreach ($_SERVER['argv'] ?? [] as $arg) {
            if (!is_string($arg)) {
                continue;
            }
            if (strpos($arg, '--database=') === 0) {
                $database = trim(substr($arg, 11), '"\'');
            } elseif ($arg === '--force') {
                $force = true;
            }
        }

        if (empty($table)) {
            CLI::error('Error: debes especificar el nombre de la tabla.');
            CLI::write('Uso: php spark development:generate-creator access_events', 'yellow');
            return EXIT_ERROR;
        }

        $eid           = explode('_', $table);
        $ucf_module    = ucfirst(strtolower($eid[0]));
        $ucf_component = ucfirst(strtolower($eid[1]));
        $ucf_options   = isset($eid[2]) ? ucfirst(strtolower($eid[2])) : null;
        $slc_module    = strtolower($eid[0]);
        $slc_component = strtolower($eid[1]);
        $slc_options   = isset($eid[2]) ? strtolower($eid[2]) : null;
        $is_triple     = count($eid) === 3;

        if ($is_triple) {
            $pathfiles = APPPATH . "Modules/{$ucf_module}/Views/{$ucf_component}/{$ucf_options}/_Create";
            $singular  = "{$slc_module}-{$slc_component}-{$slc_options}-create";
        } else {
            $pathfiles = APPPATH . "Modules/{$ucf_module}/Views/{$ucf_component}/_Create";
            $singular  = "{$slc_module}-{$slc_component}-create";
        }

        CLI::write("Tabla   : {$table}", 'cyan');
        CLI::write("Destino : {$pathfiles}", 'cyan');
        CLI::newLine();

        if (!empty($database)) {
            try {
                $cfg = config('Database');
                $cfg->default['database'] = $database;
                Database::connect('default', false);
            } catch (\Exception $e) {
                CLI::error('Error al conectar: ' . $e->getMessage());
                return EXIT_ERROR;
            }
        }

        $db = Database::connect('default');
        if (!$db->tableExists($table)) {
            CLI::error("La tabla '{$table}' no existe.");
            return EXIT_ERROR;
        }
        $fields = $db->getFieldNames($table);
        CLI::write('Campos: ' . implode(', ', $fields), 'white');
        CLI::newLine();

        if (!is_dir($pathfiles)) {
            mkdir($pathfiles, 0755, true);
        }

        $files = [
            'index.php'      => $this->buildIndex($ucf_module, $ucf_component, $ucf_options, $slc_module, $slc_component, $slc_options, $singular, $is_triple),
            'form.php'       => $this->buildForm($ucf_module, $ucf_component, $ucf_options, $slc_module, $slc_component, $slc_options, $fields, $is_triple),
            'processor.php'  => $this->buildProcessor($ucf_module, $ucf_component, $ucf_options, $slc_module, $slc_component, $slc_options, $fields, $is_triple),
            'validator.php'  => $this->buildValidator($ucf_module, $ucf_component, $ucf_options, $fields, $is_triple),
            'breadcrumb.php' => $this->buildBreadcrumb($ucf_module, $ucf_component, $ucf_options, $slc_module, $slc_component, $is_triple),
            'deny.php'       => $this->buildDeny($ucf_module, $ucf_component, $ucf_options, $slc_module, $slc_component, $is_triple),
        ];

        foreach ($files as $filename => $content) {
            $filepath = "{$pathfiles}/{$filename}";
            if (file_exists($filepath) && !$force) {
                $answer = CLI::prompt("  '{$filename}' ya existe. ¿Sobreescribir?", ['y', 'n']);
                if ($answer !== 'y') {
                    CLI::write("  → Saltando {$filename}", 'yellow');
                    continue;
                }
            }
            file_put_contents($filepath, $content);
            CLI::write("  ✓ {$filename}", 'green');
        }

        CLI::newLine();
        CLI::write('  ¡Generación completada!', 'yellow');
        CLI::write('============================================', 'green');
        return EXIT_SUCCESS;
    }

    protected function buildIndex(
        string $ucf_module,
        string $ucf_component,
        ?string $ucf_options,
        string $slc_module,
        string $slc_component,
        ?string $slc_options,
        string $singular,
        bool $is_triple
    ): string {
        $namespaced = $is_triple
            ? "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\{$ucf_options}\\Create\\index.php"
            : "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\Create\\index.php";

        $c  = "<?php\n";
        $c .= $this->copyright($namespaced);
        $c .= COMMENT_HR_VARS;
        $c .= COMMENT_MODULECONTROLER_VARS;
        $c .= "\$data = \$parent->get_Array();\n";
        $c .= "\$data['permissions'] = array('singular' => '{$singular}', \"plural\" =>false);\n";
        $c .= "\$singular = \$authentication->has_Permission(\$data['permissions']['singular']);\n";
        $c .= "\$submited = \$request->getPost(\"submited\");\n";
        $c .= "\$validator = \$component . '\\validator';\n";
        $c .= "\$breadcrumb = \$component . '\breadcrumb';\n";
        $c .= "\$form = \$component . '\\form';\n";
        $c .= "\$deny = \$component . '\\deny';\n";
        $c .= COMMENT_HR_BUILD;
        $c .= "if (\$singular) {\n";
        $c .= "\t\tif (!empty(\$submited)){\n";
        $c .= "\t\t\t\t\$json = array(\n";
        $c .= "\t\t\t\t\t 'breadcrumb' => view(\$breadcrumb, \$data),\n";
        $c .= "\t\t\t\t\t 'main' => view(\$validator, \$data),\n";
        $c .= "\t\t\t\t\t 'right' => \"\",\n";
        $c .= "\t\t\t\t\t 'main_template' =>'c8c4',//'c12',\n";
        $c .= "\t\t\t\t );\n";
        $c .= "\t\t} else {\n";
        $c .= "\t\t\t\t\$json = array(\n";
        $c .= "\t\t\t\t\t 'breadcrumb' => view(\$breadcrumb, \$data),\n";
        $c .= "\t\t\t\t\t 'main' => view(\$form, \$data),\n";
        $c .= "\t\t\t\t\t 'right' => \"\",\n";
        $c .= "\t\t\t\t\t 'main_template' =>'c8c4',//'c12',\n";
        $c .= "\t\t\t\t );\n";
        $c .= "\t\t}\n";
        $c .= "} else {\n";
        $c .= "\t\t\t\t\$json = array(\n";
        $c .= "\t\t\t\t\t 'breadcrumb' => view(\$breadcrumb, \$data),\n";
        $c .= "\t\t\t\t\t 'main' => view(\$deny, \$data),\n";
        $c .= "\t\t\t\t\t 'right' => \"\",\n";
        $c .= "\t\t\t\t\t 'main_template' =>'c8c4',//'c12',\n";
        $c .= "\t\t\t\t );\n";
        $c .= "}\n";
        $c .= "echo(json_encode(\$json));\n";
        $c .= "?>\n";
        return $c;
    }

    protected function buildForm(
        string $ucf_module,
        string $ucf_component,
        ?string $ucf_options,
        string $slc_module,
        string $slc_component,
        ?string $slc_options,
        array $fields,
        bool $is_triple
    ): string {
        $namespaced = $is_triple
            ? "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\{$ucf_options}\\Create\\form.php"
            : "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\Create\\form.php";

        $c  = "<?php\n";
        $c .= $this->copyright($namespaced);
        $c .= "use Higgs\\Frontend\\Bootstrap\\v5_3_3\\Bootstrap as BS5;\n";
        $c .= "//[Inherited from ModuleController]---------------------------------------------------\n";
        $c .= "// \$authentication, \$bootstrap, \$dates, \$strings, \$request, \$server, \$parent\n";
        $c .= "\$b = service(\"bootstrap\");\n";
        $c .= "\$f = service(\"forms\",array(\"lang\" => \"{$ucf_module}_{$ucf_component}.\"));\n";
        $c .= "\$server = service(\"server\");\n";
        $c .= COMMENT_HR_MODELS;
        $c .= "//\$model = model(\"App\\Modules\\{$ucf_module}\\Models\\{$ucf_module}_{$ucf_component}\");\n";
        $c .= COMMENT_HR_VARS;
        $c .= COMMENT_MODULECONTROLER_VARS;
        $c .= "\$back=\$server->get_Referer();\n";
        $c .= "\$r[\"back\"] = \$f->get_Value(\"back\",\$back);\n";
        foreach ($fields as $field) {
            if ($field === 'author') {
                $c .= "\$r[\"{$field}\"] = \$f->get_Value(\"{$field}\",safe_get_user());\n";
            } elseif ($field === 'date') {
                $c .= "\$r[\"{$field}\"] = \$f->get_Value(\"{$field}\",service(\"dates\")::get_Date());\n";
            } elseif ($field === 'time') {
                $c .= "\$r[\"{$field}\"] = \$f->get_Value(\"{$field}\",service(\"dates\")::get_Time());\n";
            } else {
                $c .= "\$r[\"{$field}\"] = \$f->get_Value(\"{$field}\");\n";
            }
        }
        $c .= COMMENT_HR_FIELDS;
        $c .= "\$f->add_HiddenField(\"back\",\$r[\"back\"]);\n";
        foreach ($fields as $field) {
            if ($field === 'author') {
                $c .= "\$f->add_HiddenField(\"author\",\$r[\"author\"]);\n";
            } else {
                $c .= "\$f->fields[\"{$field}\"] = \$f->get_FieldText(\"{$field}\", array(\"value\" => \$r[\"{$field}\"],\"proportion\"=>\"col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12\"));\n";
            }
        }
        $c .= "\$f->fields[\"cancel\"]=\$f->get_Cancel(\"cancel\", array(\"href\" =>\$r[\"back\"],\"text\" =>lang(\"App.Cancel\"),\"type\"=>\"secondary\",\"proportion\" =>\"col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 padding-right\"));\n";
        $c .= "\$f->fields[\"submit\"] =\$f->get_Submit(\"submit\", array(\"value\" =>lang(\"App.Create\"),\"proportion\" =>\"col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 padding-left\"));\n";
        $c .= COMMENT_HR_GROUPS;
        $skipped = ['author', 'created_at', 'updated_at', 'deleted_at'];
        $visible  = array_values(array_filter($fields, fn ($f) => !in_array($f, $skipped)));
        $chunks   = array_chunk($visible, 3);
        $grupo = 0;
        foreach ($chunks as $chunk) {
            $grupo++;
            $fields_code = implode('.', array_map(fn ($f) => "\$f->fields[\"{$f}\"]", $chunk));
            $c .= "\$f->groups[\"g{$grupo}\"]=\$f->get_Group(array(\"legend\"=>\"\",\"fields\"=>({$fields_code})));\n";
        }
        $c .= COMMENT_HR_BUTTONS;
        $c .= "\$f->groups[\"gy\"] =\$f->get_GroupSeparator();\n";
        $c .= "\$f->groups[\"gz\"] = \$f->get_Buttons(array(\"fields\"=>\$f->fields[\"submit\"].\$f->fields[\"cancel\"]));\n";
        $c .= COMMENT_HR_BUILD;
        $c .= "\$card = BS5::card([\n";
        $c .= "    'headerTitle'   => lang(\"{$ucf_module}_{$ucf_component}.create-title\"),\n";
        $c .= "    'headerButtons' => [BS5::button(['content' => BS5::icon(['icon' => 'arrow-left', 'style' => 'duotone']), 'variant' => 'secondary', 'size' => 'sm', 'attributes' => ['href' => \$back]])],\n";
        $c .= "    'content'       => \$f,\n";
        $c .= "]);\n";
        $c .= "echo(\$card);\n";
        $c .= "?>\n";
        return $c;
    }

    protected function buildProcessor(
        string $ucf_module,
        string $ucf_component,
        ?string $ucf_options,
        string $slc_module,
        string $slc_component,
        ?string $slc_options,
        array $fields,
        bool $is_triple
    ): string {
        $namespaced = $is_triple
            ? "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\{$ucf_options}\\Create\\processor.php"
            : "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\Create\\processor.php";

        $c  = "<?php\n";
        $c .= $this->copyright($namespaced);
        $c .= "use Higgs\\Frontend\\Bootstrap\\v5_3_3\\Bootstrap as BS5;\n";
        $c .= "//[Inherited from ModuleController]---------------------------------------------------\n";
        $c .= "// \$authentication, \$bootstrap, \$dates, \$strings, \$request, \$server, \$parent\n";
        $c .= "//[Models]-----------------------------------------------------------------------------\n";
        $c .= "\$f = service(\"forms\",array(\"lang\" => \"{$ucf_module}_{$ucf_component}.\"));\n";
        $c .= "\$model = model(\"App\\Modules\\{$ucf_module}\\Models\\{$ucf_module}_{$ucf_component}\");\n";
        $c .= "//[Vars]-----------------------------------------------------------------------------\n";
        $c .= "\$d = array(\n";
        foreach ($fields as $field) {
            if (!in_array($field, ['created_at', 'updated_at', 'deleted_at'])) {
                if ($field === 'author') {
                    $c .= "    \"{$field}\" => safe_get_user(),\n";
                } elseif ($field === 'date') {
                    $c .= "    \"{$field}\" => safe_get_date(),\n";
                } elseif ($field === 'time') {
                    $c .= "    \"{$field}\" => safe_get_time(),\n";
                } else {
                    $c .= "    \"{$field}\" => \$f->get_Value(\"{$field}\"),\n";
                }
            }
        }
        $c .= ");\n";
        $c .= "\$row = \$model->find(\$d[\"{$fields[0]}\"]);\n";
        $c .= "\$l[\"back\"]=\$f->get_Value(\"back\");\n";
        $c .= "\$l[\"edit\"]=\"/{$slc_module}/{$slc_component}/edit/{\$d[\"{$fields[0]}\"]}\";\n";
        $c .= COMMENT_HR_BUILD;
        $c .= "if (is_array(\$row)) {\n";
        $c .= "    \$_icon_col = BS5::col(['attributes' => ['class' => 'text-center py-3'], 'content' => BS5::icon(['icon' => 'triangle-exclamation', 'style' => 'duotone', 'size' => '2xl'])]);\n";
        $c .= "    \$_msg_col  = BS5::col(['attributes' => ['class' => 'text-center pb-2'], 'content' => lang(\"{$ucf_module}_{$ucf_component}.create-duplicate-message\")]);\n";
        $c .= "    \$_btn_col  = BS5::col(['attributes' => ['class' => 'text-center pb-3'], 'content' => BS5::button(['content' => lang('App.Continue'), 'variant' => 'warning', 'size' => 'sm', 'attributes' => ['href' => \$l['back']]])]);\n";
        $c .= "    \$_content  = BS5::row(['attributes' => ['class' => 'justify-content-center'], 'content' => \$_icon_col.\$_msg_col.\$_btn_col]);\n";
        $c .= "    \$c = BS5::card(['headerTitle' => lang(\"{$ucf_module}_{$ucf_component}.create-duplicate-title\"), 'headerClass' => 'bg-warning text-dark', 'content' => \$_content, 'attributes' => ['class' => 'border-warning shadow-sm']]);\n";
        $c .= "} else {\n";
        $c .= "    \$create = \$model->insert(\$d);\n";
        $c .= "    \$_icon_col = BS5::col(['attributes' => ['class' => 'text-center py-3'], 'content' => BS5::icon(['icon' => 'circle-check', 'style' => 'duotone', 'size' => '2xl'])]);\n";
        $c .= "    \$_msg_col  = BS5::col(['attributes' => ['class' => 'text-center pb-2'], 'content' => sprintf(lang(\"{$ucf_module}_{$ucf_component}.create-success-message\"), \$d['{$fields[0]}'])]);\n";
        $c .= "    \$_btn_col  = BS5::col(['attributes' => ['class' => 'text-center pb-3'], 'content' => BS5::button(['content' => lang('App.Continue'), 'variant' => 'success', 'size' => 'sm', 'attributes' => ['href' => \$l['back']]])]);\n";
        $c .= "    \$_content  = BS5::row(['attributes' => ['class' => 'justify-content-center'], 'content' => \$_icon_col.\$_msg_col.\$_btn_col]);\n";
        $c .= "    \$c = BS5::card(['headerTitle' => lang(\"{$ucf_module}_{$ucf_component}.create-success-title\"), 'headerClass' => 'bg-success text-white', 'content' => \$_content, 'attributes' => ['class' => 'border-success shadow-sm']]);\n";
        $c .= "}\n";
        $c .= "echo(\$c);\n";
        $c .= "\$model->invalidateSearchCache();\n";
        $c .= "?>\n";
        return $c;
    }

    protected function buildValidator(
        string $ucf_module,
        string $ucf_component,
        ?string $ucf_options,
        array $fields,
        bool $is_triple
    ): string {
        $namespaced = $is_triple
            ? "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\{$ucf_options}\\Create\\validator.php"
            : "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\Create\\validator.php";

        $c  = "<?php\n";
        $c .= $this->copyright($namespaced);
        $c .= "use Higgs\\Frontend\\Bootstrap\\v5_3_3\\Bootstrap as BS5;\n";
        $c .= "\$f = service(\"forms\",array(\"lang\" => \"{$ucf_module}_{$ucf_component}.\"));\n";
        $c .= "//[Request]-----------------------------------------------------------------------------\n";
        foreach ($fields as $field) {
            $c .= "//\$f->set_ValidationRule(\"{$field}\",\"trim|required\");\n";
        }
        $c .= "//[Validation]-----------------------------------------------------------------------------\n";
        $c .= "if (\$f->run_Validation()) {\n";
        $c .= "    \$c = view(\$component.'\\\\processor', \$parent->get_Array());\n";
        $c .= "} else {\n";
        $c .= "    \$_icon_col  = BS5::col(['attributes' => ['class' => 'text-center py-3'], 'content' => BS5::icon(['icon' => 'triangle-exclamation', 'style' => 'duotone', 'size' => '2xl'])]);\n";
        $c .= "    \$_msg_col   = BS5::col(['attributes' => ['class' => 'text-center pb-2'], 'content' => lang('App.validator-errors-message')]);\n";
        $c .= "    \$_errors_col= BS5::col(['attributes' => ['class' => 'pb-2'], 'content' => \$f->validation->listErrors()]);\n";
        $c .= "    \$_content   = BS5::row(['attributes' => ['class' => 'justify-content-center'], 'content' => \$_icon_col.\$_msg_col.\$_errors_col]);\n";
        $c .= "    \$c = BS5::card(['headerTitle' => lang('App.validator-errors-title'), 'headerClass' => 'bg-danger text-white', 'content' => \$_content, 'attributes' => ['class' => 'border-danger shadow-sm']]);\n";
        $c .= "    \$c .= view(\$component.'\\\\form', \$parent->get_Array());\n";
        $c .= "}\n";
        $c .= "//[Build]-----------------------------------------------------------------------------\n";
        $c .= "echo(\$c);\n";
        $c .= "?>\n";
        return $c;
    }

    protected function buildBreadcrumb(
        string $ucf_module,
        string $ucf_component,
        ?string $ucf_options,
        string $slc_module,
        string $slc_component,
        bool $is_triple
    ): string {
        $namespaced = $is_triple
            ? "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\{$ucf_options}\\Create\\breadcrumb.php"
            : "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\Create\\breadcrumb.php";
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

    protected function buildDeny(
        string $ucf_module,
        string $ucf_component,
        ?string $ucf_options,
        string $slc_module,
        string $slc_component,
        bool $is_triple
    ): string {
        $namespaced = $is_triple
            ? "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\{$ucf_options}\\Create\\deny.php"
            : "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\Create\\deny.php";
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

    protected function copyright(string $path): string
    {
        $date = date('Y-m-d H:i:s');
        $c  = "\n/**\n* █ -------------------------------------------------\n";
        $c .= "* █ ░FRAMEWORK                    {$date}\n";
        $c .= "* █ [{$path}]\n";
        $c .= "* █ Copyright 2023 - CloudEngine S.A.S., Inc.\n";
        $c .= "* █ @Author Jose Alexis Correa Valencia <jalexiscv@gmail.com>\n";
        $c .= "* █ @Version 1.5.1 @since PHP 8,PHP 9\n";
        $c .= "**/\n";
        return $c;
    }
}
