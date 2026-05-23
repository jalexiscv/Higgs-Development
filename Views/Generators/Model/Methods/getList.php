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
$code = '';
$code .= "\t\t /**\n";
$code .= "\t\t * Obtiene una lista de registros con un rango especificado y opcionalmente filtrados por un término de búsqueda.\n";
$code .= "\t\t * con opciones de filtrado y paginación.\n";
$code .= "\t\t * @param int \$limit El número máximo de registros a obtener por página.\n";
$code .= "\t\t * @param int \$offset El número de registros a omitir antes de comenzar a seleccionar.\n";
$code .= "\t\t * @param string \$search (Opcional) El término de búsqueda para filtrar resultados.\n";
$code .= "\t\t * @return array|false\t\tUn arreglo de registros combinados o false si no se encuentran registros.\n";
$code .= "\t\t */\n";
$code .= "\t\tpublic function getList(int \$limit, int \$offset, string \$search = \"\"): array|false\n";
$code .= "\t\t{\n";
$code .= "\t\t\t\t\$result = \$this\n";
$code .= "\t\t\t\t\t\t->groupStart()\n";
$code .= "\t\t\t\t\t\t->like(\"{$primary}\", \"%{\$search}%\")\n";
foreach ($fields as $field) {
    if ($field != $primary && $field != 'created_at' && $field != 'updated_at' && $field != 'deleted_at') {
        $code .= "\t\t\t\t\t\t->orLike(\"{$field}\", \"%{\$search}%\")\n";
    }
}
$code .= "\t\t\t\t\t\t->groupEnd()\n";
$code .= "\t\t\t\t\t\t->orderBy(\"created_at\", \"DESC\")\n";
$code .= "\t\t\t\t\t\t->findAll(\$limit, \$offset);\n";
$code .= "\t\t\t\tif (is_array(\$result)) {\n";
$code .= "\t\t\t\t\t\treturn \$result;\n";
$code .= "\t\t\t\t} else {\n";
$code .= "\t\t\t\t\t\treturn false;\n";
$code .= "\t\t\t\t}\n";
$code .= "\t\t}\n";
echo($code);
