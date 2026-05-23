<?php

/*
 * **
 *  ** █ ---------------------------------------------------------------------------------------------------------------------
 *  ** █ ░FRAMEWORK                                  2023-12-01 23:19:27
 *  ** █ ░█▀▀█ █▀▀█ █▀▀▄ █▀▀ ░█─░█ ─▀─ █▀▀▀ █▀▀▀ █▀▀ [App\Modules\Account\Views\Processes\Creator\deny.php]
 *  ** █ ░█─── █──█ █──█ █▀▀ ░█▀▀█ ▀█▀ █─▀█ █─▀█ ▀▀█ Copyright 2023 - CloudEngine S.A.S., Inc. <admin@cgine.com>
 *  ** █ ░█▄▄█ ▀▀▀▀ ▀▀▀─ ▀▀▀ ░█─░█ ▀▀▀ ▀▀▀▀ ▀▀▀▀ ▀▀▀ Para obtener información completa sobre derechos de autor y licencia,
 *  ** █                                             consulte la LICENCIA archivo que se distribuyó con este código fuente.
 *  ** █ ---------------------------------------------------------------------------------------------------------------------
 *  ** █ EL SOFTWARE SE PROPORCIONA -TAL CUAL-, SIN GARANTÍA DE NINGÚN TIPO, EXPRESA O
 *  ** █ IMPLÍCITA, INCLUYENDO PERO NO LIMITADO A LAS GARANTÍAS DE COMERCIABILIDAD,
 *  ** █ APTITUD PARA UN PROPÓSITO PARTICULAR Y NO INFRACCIÓN. EN NINGÚN CASO SERÁ
 *  ** █ LOS AUTORES O TITULARES DE LOS DERECHOS DE AUTOR SERÁN RESPONSABLES DE CUALQUIER
 *  ** █ RECLAMO, DAÑOS U OTROS RESPONSABILIDAD, YA SEA EN UNA ACCIÓN DE CONTRATO,
 *  ** █ AGRAVIO O DE OTRO MODO, QUE SURJA DESDE, FUERA O EN RELACIÓN CON EL SOFTWARE
 *  ** █ O EL USO U OTROS NEGOCIACIONES EN EL SOFTWARE.
 *  ** █ ---------------------------------------------------------------------------------------------------------------------
 *  ** █ @Author Jose Alexis Correa Valencia <jalexiscv@gmail.com>
 *  ** █ @link https://www.codehiggs.com
 *  ** █ @Version 1.5.0 @since PHP 7, PHP 8
 *  ** █ ---------------------------------------------------------------------------------------------------------------------
 *  ** █ Datos recibidos desde el controlador - @ModuleController
 *  ** █ ---------------------------------------------------------------------------------------------------------------------
 *  ** █ @authentication, @request, @dates, @parent, @component, @view, @oid, @views, @prefix
 *  ** █ ---------------------------------------------------------------------------------------------------------------------
 *  **
 */

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
