<?php

/** @var array $params - parameters passed from the parent view */

$code = "/**\n";
$code .= "* Obtiene la clave de caché para un identificador dado.\n";
$code .= "* @param mixed \$id Identificador único para el objeto en caché.\n";
$code .= "* @return string Clave de caché generada para el identificador.\n";
$code .= "**/\n";
$code .= "protected function get_CacheKey(\$id)\n";
$code .= "{\n";
$code .= "\$id = is_array(\$id) ? implode(\"\", \$id) : \$id;\n";
$code .= "\$node = APPNODE;\n";
$code .= "\$table = \$this->table;\n";
$code .= "\$class = urlencode(get_class(\$this));\n";
$code .= "\$version = \$this->version;\n";
$code .= "\$key = \"{\$node}-{\$table}-{\$class}-{\$version}-{\$id}\";\n";
$code .= "return md5(\$key);\n";
$code .= "}\n";
echo($code);
