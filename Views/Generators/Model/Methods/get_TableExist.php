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

$code = "";
$code .= "/**\n";
$code .= "\t\t * Este método verifica si la tabla especificada existe en la base de datos utilizando la función tableExists()\n";
$code .= "\t\t * del objeto db de Higgs. Además, utiliza la caché para almacenar el resultado de la verificación para mejorar\n";
$code .= "\t\t * el rendimiento y evitar la sobrecarga de la base de datos. La clave de caché se crea utilizando el método\n";
$code .= "\t\t * get_CacheKey(), que se supone que retorna una clave única para la tabla especificada. El tiempo de duración de\n";
$code .= "\t\t * la caché se establece en el atributo \$cache_time.\n";
$code .= "\t\t * @return bool Devuelve true si la tabla existe, false en caso contrario.\n";
$code .= "\t\t */\n";
$code .= "\t\tprivate function get_TableExist(): bool\n";
$code .= "\t\t{\n";
$code .= "\t\t\t\t\$cache_key = \$this->get_CacheKey(\$this->table);\n";
$code .= "\t\t\t\tif (!\$data = cache(\$cache_key)) {\n";
$code .= "\t\t\t\t\t\t\$data = \$this->db->tableExists(\$this->table);\n";
$code .= "\t\t\t\t\t\tcache()->save(\$cache_key, \$data, \$this->cache_time);\n";
$code .= "\t\t\t\t}\n";
$code .= "\t\t\t\treturn \$data;\n";
$code .= "\t\t}\n";
echo($code);
?>