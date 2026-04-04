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
/** @var string $primary */
/** @var array $fields */
$code = "/**\n";
$code .= "\t\t * Obtiene el número total de registros que coinciden con un término de búsqueda.\n";
$code .= "\t\t * @param string \$search (Opcional) El término de búsqueda para filtrar resultados.\n";
$code .= "\t\t * @return int Devuelve el número total de registros que coinciden con el término de búsqueda.\n";
$code .= "\t\t */\n";
$code .= "\t\tfunction get_Total(string \$search = \"\"): int\n";
$code .= "\t\t{\n";
$code .= "\t\t\t\t\$result = \$this\n";
$code .= "\t\t\t\t\t\t->groupStart()\n";

foreach ($fields as $field) {
    if ($field != $primary && $field != "created_at" && $field != "updated_at" && $field != "deleted_at") {
        $code .= "\t\t\t\t\t\t->orLike(\"{$field}\", \"%{\$search}%\")\n";
    }
}

$code .= "\t\t\t\t\t\t->groupEnd()\n";
$code .= "\t\t\t\t\t\t->countAllResults();\n";
$code .= "\t\t\t\treturn (\$result);\n";
$code .= "\t\t}\n";
echo($code);
?>