# Guía Completa: Generador de Cruds "Deleter" en Higgs Framework

## 1. Introducción

El **Generador de Deleters** es una herramienta automatizada que genera vistas completas para eliminar registros de datos. Crea seis archivos PHP principales que manejan:

- **Formulario de confirmación** de eliminación
- **Procesamiento de eliminación** de registros
- **Validación de datos** antes de eliminar
- **Control de permisos** (Deny)
- **Navegación de breadcrumb**
- **Control de acceso**

---

## 2. Arquitectura General del Generador

```
/Deleter/
├── index.php                 ← Punto de entrada (verifica permisos)
├── form.php                  ← Formulario que muestra el código a generar
├── validator.php             ← Valida los campos del formulario
├── processor.php             ← Procesa y escribe los archivos generados
├── breadcrumb.php            ← Breadcrumb de la página del generador
├── deny.php                  ← Página de acceso denegado
└── coders/
    ├── index.php             ← Genera código para index.php
    ├── form.php              ← Genera código para formulario de eliminación
    ├── processor.php         ← Genera código para procesamiento
    ├── validator.php         ← Genera código para validación
    ├── deny.php              ← Genera código para deny.php
    └── breadcrumb.php        ← Genera código para breadcrumb.php
```

---

## 3. Flujo de Trabajo del Generador

### 3.1 Etapa 1: Verificación de Permisos (index.php)

```
┌─────────────────────────────────────────┐
│ Usuario accede al generador             │
└──────────────┬──────────────────────────┘
               │
               ↓
       ┌───────────────────┐
       │ ¿Tiene permiso?   │
       └─┬─────────────┬───┘
         │             │
      SÍ │             │ NO
         ↓             ↓
    [Ver Formulario]   [Mostrar Deny]
```

**Verificación:**
- Se comprueba el permiso: `nexus-access` (singular)
- Si falta permiso → Muestra `deny.php`
- Si tiene permiso → Muestra `form.php`

---

### 3.2 Etapa 2: Mostrar Formulario (form.php)

El formulario contiene:

1. **Ruta de destino** (readonly):
   - Ejemplo: `app/Modules/Firewall/Views/IpRange/_Delete`
   - Se construye con: `APPPATH . "Modules/{ModuleName}/Views/{ComponentName}/_Delete"`

2. **Código PHP a generar** (área editable):
   - Contiene el código combinado de los 6 coders
   - Usuario puede revisar y editar antes de guardar

3. **Campos ocultos** que contienen:
   - `pathfiles` → Ruta destino
   - `cindex` → Código del index.php (URL encoded)
   - `cdeny` → Código del deny.php (URL encoded)
   - `cform` → Código del form.php (URL encoded)
   - `cprocessor` → Código del processor.php (URL encoded)
   - `cvalidator` → Código del validator.php (URL encoded)
   - `cbreadcrumb` → Código del breadcrumb.php (URL encoded)

---

### 3.3 Etapa 3: Validación (validator.php)

Se validan los campos requeridos:

```php
$f->set_ValidationRule("pathfiles", "trim|required");
$f->set_ValidationRule("cindex", "trim|required");
$f->set_ValidationRule("cdeny", "trim|required");
$f->set_ValidationRule("cform", "trim|required");
$f->set_ValidationRule("cprocessor", "trim|required");
$f->set_ValidationRule("cvalidator", "trim|required");
```

**Si la validación falla:**
- Muestra tarjeta de error con los mensajes de validación

**Si la validación pasa:**
- Llama a `processor.php` para escribir los archivos

---

### 3.4 Etapa 4: Procesamiento y Creación de Archivos (processor.php)

```php
$generatedFiles = [
    "{$pathfiles}/index.php" => urldecode($cindex),
    "{$pathfiles}/deny.php" => urldecode($cdeny),
    "{$pathfiles}/form.php" => urldecode($cform),
    "{$pathfiles}/processor.php" => urldecode($cprocessor),
    "{$pathfiles}/validator.php" => urldecode($cvalidator),
    "{$pathfiles}/breadcrumb.php" => urldecode($cbreadcrumb),
];
```

**Proceso:**
1. Crea el directorio `$pathfiles` si no existe
2. Asigna permisos al directorio: `0775`
3. Escribe cada archivo con el contenido decodificado
4. Asigna permisos a cada archivo: `0664`
5. Muestra mensaje de éxito o advertencia

---

## 4. Estructura de Identificadores (OID)

El generador usa un identificador compuesto llamado **OID** (Object ID):

```
{module}_{component}_{options}
```

**Ejemplos:**
- `firewall_iprange` → 2 componentes
- `firewall_iprange_log` → 3 componentes

**Transformaciones:**
```php
$eid = explode("_", $oid);                    // Divide el OID
$ucf_module = safe_ucfirst($eid[0]);          // "Firewall"
$slc_module = safe_strtolower($eid[0]);       // "firewall"
$ucf_component = safe_ucfirst($eid[1]);       // "Iprange"
$slc_component = safe_strtolower($eid[1]);    // "iprange"
```

---

## 5. Archivos Generados

### 5.1 index.php

**Ubicación final:** `app/Modules/{Module}/Views/{Component}/_Delete/index.php`

**Responsabilidad:**
- Punto de entrada para iniciar proceso de eliminación
- Verifica permisos: singular (eliminar propio) y plural (eliminar cualquiera)
- Verifica autoridad del usuario sobre el registro
- Redirige a `form.php` para confirmación
- Redirige a `validator.php` cuando se confirma la eliminación
- Redirige a `deny.php` si no tiene permisos

**Variables disponibles:**
```php
$data['permissions']['singular'] = "firewall-iprange-delete";
$data['permissions']['plural'] = "firewall-iprange-delete-all";
$singular = $authentication->has_Permission($data['permissions']['singular']);
$plural = $authentication->has_Permission($data['permissions']['plural']);
$author = $data['model']->getAuthority($oid, safe_get_user());
$authority = ($singular && $author) ? true : false;
```

**Estructura:**
```php
if ($plural || $authority) {
    if (!empty($submited)) {
        // Mostrar validador
        $json = [...view($validator, $data)...]
    } else {
        // Mostrar formulario de confirmación
        $json = [...view($form, $data)...]
    }
} else {
    // Acceso denegado
    $json = [...view($deny, $data)...]
}
echo json_encode($json);
```

---

### 5.2 form.php (Confirmación de Eliminación)

**Ubicación final:** `app/Modules/{Module}/Views/{Component}/_Delete/form.php`

**Responsabilidad:**
- Mostrar formulario de confirmación de eliminación
- Recuperar datos del registro a eliminar
- Mostrar mensaje de advertencia
- Generar botones Cancel y Delete

**Características principales:**

#### a) Recuperación de Datos
```php
$model = model("App\Modules\{Module}\Models\{Module}_{Component}");
$record = $model->get{Component}($oid);
$name = urldecode($record["name"]);
```

#### b) Construcción del Mensaje
```php
$message = sprintf(
    lang("{Module}_{Component}.delete-message"),
    $name
);
```

#### c) Creación del Formulario
```php
$f = service("forms", array("lang" => "{Module}_{Component}."));
$f->add_HiddenField("back", $server->get_Referer());
$f->add_HiddenField("pkey", $oid);

$f->fields["cancel"] = $f->get_Cancel(
    "cancel",
    array("href" => $back, "text" => lang("App.Cancel"))
);
$f->fields["submit"] = $f->get_Submit(
    "submit",
    array("value" => lang("App.Delete"))
);
```

#### d) Construcción de Tarjeta Bootstrap
```php
$card = BS5::card([
    'header' => [
        'title' => sprintf(lang("{Module}_{Component}.delete-title"), $name),
        'class' => 'bg-danger text-white mx-0',
        'buttons' => [BS5::button([...back button...])]
    ],
    'content' => ["htmlContent" => $message . $form]
]);

echo($card);
```

---

### 5.3 validator.php (Validación)

**Ubicación final:** `app/Modules/{Module}/Views/{Component}/_Delete/validator.php`

**Responsabilidad:**
- Validar que el registro a eliminar exista
- Validar que la clave primaria sea proporcionada
- Redirigir a processor si validación pasa
- Mostrar errores si validación falla

**Estructura:**
```php
$f->set_ValidationRule("pkey", "trim|required");

if ($f->run_Validation()) {
    $c = view($component . '\processor', $parent->get_Array());
} else {
    $errors = $f->validation->listErrors();
    $c = $bootstrap->get_Card('validator', array(
        'class' => 'card-danger',
        'icon' => 'fa-duotone fa-triangle-exclamation',
        'text' => lang('App.validator-errors-message'),
        'errors' => $errors,
    ));
    $c .= view($component . '\form', $parent->get_Array());
}

echo($c);
```

---

### 5.4 processor.php (Procesamiento)

**Ubicación final:** `app/Modules/{Module}/Views/{Component}/_Delete/processor.php`

**Responsabilidad:**
- Ejecutar la eliminación del registro
- Mostrar mensaje de éxito o advertencia
- Invalidar caché de búsqueda
- Verificar si el registro existe antes de eliminar

**Estructura de Éxito:**
```php
if (isset($row["id"])) {
    $delete = $model->delete($pkey);
    $_icon = BS5::icon(['icon' => 'circle-check', 'style' => 'duotone']);
    $_body = '<p class="text-center pb-2">'
        . lang("{Module}_{Component}.delete-success-message") . '</p>';
    
    $card = BS5::card([
        'header' => [
            'title' => lang("{Module}_{Component}.delete-success-title"),
            'class' => 'bg-success border-success text-white'
        ],
        'content' => ['htmlContent' => $_body],
    ]);
} else {
    // Registro no existe
    $_icon = BS5::icon(['icon' => 'triangle-exclamation']);
    $_body = '<p class="text-center pb-2">'
        . lang("{Module}_{Component}.delete-noexist-message") . '</p>';
    
    $card = BS5::card([
        'header' => [
            'title' => lang("{Module}_{Component}.delete-noexist-title"),
            'class' => 'bg-warning border-warning text-dark'
        ],
        'content' => ['htmlContent' => $_body],
    ]);
}

echo($card);
$model->invalidateSearchCache();
```

---

### 5.5 deny.php (Control de Acceso)

**Ubicación final:** `app/Modules/{Module}/Views/{Component}/_Delete/deny.php`

**Responsabilidad:**
- Mostrar pantalla de acceso denegado
- Diferencia entre usuario no autenticado vs. sin permisos
- Listar permisos requeridos

**Lógica:**
```php
$continue = "/{module}/{component}/list/".lpk();

if ($authentication->get_LoggedIn()) {
    // Usuario AUTENTICADO pero SIN PERMISOS
    $_icon = BS5::icon(['icon' => 'ban', 'style' => 'duotone']);
    $_body = '<p class="text-center pb-2">'
        . lang('App.Access-denied-message') . '</p>';
    $_permissions = "<p class=\"text-center pb-2\">Permisos requeridos: "
        . implode(" - ", $permissions) . "</p>";
    
    $card = BS5::card([
        'header' => ['title' => lang('App.Access-denied-title'), ...],
        'content' => ['htmlContent' => $_body . $_permissions, ...],
    ]);
} else {
    // Usuario NO AUTENTICADO
    $_icon = BS5::icon(['icon' => 'lock', 'style' => 'duotone']);
    $_body = '<p class="text-center pb-2">'
        . lang('App.login-required-message') . '</p>';
    
    $card = BS5::card([
        'header' => ['title' => lang('App.login-required-title'), ...],
        'content' => ['htmlContent' => $_body, ...],
    ]);
}

echo($card);
```

---

### 5.6 breadcrumb.php (Navegación)

**Ubicación final:** `app/Modules/{Module}/Views/{Component}/_Delete/breadcrumb.php`

**Responsabilidad:**
- Mostrar ruta de navegación actual
- Permitir navegación hacia atrás
- Mostrar contexto de eliminación

**Estructura:**
```php
echo BS5::breadcrumb(['items' => [
    ['label' => '{module}', 'href' => '/{module}/'],
    ['label' => lang('App.{component}'),
     'href' => '/{module}/{component}/list/'.lpk(),
     'active' => true],
]]);
```

---

## 6. Convenciones de Nombres

### 6.1 Rutas de Archivos

```
app/Modules/{ModuleName}/
├── Views/
│   └── {ComponentName}/
│       └── _Delete/
│           ├── index.php
│           ├── form.php
│           ├── validator.php
│           ├── processor.php
│           ├── deny.php
│           └── breadcrumb.php
```

### 6.2 Nombres de Clases y Espacios

**Para 2 componentes (`firewall_iprange`):**
```php
Namespace: App\Modules\Firewall\Models
Clase: Firewall_Iprange
URL: /firewall/iprange/delete/{id}
Permiso Singular: firewall-iprange-delete
Permiso Plural: firewall-iprange-delete-all
```

**Para 3 componentes (`firewall_iprange_log`):**
```php
Namespace: App\Modules\Firewall\Models
Clase: Firewall_Iprange_Log
URL: /firewall/iprange/log/delete/{id}
Permiso Singular: firewall-iprange-log-delete
Permiso Plural: firewall-iprange-log-delete-all
```

### 6.3 Permisos

```
Singular (Eliminar propio): {module}-{component}-delete
Plural (Eliminar cualquiera): {module}-{component}-delete-all
```

---

## 7. Constantes y Variables Disponibles

### 7.1 En los Coders

```php
COMMENT_HR_VARS          // Comentario separador de variables
COMMENT_MODULECONTROLER_VARS  // Documentación de variables heredadas
COMMENT_HR_BUILD         // Comentario separador de construcción
COMMENT_HR_MODELS        // Comentario separador de modelos
COMMENT_HR_FIELDS        // Comentario separador de campos
COMMENT_HR_GROUPS        // Comentario separador de grupos
COMMENT_HR_BUTTONS       // Comentario separador de botones
```

### 7.2 Variables de Instancia

```php
$parent          // Instancia de ModuleController
$authentication  // Servicio de autenticación
$request         // Servicio de solicitud (GET/POST)
$bootstrap       // Servicio Bootstrap
$dates           // Servicio de fechas
$strings         // Servicio de cadenas
$server          // Servicio de servidor
$oid             // Identificador de objeto (ej: firewall_iprange)
$data            // Array con datos globales del módulo
$pkey            // Clave primaria del registro a eliminar
```

---

## 8. Uso Paso a Paso

### 8.1 Acceder al Generador

```
1. URL: /development/generators/delete/
2. Seleccionar tabla: firewall_iprange (del combo)
3. El generador carga automáticamente
```

### 8.2 Revisar el Código Generado

```
1. El formulario muestra el código PHP combinado
2. Copiar/revisar la ruta de destino
3. Editar si es necesario (código personalizado)
4. Validar que los permisos coincidan con la aplicación
```

### 8.3 Guardar los Archivos

```
1. Click en "Guardar Eliminador"
2. Validación de campos requeridos
3. Creación de archivos en:
   app/Modules/{Module}/Views/{Component}/_Delete/
4. Mensaje de éxito/advertencia
```

### 8.4 Verificar los Archivos Creados

```bash
ls -la app/Modules/Firewall/Views/IpRange/_Delete/
```

Debería mostrar:
```
-rw-rw-r-- breadcrumb.php
-rw-rw-r-- deny.php
-rw-rw-r-- form.php
-rw-rw-r-- index.php
-rw-rw-r-- processor.php
-rw-rw-r-- validator.php
```

---

## 9. Personalización

### 9.1 Modificar Mensaje de Confirmación

En `form.php`, personalizar el archivo de idioma:

```php
// En app/Language/es/Firewall_Iprange.php
$lang['delete-message'] = 'Deseas eliminar permanentemente: <strong>%s</strong>?';
$lang['delete-title'] = 'Eliminar: %s';
$lang['delete-success-message'] = 'El registro ha sido eliminado exitosamente.';
$lang['delete-success-title'] = 'Eliminación Exitosa';
$lang['delete-noexist-message'] = 'El registro no existe.';
$lang['delete-noexist-title'] = 'Registro No Encontrado';
```

### 9.2 Agregar Validaciones Adicionales

En `validator.php`, añadir validaciones:

```php
$f->set_ValidationRule("pkey", "trim|required");
// Agregar validaciones personalizadas
$f->set_ValidationRule("pkey", "trim|required|callback_validate_record_exists");
```

Y crear el método callback:
```php
public function validate_record_exists($value) {
    $model = model("App\Modules\{Module}\Models\{Module}_{Component}");
    $record = $model->find($value);
    
    if (!$record) {
        $this->form->setError("pkey", "El registro no existe");
        return false;
    }
    return true;
}
```

### 9.3 Registrar Eliminación en Auditoría

En `processor.php`, antes de eliminar:

```php
// Registrar auditoría
$audit = [
    'action' => 'delete',
    'user_id' => safe_get_user(),
    'record_id' => $pkey,
    'timestamp' => date('Y-m-d H:i:s'),
    'old_data' => json_encode($row)
];
$model->recordAudit($audit);

// Luego ejecutar eliminación
$delete = $model->delete($pkey);
```

### 9.4 Redirección Personalizada

En `processor.php`, cambiar destino de retorno:

```php
// En lugar de $l['back']
$back = "/firewall/iprange/list/" . lpk();

// Agregar botón de retorno personalizado
$_continue = BS5::button([
    'content' => lang('App.Continue'),
    'variant' => 'success',
    'size' => 'md',
    'attributes' => ['href' => $back]
]);
```

---

## 10. Detalles Técnicos

### 10.1 Base de Datos

El generador consulta la tabla especificada:

```php
$db = Database::connect("default");
$fields = $db->getFieldNames($oid);  // Obtiene nombres de campos
$pk = $fields[0] ?? 'id';            // Identifica la clave primaria
```

### 10.2 Eliminación Lógica vs Física

**Física (Estándar):**
```php
$delete = $model->delete($pkey);  // Elimina permanentemente
```

**Lógica (Con soft delete):**
```php
// Si el modelo usa SoftDeletes
$delete = $model->delete($pkey);  // Marca como eliminado, no borra

// Para recuperar eliminados:
$row = $model->withDeleted()->find($pkey);
```

### 10.3 Invalidación de Caché

Después de eliminar:

```php
$model->invalidateSearchCache();
// O para caché específico:
// $model->clear_AllCache();
// $model->clearCache("search");
```

### 10.4 URL Encoding

Los coders generan código que será:
1. URL encoded antes de guardarse en campos ocultos
2. URL decoded antes de escribirse en archivos

```php
// En form.php (encoding)
$f->add_HiddenField("cindex", urlencode($cindex));

// En processor.php (decoding)
$generatedFiles["{$pathfiles}/index.php"] => urldecode($cindex)
```

### 10.5 Autoridad del Usuario

El sistema valida si el usuario tiene autoridad sobre el registro:

```php
$author = $data['model']->getAuthority($oid, safe_get_user());
$authority = ($singular && $author) ? true : false;
```

Esto permite:
- Eliminar solo registros propios con permiso `singular`
- Eliminar cualquier registro con permiso `plural`

---

## 11. Errores Comunes

| Error | Causa | Solución |
|-------|-------|----------|
| "Permiso denegado" | Usuario sin acceso | Asignar permiso `nexus-access` y permisos de eliminación |
| Archivos no creados | Permisos del servidor | Verificar `chmod` en directorio |
| "Registro no existe" | Registro ya eliminado | Validar que el ID sea válido |
| Caché no se invalida | Método no existe | Verificar que modelo implemente `invalidateSearchCache()` |
| Formulario no valida | Campo `pkey` vacío | Verificar que se envíe el ID del registro |
| Botón Delete inactivo | Falta permiso | Verificar permisos en base de datos |

---

## 12. Ejemplo Completo

### Generar Deleter para Módulo Firewall - Tabla IP Ranges

**1. Ingresar al generador:**
```
URL: /development/generators/delete/
```

**2. Seleccionar tabla:**
```
OID: firewall_iprange
```

**3. Revisar:**
```
Ruta: app/Modules/Firewall/Views/IpRange/_Delete
Permisos:
  - Singular: firewall-iprange-delete
  - Plural: firewall-iprange-delete-all
```

**4. Guardar**

**5. Archivos creados:**
```
app/Modules/Firewall/Views/IpRange/_Delete/
├── index.php           (Punto de entrada)
├── form.php            (Confirmación)
├── validator.php       (Validación)
├── processor.php       (Procesamiento)
├── deny.php            (Acceso denegado)
└── breadcrumb.php      (Navegación)
```

**6. Estructura del flujo:**

Usuario accede a `/firewall/iprange/delete/123`:
1. `index.php` → Verifica permisos
2. Si tiene permiso → Muestra `form.php`
3. Usuario confirma eliminación
4. `validator.php` → Valida datos
5. `processor.php` → Ejecuta eliminación
6. Muestra mensaje de éxito/error

**7. Crear entrada en rutas (Routes):**
```php
$routes->get('firewall/iprange/delete/(:num)',
    'Modules\Firewall\Controllers\IpRange::delete/$1');
```

**8. Crear método en Controlador:**
```php
public function delete($id = null) {
    $oid = 'firewall_iprange';
    return view('Modules\Firewall\Views\IpRange\_Delete\index', [
        'oid' => $oid,
        'component' => 'Modules\Firewall\Views\IpRange\_Delete'
    ]);
}
```

---

## 13. Resumen Operacional

```
┌─────────────────────────────────────────────────────────────┐
│  Acceso: /development/generators/delete/                    │
└──────────────┬──────────────────────────────────────────────┘
               │
               ↓
        ┌──────────────┐
        │ Seleccionar  │
        │ tabla (OID)  │
        └──────┬───────┘
               │
               ↓
      ┌────────────────────┐
      │ Ver código PHP     │
      │ generado           │
      │ (editable)         │
      └────────┬───────────┘
               │
               ↓
      ┌────────────────────┐
      │ Validar campos     │
      │ requeridos         │
      └────────┬───────────┘
               │
               ↓
      ┌────────────────────────────────────────┐
      │ Escribir 6 archivos en:                │
      │ app/Modules/{M}/Views/{C}/_Delete/    │
      │ ├── index.php                         │
      │ ├── form.php                          │
      │ ├── validator.php                     │
      │ ├── processor.php                     │
      │ ├── deny.php                          │
      │ └── breadcrumb.php                    │
      └────────┬─────────────────────────────┘
               │
               ↓
      ┌────────────────────┐
      │ Mensaje de éxito   │
      └────────────────────┘
               │
               ↓
      ┌──────────────────────────────┐
      │ Flujo de Eliminación:         │
      │                              │
      │ 1. Usuario → /delete/{id}    │
      │ 2. Verifica permisos         │
      │ 3. Muestra confirmación      │
      │ 4. Valida datos              │
      │ 5. Ejecuta eliminación       │
      │ 6. Muestra resultado         │
      │ 7. Invalida caché            │
      └──────────────────────────────┘
```

---

## 14. Diagrama de Autorización

```
┌─────────────────────────────────────────┐
│ Usuario solicita eliminar registro      │
└──────────────┬──────────────────────────┘
               │
               ↓
      ┌────────────────────┐
      │ ¿Autenticado?      │
      └─┬──────────┬───────┘
        │          │
       NO         SÍ
        │          │
        ↓          ↓
     [DENY]  ┌────────────────────┐
            │ ¿Tiene permiso      │
            │ plural?             │
            └─┬─────────┬─────────┘
              │         │
             SÍ         NO
              │         │
              ↓         ↓
         [ALLOW]  ┌────────────────────┐
                 │ ¿Tiene permiso      │
                 │ singular + autoridad?
                 └─┬─────────┬─────────┘
                   │         │
                  SÍ         NO
                   │         │
                   ↓         ↓
              [ALLOW]    [DENY]
```

---

## 15. Referencias

- **Ubicación del código:** `/www/wwwroot/_development/app/Modules/Development/Views/Generators/Deleter/`
- **Framework:** Higgs (CodeIgniter 4 fork)
- **Bootstrap:** v5.3.3 con componentes customizados
- **Estándar de código:** PSR-12 (PHP)

---

**Última actualización:** 2026-05-06  
**Versión Deleter:** 1.5.0  
**Autor:** Jose Alexis Correa Valencia
