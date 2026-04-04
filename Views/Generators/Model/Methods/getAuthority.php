<?php
$code = "";
$code .= "/**\n";
$code .= "\t\t * Retorna falso o verdadero si el usuario activo ne la sesión es el\n";
$code .= "\t\t * autor del regsitro que se desea acceder, editar o eliminar.\n";
$code .= "\t\t * @param type \$id codigo primario del registro a consultar\n";
$code .= "\t\t * @param type \$author codigo del usuario del cual se pretende establecer la autoria\n";
$code .= "\t\t * @return boolean falso o verdadero segun sea el caso\n";
$code .= "\t\t */\n";
$code .= "\t\tpublic function getAuthority(\$id, \$author): bool\n";
$code .= "\t\t{\n";
$code .= "\t\t\t\t\$row = parent::getCachedFirst([\$this->primaryKey => \$id]);\n";
$code .= "\t\t\t\tif (isset(\$row[\"author\"]) && \$row[\"author\"] == \$author) {\n";
$code .= "\t\t\t\t\t\treturn (true);\n";
$code .= "\t\t\t\t} else {\n";
$code .= "\t\t\t\t\t\treturn (false);\n";
$code .= "\t\t\t\t}\n";
$code .= "\t\t}\n";
echo($code);
?>