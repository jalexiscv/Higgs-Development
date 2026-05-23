<?php

include __DIR__ . '/_shared.php';

$namespacedFile = $g->namespaced;

$code = '';
$code .= "<?php\n";
$code .= "\n";
$code .= "namespace App\\Modules\\{$g->ucf_module}\\Models;\n";
$code .= "\n";
$code .= "use App\\Models\\CachedModel5;\n";
$code .= "use Config\\Database;\n";
$code .= "\n";

$code .= "/**\n";
$code .= "* {$g->classname} Model\n";
$code .= "*\n";
$code .= "* Modelo generado automaticamente usando CachedModel5.\n";
$code .= "*\n";
$code .= "* @Copilot Siempre que en el codigo se cree una variable llamada \$m{$g->slc_component}, esta debera ser igualada a  model('App\\Modules\\{$g->ucf_module}\\Models\\{$g->classname}');\n";
$code .= "* @Instruction \$m{$g->slc_component} = model('App\\Modules\\{$g->ucf_module}\\Models\\{$g->classname}');\n";
$code .= "* @method insert(array \$data, bool \$returnID = true, bool \$protect = true): int|string\n";
$code .= "* @method update(string \$id, array \$data): bool\n";
$code .= "* @method delete(string \$id, bool \$purge = false): bool\n";
$code .= "* @method getCached(mixed \$id): array|object|null\n";
$code .= "* @method getCachedFirst(array \$conditions, string \$orderBy = 'created_at DESC'): array|object|null\n";
$code .= "* @method getCachedSearch(array \$conditions = [], int \$limit = 10, int \$offset = 0, string \$orderBy = '', int \$page = 1): array\n";
$code .= "* @method getCachedCustomQuery(callable \$queryBuilder, string \$cacheKeySuffix, ?int \$ttl = null): array\n";
$code .= "* @method withTags(array \$tags): static\n";
$code .= "* @method invalidateTag(string \$tag): int\n";
$code .= "* @method invalidateSearchCache(): void\n";
$code .= "* @method getSelectDataCached(?string \$labelColumn = 'name', int \$ttl = 3600): array\n";
$code .= "* @package App\\Modules\\{$g->ucf_module}\\Models\n";
$code .= "* @version 2.1.0\n";
$code .= "* @see CachedModel5\n";
$code .= "*/\n";

$code .= "class {$g->classname} extends CachedModel5\n";
$code .= "\t {\n";
$code .= "\t\t protected \$table = \"{$oid}\";\n";
$code .= "\t\t protected \$primaryKey = \"{$g->fields[0]}\";\n";
$code .= "\t\t protected \$returnType = 'array';\n";
$code .= "\t\t protected \$useSoftDeletes = true;\n";
$code .= "\t\t protected \$allowedFields = [\n";
foreach ($g->fields as $field) {
    $code .= "\t\t\t '{$field}',\n";
}
$code .= "\t\t ];\n";
$code .= "\t\t protected \$useTimestamps = true;\n";
$code .= "\t\t protected \$createdField = 'created_at';\n";
$code .= "\t\t protected \$updatedField = 'updated_at';\n";
$code .= "\t\t protected \$deletedField = 'deleted_at';\n";
$code .= "\t\t protected \$validationRules = [];\n";
$code .= "\t\t protected \$validationMessages = [];\n";
$code .= "\t\t protected \$skipValidation = false;\n";
$code .= "\t\t protected \$DBGroup = 'authentication';//default\n";
$code .= "\t\t protected \$version = '1.0.1';\n";
$code .= "\t\t protected \$cache_time = 60;\n";
$code .= "\t\t protected array \$cacheTags = ['table:{$oid}'];\n";
$code .= view($component . '\Methods\__construct', []);
$code .= view($component . '\Methods\exec_Migrate', ['module' => $g->ucf_module]);
$code .= view($component . '\Methods\getAuthority', []);
$code .= view($component . '\Methods\getList', ['primary' => $g->fields[0], 'fields' => $g->fields]);
$code .= view($component . '\Methods\getSelectData', []);
$code .= view($component . '\Methods\get_Row', ['primary' => $g->fields[0]]);
$code .= "}\n";
$code .= "\n";
$code .= "?>\n";

echo($code);
