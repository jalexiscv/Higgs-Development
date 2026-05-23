<?php

/** @var array $params - parameters passed from the parent view */

$code = '';
$code .= "protected function _exec_FindCache(array \$data)\n";
$code .= "{\n";
$code .= "\$id = \$data['id'] ?? null;\n";
$code .= "cache()->save(\$this->get_CacheKey(\$id), \$data['data'], \$this->cache_time);\n";
$code .= "return (\$data);\n";
$code .= "}\n";
echo($code);
