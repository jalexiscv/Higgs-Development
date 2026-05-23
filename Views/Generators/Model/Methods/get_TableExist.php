<?php

/** @var array $params - parameters passed from the parent view */

$code = '';
$code .= "/**\n";
$code .= "\t\t * Este método verifica si la tabla especificada existe en la base de datos utilizando la función tableExists()\n";
$code .= "\t\t * del objeto db de Higgs. Además, utiliza la caché para almacenar el resultado de la verificación para mejorar\n";
$code .= "\t\t * el rendimiento y evitar la sobrecarga de la base de datos. La clave de caché se crea utilizando el método\n";
$code .= "\t\t * get_CacheKey(), que se supone que retorna una clave única para la tabla especificada. El tiempo de duración de\n";
$code .= "\t\t * la caché se establece en el atributo \$cache_time.\n";
$code .= "\t\t * @return bool Devuelve true si la tabla existe, false en caso contrario.\n";
$code .= "\t\t */\n";
$code .= "\t\tprivate function get_TableExist(): bool\n";
$code .= "\t\t{\n";
$code .= "\t\t\t\t\$cache_key = \$this->get_CacheKey(\$this->table);\n";
$code .= "\t\t\t\tif (!\$data = cache(\$cache_key)) {\n";
$code .= "\t\t\t\t\t\t\$data = \$this->db->tableExists(\$this->table);\n";
$code .= "\t\t\t\t\t\tcache()->save(\$cache_key, \$data, \$this->cache_time);\n";
$code .= "\t\t\t\t}\n";
$code .= "\t\t\t\treturn \$data;\n";
$code .= "\t\t}\n";
echo($code);
