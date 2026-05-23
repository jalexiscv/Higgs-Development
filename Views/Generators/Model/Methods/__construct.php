<?php

/** @var array $params - parameters passed from the parent view */

$code = '';
$code .= "\t\t /**\n";
$code .= "\t\t * Inicializa el modelo y la regeneración de la tabla asociada si esta no existe\n";
$code .= "\t\t **/\n";
$code .= "\t\t public function __construct()\n";
$code .= "\t\t {\n";
$code .= "\t\t\t parent::__construct();\n";
$code .= "\t\t\t \$this->exec_Migrate();\n";
$code .= "\t\t }\n";
echo($code);
