<?php
/** @var string $module */
$code = "";
$code .= "\t\t /**\n";
$code .= "\t\t * Ejecuta las migraciones para el módulo actual.\n";
$code .= "\t\t * @return void\n";
$code .= "\t\t */\n";
$code .= "\t\tprivate function exec_Migrate():void\n";
$code .= "\t\t{\n";
$code .= "\t\t\t\t\$migrations = \Config\Services::migrations();\n";
$code .= "\t\t\t\ttry {\n";
$code .= "\t\t\t\t\t\t\$migrations->setNamespace('App\\Modules\\" . $module . "');// Set the namespace for the current module\n";
$code .= "\t\t\t\t\t\t\$migrations->latest();// Run the migrations for the current module\n";
$code .= "\t\t\t\t\t\t\$all = \$migrations->findMigrations();// Find all migrations for the current module\n";
$code .= "\t\t\t\t}catch(Throwable \$e){\n";
$code .= "\t\t\t\t\t\techo(\$e->getMessage());\n";
$code .= "\t\t\t\t}\n";
$code .= "\t\t}\n";
echo($code);
?>