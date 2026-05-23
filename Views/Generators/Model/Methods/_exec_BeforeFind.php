<?php

/** @var array $params - parameters passed from the parent view */

$code = '';
$code .= "protected function _exec_BeforeFind(array \$data)\n";
$code .= "{\n";
$code .= "if (isset(\$data['id']) && \$item = \$this->get_CachedItem(\$data['id'])) {\n";
$code .= "\$data['data'] = \$item;\n";
$code .= "\$data['returnData'] = true;\n";
$code .= "return \$data;\n";
$code .= "}\n";
$code .= "}\n";
echo($code);
