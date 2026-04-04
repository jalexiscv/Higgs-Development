<?php

namespace App\Modules\Development\Commands;

use Config\Database;
use Higgs\CLI\BaseCommand;
use Higgs\CLI\CLI;

/**
 * Spark command: development:generate-model
 *
 * Generates the Models/_{ClassName}.php model file for a given table.
 * Usage: php spark development:generate-model <table>
 * Example: php spark development:generate-model access_events
 */
class GenerateModel extends BaseCommand
{
    protected $group       = 'Development';
    protected $name        = 'development:generate-model';
    protected $description = 'Generates the Models/_{ClassName}.php model file for a given table.';
    protected $usage       = 'development:generate-model <table>';
    protected $arguments   = [
        'table' => 'Table name (e.g. access_events or module_component_options)',
    ];

    public function run(array $params): int
    {
        $table = array_shift($params);
        if (empty($table)) {
            CLI::error('Usage: php spark development:generate-model <table>');
            return EXIT_ERROR;
        }

        $eid           = explode('_', $table);
        $ucf_module    = ucfirst($eid[0]);
        $ucf_component = ucfirst($eid[1]);
        $ucf_options   = isset($eid[2]) ? ucfirst($eid[2]) : '';
        $slc_module    = strtolower($eid[0]);
        $slc_component = strtolower($eid[1]);

        $classname = count($eid) === 3
            ? "{$ucf_module}_{$ucf_component}_{$ucf_options}"
            : "{$ucf_module}_{$ucf_component}";

        // Get DB field names and data
        $db     = Database::connect('default');
        $fields = $db->getFieldNames($table);
        if (empty($fields)) {
            CLI::error("Table '{$table}' not found or has no fields.");
            return EXIT_ERROR;
        }
        $primary = $fields[0];
        $ucf_primary = ucfirst(strtolower($primary));

        $modelsDir = APPPATH . "Modules/{$ucf_module}/Models";
        $pathfile  = "{$modelsDir}/_{$classname}.php";
        $relative  = "Modules/{$ucf_module}/Models/_{$classname}.php";

        if (!is_dir($modelsDir)) {
            mkdir($modelsDir, 0755, true);
            CLI::write("Created directory: {$modelsDir}", 'green');
        }

        $content = $this->buildModel($classname, $table, $ucf_module, $slc_module, $slc_component, $primary, $ucf_primary, $fields, $relative);

        file_put_contents($pathfile, $content);
        CLI::write("  Created: {$pathfile}", 'yellow');
        CLI::write('Model file generated successfully.', 'green');
        return EXIT_SUCCESS;
    }

    // -------------------------------------------------------------------------

    private function buildModel(string $classname, string $table, string $ucf_module, string $slc_module, string $slc_component, string $primary, string $ucf_primary, array $fields, string $relative): string
    {
        $c  = "<?php\n";
        $c .= "\n";
        $c .= "namespace App\\Modules\\{$ucf_module}\\Models;\n";
        $c .= "\n";
        $c .= "use App\\Models\\CachedModel5;\n";
        $c .= "use Config\\Database;\n";
        $c .= "\n";

        // PHPDoc block
        $c .= "/**\n";
        $c .= "* {$classname} Model\n";
        $c .= "*\n";
        $c .= "* Modelo generado automáticamente usando CachedModel5.\n";
        $c .= "*\n";
        $c .= "* @Copilot Siempre que en el código se cree una variable llamada \${$slc_component}, esta deberá ser igualada a  model('App\\Modules\\{$ucf_module}\\Models\\{$classname}');\n";
        $c .= "* @Instruction \$m{$slc_component} = model('App\\Modules\\{$ucf_module}\\Models\\{$classname}');\n";
        $c .= "* @method insert(array \$data, bool \$returnID = true, bool \$protect = true): int|string\n";
        $c .= "* @method update(string \$id, array \$data): bool\n";
        $c .= "* @method delete(string \$id, bool \$purge = false): bool\n";
        $c .= "* @method getCached(mixed \$id): array|object|null\n";
        $c .= "* @method getCachedFirst(array \$conditions, string \$orderBy = 'created_at DESC'): array|object|null\n";
        $c .= "* @method getCachedSearch(array \$conditions = [], int \$limit = 10, int \$offset = 0, string \$orderBy = '', int \$page = 1): array\n";
        $c .= "* @method getCachedCustomQuery(callable \$queryBuilder, string \$cacheKeySuffix, ?int \$ttl = null): array\n";
        $c .= "* @method withTags(array \$tags): static\n";
        $c .= "* @method invalidateTag(string \$tag): int\n";
        $c .= "* @method invalidateSearchCache(): void\n";
        $c .= "* @method getSelectDataCached(?string \$labelColumn = 'name', int \$ttl = 3600): array\n";
        $c .= "* @package App\\Modules\\{$ucf_module}\\Models\n";
        $c .= "* @version 2.1.0\n";
        $c .= "* @see CachedModel5\n";
        $c .= "*/\n";

        // Class definition
        $c .= "class {$classname} extends CachedModel5\n";
        $c .= "\t {\n";
        $c .= "\t\t protected \$table = \"{$table}\";\n";
        $c .= "\t\t protected \$primaryKey = \"{$primary}\";\n";
        $c .= "\t\t protected \$returnType = \"array\";\n";
        $c .= "\t\t protected \$useSoftDeletes = true;\n";
        $c .= "\t\t protected \$allowedFields = [\n";
        foreach ($fields as $field) {
            $c .= "\t\t\t \"{$field}\",\n";
        }
        $c .= "\t\t ];\n";
        $c .= "\t\t protected \$useTimestamps = true;\n";
        $c .= "\t\t protected \$createdField = \"created_at\";\n";
        $c .= "\t\t protected \$updatedField = \"updated_at\";\n";
        $c .= "\t\t protected \$deletedField = \"deleted_at\";\n";
        $c .= "\t\t protected \$validationRules = [];\n";
        $c .= "\t\t protected \$validationMessages = [];\n";
        $c .= "\t\t protected \$skipValidation = false;\n";
        $c .= "\t\t protected \$DBGroup = \"default\";\n";
        $c .= "\t\t protected \$version = '1.0.1';\n";
        $c .= "\t\t protected \$cache_time = 60;\n";
        $c .= "\t\t protected array \$cacheTags = ['table:{$table}'];\n";

        // __construct method
        $c .= "\t\t /**\n";
        $c .= "\t\t * Inicializa el modelo y la regeneración de la tabla asociada si esta no existe\n";
        $c .= "\t\t **/\n";
        $c .= "\t\t public function __construct()\n";
        $c .= "\t\t {\n";
        $c .= "\t\t\t parent::__construct();\n";
        $c .= "\t\t\t \$this->exec_Migrate();\n";
        $c .= "\t\t }\n";

        // exec_Migrate method
        $c .= "\t\t /**\n";
        $c .= "\t\t * Ejecuta las migraciones para el módulo actual.\n";
        $c .= "\t\t * @return void\n";
        $c .= "\t\t */\n";
        $c .= "\t\tprivate function exec_Migrate():void\n";
        $c .= "\t\t{\n";
        $c .= "\t\t\t\t\$migrations = \Config\Services::migrations();\n";
        $c .= "\t\t\t\ttry {\n";
        $c .= "\t\t\t\t\t\t\$migrations->setNamespace('App\\Modules\\{$ucf_module}');// Set the namespace for the current module\n";
        $c .= "\t\t\t\t\t\t\$migrations->latest();// Run the migrations for the current module\n";
        $c .= "\t\t\t\t\t\t\$all = \$migrations->findMigrations();// Find all migrations for the current module\n";
        $c .= "\t\t\t\t}catch(Throwable \$e){\n";
        $c .= "\t\t\t\t\t\techo(\$e->getMessage());\n";
        $c .= "\t\t\t\t}\n";
        $c .= "\t\t}\n";

        // getAuthority method
        $c .= "/**\n";
        $c .= "\t\t * Retorna falso o verdadero si el usuario activo ne la sesión es el\n";
        $c .= "\t\t * autor del regsitro que se desea acceder, editar o eliminar.\n";
        $c .= "\t\t * @param type \$id codigo primario del registro a consultar\n";
        $c .= "\t\t * @param type \$author codigo del usuario del cual se pretende establecer la autoria\n";
        $c .= "\t\t * @return boolean falso o verdadero segun sea el caso\n";
        $c .= "\t\t */\n";
        $c .= "\t\tpublic function getAuthority(\$id, \$author): bool\n";
        $c .= "\t\t{\n";
        $c .= "\t\t\t\t\$row = parent::getCachedFirst([\$this->primaryKey => \$id]);\n";
        $c .= "\t\t\t\tif (isset(\$row[\"author\"]) && \$row[\"author\"] == \$author) {\n";
        $c .= "\t\t\t\t\t\treturn (true);\n";
        $c .= "\t\t\t\t} else {\n";
        $c .= "\t\t\t\t\t\treturn (false);\n";
        $c .= "\t\t\t\t}\n";
        $c .= "\t\t}\n";

        // getList method
        $c .= "\t\t /**\n";
        $c .= "\t\t * Obtiene una lista de registros con un rango especificado y opcionalmente filtrados por un término de búsqueda.\n";
        $c .= "\t\t * @param int \$limit El número máximo de registros a obtener por página.\n";
        $c .= "\t\t * @param int \$offset El número de registros a omitir antes de comenzar a seleccionar.\n";
        $c .= "\t\t * @param string \$search (Opcional) El término de búsqueda para filtrar resultados.\n";
        $c .= "\t\t * @return array|false\n";
        $c .= "\t\t */\n";
        $c .= "\t\tpublic function getList(int \$limit, int \$offset, string \$search = \"\"): array|false\n";
        $c .= "\t\t{\n";
        $c .= "\t\t\t\t\$result = \$this\n";
        $c .= "\t\t\t\t\t\t->groupStart()\n";
        $c .= "\t\t\t\t\t\t->like(\"{$primary}\", \"%{\$search}%\")\n";
        $skip = [$primary, 'created_at', 'updated_at', 'deleted_at'];
        foreach ($fields as $field) {
            if (!in_array($field, $skip)) {
                $c .= "\t\t\t\t\t\t->orLike(\"{$field}\", \"%{\$search}%\")\n";
            }
        }
        $c .= "\t\t\t\t\t\t->groupEnd()\n";
        $c .= "\t\t\t\t\t\t->orderBy(\"created_at\", \"DESC\")\n";
        $c .= "\t\t\t\t\t\t->findAll(\$limit, \$offset);\n";
        $c .= "\t\t\t\tif (is_array(\$result)) {\n";
        $c .= "\t\t\t\t\t\treturn \$result;\n";
        $c .= "\t\t\t\t} else {\n";
        $c .= "\t\t\t\t\t\treturn false;\n";
        $c .= "\t\t\t\t}\n";
        $c .= "\t\t}\n";

        // getSelectData method
        $c .= "\t\t /**\n";
        $c .= "\t\t * Retorna el listado de elementos existentes de forma que se pueda cargar un field tipo select.\n";
        $c .= "\t\t * Ejemplo de uso:\n";
        $c .= "\t\t * \$model = model(\"App\\Modules\\{$ucf_module}\\Models\\{$classname}\");\n";
        $c .= "\t\t * \$list = \$model->getSelectData();\n";
        $c .= "\t\t */\n";
        $c .= "\t\t public function getSelectData()\n";
        $c .= "\t\t\t {\n";
        $c .= "\t\t\t\t \$result = \$this->select(\"`{\$this->primaryKey}` AS `value`,`name` AS `label`\")->findAll();\n";
        $c .= "\t\t\t\t if (is_array(\$result)) {\n";
        $c .= "\t\t\t\t\t return (\$result);\n";
        $c .= "\t\t\t\t } else {\n";
        $c .= "\t\t\t\t\t return (false);\n";
        $c .= "\t\t\t\t }\n";
        $c .= "\t\t }\n";

        // get{PrimaryKey} method
        $c .= "\t\t /**\n";
        $c .= "\t\t * Obtiene la clave de caché para un identificador dado.\n";
        $c .= "\t\t * @param \${$primary}\n";
        $c .= "\t\t * @return array|false\n";
        $c .= "\t\t */\n";
        $c .= "\t\t public function get{$ucf_primary}(\${$primary}):false|array\n";
        $c .= "\t\t{\n";
        $c .= "\t\t\t\t\$result = parent::getCached(\${$primary});\n";
        $c .= "\t\t\t\tif (is_array(\$result)) {\n";
        $c .= "\t\t\t\t\t\treturn (\$result);\n";
        $c .= "\t\t\t\t} else {\n";
        $c .= "\t\t\t\t\t\treturn (false);\n";
        $c .= "\t\t\t\t}\n";
        $c .= "\t\t}\n";

        $c .= "}\n";
        $c .= "\n";
        $c .= "?>\n";
        return $c;
    }
}
