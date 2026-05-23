<?php

/** @var array $params - parameters passed from the parent view */
// @$primary Recibe el campo clave primaria
$strings = service('strings');
/** @var string $primary */
$name = $strings->get_Strtolower($primary);
$ucfname = $strings->get_Ucfirst($name);
$code = '';
$code .= "\t\t /**\n";
$code .= "\t\t * Obtiene la clave de caché para un identificador dado.\n";
$code .= "\t\t * @param \$product\n";
$code .= "\t\t * @return array|false\n";
$code .= "\t\t */\n";
$code .= "\t\t public function get{$ucfname}(\${$name}):false|array\n";
$code .= "\t\t{\n";
$code .= "\t\t\t\t\$result = parent::getCached(\${$name});\n";
$code .= "\t\t\t\tif (is_array(\$result)) {\n";
$code .= "\t\t\t\t\t\treturn (\$result);\n";
$code .= "\t\t\t\t} else {\n";
$code .= "\t\t\t\t\t\treturn (false);\n";
$code .= "\t\t\t\t}\n";
$code .= "\t\t}\n";
echo($code);
