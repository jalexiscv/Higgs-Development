<?php


$code = "";
$code .= "private function get_CachedItem(\$id)\n";
$code .= "{\n";
$code .= "\$cacheKey = \$this->get_CacheKey(\$id);\n";
$code .= "\$cachedData = cache(\$cacheKey);\n";
$code .= "return \$cachedData !== null ? \$cachedData : false;\n";
$code .= "}\n";
echo($code);
?>