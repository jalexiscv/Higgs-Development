<?php

/** @var array $params - parameters passed from the parent view */
/** @var string $primary */
/** @var array $fields */
$code = '';
$code .= "\t\t /**\n";
$code .= "\t\t * Obtiene una lista de registros con un rango especificado y opcionalmente filtrados por un término de búsqueda.\n";
$code .= "\t\t * con opciones de filtrado y paginación.\n";
$code .= "\t\t * @param int \$limit El número máximo de registros a obtener por página.\n";
$code .= "\t\t * @param int \$offset El número de registros a omitir antes de comenzar a seleccionar.\n";
$code .= "\t\t * @param string \$search (Opcional) El término de búsqueda para filtrar resultados.\n";
$code .= "\t\t * @return array|false\t\tUn arreglo de registros combinados o false si no se encuentran registros.\n";
$code .= "\t\t */\n";
$code .= "\t\tpublic function getList(int \$limit, int \$offset, string \$search = \"\"): array|false\n";
$code .= "\t\t{\n";
$code .= "\t\t\t\t\$result = \$this\n";
$code .= "\t\t\t\t\t\t->groupStart()\n";
$code .= "\t\t\t\t\t\t->like(\"{$primary}\", \"%{\$search}%\")\n";
foreach ($fields as $field) {
    if ($field != $primary && $field != 'created_at' && $field != 'updated_at' && $field != 'deleted_at') {
        $code .= "\t\t\t\t\t\t->orLike(\"{$field}\", \"%{\$search}%\")\n";
    }
}
$code .= "\t\t\t\t\t\t->groupEnd()\n";
$code .= "\t\t\t\t\t\t->orderBy(\"created_at\", \"DESC\")\n";
$code .= "\t\t\t\t\t\t->findAll(\$limit, \$offset);\n";
$code .= "\t\t\t\tif (is_array(\$result)) {\n";
$code .= "\t\t\t\t\t\treturn \$result;\n";
$code .= "\t\t\t\t} else {\n";
$code .= "\t\t\t\t\t\treturn false;\n";
$code .= "\t\t\t\t}\n";
$code .= "\t\t}\n";
echo($code);
