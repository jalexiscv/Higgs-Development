<?php

/** @var array $params - parameters passed from the parent view */

$code = '';
$code .= "/**\n";
$code .= "\t\t * Método is_CacheValid\n";
$code .= "\t\t * Este método verifica si los datos recuperados de la caché son válidos.\n";
$code .= "\t\t * @param mixed \$cache - Los datos recuperados de la caché.\n";
$code .= "\t\t * @return bool - Devuelve true si los datos de la caché son válidos, false en caso contrario.\n";
$code .= "\t\t */\n";
$code .= "\t\tprivate function is_CacheValid(mixed \$cache): bool\n";
$code .= "\t\t{\n";
$code .= "\t\t\t\treturn is_array(\$cache) && array_key_exists('retrieved', \$cache) && \$cache['retrieved'] === true;\n";
$code .= "\t\t}\n";
echo($code);
