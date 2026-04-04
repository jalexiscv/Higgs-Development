<?php

use Config\Database;

$action = "";
$module = "";
$component = "";
$f = service("forms", array("lang" => "Nexus."));
/** request * */
$r["client"] = $f->get_Value("client", strtoupper(uniqid()));
$r["time"] = $f->get_Value("time", service("dates")::get_Time());
$id = $oid;
$eid = explode("_", $id);
$ucf_module = safe_ucfirst($eid[0]);
$ucf_component = safe_ucfirst($eid[1]);
$ucf_options = safe_ucfirst(@$eid[2]);
$slc_module = safe_strtolower($eid[0]);
$slc_component = safe_strtolower($eid[1]);
$slc_options = safe_strtolower(@$eid[2]);

if (count($eid) == 3) {
    $model = "App\\Modules\\{$ucf_module}\\Models\\{$ucf_module}_{$ucf_component}_{$ucf_options}";
    $path = '/' . $slc_module . '/' . $slc_component . '/' . $slc_options;
    $namespaced = "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\{$ucf_options}\\Creator\\index.php";
    $singular = "{$slc_module}-{$slc_component}-{$slc_options}-create";
    $pathfiles = APPPATH . "Modules/{$ucf_module}/Views/{$ucf_component}/{$ucf_options}/_List";
    $ajax = "/{$slc_module}/{$slc_component}/{$slc_options}/ajax/list?time=\".time()";
} else {
    $model = "App\\Modules\\{$ucf_module}\\Models\\{$ucf_module}_{$ucf_component}";
    $path = '/' . $slc_module . '/' . $slc_component;
    $namespaced = "App\\Modules\\{$ucf_module}\\Views\\{$ucf_component}\\Creator\\index.php";
    $singular = "{$slc_module}-{$slc_component}-create";
    $pathfiles = APPPATH . "Modules/{$ucf_module}/Views/{$ucf_component}/_List";
    $ajax = "/{$slc_module}/{$slc_component}/ajax/list/";
}

$db = Database::connect("default");
$fields = $db->getFieldNames($id);


$code = "<?php\n";
$code .= get_development_code_copyright(array("path" => $namespaced));
$code .= "return [\n";
$code .= "\t// - {$ucf_component} fields \n";
foreach ($fields as $field) {
    $code .= "\t\"label_{$field}\"=>\"{$field}\",\n";
}
foreach ($fields as $field) {
    $code .= "\t\"placeholder_{$field}\"=>\"{$field}\",\n";
}
foreach ($fields as $field) {
    $code .= "\t\"help_{$field}\"=>\"{$field}\",\n";
}
$code .= "\t// - {$ucf_component} creator \n";
$code .= "\t\"create-denied-title\"=>\"Acceso denegado!\",\n";
$code .= "\t\"create-denied-message\"=>\"Su rol en la plataforma no posee los privilegios requeridos para crear nuevos #plural, por favor póngase en contacto con el administrador del sistema o en su efecto contacte al personal de soporte técnico para que estos le sean asignados, según sea el caso. Para continuar presioné la opción correspondiente en la parte inferior de este mensaje.\",\n";
$code .= "\t\"create-title\"=>\"Crear nuevo #singular\",\n";
$code .= "\t\"create-errors-title\"=>\"¡Advertencia!\",\n";
$code .= "\t\"create-errors-message\"=>\"Los datos proporcionados son incorrectos o están incompletos, por favor verifique eh inténtelo nuevamente.\",\n";
$code .= "\t\"create-duplicate-title\"=>\"¡#singular existente!\",\n";
$code .= "\t\"create-duplicate-message\"=>\"Este #singular ya se había registrado previamente, presioné continuar en la parte inferior de este mensaje para retornar al listado general de #plural.\",\n";
$code .= "\t\"create-success-title\"=>\"¡#singular registrada exitosamente!\",\n";
$code .= "\t\"create-success-message\"=>\"La #singular se registró exitosamente, para retornar al listado general de #plural presioné continuar en la parte inferior de este mensaje.\",\n";
$code .= "\t// - {$ucf_component} viewer \n";
$code .= "\t\"view-denied-title\"=>\"¡Acceso denegado!\",\n";
$code .= "\t\"view-denied-message\"=>\"Los roles asignados a su perfil, no le conceden los privilegios necesarios para visualizar #plural en esta plataforma. Contacte al departamento de soporte técnico para información adicional, o la asignación de los permisos necesarios si es el caso. Para continuar seleccione la opción correspondiente en la parte inferior de este mensaje.\",\n";
$code .= "\t\"view-title\"=>\"Vista\",\n";
$code .= "\t\"view-errors-title\"=>\"¡Advertencia!\",\n";
$code .= "\t\"view-errors-message\"=>\"Los datos proporcionados son incorrectos o están incompletos, por favor verifique eh inténtelo nuevamente.\",\n";
$code .= "\t\"view-noexist-title\"=>\"¡No existe!\",\n";
$code .= "\t\"view-noexist-message\"=>\"\",\n";
$code .= "\t\"view-success-title\"=>\"\",\n";
$code .= "\t\"view-success-message\"=>\"\",\n";
$code .= "\t// - {$ucf_component} editor \n";
$code .= "\t\"edit-denied-title\"=>\"¡Advertencia!\",\n";
$code .= "\t\"edit-denied-message\"=>\"Los roles asignados a su perfil, no le conceden los privilegios necesarios para actualizar #plural en esta plataforma. Contacte al departamento de soporte técnico para información adicional, o la asignación de los permisos necesarios si es el caso. Para continuar seleccione la opción correspondiente en la parte inferior de este mensaje.\",\n";
$code .= "\t\"edit-title\"=>\"¡Actualizar #singular!\",\n";
$code .= "\t\"edit-errors-title\"=>\"¡Advertencia!\",\n";
$code .= "\t\"edit-errors-message\"=>\"Los datos proporcionados son incorrectos o están incompletos, por favor verifique eh inténtelo nuevamente.\",\n";
$code .= "\t\"edit-noexist-title\"=>\"¡No existe!\",\n";
$code .= "\t\"edit-noexist-message\"=>\"El elemento que actualizar no existe o se elimino previamente, para retornar al listado general de #plural presioné continuar en la parte inferior de este mensaje. \",\n";
$code .= "\t\"edit-success-title\"=>\"¡#singular actualizada!\",\n";
$code .= "\t\"edit-success-message\"=>\"Los datos de #singular se <b>actualizaron exitosamente</b>, para retornar al listado general de #plural presioné el botón continuar en la parte inferior del presente mensaje.\",\n";
$code .= "\t// - {$ucf_component} deleter \n";
$code .= "\t\"delete-denied-title\"=>\"¡Advertencia!\",\n";
$code .= "\t\"delete-denied-message\"=>\"Los roles asignados a su perfil, no le conceden los privilegios necesarios para eliminar #plural en esta plataforma. Contacte al departamento de soporte técnico para información adicional, o la asignación de los permisos necesarios si es el caso. Para continuar seleccione la opción correspondiente en la parte inferior de este mensaje.\",\n";
$code .= "\t\"delete-title\"=>\"¡Eliminar #singular!\",\n";
$code .= "\t\"delete-message\"=>\"Para confirmar la eliminación del #singular <b>%s</b>, presioné eliminar, para retornar al listado general de #plural presioné cancelar.\",\n";
$code .= "\t\"delete-errors-title\"=>\"¡Advertencia!\",\n";
$code .= "\t\"delete-errors-message\"=>\"Los datos proporcionados son incorrectos o están incompletos, por favor verifique eh inténtelo nuevamente.\",\n";
$code .= "\t\"delete-noexist-title\"=>\"¡No existe!\",\n";
$code .= "\t\"delete-noexist-message\"=>\"\El elemento que intenta eliminar no existe o se elimino previamente, para retornar al listado general de #plural presioné continuar en la parte inferior de este mensaje.\",\n";
$code .= "\t\"delete-success-title\"=>\"¡#Singular eliminad@ exitosamente!\",\n";
$code .= "\t\"delete-success-message\"=>\"La #singular se elimino exitosamente, para retornar al listado de general de #plural presioné el botón continuar en la parte inferior de este mensaje.\",\n";
$code .= "\t// - {$ucf_component} list \n";
$code .= "\t\"list-denied-title\"=>\"¡Advertencia!\",\n";
$code .= "\t\"list-denied-message\"=>\"Los roles asignados a su perfil, no le conceden los privilegios necesarios para acceder al listado general de #plural en esta plataforma. Contacte al departamento de soporte técnico para información adicional, o la asignación de los permisos necesarios si es el caso. Para continuar seleccione la opción correspondiente en la parte inferior de este mensaje.\",\n";
$code .= "\t\"list-title\"=>\"Listado de #plural\",\n";
$code .= "\t\"list-description\"=>\"Descripción de #plural\",\n";
$code .= "];\n";
$code .= "\n";
$code .= "?>\n";
echo($code);
?>