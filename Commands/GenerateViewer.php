<?php

/**
 * █ ---------------------------------------------------------------------------------------------------------------------
 * █ ░FRAMEWORK
 * █ ░█▀▀█ █▀▀█ █▀▀▄ █▀▀ ░█─░█ ─▀─ █▀▀▀ █▀▀▀ █▀▀ [App\Modules\Development\Commands\GenerateViewer]
 * █ ░█─── █──█ █──█ █▀▀ ░█▀▀█ ▀█▀ █─▀█ █─▀█ ▀▀█ Copyright 2023 - CloudEngine S.A.S., Inc. <admin@cgine.com>
 * █ ░█▄▄█ ▀▀▀▀ ▀▀▀─ ▀▀▀ ░█─░█ ▀▀▀ ▀▀▀▀ ▀▀▀▀ ▀▀▀
 * █ ---------------------------------------------------------------------------------------------------------------------
 * █ Genera las 6 vistas del tipo "View/" a partir del nombre de una tabla de base de datos,
 * █ replicando el comportamiento del generador web Development/Generators/Viewer.
 * █ ---------------------------------------------------------------------------------------------------------------------
 * █ Uso:
 * █   php spark development:generate-viewer <tabla> [--database=nombre_bd] [--force]
 * █
 * █ Ejemplos:
 * █   php spark development:generate-viewer access_events
 * █   php spark development:generate-viewer sie_pensums_prerequisites --database=higgs_sie
 * █   php spark development:generate-viewer access_attendances --force
 * █ ---------------------------------------------------------------------------------------------------------------------
 **/

namespace App\Modules\Development\Commands;

use Config\Database;
use Higgs\CLI\BaseCommand;
use Higgs\CLI\CLI;

class GenerateViewer extends BaseCommand
{
    protected $group       = 'Development';
    protected $name        = 'development:generate-viewer';
    protected $description = 'Genera las vistas View/ (index, form, processor, validator, breadcrumb, deny) desde una tabla de BD';
    protected $usage       = 'development:generate-viewer <tabla> [--database=nombre_bd] [--force]';
    protected $arguments   = [
        'tabla' => 'Nombre de la tabla BD (ej: access_events, sie_pensums_prerequisites)',
    ];
    protected $options     = [
        '--database' => 'Base de datos a conectar (default: la configurada en .env)',
        '--force'    => 'Sobreescribir archivos existentes sin confirmar',
    ];

    // ─────────────────────────────────────────────────────────────────────────
    // Entry point
    // ─────────────────────────────────────────────────────────────────────────

    public function run(array $params): int
    {
        CLI::write('============================================', 'green');
        CLI::write('  Development: Generate Viewer', 'yellow');
        CLI::write('============================================', 'green');
        CLI::newLine();

        // ── parse arguments & options ────────────────────────────────────────
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
            CLI::write('Uso: php spark development:generate-viewer access_events', 'yellow');
            return EXIT_ERROR;
        }

        // ── parse table name into module/component[/options] ─────────────────
        $eid           = explode('_', $table);
        $ucf_module    = ucfirst(strtolower($eid[0]));
        $ucf_component = ucfirst(strtolower($eid[1]));
        $ucf_options   = isset($eid[2]) ? ucfirst(strtolower($eid[2])) : null;
        $slc_module    = strtolower($eid[0]);
        $slc_component = strtolower($eid[1]);
        $slc_options   = isset($eid[2]) ? strtolower($eid[2]) : null;
        $is_triple     = count($eid) === 3;

        if ($is_triple) {
            $pathfiles = APPPATH . "Modules/{$ucf_module}/Views/{$ucf_component}/{$ucf_options}/_View";
            $singular  = "{$slc_module}-{$slc_component}-{$slc_options}-view";
            $plural    = "{$slc_module}-{$slc_component}-{$slc_options}-view-all";
        } else {
            $pathfiles = APPPATH . "Modules/{$ucf_module}/Views/{$ucf_component}/_View";
            $singular  = "{$slc_module}-{$slc_component}-view";
            $plural    = "{$slc_module}-{$slc_component}-view-all";
        }

        // singular component name for method calls  (Events → Event, Attendances → Attendance)
        $sucf_component = service('strings')->removePluralEnding($ucf_component);

        CLI::write("Tabla      : {$table}", 'cyan');
        CLI::write("Destino    : {$pathfiles}", 'cyan');
        CLI::write("Singular   : {$singular}", 'white');
        CLI::write("Plural     : {$plural}", 'white');
        CLI::newLine();

        // ── connect to DB ────────────────────────────────────────────────────
        if (!empty($database)) {
            try {
                $cfg = config('Database');
                $cfg->default['database'] = $database;
                Database::connect('default', false);
                CLI::write("Base de datos: {$database}", 'cyan');
            } catch (\Exception $e) {
                CLI::error('Error al conectar: ' . $e->getMessage());
                return EXIT_ERROR;
            }
        }

        $db = Database::connect('default');

        if (!$db->tableExists($table)) {
            CLI::error("La tabla '{$table}' no existe en la base de datos.");
            return EXIT_ERROR;
        }

        $fields = $db->getFieldNames($table);
        CLI::write('Campos: ' . implode(', ', $fields), 'white');
        CLI::newLine();

        // ── ensure destination directory exists ──────────────────────────────
        if (!is_dir($pathfiles)) {
            mkdir($pathfiles, 0755, true);
            CLI::write("Directorio creado: {$pathfiles}", 'green');
        }

        // ── generate & write files ───────────────────────────────────────────
        $files = [
            'index.php'      => $this->buildIndex($ucf_module, $ucf_component, $ucf_options, $slc_module, $slc_component, $slc_options, $singular, $plural, $is_triple),
            'form.php'       => $this->buildForm($ucf_module, $ucf_component, $ucf_options, $slc_module, $slc_component, $slc_options, $sucf_component, $fields, $is_triple),
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
        CLI::write('============================================', 'green');
        CLI::write('  ¡Generación completada!', 'yellow');
        CLI::write('============================================', 'green');

        return EXIT_SUCCESS;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // File builders  (each replicates its matching coders/*.php)
    // ─────────────────────────────────────────────────────────────────────────

    protected function buildIndex(
        string $ucf_module, string $ucf_component, ?string $ucf_options,
        string $slc_module, string $slc_component, ?string $slc_options,
        string $singular, string $plural, bool $is_triple
    ): string {
        $namespaced = $is_triple
            ? "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\{$ucf_options}\\View\\index.php"
            : "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\View\\index.php";

        $c  = "<?php\n";
        $c .= $this->copyright($namespaced);
        $c .= COMMENT_HR_VARS;
        $c .= COMMENT_MODULECONTROLER_VARS;
        $c .= "\$data = \$parent->get_Array();\n";
        $c .= "\$data['model']=model(\"App\\Modules\\{$ucf_module}\\Models\\{$ucf_module}_{$ucf_component}\");\n";
        $c .= "\$data['permissions'] = array('singular' => '{$singular}', \"plural\" =>'{$plural}');\n";
        $c .= "\$singular = \$authentication->has_Permission(\$data['permissions']['singular']);\n";
        $c .= "\$plural = \$authentication->has_Permission(\$data['permissions']['plural']);\n";
        $c .= "\$author= \$data['model']->getAuthority(\$oid,safe_get_user());\n";
        $c .= "\$authority= (\$singular&&\$author)?true:false;\n";
        $c .= "\$submited = \$request->getPost(\"submited\");\n";
        $c .= "\$breadcrumb = \$component . '\breadcrumb';\n";
        $c .= "\$validator = \$component . '\\validator';\n";
        $c .= "\$form = \$component . '\\form';\n";
        $c .= "\$deny = \$component . '\\deny';\n";
        $c .= COMMENT_HR_BUILD;
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

    protected function buildForm(
        string $ucf_module, string $ucf_component, ?string $ucf_options,
        string $slc_module, string $slc_component, ?string $slc_options,
        string $sucf_component, array $fields, bool $is_triple
    ): string {
        $namespaced = $is_triple
            ? "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\{$ucf_options}\\View\\form.php"
            : "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\View\\form.php";

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
        $c .= "\$f = service(\"forms\",array(\"lang\" => \"{$ucf_module}_{$ucf_component}.\"));\n";
        $c .= COMMENT_HR_MODELS;
        $c .= COMMENT_HR_VARS;
        $c .= "\$row= \$model->get{$sucf_component}(\$oid);\n";
        foreach ($fields as $field) {
            $c .= "\$r[\"{$field}\"] =\$row[\"{$field}\"];\n";
        }
        $c .= "\$back=\$f->get_Value(\"back\",\$server->get_Referer());\n";
        $c .= COMMENT_HR_FIELDS;
        foreach ($fields as $field) {
            if ($field === 'author') {
                $c .= "\$f->add_HiddenField(\"author\",\$r[\"author\"]);\n";
            } else {
                $c .= "\$f->fields[\"{$field}\"] = \$f->get_FieldView(\"{$field}\", array(\"value\" => \$r[\"{$field}\"],\"proportion\"=>\"col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12\"));\n";
            }
        }
        $c .= "\$f->fields[\"cancel\"]=\$f->get_Cancel(\"cancel\", array(\"href\" =>\$back,\"text\" =>lang(\"App.Cancel\"),\"type\"=>\"secondary\",\"proportion\" =>\"col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 padding-right\"));\n";
        $c .= "\$f->fields[\"edit\"] =\$f->get_Button(\"edit\", array(\"href\" =>\"/{$slc_module}/{$slc_component}/edit/\".\$oid,\"text\" =>lang(\"App.Edit\"),\"class\"=>\"btn btn-secondary\",\"proportion\" =>\"col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 padding-right\"));\n";
        $c .= COMMENT_HR_GROUPS;

        // chunk visible fields in groups of 3
        $skipped = ['author', 'created_at', 'updated_at', 'deleted_at'];
        $visible  = array_values(array_filter($fields, fn($f) => !in_array($f, $skipped)));
        $chunks   = array_chunk($visible, 3);
        $grupo    = 0;
        foreach ($chunks as $chunk) {
            $grupo++;
            $fields_code = implode('.', array_map(fn($f) => "\$f->fields[\"{$f}\"]", $chunk));
            $c .= "\$f->groups[\"g{$grupo}\"]=\$f->get_Group(array(\"legend\"=>\"\",\"fields\"=>({$fields_code})));\n";
        }

        $c .= COMMENT_HR_BUTTONS;
        $c .= "\$f->groups[\"gy\"] =\$f->get_GroupSeparator();\n";
        $c .= "\$f->groups[\"gz\"] = \$f->get_Buttons(array(\"fields\"=>\$f->fields[\"edit\"].\$f->fields[\"cancel\"]));\n";
        $c .= COMMENT_HR_BUILD;
        $c .= "\$card = BS5::card([\n";
        $c .= "    'headerTitle'   => lang(\"{$ucf_module}_{$ucf_component}.view-title\"),\n";
        $c .= "    'headerButtons' => [BS5::button(['content' => BS5::icon(['icon' => 'arrow-left', 'style' => 'duotone']), 'variant' => 'secondary', 'size' => 'sm', 'attributes' => ['href' => \$back]])],\n";
        $c .= "    'content'       => \$f,\n";
        $c .= "]);\n";
        $c .= "echo(\$card);\n";
        $c .= "?>\n";
        return $c;
    }

    protected function buildProcessor(
        string $ucf_module, string $ucf_component, ?string $ucf_options,
        string $slc_module, string $slc_component, ?string $slc_options,
        array $fields, bool $is_triple
    ): string {
        $namespaced = $is_triple
            ? "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\{$ucf_options}\\View\\processor.php"
            : "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\View\\processor.php";

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
        $c .= COMMENT_HR_VARS;
        $c .= COMMENT_MODULECONTROLER_VARS;
        $c .= "\$f = service(\"forms\",array(\"lang\" => \"{$ucf_module}_{$ucf_component}.\"));\n";
        $c .= "\$model = model(\"App\\Modules\\{$ucf_module}\\Models\\{$ucf_module}_{$ucf_component}\");\n";
        $c .= "\$d = array(\n";
        foreach ($fields as $field) {
            if (!in_array($field, ['created_at', 'updated_at', 'deleted_at'])) {
                if ($field === 'author') {
                    $c .= "    \"{$field}\" => safe_get_user(),\n";
                } else {
                    $c .= "    \"{$field}\" => \$f->get_Value(\"{$field}\"),\n";
                }
            }
        }
        $c .= ");\n";
        $c .= COMMENT_HR_BUILD;
        $c .= "\$row = \$model->find(\$d[\"{$fields[0]}\"]);\n";
        $c .= "if (isset(\$row[\"{$fields[0]}\"])) {\n";
        $c .= "    \$_icon_col = BS5::col(['attributes' => ['class' => 'text-center py-3'], 'content' => BS5::icon(['icon' => 'circle-check', 'style' => 'duotone', 'size' => '2xl'])]);\n";
        $c .= "    \$_msg_col  = BS5::col(['attributes' => ['class' => 'text-center pb-2'], 'content' => lang(\"{$ucf_module}_{$ucf_component}.view-success-message\")]);\n";
        $c .= "    \$_btn_col  = BS5::col(['attributes' => ['class' => 'text-center pb-3'], 'content' => BS5::button(['content' => lang('App.Continue'), 'variant' => 'success', 'size' => 'sm', 'attributes' => ['href' => \$l['back']]])]);\n";
        $c .= "    \$_content  = BS5::row(['attributes' => ['class' => 'justify-content-center'], 'content' => \$_icon_col.\$_msg_col.\$_btn_col]);\n";
        $c .= "    \$c = BS5::card([\n";
        $c .= "        'headerTitle' => lang(\"{$ucf_module}_{$ucf_component}.view-success-title\"),\n";
        $c .= "        'headerClass' => 'bg-success text-white',\n";
        $c .= "        'content'     => \$_content,\n";
        $c .= "        'attributes'  => ['class' => 'border-success shadow-sm'],\n";
        $c .= "    ]);\n";
        $c .= "} else {\n";
        $c .= "    \$_icon_col = BS5::col(['attributes' => ['class' => 'text-center py-3'], 'content' => BS5::icon(['icon' => 'triangle-exclamation', 'style' => 'duotone', 'size' => '2xl'])]);\n";
        $c .= "    \$_msg_col  = BS5::col(['attributes' => ['class' => 'text-center pb-2'], 'content' => lang(\"{$ucf_module}_{$ucf_component}.view-noexist-message\")]);\n";
        $c .= "    \$_btn_col  = BS5::col(['attributes' => ['class' => 'text-center pb-3'], 'content' => BS5::button(['content' => lang('App.Continue'), 'variant' => 'warning', 'size' => 'sm', 'attributes' => ['href' => \$l['back']]])]);\n";
        $c .= "    \$_content  = BS5::row(['attributes' => ['class' => 'justify-content-center'], 'content' => \$_icon_col.\$_msg_col.\$_btn_col]);\n";
        $c .= "    \$c = BS5::card([\n";
        $c .= "        'headerTitle' => lang(\"{$ucf_module}_{$ucf_component}.view-noexist-title\"),\n";
        $c .= "        'headerClass' => 'bg-warning text-dark',\n";
        $c .= "        'content'     => \$_content,\n";
        $c .= "        'attributes'  => ['class' => 'border-warning shadow-sm'],\n";
        $c .= "    ]);\n";
        $c .= "}\n";
        $c .= "echo(\$c);\n";
        $c .= "?>\n";
        return $c;
    }

    protected function buildValidator(
        string $ucf_module, string $ucf_component, ?string $ucf_options,
        array $fields, bool $is_triple
    ): string {
        $namespaced = $is_triple
            ? "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\{$ucf_options}\\View\\validator.php"
            : "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\View\\validator.php";

        $c  = "<?php\n";
        $c .= $this->copyright($namespaced);
        $c .= "\$bootstrap = service('bootstrap');\n";
        $c .= "\$f = service(\"forms\",array(\"lang\" => \"{$ucf_module}_{$ucf_component}.\"));\n";
        $c .= "//[Request]-----------------------------------------------------------------------------\n";
        foreach ($fields as $field) {
            $c .= "//\$f->set_ValidationRule(\"{$field}\",\"trim|required\");\n";
        }
        $c .= "//[Validation]-----------------------------------------------------------------------------\n";
        $c .= "if (\$f->run_Validation()) {\n";
        $c .= "   \$c=view(\$component.'\\processor',\$parent->get_Array());\n";
        $c .= "}else {\n";
        $c .= "\$c =\$bootstrap->get_Card('access-denied', array(\n";
        $c .= "    'class'=>'card-danger',\n";
        $c .= "    'icon'=>'fa-duotone fa-triangle-exclamation',\n";
        $c .= "    'text-class' => 'text-center',\n";
        $c .= "    'text' => lang('App.validator-errors-message'),\n";
        $c .= "    'errors' => \$f->validation->listErrors(),\n";
        $c .= "    'footer-class'=>'text-center',\n";
        $c .= "    'voice'=>\"app/validator-errors-message.mp3\",\n";
        $c .= "));\n";
        $c .= "   \$c.=view(\$component.'\\form',\$parent->get_Array());\n";
        $c .= "}\n";
        $c .= "//[Build]-----------------------------------------------------------------------------\n";
        $c .= "echo(\$c);\n";
        $c .= "?>\n";
        return $c;
    }

    protected function buildBreadcrumb(
        string $ucf_module, string $ucf_component, ?string $ucf_options,
        string $slc_module, string $slc_component, bool $is_triple
    ): string {
        $namespaced = $is_triple
            ? "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\{$ucf_options}\\View\\breadcrumb.php"
            : "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\View\\breadcrumb.php";

        $c  = "<?php\n";
        $c .= $this->copyright($namespaced);
        $c .= "use Higgs\\Frontend\\Bootstrap\\v5_3_3\\Bootstrap as BS5;\n";
        $c .= "echo BS5::breadcrumb(['items' => [\n";
        $c .= "    ['label' => '{$slc_module}', 'href' => '/{$slc_module}/'],\n";
        $c .= "    ['label' => lang('App.{$slc_component}'), 'href' => '/{$slc_module}/{$slc_component}/home/'.lpk(), 'active' => true],\n";
        $c .= "]]);\n";
        $c .= "?>";
        return $c;
    }

    protected function buildDeny(
        string $ucf_module, string $ucf_component, ?string $ucf_options,
        string $slc_module, string $slc_component, bool $is_triple
    ): string {
        $namespaced = $is_triple
            ? "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\{$ucf_options}\\View\\deny.php"
            : "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\View\\deny.php";

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
        $c .= "?>";
        return $c;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Shared helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Genera el bloque de copyright idéntico a get_development_code_copyright()
     */
    protected function copyright(string $path): string
    {
        $author = "Jose Alexis Correa Valencia <jalexiscv@gmail.com>";
        $date   = date("Y-m-d H:i:s");
        $c  = "\n/**";
        $c .= "\n* █ ---------------------------------------------------------------------------------------------------------------------";
        $c .= "\n* █ ░FRAMEWORK                                  {$date}";
        $c .= "\n* █ ░█▀▀█ █▀▀█ █▀▀▄ █▀▀ ░█─░█ ─▀─ █▀▀▀ █▀▀▀ █▀▀ [{$path}]";
        $c .= "\n* █ ░█─── █──█ █──█ █▀▀ ░█▀▀█ ▀█▀ █─▀█ █─▀█ ▀▀█ Copyright 2023 - CloudEngine S.A.S., Inc. <admin@cgine.com>";
        $c .= "\n* █ ░█▄▄█ ▀▀▀▀ ▀▀▀─ ▀▀▀ ░█─░█ ▀▀▀ ▀▀▀▀ ▀▀▀▀ ▀▀▀ Para obtener información completa sobre derechos de autor y licencia,";
        $c .= "\n* █                                             consulte la LICENCIA archivo que se distribuyó con este código fuente.";
        $c .= "\n* █ ---------------------------------------------------------------------------------------------------------------------";
        $c .= "\n* █ EL SOFTWARE SE PROPORCIONA -TAL CUAL-, SIN GARANTÍA DE NINGÚN TIPO, EXPRESA O";
        $c .= "\n* █ IMPLÍCITA, INCLUYENDO PERO NO LIMITADO A LAS GARANTÍAS DE COMERCIABILIDAD,";
        $c .= "\n* █ APTITUD PARA UN PROPÓSITO PARTICULAR Y NO INFRACCIÓN. EN NINGÚN CASO SERÁ";
        $c .= "\n* █ LOS AUTORES O TITULARES DE LOS DERECHOS DE AUTOR SERÁN RESPONSABLES DE CUALQUIER";
        $c .= "\n* █ RECLAMO, DAÑOS U OTROS RESPONSABILIDAD, YA SEA EN UNA ACCIÓN DE CONTRATO,";
        $c .= "\n* █ AGRAVIO O DE OTRO MODO, QUE SURJA DESDE, FUERA O EN RELACIÓN CON EL SOFTWARE";
        $c .= "\n* █ O EL USO U OTROS NEGOCIACIONES EN EL SOFTWARE.";
        $c .= "\n* █ ---------------------------------------------------------------------------------------------------------------------";
        $c .= "\n* █ @Author {$author}";
        $c .= "\n* █ @link https://www.higgs.com.co";
        $c .= "\n* █ @Version 1.5.1 @since PHP 8,PHP 9";
        $c .= "\n* █ ---------------------------------------------------------------------------------------------------------------------";
        $c .= "\n**/\n";
        return $c;
    }
}
