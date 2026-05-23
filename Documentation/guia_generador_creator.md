# Guía Completa: Generador de Cruds "Creator" en Higgs Framework

## 1. Introducción

El **Generador de Creators** es una herramienta automatizada que genera vistas completas para crear nuevos registros en la base de datos. Crea seis archivos PHP principales que manejan:

- **Formulario de creación** (form.php)
- **Validación de datos** (validator.php)
- **Procesamiento e inserción** en base de datos (processor.php)
- **Pantalla del índice** (index.php)
- **Control de permisos** (deny.php)
- **Navegación de breadcrumb** (breadcrumb.php)

---

## 2. Arquitectura General del Generador

```
/Creator/
├── index.php                 ← Punto de entrada (verifica permisos)
├── form.php                  ← Formulario que muestra el código a generar
├── validator.php             ← Valida los campos del formulario
├── processor.php             ← Procesa y escribe los archivos generados
├── breadcrumb.php            ← Breadcrumb de la página del generador
├── deny.php                  ← Página de acceso denegado
└── coders/
    ├── index.php             ← Genera código para index.php
    ├── form.php              ← Genera código para el formulario (form.php)
    ├── processor.php         ← Genera código para processor.php
    ├── validator.php         ← Genera código para validator.php
    ├── breadcrumb.php        ← Genera código para breadcrumb.php
    └── deny.php              ← Genera código para deny.php
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
- Se comprueba el permiso: `development-access` (singular)
- Si falta permiso → Muestra `deny.php`
- Si tiene permiso → Muestra `form.php`

---

### 3.2 Etapa 2: Mostrar Formulario (form.php)

El formulario contiene:

1. **Ruta de destino** (readonly):
   - Ejemplo: `app/Modules/Firewall/Views/IpRange/_Create`
   - Se construye con: `APPPATH . "Modules/{ModuleName}/Views/{ComponentName}/_Create"`

2. **Código PHP a generar** (área editable):
   - Contiene el código combinado de los 6 coders
   - Usuario puede revisar y editar antes de guardar

3. **Campos ocultos** que contienen:
   - `pathfiles` → Ruta destino
   - `cindex` → Código del index.php (URL encoded)
   - `cform` → Código del form.php (URL encoded)
   - `cprocessor` → Código del processor.php (URL encoded)
   - `cvalidator` → Código del validator.php (URL encoded)
   - `cbreadcrumb` → Código del breadcrumb.php (URL encoded)
   - `cdeny` → Código del deny.php (URL encoded)

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

**Ubicación final:** `app/Modules/{Module}/Views/{Component}/_Create/index.php`

**Responsabilidad:**
- Punto de entrada para crear nuevos registros
- Verifica el permiso `{module}-{component}-create` (singular)
- Redirige a `form.php` si tiene permiso
- Redirige a `deny.php` si no tiene permiso
- Maneja la validación si se envía el formulario

**Variables disponibles:**
```php
$data['permissions']['singular'] = "firewall-iprange-create";
$singular = $authentication->has_Permission($data['permissions']['singular']);
```

**Estructura:**
```php
if ($singular) {
    if (!empty($submited)) {
        // Muestra validador
        $json = [...view($validator, $data)...]
    } else {
        // Muestra formulario
        $json = [...view($form, $data)...]
    }
} else {
    // Acceso denegado
    $json = [...view($deny, $data)...]
}
echo json_encode($json);
```

**Flujo:**
- Comprueba si el usuario tiene permiso de creación
- Si NO tiene permiso → Muestra `deny.php` (tarjeta de acceso denegado)
- Si SÍ tiene permiso → Verifica si es primer acceso o envío del formulario
- Si es primer acceso → Muestra `form.php` (formulario vacío)
- Si es envío → Muestra `validator.php` (validación)

---

### 5.2 form.php (Formulario de Creación)

**Ubicación final:** `app/Modules/{Module}/Views/{Component}/_Create/form.php`

**Responsabilidad:**
- Genera el formulario HTML para crear nuevos registros
- Carga los campos de la tabla automáticamente
- Establece valores por defecto (autor, fecha, hora)
- Agrupa campos en filas de 3 columnas
- Incluye botones de Enviar y Cancelar

**Características principales:**

#### a) Importaciones y Servicios
```php
use Higgs\Frontend\Bootstrap\v5_3_3\Bootstrap as BS5;
$b = service("bootstrap");
$f = service("forms", array("lang" => "{Module}_{Component}."));
$server = service("server");
```

#### b) Variables y Valores Iniciales
```php
$back = $server->get_Referer();
$r["back"] = $f->get_Value("back", $back);

// Para cada campo de la tabla
foreach ($fields as $field) {
    if ($field == "author") {
        $r[$field] = $f->get_Value($field, safe_get_user());
    } else if ($field == "date") {
        $r[$field] = $f->get_Value($field, service("dates")::get_Date());
    } else if ($field == "time") {
        $r[$field] = $f->get_Value($field, service("dates")::get_Time());
    } else {
        $r[$field] = $f->get_Value($field);
    }
}
```

#### c) Definición de Campos
```php
$f->add_HiddenField("back", $r["back"]);

foreach ($fields as $field) {
    if ($field == "author") {
        $f->add_HiddenField("author", $r["author"]);
    } else {
        $f->fields[$field] = $f->get_FieldText($field, array(
            "value" => $r[$field],
            "proportion" => "col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12"
        ));
    }
}

$f->fields["cancel"] = $f->get_Cancel("cancel", array(
    "href" => $r["back"],
    "text" => lang("App.Cancel"),
    "type" => "secondary",
    "proportion" => "col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 padding-right"
));

$f->fields["submit"] = $f->get_Submit("submit", array(
    "value" => lang("App.Create"),
    "proportion" => "col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 padding-left"
));
```

#### d) Agrupación en Grupos de Campos
Los campos (excepto author, created_at, updated_at, deleted_at) se agrupan en bloques de 3:

```php
$skipped = ["author", "created_at", "updated_at", "deleted_at"];
$visible_fields = array_values(array_filter($fields, fn($f) => !in_array($f, $skipped)));
$chunks = array_chunk($visible_fields, 3);

foreach ($chunks as $chunk) {
    $grupo++;
    $fields_code = implode('.', array_map(fn($f) => "\$f->fields[\"{$f}\"]", $chunk));
    $f->groups["g{$grupo}"] = $f->get_Group(array(
        "legend" => "",
        "fields" => ...
    ));
}
```

#### e) Construcción y Renderizado
```php
$card = BS5::card([
    'headerTitle' => lang("{Module}_{Component}.create-title"),
    'headerButtons' => [BS5::button([
        'content' => BS5::icon(['icon' => 'arrow-left', 'style' => 'duotone']),
        'variant' => 'secondary',
        'size' => 'sm',
        'attributes' => ['href' => $back]
    ])],
    'content' => ["htmlContent" => $f,],
]);

echo($card);
```

**Campos excluidos de la UI:**
- `author` → Hidden field (se rellena automáticamente)
- `created_at` → No aparece en el formulario
- `updated_at` → No aparece en el formulario
- `deleted_at` → No aparece en el formulario

---

### 5.3 processor.php (Procesamiento e Inserción)

**Ubicación final:** `app/Modules/{Module}/Views/{Component}/_Create/processor.php`

**Responsabilidad:**
- Procesa los datos enviados desde el formulario
- Inserta el nuevo registro en la base de datos
- Maneja duplicados (registros que ya existen)
- Muestra mensaje de éxito o advertencia

**Características principales:**

#### a) Importaciones
```php
use Higgs\Frontend\Bootstrap\v5_3_3\Bootstrap as BS5;
```

#### b) Recolección de Datos
```php
$f = service("forms", array("lang" => "{Module}_{Component}."));
$model = model("App\Modules\{Module}\Models\{Module}_{Component}");

$d = array(
    "{field1}" => {valor_inicial},
    "{field2}" => $f->get_Value("{field2}"),
    "author" => safe_get_user(),
    "date" => safe_get_date(),
    "time" => safe_get_time(),
);

$row = $model->find($d["{primary_key}"]);
$l["back"] = $f->get_Value("back");
$l["edit"] = "/{module}/{component}/edit/{$d[\"{primary_key}\"]}";
```

#### c) Verificación de Duplicados
```php
if (is_array($row)) {
    // REGISTRO YA EXISTE
    $_icon = (string)BS5::icon(['icon' => 'triangle-exclamation', 'style' => 'duotone', 'size' => '4x']);
    $_body = '<div class="text-center py-3">' . $_icon . '</div>'
        . '<p class="text-center pb-2">' . lang("{Module}_{Component}.create-duplicate-message") . '</p>'
        . '<div class="text-center pb-3">' . (string)BS5::button([...]) . '</div>';
    
    $c = BS5::card([
        'header' => [
            'title' => lang("{Module}_{Component}.create-duplicate-title"),
            'class' => 'bg-warning border-warning text-dark'
        ],
        'content' => [
            'htmlContent' => $_content,
            'class' => 'bg-warning text-dark'
        ],
        'attributes' => ['class' => 'border-warning shadow-sm'],
    ]);
} else {
    // INSERTAR NUEVO REGISTRO
    $create = $model->insert($d);
    $_icon = (string)BS5::icon(['icon' => 'circle-check', 'style' => 'duotone', 'size' => '4x']);
    $_body = '<div class="text-center py-3">' . $_icon . '</div>'
        . '<p class="text-center pb-2">' . sprintf(lang("{Module}_{Component}.create-success-message"), $d['{primary_key}']) . '</p>'
        . '<div class="text-center pb-3">' . (string)BS5::button([...]) . '</div>';
    
    $c = BS5::card([
        'header' => [
            'title' => lang("{Module}_{Component}.create-success-title"),
            'class' => 'bg-success border-success text-white'
        ],
        'content' => [
            'htmlContent' => $_content,
            'class' => 'bg-success text-white'
        ],
        'attributes' => ['class' => 'border-success shadow-sm'],
    ]);
    
    $model->invalidateSearchCache();
}

echo($c);
```

**Scenarios de salida:**
1. **Duplicado:** Tarjeta amarilla (warning) con opción de continuar
2. **Éxito:** Tarjeta verde (success) con mensaje y opción de continuar
3. **Error:** Tarjeta roja (danger) si hay error en inserción (muy raro)

---

### 5.4 validator.php (Validación)

**Ubicación final:** `app/Modules/{Module}/Views/{Component}/_Create/validator.php`

**Responsabilidad:**
- Valida los datos enviados desde el formulario
- Ejecuta reglas de validación de cada campo
- Si falla → Muestra errores y el formulario de nuevo
- Si pasa → Llama a `processor.php` para insertar

**Estructura:**

#### a) Configuración de Validación
```php
$f = service("forms", array("lang" => "{Module}_{Component}."));

// Comentadas por defecto - descomenta según necesidad
foreach ($fields as $field) {
    //$f->set_ValidationRule($field, "trim|required");
}
```

**Reglas comunes disponibles:**
```php
"trim"              // Elimina espacios en blanco
"required"          // Campo obligatorio
"min_length[3]"     // Mínimo 3 caracteres
"max_length[50]"    // Máximo 50 caracteres
"valid_email"       // Validar email
"numeric"           // Solo números
"alpha_numeric"     // Alfanuméricos
"matches[campo]"    // Debe coincidir con otro campo
```

#### b) Ejecución de Validación
```php
if ($f->run_Validation()) {
    // VALIDACIÓN EXITOSA - ir a processor.php
    $c = view($component . '\processor', $parent->get_Array());
} else {
    // VALIDACIÓN FALLIDA - mostrar errores
    $_icon_col = BS5::row([...BS5::icon(['icon' => 'triangle-exclamation', ...])...]);
    $_msg_col = BS5::row([...lang('App.validator-errors-message')...]);
    $_errors_col = BS5::row([...$f->validation->listErrors()...]);
    $_content = BS5::col([...htmlContent...]);
    
    $c = BS5::card([
        'headerTitle' => lang('App.validator-errors-title'),
        'headerClass' => 'bg-danger text-white',
        'content' => ["htmlContent" => $_content,],
        'attributes' => ['class' => 'border-danger shadow-sm'],
    ]);
    
    // Muestra el formulario de nuevo debajo de los errores
    $c .= view($component . '\form', $parent->get_Array());
}

echo($c);
```

**Flujo:**
1. Si la validación pasa → Va a `processor.php`
2. Si la validación falla → Muestra tarjeta de error + formulario

---

### 5.5 breadcrumb.php (Navegación)

**Ubicación final:** `app/Modules/{Module}/Views/{Component}/_Create/breadcrumb.php`

**Responsabilidad:**
- Mostrar ruta de navegación actual
- Permitir navegación hacia atrás
- Indicar que se está en la página de creación

**Estructura:**
```php
use Higgs\Frontend\Bootstrap\v5_3_3\Bootstrap as BS5;

echo BS5::breadcrumb(['items' => [
    ['label' => '{module}', 'href' => '/{module}/'],
    ['label' => lang('App.{component}'), 'href' => '/{module}/{component}/home/'.lpk(), 'active' => true],
]]);
```

**Características:**
- El último elemento tiene `'active' => true` (no es clickable)
- Los anteriores son clickables para navegar hacia atrás

---

### 5.6 deny.php (Control de Acceso)

**Ubicación final:** `app/Modules/{Module}/Views/{Component}/_Create/deny.php`

**Responsabilidad:**
- Mostrar pantalla de acceso denegado
- Diferencia entre usuario no autenticado vs. sin permisos

**Lógica:**

#### a) Usuario AUTENTICADO pero SIN PERMISOS
```php
if ($authentication->get_LoggedIn()) {
    $_icon = (string)BS5::icon(['icon' => 'ban', 'style' => 'duotone', 'size' => '4x']);
    $_body = '<div class="text-center py-3">' . $_icon . '</div>'
        . '<p class="text-center pb-2">' . lang('App.Access-denied-message') . '</p>';
    $_permissions = "<p class=\"text-center pb-2\">Permisos requeridos: " . implode(" - ", $permissions) . "</p>";
    $_continue = BS5::button(['content' => lang('App.Continue'), 'variant' => 'danger', 'size' => 'md', 'attributes' => ['href' => $continue]]);
    
    $card = BS5::card([
        'header' => [
            'title' => lang('App.Access-denied-title'),
            'class' => 'bg-danger border-danger text-white'
        ],
        'content' => [
            'htmlContent' => $_body . $_permissions,
            'class' => 'bg-danger text-white',
        ],
        'footer' => [
            'content' => $_continue,
            'class' => 'bg-danger text-white d-flex justify-content-end',
        ],
        'attributes' => [
            'class' => 'border-danger shadow-sm'
        ],
    ]);
}
```

#### b) Usuario NO AUTENTICADO
```php
else {
    $_icon = (string)BS5::icon(['icon' => 'lock', 'style' => 'duotone', 'size' => '4x']);
    $_body = '<div class="text-center py-3">' . $_icon . '</div>'
        . '<p class="text-center pb-2">' . lang('App.login-required-message') . '</p>';
    $_continue = BS5::button(['content' => lang('App.Continue'), 'variant' => 'danger', 'size' => 'md', 'attributes' => ['href' => $continue]]);
    
    $card = BS5::card([
        'header' => [
            'title' => lang('App.login-required-title'),
            'class' => 'bg-danger text-white'
        ],
        'content' => [
            'htmlContent' => $_body,
            'class' => 'bg-danger text-white',
        ],
        'footer' => [
            'content' => $_continue,
            'class' => 'bg-danger text-white d-flex justify-content-end',
        ],
        'attributes' => [
            'class' => 'border-danger shadow-sm'
        ],
    ]);
}

echo($card);
```

**URLs de retorno:**
- Redirige a: `/{module}/{component}/list/` (lista de registros)

---

## 6. Convenciones de Nombres

### 6.1 Rutas de Archivos

```
app/Modules/{ModuleName}/
├── Views/
│   └── {ComponentName}/
│       └── _Create/
│           ├── index.php
│           ├── form.php
│           ├── processor.php
│           ├── validator.php
│           ├── breadcrumb.php
│           └── deny.php
```

### 6.2 Nombres de Clases y Espacios

**Para 2 componentes (`firewall_iprange`):**
```php
Namespace: App\Modules\Firewall\Models
Clase: Firewall_Iprange
URL: /firewall/iprange/create/
Permiso: firewall-iprange-create
Ruta archivos: app/Modules/Firewall/Views/IpRange/_Create/
```

**Para 3 componentes (`firewall_iprange_log`):**
```php
Namespace: App\Modules\Firewall\Models
Clase: Firewall_Iprange_Log
URL: /firewall/iprange/log/create/
Permiso: firewall-iprange-log-create
Ruta archivos: app/Modules/Firewall/Views/IpRange/Log/_Create/
```

### 6.3 Permisos

```
Singular (Principal): {module}-{component}-create
Plural (No usado en Creator): {module}-{component}-view-all
```

---

## 7. Constantes y Variables Disponibles

### 7.1 En los Coders

```php
COMMENT_HR_VARS              // Comentario separador: [Vars]
COMMENT_MODULECONTROLER_VARS // Documentación de variables heredadas
COMMENT_HR_BUILD             // Comentario separador: [Build]
COMMENT_HR_MODELS            // Comentario separador: [Models]
COMMENT_HR_SERVICES          // Comentario separador: [Services]
COMMENT_HR_FIELDS            // Comentario separador: [Fields]
COMMENT_HR_GROUPS            // Comentario separador: [Groups]
COMMENT_HR_BUTTONS           // Comentario separador: [Buttons]
COMMENT_HR_REQUEST           // Comentario separador: [Request]
COMMENT_HR_VALIDATION        // Comentario separador: [Validation]
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
```

### 7.3 Helpers de Seguridad

```php
safe_ucfirst()       // Upper case first con manejo de acentos
safe_strtolower()    // Lower case con manejo de acentos
safe_get_user()      // Obtiene usuario actual
safe_get_date()      // Obtiene fecha actual
safe_get_time()      // Obtiene hora actual
lpk()                // Language Pack Key
lang()               // Obtiene traducción
```

---

## 8. Uso Paso a Paso

### 8.1 Acceder al Generador

```
1. URL: /development/generators/list/
2. Buscar "creator" o desplazarse para encontrar el Creator
3. El generador carga automáticamente
```

### 8.2 Seleccionar Tabla

```
1. Ingresar el identificador de tabla (OID)
   Ejemplo: firewall_iprange
2. El generador obtiene automáticamente los campos de la tabla
3. Genera código basado en esos campos
```

### 8.3 Revisar el Código Generado

```
1. El formulario muestra el código PHP combinado de los 6 archivos
2. Revisar la ruta de destino (_Create)
3. Editar código si es necesario (personalizaciones)
```

### 8.4 Guardar los Archivos

```
1. Click en "Guardar Creador"
2. Validación de campos requeridos
3. Creación de archivos en:
   app/Modules/{Module}/Views/{Component}/_Create/
4. Mensaje de éxito/advertencia
```

### 8.5 Verificar los Archivos Creados

```bash
ls -la app/Modules/Firewall/Views/IpRange/_Create/
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

### 8.6 Crear Primeros Registros

```
1. URL: /firewall/iprange/create/
2. Se muestra el formulario con los campos disponibles
3. Completar datos
4. Click en "Crear"
5. Validación de datos
6. Inserción en base de datos
7. Mensaje de éxito o error de duplicado
```

---

## 9. Personalización

### 9.1 Modificar Campos del Formulario

En `form.php`, bajo la sección `[Fields]`:

```php
// Cambiar tipo de campo
// De texto simple:
$f->fields["description"] = $f->get_FieldText("description", [
    "value" => $r["description"],
    "proportion" => "col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12"
]);

// A área de texto:
$f->fields["description"] = $f->get_FieldTextArea("description", [
    "value" => $r["description"],
    "proportion" => "col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12"
]);

// O a combobox:
$f->fields["type"] = $f->get_FieldSelect("type", [
    "value" => $r["type"],
    "options" => [
        "active" => lang("App.active"),
        "inactive" => lang("App.inactive")
    ],
    "proportion" => "col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12"
]);
```

### 9.2 Agregar Validaciones

En `validator.php`, bajo `[Request]`:

```php
// Descomentar y personalizar:
$f->set_ValidationRule("email", "trim|required|valid_email");
$f->set_ValidationRule("phone", "trim|required|numeric");
$f->set_ValidationRule("name", "trim|required|min_length[3]|max_length[100]");
$f->set_ValidationRule("password", "required|min_length[8]");
$f->set_ValidationRule("confirm", "required|matches[password]");
```

### 9.3 Cambiar Campos Ocultos

En `form.php`, si necesitas convertir un campo de oculto a visible:

```php
// De oculto:
$f->add_HiddenField("author", $r["author"]);

// A visible:
$f->fields["author"] = $f->get_FieldText("author", [
    "value" => $r["author"],
    "readonly" => true,  // Opcional
    "proportion" => "col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12"
]);
```

### 9.4 Personalizar Mensajes

Editar las claves de lenguaje en archivos de traducción:

```
app/Modules/{Module}/Language/es/{Module}_{Component}.php
```

Claves necesarias:
```php
'create-title' => 'Crear nuevo ...',
'create-success-title' => 'Registro creado exitosamente',
'create-success-message' => 'El registro %s ha sido creado',
'create-duplicate-title' => 'Registro duplicado',
'create-duplicate-message' => 'Este registro ya existe en el sistema',
```

### 9.5 Modificar Redirección Posterior

En `processor.php`, cambiar la URL de continuación:

```php
// Actual:
$l["edit"] = "/{$slc_module}/{$slc_component}/edit/{$d["{$fields[0]}"]}";

// Personalizado:
$l["view"] = "/{$slc_module}/{$slc_component}/view/{$d["{$fields[0]}"]}";
$l["back"] = $f->get_Value("back");
```

Y en el botón de continuación:
```php
'attributes' => ['href' => $l['view']]  // o $l['back']
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

**Campos que se excluyen automáticamente:**
- `created_at` → Manejado por framework
- `updated_at` → Manejado por framework
- `deleted_at` → Manejado por soft delete
- `author` → Se rellena automáticamente con usuario actual

### 10.2 Procesamiento de Campos Especiales

El generador reconoce ciertos campos y los trata especialmente:

```php
if ($field == "author") {
    $code .= "\$r[\"{$field}\"] = \$f->get_Value(\"{$field}\", safe_get_user());\n";
} else if ($field == "date") {
    $code .= "\$r[\"{$field}\"] = \$f->get_Value(\"{$field}\", service(\"dates\")::get_Date());\n";
} else if ($field == "time") {
    $code .= "\$r[\"{$field}\"] = \$f->get_Value(\"{$field}\", service(\"dates\")::get_Time());\n";
} else {
    $code .= "\$r[\"{$field}\"] = \$f->get_Value(\"{$field}\");\n";
}
```

### 10.3 Invalidación de Caché

Después de insertar un nuevo registro:

```php
$model->invalidateSearchCache();
```

Esto asegura que los caches se actualicen para que el nuevo registro sea visible inmediatamente.

### 10.4 URL Encoding

Los coders generan código que será:
1. URL encoded antes de guardarse en campos ocultos
2. URL decoded antes de escribirse en archivos

```php
// En form.php (encoding)
$f->add_HiddenField("cform", urlencode($cform));

// En processor.php (decoding)
$generatedFiles["{$pathfiles}/form.php"] => urldecode($cform)
```

### 10.5 Permisos del Sistema de Archivos

Los archivos se crean con permisos:

```php
chmod($pathfiles, 0775);   // Directorio: lectura, escritura, ejecución
chmod($filepath, 0664);    // Archivos: lectura, escritura (grupo/otros solo lectura)
```

---

## 11. Errores Comunes

| Error | Causa | Solución |
|-------|-------|----------|
| "Permiso denegado" | Usuario sin acceso | Asignar permiso `development-access` |
| Archivos no creados | Permisos del servidor | Verificar `chmod` en directorio |
| Tabla vacía | OID incorrecto | Verificar nombre exacto de tabla |
| Formulario no se carga | Campos no encontrados | Regenerar después de agregar campos |
| Duplicado no detecta | Clave primaria mal | Verificar primera columna de tabla |
| Campos no se validan | Reglas comentadas | Descomentar reglas en validator.php |
| Mensajes no aparecen | Claves de lenguaje | Crear claves en archivo Language |

---

## 12. Ejemplo Completo

### Generar Creator para Módulo Firewall - Tabla IP Ranges

**1. Ingresar al generador:**
```
URL: /development/generators/list/
```

**2. Seleccionar tabla:**
```
OID: firewall_iprange
```

**3. Revisar:**
```
Ruta: app/Modules/Firewall/Views/IpRange/_Create
Permiso: firewall-iprange-create
Campos: id, name, description, start_ip, end_ip, author, date, time
```

**4. Personalizar (opcional):**
```
- Descomentar validaciones en validator.php
- Agregar reglas como: trim|required|valid_ip
- Cambiar tipos de campos si es necesario
```

**5. Guardar**

**6. Archivos creados:**
```
app/Modules/Firewall/Views/IpRange/_Create/
├── index.php        (Punto de entrada y control de permisos)
├── form.php         (Formulario HTML)
├── processor.php    (Inserción en DB)
├── validator.php    (Validación de datos)
├── deny.php         (Acceso denegado)
└── breadcrumb.php   (Navegación)
```

**7. Crear archivo de lenguaje:**
```php
// app/Modules/Firewall/Language/es/Firewall_IpRange.php

return [
    'create-title' => 'Crear nuevo rango IP',
    'create-success-title' => 'Rango IP creado',
    'create-success-message' => 'El rango IP %s ha sido creado exitosamente',
    'create-duplicate-title' => 'Rango IP duplicado',
    'create-duplicate-message' => 'Este rango IP ya existe en el sistema',
];
```

**8. Agregar permisos al usuario:**
```
Permiso: firewall-iprange-create
Asignar al usuario que quiera crear registros
```

**9. Acceder a:**
```
/firewall/iprange/create/
```

**10. Crear primer registro:**
```
- Llenar formulario
- Click "Crear"
- Ver mensaje de éxito
- Nuevo registro en base de datos
```

---

## 13. Resumen Operacional

```
┌─────────────────────────────────────────────────────────────┐
│  Acceso: /development/generators/list/                      │
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
      │ generado (6 files) │
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
      │ app/Modules/{M}/Views/{C}/_Create/    │
      │ ├── index.php                         │
      │ ├── form.php                          │
      │ ├── processor.php                     │
      │ ├── validator.php                     │
      │ ├── deny.php                          │
      │ └── breadcrumb.php                    │
      └────────┬─────────────────────────────┘
               │
               ↓
      ┌────────────────────┐
      │ Mensaje de éxito   │
      └────────┬───────────┘
               │
               ↓
      ┌──────────────────────────────────┐
      │ Usuario accede a /module/comp/   │
      │ create/ para crear registros     │
      └──────────────────────────────────┘
```

### Flujo de Usuario Final (Crear Registro)

```
┌─────────────────────────────────────────┐
│ Usuario accede a /module/component/     │
│ create/                                 │
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
         │
         ↓
    [Llenar Formulario]
         │
         ↓
    [Enviador (POST)]
         │
         ↓
    [Validar Datos]
    ┌─┬──────────┬─┐
    │ │          │ │
NO  │ │          │ │ SÍ
  FALLA│          │PASA
    ↓ │          ↓
[Mostrar Errores] [Ir a Processor]
      │               │
      └───────┬───────┘
              │
              ↓
      [Buscar Duplicado]
      ┌─┬──────────┬─┐
  NO  │ │          │ │ SÍ
      │ │ EXISTE   │ │ NO EXISTE
      ↓ │          ↓
    [Tarjeta Warning] [Insertar en DB]
                           │
                           ↓
                      [Tarjeta Success]
                           │
                           ↓
                      [Mostrar Opción
                       de Continuar]
```

---

## 14. Archivos de Configuración Requeridos

### Language Files

```php
// app/Modules/{Module}/Language/es/{Module}_{Component}.php

return [
    'create-title' => 'Crear nuevo {component}',
    'create-success-title' => '{Component} creado exitosamente',
    'create-success-message' => '{Component} "%s" creado exitosamente',
    'create-duplicate-title' => '{Component} ya existe',
    'create-duplicate-message' => 'Este {component} ya existe en el sistema',
];
```

### Permission Configuration

Los permisos deben ser asignados manualmente en el módulo de seguridad:

```
development-access        (Acceder al generador)
{module}-{component}-create (Crear registros)
```

---

## 15. Referencias

- **Ubicación del código:** `/www/wwwroot/_development/app/Modules/Development/Views/Generators/Creator/`
- **Framework:** Higgs (CodeIgniter 4 fork)
- **Bootstrap:** v5.3.3 con componentes customizados
- **Estándar de código:** PSR-12 (PHP)

---

**Última actualización:** 2026-05-06  
**Versión Creator:** 1.5.0  
**Autor:** Jose Alexis Correa Valencia  
**Documentación creada:** 2026-05-06
