<?php

/**
 * █ Genera las vistas Edit/ (index, form, processor, validator, breadcrumb, deny)
 * █ Equivalente al generador web Development/Generators/Editor
 * █ Uso: php spark development:generate-editor <tabla> [--database=nombre_bd] [--force]
 **/

namespace App\Modules\Development\Commands;

use Config\Database;
use Higgs\CLI\BaseCommand;
use Higgs\CLI\CLI;

class GenerateEditor extends BaseCommand
{
    protected $group       = 'Development';
    protected $name        = 'development:generate-editor';
    protected $description = 'Genera las vistas Edit/ (index, form, processor, validator, breadcrumb, deny) desde una tabla de BD';
    protected $usage       = 'development:generate-editor <tabla> [--database=nombre_bd] [--force]';
    protected $arguments   = ['tabla' => 'Nombre de la tabla BD'];
    protected $options     = [
        '--database' => 'Base de datos a conectar',
        '--force'    => 'Sobreescribir sin confirmar',
    ];

    public function run(array $params): int
    {
        CLI::write('============================================', 'green');
        CLI::write('  Development: Generate Editor', 'yellow');
        CLI::write('============================================', 'green');
        CLI::newLine();

        $table    = $params[0] ?? null;
        $database = null;
        $force    = false;

        foreach ($_SERVER['argv'] ?? [] as $arg) {
            if (!is_string($arg)) continue;
            if (strpos($arg, '--database=') === 0) $database = trim(substr($arg, 11), '"\'');
            elseif ($arg === '--force') $force = true;
        }

        if (empty($table)) {
            CLI::error('Error: debes especificar el nombre de la tabla.');
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
        $sucf_component = service('strings')->removePluralEnding($ucf_component);

        if ($is_triple) {
            $pathfiles = APPPATH . "Modules/{$ucf_module}/Views/{$ucf_component}/{$ucf_options}/_Edit";
            $singular  = "{$slc_module}-{$slc_component}-{$slc_options}-edit";
            $plural    = "{$slc_module}-{$slc_component}-{$slc_options}-edit-all";
        } else {
            $pathfiles = APPPATH . "Modules/{$ucf_module}/Views/{$ucf_component}/_Edit";
            $singular  = "{$slc_module}-{$slc_component}-edit";
            $plural    = "{$slc_module}-{$slc_component}-edit-all";
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
        if (!$db->tableExists($table)) { CLI::error("La tabla '{$table}' no existe."); return EXIT_ERROR; }
        $fields = $db->getFieldNames($table);
        CLI::write('Campos: ' . implode(', ', $fields), 'white');
        CLI::newLine();

        if (!is_dir($pathfiles)) mkdir($pathfiles, 0755, true);

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
                if ($answer !== 'y') { CLI::write("  → Saltando {$filename}", 'yellow'); continue; }
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
        string $ucf_module, string $ucf_component, ?string $ucf_options,
        string $slc_module, string $slc_component, ?string $slc_options,
        string $singular, string $plural, bool $is_triple
    ): string {
        $namespaced = $is_triple
            ? "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\{$ucf_options}\\Edit\\index.php"
            : "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\Edit\\index.php";

        $c  = "<?php\n";
        $c .= $this->copyright($namespaced);
        $c .= COMMENT_HR_VARS;
        $c .= COMMENT_MODULECONTROLER_VARS;
        $c .= "\$data = \$parent->get_Array();\n";
        $c .= "\$data['model'] = model(\"App\\Modules\\{$ucf_module}\\Models\\{$ucf_module}_{$ucf_component}\");\n";
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
        $c .= "\t\t if (!empty(\$submited)) {\n";
        $c .= "\t\t\t\t\t\t \$json = array(\n";
        $c .= "\t\t\t\t\t\t 'breadcrumb' => view(\$breadcrumb, \$data),\n";
        $c .= "\t\t\t\t\t\t\t 'main' => view(\$validator, \$data),\n";
        $c .= "\t\t\t\t\t\t\t 'right' => \"\",\n";
        $c .= "\t\t\t\t\t\t\t 'main_template' =>'c8c4',//'c12',\n";
        $c .= "\t\t\t\t\t\t );\n";
        $c .= "\t\t\t\t} else {\n";
        $c .= "\t\t\t\t\t\t\$json = array(\n";
        $c .= "\t\t\t\t\t\t\t 'breadcrumb' => view(\$breadcrumb, \$data),\n";
        $c .= "\t\t\t\t\t\t\t 'main' => view(\$form, \$data),\n";
        $c .= "\t\t\t\t\t\t\t 'right' => \"\",\n";
        $c .= "\t\t\t\t\t\t\t 'main_template' =>'c8c4',//'c12',\n";
        $c .= "\t\t\t\t\t\t );\n";
        $c .= "\t\t\t\t}\n";
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
        string $ucf_module, string $ucf_component, ?string $ucf_options,
        string $slc_module, string $slc_component, ?string $slc_options,
        string $sucf_component, array $fields, bool $is_triple
    ): string {
        $namespaced = $is_triple
            ? "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\{$ucf_options}\\Edit\\form.php"
            : "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\Edit\\form.php";

        $c  = "<?php\n";
        $c .= $this->copyright($namespaced);
        $c .= "use Higgs\\Frontend\\Bootstrap\\v5_3_3\\Bootstrap as BS5;\n";
        $c .= "// \$authentication, \$bootstrap, \$dates, \$strings, \$request, \$server, \$parent\n";
        $c .= "\$server = service(\"server\");\n";
        $c .= "\$f = service(\"forms\",array(\"lang\" => \"{$ucf_module}_{$ucf_component}.\"));\n";
        $c .= COMMENT_HR_MODELS;
        $c .= "//\$model = model(\"App\\Modules\\{$ucf_module}\\Models\\{$ucf_module}_{$ucf_component}\");\n";
        $c .= COMMENT_HR_VARS;
        $c .= COMMENT_MODULECONTROLER_VARS;
        $c .= "\$row= \$model->get{$sucf_component}(\$oid);\n";
        foreach ($fields as $field) {
            if ($field === 'author') $c .= "\$r[\"{$field}\"] = \$f->get_Value(\"{$field}\",safe_get_user());\n";
            elseif ($field === 'date') $c .= "\$r[\"{$field}\"] = \$f->get_Value(\"{$field}\",service(\"dates\")::get_Date());\n";
            elseif ($field === 'time') $c .= "\$r[\"{$field}\"] = \$f->get_Value(\"{$field}\",service(\"dates\")::get_Time());\n";
            else $c .= "\$r[\"{$field}\"] = \$f->get_Value(\"{$field}\",\$row[\"{$field}\"]);\n";
        }
        $c .= "\$back=\$f->get_Value(\"back\",\$server->get_Referer());\n";
        $c .= COMMENT_HR_FIELDS;
        $c .= "\$f->add_HiddenField(\"back\",\$back);\n";
        foreach ($fields as $field) {
            if ($field === 'author') $c .= "\$f->add_HiddenField(\"author\",\$r[\"author\"]);\n";
            else $c .= "\$f->fields[\"{$field}\"] = \$f->get_FieldText(\"{$field}\", array(\"value\" => \$r[\"{$field}\"],\"proportion\"=>\"col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12\"));\n";
        }
        $c .= "\$f->fields[\"cancel\"]=\$f->get_Cancel(\"cancel\", array(\"href\" =>\$back,\"text\" =>lang(\"App.Cancel\"),\"type\"=>\"secondary\",\"proportion\" =>\"col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 padding-right\"));\n";
        $c .= "\$f->fields[\"submit\"] =\$f->get_Submit(\"submit\", array(\"value\" =>lang(\"App.Edit\"),\"proportion\" =>\"col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 padding-left\"));\n";
        $c .= COMMENT_HR_GROUPS;
        $skipped = ['author', 'created_at', 'updated_at', 'deleted_at'];
        $visible  = array_values(array_filter($fields, fn($f) => !in_array($f, $skipped)));
        $chunks   = array_chunk($visible, 3);
        $grupo = 0;
        foreach ($chunks as $chunk) {
            $grupo++;
            $fields_code = implode('.', array_map(fn($f) => "\$f->fields[\"{$f}\"]", $chunk));
            $c .= "\$f->groups[\"g{$grupo}\"]=\$f->get_Group(array(\"legend\"=>\"\",\"fields\"=>({$fields_code})));\n";
        }
        $c .= COMMENT_HR_BUTTONS;
        $c .= "\$f->groups[\"gy\"] =\$f->get_GroupSeparator();\n";
        $c .= "\$f->groups[\"gz\"] = \$f->get_Buttons(array(\"fields\"=>\$f->fields[\"submit\"].\$f->fields[\"cancel\"]));\n";
        $c .= COMMENT_HR_BUILD;
        $c .= "\$card = BS5::card([\n";
        $c .= "    'headerTitle'   => lang(\"{$ucf_module}_{$ucf_component}.edit-title\"),\n";
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
            ? "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\{$ucf_options}\\Edit\\processor.php"
            : "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\Edit\\processor.php";

        $c  = "<?php\n";
        $c .= $this->copyright($namespaced);
        $c .= "use Higgs\\Frontend\\Bootstrap\\v5_3_3\\Bootstrap as BS5;\n";
        $c .= "// \$authentication, \$bootstrap, \$dates, \$strings, \$request, \$server, \$parent\n";
        $c .= "//[Models]-----------------------------------------------------------------------------\n";
        $c .= "\$f = service(\"forms\",array(\"lang\" => \"{$ucf_module}_{$ucf_component}.\"));\n";
        $c .= "\$model = model(\"App\\Modules\\{$ucf_module}\\Models\\{$ucf_module}_{$ucf_component}\");\n";
        $c .= "\$d = array(\n";
        foreach ($fields as $field) {
            if (!in_array($field, ['created_at', 'updated_at', 'deleted_at'])) {
                if ($field === 'author') $c .= "    \"{$field}\" => safe_get_user(),\n";
                else $c .= "    \"{$field}\" => \$f->get_Value(\"{$field}\"),\n";
            }
        }
        $c .= ");\n";
        $c .= "//[Elements]-----------------------------------------------------------------------------\n";
        $c .= "\$row = \$model->find(\$d[\"{$fields[0]}\"]);\n";
        $c .= "\$l[\"back\"]=\$f->get_Value(\"back\");\n";
        $c .= "\$l[\"edit\"]=\"/{$slc_module}/{$slc_component}/edit/{\$d[\"{$fields[0]}\"]}\";\n";
        $c .= COMMENT_HR_BUILD;
        $c .= "if (is_array(\$row)) {\n";
        $c .= "    \$edit = \$model->update(\$d['{$fields[0]}'],\$d);\n";
        $c .= "    \$_icon_col = BS5::col(['attributes' => ['class' => 'text-center py-3'], 'content' => BS5::icon(['icon' => 'circle-check', 'style' => 'duotone', 'size' => '2xl'])]);\n";
        $c .= "    \$_msg_col  = BS5::col(['attributes' => ['class' => 'text-center pb-2'], 'content' => lang(\"{$ucf_module}_{$ucf_component}.edit-success-message\")]);\n";
        $c .= "    \$_btn_col  = BS5::col(['attributes' => ['class' => 'text-center pb-3'], 'content' => BS5::button(['content' => lang('App.Continue'), 'variant' => 'success', 'size' => 'sm', 'attributes' => ['href' => \$l['back']]])]);\n";
        $c .= "    \$_content  = BS5::row(['attributes' => ['class' => 'justify-content-center'], 'content' => \$_icon_col.\$_msg_col.\$_btn_col]);\n";
        $c .= "    \$c = BS5::card(['headerTitle' => lang(\"{$ucf_module}_{$ucf_component}.edit-success-title\"), 'headerClass' => 'bg-success text-white', 'content' => \$_content, 'attributes' => ['class' => 'border-success shadow-sm']]);\n";
        $c .= "} else {\n";
        $c .= "    \$_icon_col = BS5::col(['attributes' => ['class' => 'text-center py-3'], 'content' => BS5::icon(['icon' => 'triangle-exclamation', 'style' => 'duotone', 'size' => '2xl'])]);\n";
        $c .= "    \$_msg_col  = BS5::col(['attributes' => ['class' => 'text-center pb-2'], 'content' => sprintf(lang(\"{$ucf_module}_{$ucf_component}.edit-noexist-message\"), \$d['{$fields[0]}'])]);\n";
        $c .= "    \$_btn_col  = BS5::col(['attributes' => ['class' => 'text-center pb-3'], 'content' => BS5::button(['content' => lang('App.Continue'), 'variant' => 'warning', 'size' => 'sm', 'attributes' => ['href' => \$l['back']]])]);\n";
        $c .= "    \$_content  = BS5::row(['attributes' => ['class' => 'justify-content-center'], 'content' => \$_icon_col.\$_msg_col.\$_btn_col]);\n";
        $c .= "    \$c = BS5::card(['headerTitle' => lang(\"{$ucf_module}_{$ucf_component}.edit-noexist-title\"), 'headerClass' => 'bg-warning text-dark', 'content' => \$_content, 'attributes' => ['class' => 'border-warning shadow-sm']]);\n";
        $c .= "}\n";
        $c .= "echo(\$c);\n";
        $c .= "\$model->invalidateSearchCache();\n";
        $c .= "?>\n";
        return $c;
    }

    protected function buildValidator(string $ucf_module, string $ucf_component, ?string $ucf_options, array $fields, bool $is_triple): string
    {
        $namespaced = $is_triple
            ? "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\{$ucf_options}\\Edit\\validator.php"
            : "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\Edit\\validator.php";
        $c  = "<?php\n";
        $c .= $this->copyright($namespaced);
        $c .= "\$bootstrap = service('bootstrap');\n";
        $c .= "\$f = service(\"forms\",array(\"lang\" => \"{$ucf_module}_{$ucf_component}.\"));\n";
        $c .= "//[Request]-----------------------------------------------------------------------------\n";
        foreach ($fields as $field) $c .= "//\$f->set_ValidationRule(\"{$field}\",\"trim|required\");\n";
        $c .= COMMENT_HR_BUILD;
        $c .= "if (\$f->run_Validation()) {\n";
        $c .= "   \$c=view(\$component.'\\processor',\$parent->get_Array());\n";
        $c .= "}else {\n";
        $c .= "\$c =\$bootstrap->get_Card('validator-error', array(\n";
        $c .= "    'class'=>'card-danger','icon'=>'fa-duotone fa-triangle-exclamation',\n";
        $c .= "    'text-class' => 'text-center','text' => lang(\"App.validator-errors-message\"),\n";
        $c .= "    'errors' => \$f->validation->listErrors(),'footer-class'=>'text-center',\n";
        $c .= "    'voice'=>\"app/validator-errors-message.mp3\",\n";
        $c .= "));\n";
        $c .= "   \$c.=view(\$component.'\\form',\$parent->get_Array());\n";
        $c .= "}\n";
        $c .= "echo(\$c);\n";
        $c .= "?>\n";
        return $c;
    }

    protected function buildBreadcrumb(string $ucf_module, string $ucf_component, ?string $ucf_options, string $slc_module, string $slc_component, bool $is_triple): string
    {
        $namespaced = $is_triple
            ? "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\{$ucf_options}\\Edit\\breadcrumb.php"
            : "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\Edit\\breadcrumb.php";
        $c  = "<?php\n" . $this->copyright($namespaced);
        $c .= "use Higgs\\Frontend\\Bootstrap\\v5_3_3\\Bootstrap as BS5;\n";
        $c .= "echo BS5::breadcrumb(['items' => [\n";
        $c .= "    ['label' => '{$slc_module}', 'href' => '/{$slc_module}/'],\n";
        $c .= "    ['label' => lang('App.{$slc_component}'), 'href' => '/{$slc_module}/{$slc_component}/home/'.lpk(), 'active' => true],\n";
        $c .= "]]);\n?>";
        return $c;
    }

    protected function buildDeny(string $ucf_module, string $ucf_component, ?string $ucf_options, string $slc_module, string $slc_component, bool $is_triple): string
    {
        $namespaced = $is_triple
            ? "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\{$ucf_options}\\Edit\\deny.php"
            : "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\Edit\\deny.php";
        $c  = "<?php\n" . $this->copyright($namespaced);
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
        $c .= "}\necho(\$card);\n?>";
        return $c;
    }

    protected function copyright(string $path): string
    {
        $date = date("Y-m-d H:i:s");
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
