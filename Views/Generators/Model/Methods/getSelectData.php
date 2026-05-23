<?php

/** @var array $params - parameters passed from the parent view */

$code = '';
$code .= "\t\t /**\n";
$code .= "\t\t * Retorna el listado de elementos existentes de forma que se pueda cargar un field tipo select.\n";
$code .= "\t\t * Ejemplo de uso:\n";
$code .= "\t\t * \$model = model(\"App\Modules\Sie\Models\Sie_Modules\");\n";
$code .= "\t\t * \$list = \$model->getSelectData();\n";
$code .= "\t\t * \$f->get_FieldSelect(\"list\", array(\"selected\" => \$r[\"list\"], \"data\" => \$list, \"proportion\" => \"col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12\"));\n";
$code .= "\t\t */\n";
$code .= "\t\t public function getSelectData()\n";
$code .= "\t\t\t {\n";
$code .= "\t\t\t\t \$result = \$this->select(\"`{\$this->primaryKey}` AS `value`,`name` AS `label`\")->findAll();\n";
$code .= "\t\t\t\t if (is_array(\$result)) {\n";
$code .= "\t\t\t\t\t return (\$result);\n";
$code .= "\t\t\t\t } else {\n";
$code .= "\t\t\t\t\t return (false);\n";
$code .= "\t\t\t\t }\n";
$code .= "\t\t }\n";
echo($code);
