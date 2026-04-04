<?php

namespace App\Modules\Development\Commands;

use Config\Database;
use Higgs\CLI\BaseCommand;
use Higgs\CLI\CLI;

/**
 * Spark command: development:generate-lang
 *
 * Generates the Language/es/{Module}_{Component}.php file for a given table.
 * Usage: php spark development:generate-lang <table>
 * Example: php spark development:generate-lang access_events
 */
class GenerateLang extends BaseCommand
{
    protected $group       = 'Development';
    protected $name        = 'development:generate-lang';
    protected $description = 'Generates the Language/es/{Module}_{Component}.php file for a given table.';
    protected $usage       = 'development:generate-lang <table>';
    protected $arguments   = [
        'table' => 'Table name (e.g. access_events or module_component_options)',
    ];

    public function run(array $params): int
    {
        $table = array_shift($params);
        if (empty($table)) {
            CLI::error('Usage: php spark development:generate-lang <table>');
            return EXIT_ERROR;
        }

        $eid           = explode('_', $table);
        $ucf_module    = ucfirst($eid[0]);
        $ucf_component = ucfirst($eid[1]);
        $ucf_options   = isset($eid[2]) ? ucfirst($eid[2]) : '';
        $slc_module    = strtolower($eid[0]);
        $slc_component = strtolower($eid[1]);

        // Derive classname for the lang file name
        if (count($eid) === 3) {
            $classname = "{$ucf_module}_{$ucf_component}_{$ucf_options}";
        } else {
            $classname = "{$ucf_module}_{$ucf_component}";
        }

        // Get DB field names
        $db     = Database::connect('default');
        $fields = $db->getFieldNames($table);
        if (empty($fields)) {
            CLI::error("Table '{$table}' not found or has no fields.");
            return EXIT_ERROR;
        }

        $langDir  = APPPATH . "Modules/{$ucf_module}/Language/es";
        $langFile = "{$langDir}/{$classname}.php";

        if (!is_dir($langDir)) {
            mkdir($langDir, 0755, true);
            CLI::write("Created directory: {$langDir}", 'green');
        }

        $namespaced = "App\\Modules\\{$ucf_module}\\Language\\es\\{$classname}.php";
        $content = $this->buildLang($namespaced, $ucf_component, $classname, $fields);

        file_put_contents($langFile, $content);
        CLI::write("  Created: {$langFile}", 'yellow');
        CLI::write('Language file generated successfully.', 'green');
        return EXIT_SUCCESS;
    }

    // -------------------------------------------------------------------------

    private function buildLang(string $namespaced, string $ucf_component, string $classname, array $fields): string
    {
        $c  = "<?php\n";
        $c .= $this->copyright($namespaced);
        $c .= "return [\n";
        $c .= "\t// - {$ucf_component} fields \n";
        foreach ($fields as $field) {
            $c .= "\t\"label_{$field}\"=>\"{$field}\",\n";
        }
        foreach ($fields as $field) {
            $c .= "\t\"placeholder_{$field}\"=>\"{$field}\",\n";
        }
        foreach ($fields as $field) {
            $c .= "\t\"help_{$field}\"=>\"{$field}\",\n";
        }
        $c .= "\t// - {$ucf_component} creator \n";
        $c .= "\t\"create-denied-title\"=>\"Acceso denegado!\",\n";
        $c .= "\t\"create-denied-message\"=>\"Su rol en la plataforma no posee los privilegios requeridos para crear nuevos #plural, por favor póngase en contacto con el administrador del sistema o en su efecto contacte al personal de soporte técnico para que estos le sean asignados, según sea el caso. Para continuar presioné la opción correspondiente en la parte inferior de este mensaje.\",\n";
        $c .= "\t\"create-title\"=>\"Crear nuevo #singular\",\n";
        $c .= "\t\"create-errors-title\"=>\"¡Advertencia!\",\n";
        $c .= "\t\"create-errors-message\"=>\"Los datos proporcionados son incorrectos o están incompletos, por favor verifique eh inténtelo nuevamente.\",\n";
        $c .= "\t\"create-duplicate-title\"=>\"¡#singular existente!\",\n";
        $c .= "\t\"create-duplicate-message\"=>\"Este #singular ya se había registrado previamente, presioné continuar en la parte inferior de este mensaje para retornar al listado general de #plural.\",\n";
        $c .= "\t\"create-success-title\"=>\"¡#singular registrada exitosamente!\",\n";
        $c .= "\t\"create-success-message\"=>\"La #singular se registró exitosamente, para retornar al listado general de #plural presioné continuar en la parte inferior de este mensaje.\",\n";
        $c .= "\t// - {$ucf_component} viewer \n";
        $c .= "\t\"view-denied-title\"=>\"¡Acceso denegado!\",\n";
        $c .= "\t\"view-denied-message\"=>\"Los roles asignados a su perfil, no le conceden los privilegios necesarios para visualizar #plural en esta plataforma. Contacte al departamento de soporte técnico para información adicional, o la asignación de los permisos necesarios si es el caso. Para continuar seleccione la opción correspondiente en la parte inferior de este mensaje.\",\n";
        $c .= "\t\"view-title\"=>\"Vista\",\n";
        $c .= "\t\"view-errors-title\"=>\"¡Advertencia!\",\n";
        $c .= "\t\"view-errors-message\"=>\"Los datos proporcionados son incorrectos o están incompletos, por favor verifique eh inténtelo nuevamente.\",\n";
        $c .= "\t\"view-noexist-title\"=>\"¡No existe!\",\n";
        $c .= "\t\"view-noexist-message\"=>\"\",\n";
        $c .= "\t\"view-success-title\"=>\"\",\n";
        $c .= "\t\"view-success-message\"=>\"\",\n";
        $c .= "\t// - {$ucf_component} editor \n";
        $c .= "\t\"edit-denied-title\"=>\"¡Advertencia!\",\n";
        $c .= "\t\"edit-denied-message\"=>\"Los roles asignados a su perfil, no le conceden los privilegios necesarios para actualizar #plural en esta plataforma. Contacte al departamento de soporte técnico para información adicional, o la asignación de los permisos necesarios si es el caso. Para continuar seleccione la opción correspondiente en la parte inferior de este mensaje.\",\n";
        $c .= "\t\"edit-title\"=>\"¡Actualizar #singular!\",\n";
        $c .= "\t\"edit-errors-title\"=>\"¡Advertencia!\",\n";
        $c .= "\t\"edit-errors-message\"=>\"Los datos proporcionados son incorrectos o están incompletos, por favor verifique eh inténtelo nuevamente.\",\n";
        $c .= "\t\"edit-noexist-title\"=>\"¡No existe!\",\n";
        $c .= "\t\"edit-noexist-message\"=>\"El elemento que actualizar no existe o se elimino previamente, para retornar al listado general de #plural presioné continuar en la parte inferior de este mensaje. \",\n";
        $c .= "\t\"edit-success-title\"=>\"¡#singular actualizada!\",\n";
        $c .= "\t\"edit-success-message\"=>\"Los datos de #singular se <b>actualizaron exitosamente</b>, para retornar al listado general de #plural presioné el botón continuar en la parte inferior del presente mensaje.\",\n";
        $c .= "\t// - {$ucf_component} deleter \n";
        $c .= "\t\"delete-denied-title\"=>\"¡Advertencia!\",\n";
        $c .= "\t\"delete-denied-message\"=>\"Los roles asignados a su perfil, no le conceden los privilegios necesarios para eliminar #plural en esta plataforma. Contacte al departamento de soporte técnico para información adicional, o la asignación de los permisos necesarios si es el caso. Para continuar seleccione la opción correspondiente en la parte inferior de este mensaje.\",\n";
        $c .= "\t\"delete-title\"=>\"¡Eliminar #singular!\",\n";
        $c .= "\t\"delete-message\"=>\"Para confirmar la eliminación del #singular <b>%s</b>, presioné eliminar, para retornar al listado general de #plural presioné cancelar.\",\n";
        $c .= "\t\"delete-errors-title\"=>\"¡Advertencia!\",\n";
        $c .= "\t\"delete-errors-message\"=>\"Los datos proporcionados son incorrectos o están incompletos, por favor verifique eh inténtelo nuevamente.\",\n";
        $c .= "\t\"delete-noexist-title\"=>\"¡No existe!\",\n";
        $c .= "\t\"delete-noexist-message\"=>\"El elemento que intenta eliminar no existe o se elimino previamente, para retornar al listado general de #plural presioné continuar en la parte inferior de este mensaje.\",\n";
        $c .= "\t\"delete-success-title\"=>\"¡#Singular eliminad@ exitosamente!\",\n";
        $c .= "\t\"delete-success-message\"=>\"La #singular se elimino exitosamente, para retornar al listado de general de #plural presioné el botón continuar en la parte inferior de este mensaje.\",\n";
        $c .= "\t// - {$ucf_component} list \n";
        $c .= "\t\"list-denied-title\"=>\"¡Advertencia!\",\n";
        $c .= "\t\"list-denied-message\"=>\"Los roles asignados a su perfil, no le conceden los privilegios necesarios para acceder al listado general de #plural en esta plataforma. Contacte al departamento de soporte técnico para información adicional, o la asignación de los permisos necesarios si es el caso. Para continuar seleccione la opción correspondiente en la parte inferior de este mensaje.\",\n";
        $c .= "\t\"list-title\"=>\"Listado de #plural\",\n";
        $c .= "\t\"list-description\"=>\"Descripción de #plural\",\n";
        $c .= "];\n";
        $c .= "\n";
        $c .= "?>\n";
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
