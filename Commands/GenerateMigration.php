<?php

namespace App\Modules\Development\Commands;

use App\Libraries\Migrations;
use Higgs\CLI\BaseCommand;
use Higgs\CLI\CLI;

/**
 * Spark command: development:generate-migration
 *
 * Generates a Database/Migrations file for a given table using the Migrations library.
 * Usage: php spark development:generate-migration <table>
 * Example: php spark development:generate-migration access_events
 */
class GenerateMigration extends BaseCommand
{
    protected $group       = 'Development';
    protected $name        = 'development:generate-migration';
    protected $description = 'Generates a Database/Migrations file for a given table.';
    protected $usage       = 'development:generate-migration <table>';
    protected $arguments   = [
        'table' => 'Table name (e.g. access_events or module_component_options)',
    ];

    public function run(array $params): int
    {
        $table = array_shift($params);
        if (empty($table)) {
            CLI::error('Usage: php spark development:generate-migration <table>');
            return EXIT_ERROR;
        }

        $eid        = explode('_', $table);
        $ucf_module = ucfirst($eid[0]);

        $migrationsDir = APPPATH . "Modules/{$ucf_module}/Database/Migrations";
        if (!is_dir($migrationsDir)) {
            mkdir($migrationsDir, 0755, true);
            CLI::write("Created directory: {$migrationsDir}", 'green');
        }

        // Build migration filename with timestamp
        $timestamp = date('Y-m-d_His');
        $ucf_table = implode('_', array_map('ucfirst', $eid));
        $filename  = "{$timestamp}_{$ucf_table}.php";
        $filepath  = "{$migrationsDir}/{$filename}";
        $relative  = "Modules/{$ucf_module}/Database/Migrations/{$filename}";

        // Use the Migrations library to generate the migration code
        try {
            $migrations     = new Migrations('frontend', $table);
            $code_migration = $migrations->generate($table);
        } catch (\Throwable $e) {
            CLI::error('Migrations library error: ' . $e->getMessage());
            return EXIT_ERROR;
        }

        $content  = "<?php\n";
        $content .= $this->copyright($relative);
        $content .= $code_migration;
        $content .= "?>\n";

        file_put_contents($filepath, $content);
        CLI::write("  Created: {$filepath}", 'yellow');
        CLI::write('Migration file generated successfully.', 'green');
        return EXIT_SUCCESS;
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
