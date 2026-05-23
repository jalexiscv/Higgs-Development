# Guía Completa: Generador de Cruds "Viewer" en Higgs Framework

## 1. Introducción

El **Generador de Viewers** es una herramienta automatizada que genera vistas completas para **mostrar/visualizar un registro individual** en detalle. Crea seis archivos PHP principales que manejan:

- **Pantalla de visualización** de registro completo (Ver/Read)
- **Formulario editable** para visualizar datos (Form)
- **Control de permisos** (Deny)
- **Procesamiento de datos** (Processor)
- **Validación de permisos** (Validator)
- **Navegación de breadcrumb**

El Viewer es diferente al Lister:
- **Lister:** Muestra **lista de registros** (múltiples)
- **Viewer:** Muestra **un registro individual** (singular)

---

## 2. Arquitectura General del Generador

```
/Viewer/
├── index.php                 ← Punto de entrada (verifica permisos)
├── form.php                  ← Editor visual del código generado
├── validator.php             ← Valida los campos del formulario
├── processor.php             ← Procesa y escribe los archivos generados
├── breadcrumb.php            ← Breadcrumb de la página del generador
├── deny.php                  ← Página de acceso denegado
└── coders/
    ├── index.php             ← Genera código para index.php
    ├── form.php              ← Genera código para form.php (visualización)
    ├── processor.php         ← Genera código para processor.php
    ├── validator.php         ← Genera código para validator.php
    ├── breadcrumb.php        ← Genera código para breadcrumb.php
    └── deny.php              ← Genera código para deny.php
```

---

## 3. Flujo de Trabajo del Generador

### 3.1 Etapa 1: Verificación de Permisos (index.php)

```
┌──────────────────────────────────────────┐
│ Usuario accede a /generators/viewer/     │
└──────────────┬───────────────────────────┘
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
- Si tiene permiso → Muestra `form.php` (formulario del generador)

---

### 3.2 Etapa 2: Mostrar Formulario del Generador (form.php)

El formulario contiene:

1. **Ruta de destino** (readonly):
   - Ejemplo: `app/Modules/Firewall/Views/IpRange/_View`
   - Se construye con: `APPPATH . "Modules/{ModuleName}/Views/{ComponentName}/_View"`

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
{module}_{component}_{options?}
```

**Ejemplos:**
- `firewall_iprange` → 2 componentes (módulo + componente)
- `firewall_iprange_log` → 3 componentes (módulo + componente + opción)

**Transformaciones:**
```php
$eid = explode("_", $oid);                    // Divide el OID
$ucf_module = safe_ucfirst($eid[0]);          // "Firewall" (PascalCase)
$slc_module = safe_strtolower($eid[0]);       // "firewall" (lowercase)
$ucf_component = safe_ucfirst($eid[1]);       // "Iprange" (PascalCase)
$slc_component = safe_strtolower($eid[1]);    // "iprange" (lowercase)
$ucf_options = safe_ucfirst(@$eid[2]);        // "Log" (PascalCase, opcional)
$slc_options = safe_strtolower(@$eid[2]);     // "log" (lowercase, opcional)
```

---

## 5. Archivos Generados

### 5.1 index.php (Punto de Entrada)

**Ubicación final:** `app/Modules/{Module}/Views/{Component}/_View/index.php`

**Responsabilidad:**
- Punto de entrada para visualizar un registro individual
- Verifica permiso singular: `{module}-{component}-view`
- Redirige a `form.php` si tiene permiso
- Redirige a `deny.php` si no tiene permiso

**Variables disponibles:**
```php
$data['model'] = model("App\Modules\{Module}\Models\{Module}_{Component}");
$data['permissions']['singular'] = "firewall-iprange-view";
$data['permissions']['plural'] = "firewall-iprange-view-all";
$singular = $authentication->has_Permission($data['permissions']['singular']);
```

**Estructura de decisión:**
```php
if ($singular) {
    if (!empty($submited)) {
        // Usuario envió datos de validación
        $json = array(
            'breadcrumb' => view($breadcrumb, $data), 
            'main' => view($validator, $data), 
            'right' => ""
        );
    } else {
        // Mostrar formulario de visualización
        $json = array(
            'breadcrumb' => view($breadcrumb, $data), 
            'main' => view($form, $data), 
            'right' => ""
        );
    }
} else {
    // Acceso denegado
    $json = array(
        'breadcrumb' => view($breadcrumb, $data), 
        'main' => view($deny, $data), 
        'right' => ""
    );
}
echo json_encode($json);
```

---

### 5.2 form.php (Visualización del Registro)

**Ubicación final:** `app/Modules/{Module}/Views/{Component}/_View/form.php`

**Responsabilidad:**
- Mostrar los datos de un registro individual en campos de solo lectura
- Proporcionar botón "Editar" para acceder al editor
- Proporcionar botón "Atrás" para regresar
- Usar Bootstrap v5.3.3 para la presentación

**Flujo de operación:**
```php
// 1. Obtener el registro de la base de datos
$row = $model->get{SingularComponent}($oid);

// 2. Copiar valores a variables locales
foreach ($fields as $field) {
    $r[$field] = $row[$field];
}

// 3. Crear campos de solo lectura (no editables)
$f->fields[$field] = $f->get_FieldView($field, array(
    "value" => $r[$field],
    "proportion" => "col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12"
));

// 4. Crear botones (Editar, Atrás)
$f->fields["edit"] = $f->get_Button("edit", array(
    "href" => "/{module}/{component}/edit/{$oid}",
    "icon" => ICON_EDIT,
    "text" => lang("App.Edit"),
    "class" => "btn btn-secondary"
));

$f->fields["cancel"] = $f->get_Cancel("cancel", array(
    "href" => $back,
    "text" => lang("App.Cancel"),
    "type" => "secondary"
));

// 5. Agrupar campos en grupos (máximo 3 campos por fila)
$chunks = array_chunk($visible_fields, 3);
foreach ($chunks as $grupo => $fields_chunk) {
    $f->groups["g{$grupo}"] = $f->get_Group(array(
        "legend" => "",
        "fields" => ($f->fields[...])
    ));
}

// 6. Crear tarjeta Bootstrap con el formulario
$card = BS5::card([
    'headerTitle' => lang("{$module}_{$component}.view-title"),
    'headerButtons' => [BS5::button([...])],
    'content' => ["htmlContent" => $f]
]);
```

**Campos generados automáticamente:**
- **Campos de datos:** Todos los campos de la tabla se muestran como `get_FieldView()` (solo lectura)
- **Campo autor:** Se oculta con `add_HiddenField()`
- **Campos timestamp:** Se excluyen (created_at, updated_at, deleted_at)

**Estructura de tamaño:**
```
Cada fila ocupa 12 columnas (100% ancho):
- 3 campos por fila: col-xl-4 (33% cada uno)
- En tablets (md): col-md-4 (33% cada uno)
- En móviles (sm): col-sm-12 (100% cada uno)
- En extras pequeños: col-12 (100% cada uno)
```

---

### 5.3 processor.php (Procesamiento de Datos)

**Ubicación final:** `app/Modules/{Module}/Views/{Component}/_View/processor.php`

**Responsabilidad:**
- Procesar datos POST del formulario
- Verificar si el registro existe
- Mostrar mensaje de éxito o error
- Guardar cambios en la base de datos (si aplica)

**Flujo de operación:**
```php
// 1. Preparar datos desde el formulario
$d = array(
    "{field1}" => $f->get_Value("{field1}"),
    "{field2}" => $f->get_Value("{field2}"),
    "author" => safe_get_user(),
    ...
);

// 2. Buscar el registro en la BD
$row = $model->find($d["{primary_key}"]);

// 3. Verificar si existe
if (isset($row["{primary_key}"])) {
    // ÉXITO: El registro existe
    $_icon = BS5::icon(['icon' => 'circle-check', ...]);
    $_body = "<!-- Mensaje de éxito -->";
    
    $c = BS5::card([
        'header' => [
            'title' => lang("{$module}_{$component}.view-success-title"),
            'class' => 'bg-success border-success text-white'
        ],
        'content' => ['htmlContent' => $_content, 'class' => 'bg-success text-white'],
        'attributes' => ['class' => 'border-success shadow-sm'],
    ]);
} else {
    // ERROR: No existe el registro
    $_icon = BS5::icon(['icon' => 'triangle-exclamation', ...]);
    $_body = "<!-- Mensaje de error -->";
    
    $c = BS5::card([
        'header' => [
            'title' => lang("{$module}_{$component}.view-noexist-title"),
            'class' => 'bg-warning border-warning text-dark'
        ],
        'content' => ['htmlContent' => $_content, 'class' => 'bg-warning text-dark'],
        'attributes' => ['class' => 'border-warning shadow-sm'],
    ]);
}

echo($c);
```

**Variables de entrada:**
- `$f` → Servicio de formularios con datos POST
- `$model` → Instancia del modelo del componente
- `$data` → Array de datos globales

**Respuestas posibles:**
1. **Éxito (registro existe):** Tarjeta verde con ícono check
2. **Error (no existe):** Tarjeta naranja con ícono advertencia

---

### 5.4 validator.php (Validación de Datos)

**Ubicación final:** `app/Modules/{Module}/Views/{Component}/_View/validator.php`

**Responsabilidad:**
- Validar los datos recibidos del formulario
- Mostrar errores si la validación falla
- Llamar a processor.php si la validación es exitosa

**Flujo de operación:**
```php
// 1. Inicializar servicio de formularios
$f = service("forms", array("lang" => "{Module}_{Component}."));

// 2. Definir reglas de validación (comentadas por defecto)
// $f->set_ValidationRule("{field1}", "trim|required");
// $f->set_ValidationRule("{field2}", "trim|required");
// ...

// 3. Ejecutar validación
if ($f->run_Validation()) {
    // Validación exitosa: Ir a processor
    $c = view($component . '\processor', $parent->get_Array());
} else {
    // Validación fallida: Mostrar errores
    $c = $bootstrap->get_Card('validator', array(
        'class' => 'card-danger',
        'icon' => 'fa-duotone fa-triangle-exclamation',
        'text-class' => 'text-center',
        'text' => lang("App.validator-errors-message"),
        'errors' => $f->validation->listErrors(),
        'footer-class' => 'text-center',
        'voice' => "app/form-errors-message.mp3",
    ));
}

echo($c);
```

**Reglas de validación disponibles:**
```php
// Ejemplos de reglas comunes:
$f->set_ValidationRule("email", "trim|required|valid_email");
$f->set_ValidationRule("name", "trim|required|max_length[100]");
$f->set_ValidationRule("id", "trim|required|is_natural_no_zero");
```

---

### 5.5 breadcrumb.php (Navegación)

**Ubicación final:** `app/Modules/{Module}/Views/{Component}/_View/breadcrumb.php`

**Responsabilidad:**
- Mostrar ruta de navegación actual
- Permitir navegación hacia atrás

**Estructura:**
```php
use Higgs\Frontend\Bootstrap\v5_3_3\Bootstrap as BS5;

echo BS5::breadcrumb(['items' => [
    ['label' => '{module}', 'href' => '/{module}/'],
    ['label' => lang('App.{component}'), 
     'href' => '/{module}/{component}/home/'.lpk(), 
     'active' => true],
]]);
```

**Elementos generados:**
- **Primer nivel:** Link al módulo (`/{module}/`)
- **Segundo nivel:** Link al componente activo (`/{module}/{component}/home/`)
- **Marcado como activo:** Último elemento sin link

---

### 5.6 deny.php (Control de Acceso)

**Ubicación final:** `app/Modules/{Module}/Views/{Component}/_View/deny.php`

**Responsabilidad:**
- Mostrar pantalla de acceso denegado
- Diferencia entre usuario no autenticado vs. sin permisos

**Lógica de presentación:**
```php
use Higgs\Frontend\Bootstrap\v5_3_3\Bootstrap as BS5;

$continue = "/{$module}/{$component}/list/".lpk();

if ($authentication->get_LoggedIn()) {
    // CASO 1: Usuario AUTENTICADO pero SIN PERMISOS
    $_icon = BS5::icon(['icon' => 'ban', 'style' => 'duotone', 'size' => '4x']);
    $_body = '<div class="text-center py-3">' . $_icon . '</div>'
        . '<p class="text-center pb-2">' . lang('App.Access-denied-message') . '</p>';
    $_permissions = "<p class=\"text-center pb-2\">Permisos requeridos: " 
        . implode(" - ", $permissions) . "</p>";
    
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
            'content' => BS5::button([...]),
            'class' => 'bg-danger text-white d-flex justify-content-end',
        ],
        'attributes' => ['class' => 'border-danger shadow-sm'],
    ]);
} else {
    // CASO 2: Usuario NO AUTENTICADO
    $_icon = BS5::icon(['icon' => 'lock', 'style' => 'duotone', 'size' => '4x']);
    $_body = '<div class="text-center py-3">' . $_icon . '</div>'
        . '<p class="text-center pb-2">' . lang('App.login-required-message') . '</p>';
    
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
            'content' => BS5::button([...]),
            'class' => 'bg-danger text-white d-flex justify-content-end',
        ],
        'attributes' => ['class' => 'border-danger shadow-sm'],
    ]);
}

echo($card);
```

**Dos escenarios:**
1. **Autenticado sin permisos:** Ícono prohibido (ban), lista de permisos requeridos
2. **No autenticado:** Ícono candado (lock), mensaje de login requerido

---

## 6. Convenciones de Nombres

### 6.1 Rutas de Archivos

```
app/Modules/{ModuleName}/
├── Views/
│   └── {ComponentName}/
│       └── _View/
│           ├── index.php          (punto de entrada)
│           ├── form.php           (visualización)
│           ├── processor.php      (procesamiento)
│           ├── validator.php      (validación)
│           ├── breadcrumb.php     (navegación)
│           └── deny.php           (control acceso)
```

### 6.2 Nombres de Clases y Espacios

**Para 2 componentes (`firewall_iprange`):**
```php
Namespace: App\Modules\Firewall\Models
Clase: Firewall_Iprange
URL: /firewall/iprange/view/{id}
Permiso singular: firewall-iprange-view
Permiso plural: firewall-iprange-view-all
```

**Para 3 componentes (`firewall_iprange_log`):**
```php
Namespace: App\Modules\Firewall\Models
Clase: Firewall_Iprange_Log
URL: /firewall/iprange/log/view/{id}
Permiso singular: firewall-iprange-log-view
Permiso plural: firewall-iprange-log-view-all
```

### 6.3 Permisos

```
Singular (Principal en Viewer): {module}-{component}-view
Plural (Alternativo): {module}-{component}-view-all
```

---

## 7. Constantes y Variables Disponibles

### 7.1 En los Coders

```php
COMMENT_HR_VARS              // Comentario separador de variables
COMMENT_MODULECONTROLER_VARS // Documentación de variables heredadas
COMMENT_HR_BUILD             // Comentario separador de construcción
COMMENT_HR_MODELS            // Comentario separador de modelos
COMMENT_HR_GROUPS            // Comentario separador de grupos
COMMENT_HR_BUTTONS           // Comentario separador de botones
ICON_VIEW                    // Ícono de visualizar
ICON_EDIT                    // Ícono de editar
ICON_DELETE                  // Ícono de eliminar
ICON_ADD                     // Ícono de añadir
ICON_BACK                    // Ícono de atrás
ICON_INFO                    // Ícono de información
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
$oid             // Identificador de registro individual (ej: 123)
$data            // Array con datos globales del módulo
$model           // Instancia del modelo (ej: Firewall_Iprange)
$row             // Datos del registro de la BD
$r               // Array de variables locales
```

### 7.3 Funciones Helper

```php
safe_ucfirst($string)       // Convierte a PascalCase
safe_strtolower($string)    // Convierte a lowercase
lpk()                       // Obtiene el token CSRF
safe_get_user()            // Obtiene el usuario actual
lang($key)                 // Obtiene cadena de idioma
service($name)             // Obtiene un servicio
model($class)              // Obtiene una instancia de modelo
view($view, $data)         // Renderiza una vista
```

---

## 8. Uso Paso a Paso

### 8.1 Acceder al Generador

```
1. URL: /development/generators/viewer/
2. O desde panel: Nexus > Desarrollo > Generadores > Viewer
3. Seleccionar tabla en combo: firewall_iprange
4. El generador carga automáticamente
```

### 8.2 Revisar el Código Generado

```
1. El formulario muestra el código PHP combinado de los 6 coders
2. Copiar/revisar la ruta de destino: app/Modules/Firewall/Views/IpRange/_View
3. Editar si es necesario (personalización de código)
```

### 8.3 Guardar los Archivos

```
1. Click en "Guardar Editor"
2. Validación de campos requeridos
3. Creación de 6 archivos PHP en:
   app/Modules/{Module}/Views/{Component}/_View/
4. Mensaje de éxito/advertencia
```

### 8.4 Verificar los Archivos Creados

```bash
ls -la app/Modules/Firewall/Views/IpRange/_View/
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

### 9.1 Agregar Validación a Campos

En el archivo generado `_View/validator.php`:

```php
// Descomentar y personalizar reglas:
$f->set_ValidationRule("email", "trim|required|valid_email");
$f->set_ValidationRule("name", "trim|required|max_length[100]");
$f->set_ValidationRule("phone", "trim|valid_phone");
```

### 9.2 Modificar Campos Mostrados

En el archivo generado `_View/form.php`, filtrar campos:

```php
// Excluir ciertos campos de la visualización:
$skipped = ["author", "created_at", "updated_at", "deleted_at", "sensitive_field"];
$visible_fields = array_values(array_filter($fields, 
    fn($f) => !in_array($f, $skipped)
));
```

### 9.3 Cambiar Disposición de Campos

En `_View/form.php`, modificar campos por fila:

```php
// De 3 campos por fila a 2:
$chunks = array_chunk($visible_fields, 2);

// De 3 campos por fila a 4:
$chunks = array_chunk($visible_fields, 4);
```

Y ajustar el tamaño de columnas Bootstrap:

```php
// Para 2 campos por fila (50% cada uno):
"proportion" => "col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12"

// Para 4 campos por fila (25% cada uno):
"proportion" => "col-xl-3 col-lg-3 col-md-3 col-sm-6 col-12"
```

### 9.4 Personalizar URL de Edición

En `_View/form.php`, cambiar href del botón Editar:

```php
// Por defecto:
"href" => "/{$module}/{$component}/edit/{$oid}"

// Personalizado:
"href" => "/{$module}/{$component}/edit/{$oid}?action=full"
```

### 9.5 Cambiar Estilos de Tarjeta

En `_View/form.php`:

```php
// Cambiar color de encabezado:
'headerTitle' => lang("{$module}_{$component}.view-title"),
'headerClass' => 'bg-primary text-white',  // Añadir esto

// En processor.php, cambiar colores de éxito/error:
'header' => [
    'title' => lang("{$module}_{$component}.view-success-title"),
    'class' => 'bg-success border-success text-white'  // Cambiar class
],
```

### 9.6 Agregar Campos Calculados o Derivados

En `_View/form.php`, añadir campos después de cargar la fila:

```php
// Después de: $row = $model->get{Component}($oid);
$r["full_name"] = $row["first_name"] . " " . $row["last_name"];
$r["status_label"] = get_status_label($row["status"]);

// Luego crear campo:
$f->fields["full_name"] = $f->get_FieldView("full_name", array(
    "value" => $r["full_name"],
    "proportion" => "col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12"
));
```

---

## 10. Detalles Técnicos

### 10.1 Modelo de Datos

El Viewer asume que el modelo tiene estos métodos:

```php
// Obtener un registro por ID
$row = $model->get{SingularComponent}($id);

// O usar find() directo
$row = $model->find($id);

// Acceso a los campos:
$row['id']
$row['name']
$row['author']
$row['created_at']
$row['updated_at']
```

### 10.2 Seguridad: Autenticación y Autorización

**En index.php:**
```php
// Verificación de permiso:
$singular = $authentication->has_Permission($data['permissions']['singular']);

// Solo usuarios con permiso 'firewall-iprange-view' pueden ver
if ($singular) {
    // Mostrar formulario
} else {
    // Mostrar deny.php
}
```

**En form.php:**
```php
// Los campos se muestran con get_FieldView() (no editables)
// Solo lectura garantizada a nivel de renderizado
```

### 10.3 Gestión de Caché

El generador utiliza caching para acelerar operaciones:

```php
// En coders:
$db = Database::connect("default");
$fields = $db->getFieldNames($id);  // Obtiene campos de la tabla

// El nombre de la tabla se obtiene del OID:
// firewall_iprange → busca tabla con ese nombre o variaciones
```

### 10.4 Manejo de Errores

**En form.php:**
```php
// Si no existe el registro:
// get_FieldView() mostrará valores vacíos o NULL
// No hay validación en el lado del formulario
```

**En processor.php:**
```php
// Si no existe el registro:
if (!isset($row[$primary_key])) {
    // Mostrar tarjeta de error (warning)
    // Proposición: puede llevar a verificación adicional
}
```

---

## 11. Errores Comunes

### Error 1: Falta de Archivo de Idioma

**Síntoma:** Mensajes muestran `{Module}_{Component}.view-title`

**Causa:** No existe archivo de idioma en `/app/Language/`

**Solución:**
```php
// Crear archivo: app/Language/es/{module}_{component}.php
// O crear en: app/Modules/{Module}/Language/es/

$lang = [
    'view-title' => 'Ver {Component}',
    'view-success-title' => 'Excelente',
    'view-success-message' => 'Datos cargados correctamente',
    'view-noexist-title' => 'No existe',
    'view-noexist-message' => 'El registro solicitado no existe',
];
```

### Error 2: Campo Primary Key no Detectado

**Síntoma:** El procesador no encuentra el registro

**Causa:** El primer campo no es la clave primaria

**Solución:** En `processor.php`, cambiar:
```php
// De:
$row = $model->find($d["{$fields[0]}"]);

// A:
$row = $model->find($d["id"]);  // O el nombre correcto de PK
```

### Error 3: Permisos Insuficientes

**Síntoma:** Muestra deny.php para todos

**Causa:** El usuario no tiene el permiso `{module}-{component}-view`

**Solución:** Asignar permiso en la tabla de permisos del sistema

### Error 4: OID Malformado

**Síntoma:** Error al obtener campos de la tabla

**Causa:** El OID no coincide con nombres de tabla reales

**Solución:** Verificar estructura: `module_component` o `module_component_option`

### Error 5: Rutas de Archivos Incorrectas

**Síntoma:** Los archivos se crean en ubicación incorrecta

**Causa:** APPPATH o estructura de carpetas diferente

**Solución:** Verificar en form.php la línea:
```php
$pathfiles = APPPATH . "Modules/{$ucf_module}/Views/{$ucf_component}/_View";
```

---

## 12. Ejemplo Completo

### Escenario: Crear Viewer para IP Ranges

**OID:** `firewall_iprange`

**Transformaciones:**
```php
$eid = ["firewall", "iprange"]
$ucf_module = "Firewall"
$slc_module = "firewall"
$ucf_component = "Iprange"
$slc_component = "iprange"
```

**Rutas generadas:**
```
Módulo: app/Modules/Firewall/
Componente: app/Modules/Firewall/Views/Iprange/
Destino: app/Modules/Firewall/Views/Iprange/_View/
Modelo: App\Modules\Firewall\Models\Firewall_Iprange
Permiso: firewall-iprange-view
```

### Proceso Completo

**Paso 1: Acceder al generador**
```
URL: /development/generators/viewer/
Seleccionar: firewall_iprange
```

**Paso 2: Revisar código**
```
El formulario muestra 6 coders:
1. index.php - Verifica permisos
2. form.php - Muestra los datos
3. processor.php - Procesa submission
4. validator.php - Valida datos
5. breadcrumb.php - Navegación
6. deny.php - Control acceso
```

**Paso 3: Guardar archivos**
```
Click "Guardar Editor"
Se crean 6 archivos en: app/Modules/Firewall/Views/Iprange/_View/
```

**Paso 4: Usar la vista generada**
```
URL: /firewall/iprange/view/123
Mostrará: Registro #123 con todos sus campos
```

**Paso 5: Navegar**
```
Breadcrumb: Firewall > Iprange (activo)
Botón Editar: /firewall/iprange/edit/123
Botón Atrás: Regresa a la URL anterior
```

---

## 13. Diagrama Operacional Completo

```
┌────────────────────────────────────────────────────────────────────────────┐
│                     GENERADOR VIEWER - FLUJO COMPLETO                      │
└────────────────────────────────────────────────────────────────────────────┘

    USUARIO ACCEDE AL GENERADOR (/development/generators/viewer/)
                         │
                         ↓
              ┌───────────────────────┐
              │ Verificar Permiso:    │
              │ development-access    │
              └─┬─────────────────┬───┘
                │                 │
             SÍ │                 │ NO
                ↓                 ↓
        ┌────────────────┐  ┌──────────────────┐
        │ Mostrar form.php   │ Mostrar deny.php │
        │ (editor visual) │  │ (acceso denegado)│
        └────────┬─────────┘  └──────────────────┘
                 │
                 │ Usuario revisa código
                 │ y hace click "Guardar"
                 ↓
        ┌─────────────────────────┐
        │ POST datos del formulario│
        └────────┬────────────────┘
                 │
                 ↓
        ┌─────────────────────────┐
        │ validator.php           │
        │ Validar campos requeridos│
        └─┬───────────────────┬───┘
          │                   │
       FAIL│                   │PASS
          ↓                   ↓
    ┌──────────────┐  ┌──────────────────┐
    │ Mostrar      │  │ processor.php    │
    │ errores      │  │ Escribir archivos│
    └──────────────┘  └────────┬─────────┘
                                │
                                ├─ Crear directorio _View
                                ├─ Escribir index.php
                                ├─ Escribir form.php
                                ├─ Escribir processor.php
                                ├─ Escribir validator.php
                                ├─ Escribir breadcrumb.php
                                ├─ Escribir deny.php
                                │
                                ↓
                    ┌──────────────────────┐
                    │ Mostrar mensaje      │
                    │ de éxito/advertencia │
                    └──────────────────────┘


┌────────────────────────────────────────────────────────────────────────────┐
│                     FLUJO DE EJECUCIÓN DE ARCHIVOS GENERADOS               │
└────────────────────────────────────────────────────────────────────────────┘

    Usuario accede a: /{module}/{component}/view/{id}
                         │
                         ↓
                    index.php
    ┌──────────────────────────────────────┐
    │ Verificar: {module}-{component}-view │
    └─┬────────────────────────────────┬───┘
      │                                │
    SÍ│                                │NO
      │                                │
      ↓                                ↓
   ¿Submitido?            deny.php
   ┌┬───────────┬┐        (Acceso
   │            │         denegado)
   ↓ NO         ↓ SÍ
form.php    validator.php
│           │
│           ├─ Valida datos
│           │
│           ├─ Si falla: Muestra errores
│           │
│           └─ Si pasa: processor.php
│
└───────────────────────────────┬──────────────────┘
                                │
                                ↓
                    Mostrar tarjeta resultado
                    (éxito o error)


┌────────────────────────────────────────────────────────────────────────────┐
│                        ESTRUCTURA DE CARPETAS GENERADA                     │
└────────────────────────────────────────────────────────────────────────────┘

app/Modules/
└── {Module}/
    └── Views/
        └── {Component}/
            └── _View/
                ├── index.php          [Entrada principal]
                ├── form.php           [Mostrar datos]
                ├── processor.php      [Procesar]
                ├── validator.php      [Validar]
                ├── breadcrumb.php     [Navegación]
                └── deny.php           [Denegado]
```

---

## 14. Resumen Operacional

| Aspecto | Descripción |
|---------|------------|
| **Tipo de Generador** | Viewer (visualización de registro individual) |
| **Archivos Generados** | 6 archivos PHP |
| **Ubicación** | `app/Modules/{Module}/Views/{Component}/_View/` |
| **Permiso Requerido** | `development-access` (para generar) |
| **Permiso de Uso** | `{module}-{component}-view` (para usar) |
| **OID Válido** | `module_component` o `module_component_option` |
| **Punto de Entrada** | `index.php` |
| **Flujo Principal** | index → form/validator → processor → resultado |
| **Bootstrap** | v5.3.3 (con clase BS5) |
| **Idioma** | Multilenguaje (lang key) |
| **Autenticación** | Por permiso en `index.php` |
| **Validación** | Comentada por defecto (activar si necesario) |
| **Seguridad** | Campos read-only, no editables en form.php |
| **Caché** | Uso opcional en model queries |
| **URL Pattern** | `/{module}/{component}/view/{id}` |
| **Respuesta** | JSON (breadcrumb, main, right) |
| **Personalización** | Editable en el generador antes de guardar |

---

## 15. Referencias Rápidas

### Comandos Útiles

```bash
# Verificar archivos generados
ls -la app/Modules/Firewall/Views/Iprange/_View/

# Ver contenido de un archivo
cat app/Modules/Firewall/Views/Iprange/_View/form.php

# Editar un archivo generado
nano app/Modules/Firewall/Views/Iprange/_View/form.php

# Cambiar permisos si es necesario
chmod 664 app/Modules/Firewall/Views/Iprange/_View/*.php
```

### Ubicaciones Importantes

```
Generador:        /development/generators/viewer/
Archivos fuente:  /Modules/Development/Views/Generators/Viewer/
Archivos coders:  /Modules/Development/Views/Generators/Viewer/coders/
Archivos generados: /Modules/{Module}/Views/{Component}/_View/
Idioma:           /Language/es/ o /Modules/{Module}/Language/es/
```

### Funciones Clave

```php
// En los archivos generados
$authentication->has_Permission($permission)  // Verificar permiso
$model->get{Component}($id)                   // Obtener registro
$f->set_ValidationRule()                      // Definir validación
$f->get_FieldView()                           // Campo de solo lectura
BS5::card()                                   // Tarjeta Bootstrap
BS5::breadcrumb()                             // Breadcrumb
lang($key)                                    // Traducción
view($view, $data)                            // Renderizar vista
```

---

**Fin de la Guía Viewer Generator**

Versión: 1.0 | Actualización: 2026-05-06 | Autor: Sistema de Documentación
