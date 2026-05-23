<?php

/** @var string $primary */
/** @var array $fields */
$code = "\t\t  /**\n";
$code .= "\t\t * Obtiene el número total de registros que coinciden con un término de búsqueda.\n";
$code .= "\t\t * @param string \$search (Opcional) El término de búsqueda para filtrar resultados.\n";
$code .= "\t\t * @return int Devuelve el número total de registros que coinciden con el término de búsqueda.\n";
$code .= "\t\t */\n";
$code .= "\t\t public function get_CountAllResults(string \$search = \"\"): int\n";
$code .= "\t\t {\n";
$code .= "\t\t\t\t \$result = \$this\n";
$code .= "\t\t\t\t\t\t ->groupStart()\n";

foreach ($fields as $field) {
    if ($field != $primary && $field != 'created_at' && $field != 'updated_at' && $field != 'deleted_at') {
        $code .= "\t\t\t\t\t\t ->orLike(\"{$field}\", \"%{\$search}%\")\n";
    }
}

$code .= "\t\t\t\t\t\t ->groupEnd()\n";
$code .= "\t\t\t\t\t\t ->countAllResults();\n";
$code .= "\t\t\t\t return (\$result);\n";
$code .= "\t\t }\n";
echo($code);
