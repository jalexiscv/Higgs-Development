<?php

$code = '';
$code .= "/**\n";
$code .= "* Implementa la lógica para eliminar la caché después de eliminar\n";
$code .= "* Por ejemplo, puedes utilizar la misma lógica que en exec_beforeFind\n";
$code .= "* para invalidar la caché.\n";
$code .= "* @param array \$data\n";
$code .= "* @return void\n";
$code .= "*/\n";
$code .= "protected function _exec_DeleteCache(array \$data)\n";
$code .= "{\n";
$code .= "\$id = \$data['id'] ?? null;\n";
$code .= "if (\$id !== null) {\n";
$code .= "\$deletedData = \$this->find(\$id);\n";
$code .= "if (\$deletedData) {\n";
$code .= "cache()->delete(\$this->get_CacheKey(\$id));\n";
$code .= "}\n";
$code .= "}\n";
$code .= "}\n";
echo($code);
