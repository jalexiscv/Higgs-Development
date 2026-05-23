<?php

$code = '';
$code .= "/**\n";
$code .= "* Implementa la lógica para actualizar la caché después de insertar o actualizar\n";
$code .= "* Por ejemplo, puedes utilizar la misma lógica que en exec_beforeFind\n";
$code .= "* y guardar los datos en la caché usando cache().\n";
$code .= "* @param array \$data\n";
$code .= "* @return void\n";
$code .= "*/\n";
$code .= "\n";
$code .= "protected function _exec_UpdateCache(array \$data)\n";
$code .= "{\n";
$code .= "\$id = \$data['id'] ?? null;\n";
$code .= "if (\$id !== null) {\n";
$code .= "\$updatedData = \$this->find(\$id);\n";
$code .= "if (\$updatedData) {\n";
$code .= "cache()->save(\$this->get_CacheKey(\$id), \$updatedData, \$this->cache_time);\n";
$code .= "}\n";
$code .= "}\n";
$code .= "}\n";
echo($code);
