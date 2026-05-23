# Guía Completa: Generador de Cruds "Editor" en Higgs Framework

## 1. Introducción

El **Generador de Editors** es una herramienta automatizada que genera vistas completas para editar datos existentes en un registro. Crea seis archivos PHP principales que manejan:

- **Formulario de edición** con campos de entrada
- **Validación de datos** antes de guardar
- **Procesamiento y actualización** de registros en base de datos
- **Control de permisos** (Deny)
- **Navegación de breadcrumb**
- **Lógica de autorización** (autoridad del registro)

El Editor es el complemento perfecto del Creator (crear nuevos registros) y permite modificar registros existentes con control fino de permisos y validación.

---

## 2. Arquitectura General del Generador

```
/Editor/
├── index.php                 ← Punto de entrada (verifica permisos y autoridad)
├── form.php                  ← Formulario que muestra el código a generar
├── validator.php             ← Valida los campos del formulario
├── processor.php             ← Procesa y escribe los archivos generados
├── breadcrumb.php            ← Breadcrumb de la página del generador
├── deny.php                  ← Página de acceso denegado
└── coders/
    ├── index.php             ← Genera código para index.php
    ├── form.php              ← Genera código para el formulario (form.php)
    ├── processor.php         ← Genera código para procesar datos (processor.php)
    ├── validator.php         ← Genera código para validar datos (validator.php)
    ├── breadcrumb.php        ← Genera código para breadcrumb.php
    └── deny.php              ← Genera código para deny.php
```

---

## 3. Flujo de Trabajo del Generador

### 3.1 Etapa 1: Verificación de Permisos y Autoridad (index.php)

```
┌─────────────────────────────────────────┐
│ Usuario accede al editor                │
└──────────────┬──────────────────────────┘
               │
               ↓
       ┌──────────────────────┐
       │ ¿Tiene permiso       │
       │ singular o plural?   │
       └─┬──────────┬─────┬───┘
         │          │     │
    SÍ  │          │     │ NO
         ↓          ↓     ↓
      [Verif.   [Verif.  [Mostrar
       Autor]    Plural]   Deny]
         │          │
         └──┬───────┘
            │
            ↓
       ┌─────────────────┐
       │ ¿Tiene          │
       │ autoridad?      │
       └─┬───────────┬───┘
         │           │
      SÍ │           │ NO
         ↓           ↓
    [Ver Formulario] [Deny]
```

**Verificación:**
- Se comprueba el permiso: `{module}-{component}-edit` (singular)
- Se comprueba también: `{module}-{component}-edit-all` (plural)
- Se valida que el usuario sea autoridad (propietario del registro)
- Si falta permiso O no tiene autoridad → Muestra `deny.php`
- Si tiene permiso Y autoridad → Muestra `form.php`

---

### 3.2 Etapa 2: Mostrar Formulario (form.php)

El formulario contiene:

1. **Ruta de destino** (readonly):
   - Ejemplo: `app/Modules/Firewall/Views/IpRange/_Editor`
   - Se construye con: `APPPATH . "Modules/{ModuleName}/Views/{ComponentName}/_Editor"`

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
$f->set_ValidationRule("cform", "trim|required");
$f->set_ValidationRule("cprocessor", "trim|required");
$f->set_ValidationRule("cvalidator", "trim|required");
$f->set_ValidationRule("cbreadcrumb", "trim|required");
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
    "{$pathfiles}/form.php" => urldecode($cform),
    "{$pathfiles}/processor.php" => urldecode($cprocessor),
    "{$pathfiles}/validator.php" => urldecode($cvalidator),
    "{$pathfiles}/breadcrumb.php" => urldecode($cbreadcrumb),
    "{$pathfiles}/deny.php" => urldecode($cdeny),
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

**Ubicación final:** `app/Modules/{Module}/Views/{Component}/_Editor/index.php`

**Responsabilidad:**
- Punto de entrada para editar un registro
- Verifica el permiso `{module}-{component}-edit` (singular) O `{module}-{component}-edit-all` (plural)
- Valida que el usuario sea la autoridad (propietario) del registro
- Redirige a `form.php` si tiene permisos y autoridad
- Redirige a `deny.php` si no tiene permisos O no es autoridad

**Variables disponibles:**
```php
$data['permissions']['singular'] = "firewall-iprange-edit";
$data['permissions']['plural'] = "firewall-iprange-edit-all";
$singular = $authentication->has_Permission($data['permissions']['singular']);
$plural = $authentication->has_Permission($data['permissions']['plural']);
$author = $data['model']->getAuthority($oid, safe_get_user());
$authority = ($singular && $author) ? true : false;  // Debe tener permiso Y ser autoridad
```

**Estructura:**
```php
if ($plural || $authority) {  // Si tiene permiso plural O es autoridad
    if (!empty($submited)) {
        // Muestra validador
        $json = array(
            'breadcrumb' => view($breadcrumb, $data),
            'main' => view($validator, $data),
            'right' => "",
            'main_template' => 'c8c4'
        );
    } else {
        // Muestra formulario
        $json = array(
            'breadcrumb' => view($breadcrumb, $data),
            'main' => view($form, $data),
            'right' => "",
            'main_template' => 'c8c4'
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

**Características principales:**
- Valida autoridad del registro: `$model->getAuthority($oid, $userId)`
- Permite acceso si tiene permiso plural O (permiso singular AND es autoridad)
- Responde con JSON para carga dinámica
- Template `c8c4` para distribución de columnas

---

### 5.2 form.php (El Formulario)

**Ubicación final:** `app/Modules/{Module}/Views/{Component}/_Editor/form.php`

**Responsabilidad:**
- Mostrar el formulario de edición
- Cargar datos actuales del registro
- Crear campos para cada columna de la tabla
- Manejar campos especiales (author, dates)
- Mostrar botones de guardar y cancelar

**Características principales:**

#### a) Carga de Datos Actuales
```php
$row = $model->get{SingularComponent}($oid);  // Obtiene registro actual
```

#### b) Inicialización de Variables de Formulario
```php
$r["author"] = $f->get_Value("author", safe_get_user());
$r["date"] = $f->get_Value("date", service("dates")::get_Date());
$r["time"] = $f->get_Value("time", service("dates")::get_Time());
$r["field1"] = $f->get_Value("field1", $row["field1"]);  // Cargar valor actual
$r["field2"] = $f->get_Value("field2", $row["field2"]);
// ... más campos
$back = $f->get_Value("back", $server->get_Referer());  // Guardar URL de retorno
```

#### c) Creación de Campos de Formulario
```php
// Campos ocultos
$f->add_HiddenField("back", $back);
$f->add_HiddenField("author", $r["author"]);

// Campos visibles
$f->fields["field1"] = $f->get_FieldText("field1", array(
    "value" => $r["field1"],
    "proportion" => "col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12"
));

// Botones
$f->fields["cancel"] = $f->get_Cancel("cancel", array(
    "href" => $back,
    "text" => lang("App.Cancel"),
    "type" => "secondary",
    "proportion" => "col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 padding-right"
));

$f->fields["submit"] = $f->get_Submit("submit", array(
    "value" => lang("App.Edit"),  // "Editar"
    "proportion" => "col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 padding-left"
));
```

#### d) Agrupación de Campos
```php
// Agrupar campos en grupos de 3 columnas
$f->groups["g1"] = $f->get_Group(array(
    "legend" => "",
    "fields" => $f->fields["field1"] . $f->fields["field2"] . $f->fields["field3"]
));

// Botones separados
$f->groups["gy"] = $f->get_GroupSeparator();
$f->groups["gz"] = $f->get_Buttons(array(
    "fields" => $f->fields["submit"] . $f->fields["cancel"]
));
```

#### e) Construcción de Tarjeta Bootstrap
```php
$card = BS5::card([
    'headerTitle' => lang("{$module}_{$component}.edit-title"),
    'headerButtons' => [BS5::button([
        'content' => BS5::icon(['icon' => 'arrow-left', 'style' => 'duotone']),
        'variant' => 'secondary',
        'size' => 'sm',
        'attributes' => ['href' => $back]
    ])],
    'content' => ['htmlContent' => $f],
]);

echo($card);
```

**Variables de contexto:**
```php
$authentication    // Servicio de autenticación
$bootstrap         // Servicio Bootstrap
$dates             // Servicio de fechas
$strings           // Servicio de cadenas
$request           // Servicio de solicitud
$server            // Servicio de servidor (para referer)
$parent            // Controlador padre (para $data)
$model             // Modelo de datos para el componente
$oid               // ID del registro a editar
$data              // Array con datos globales del módulo
```

---

### 5.3 processor.php (Procesamiento y Actualización)

**Ubicación final:** `app/Modules/{Module}/Views/{Component}/_Editor/processor.php`

**Responsabilidad:**
- Recopilar datos del formulario
- Actualizar el registro en la base de datos
- Mostrar mensaje de éxito o error
- Permitir navegar de vuelta al listado

**Características principales:**

#### a) Recopilación de Datos
```php
$f = service("forms", array("lang" => "{$module}_{$component}."));
$model = model("App\Modules\{$Module}\Models\{$Module}_{$Component}");

$d = array(
    "id" => $f->get_Value("id"),
    "author" => safe_get_user(),  // Usar autor actual
    "field1" => $f->get_Value("field1"),
    "field2" => $f->get_Value("field2"),
    // ... más campos
);
```

#### b) Búsqueda de Registro Existente
```php
$row = $model->find($d["id"]);  // Buscar el registro actual
```

#### c) Actualización de Datos
```php
if (is_array($row)) {
    $edit = $model->update($d['id'], $d);  // Actualizar registro
    // ... mostrar éxito
} else {
    // ... mostrar error (registro no encontrado)
}
```

#### d) Pantalla de Éxito
```php
$_icon = (string)BS5::icon(['icon' => 'circle-check', 'style' => 'duotone', 'size' => '4x']);
$_body = '<div class="text-center py-3">' . $_icon . '</div>'
    . '<p class="text-center pb-2">' . lang("{$module}_{$component}.edit-success-message") . '</p>'
    . '<div class="text-center pb-3">' . (string)BS5::button([
        'content' => lang('App.Continue'),
        'variant' => 'success',
        'size' => 'md',
        'attributes' => ['href' => $l['back']]
    ]) . '</div>';

$c = BS5::card([
    'header' => [
        'title' => lang("{$module}_{$component}.edit-success-title"),
        'class' => 'bg-success border-success text-white'
    ],
    'content' => [
        'htmlContent' => (string)BS5::col(['attributes' => ['class' => 'text-center'], 'htmlContent' => $_body]),
        'class' => 'bg-success text-white'
    ],
    'attributes' => ['class' => 'border-success shadow-sm'],
]);

echo($c);
$model->invalidateSearchCache();  // Limpiar caché
```

#### e) Pantalla de Error
```php
if (!is_array($row)) {
    $_icon = (string)BS5::icon(['icon' => 'triangle-exclamation', 'style' => 'duotone', 'size' => '4x']);
    $_body = '<div class="text-center py-3">' . $_icon . '</div>'
        . '<p class="text-center pb-2">' . sprintf(lang("{$module}_{$component}.edit-noexist-message"), $d['id']) . '</p>'
        . '<div class="text-center pb-3">' . (string)BS5::button([
            'content' => lang('App.Continue'),
            'variant' => 'warning',
            'size' => 'md',
            'attributes' => ['href' => $l['back']]
        ]) . '</div>';

    $c = BS5::card([
        'header' => [
            'title' => lang("{$module}_{$component}.edit-noexist-title"),
            'class' => 'bg-warning border-warning text-dark'
        ],
        'content' => [
            'htmlContent' => (string)BS5::col(['attributes' => ['class' => 'text-center'], 'htmlContent' => $_body]),
            'class' => 'bg-warning text-dark'
        ],
        'attributes' => ['class' => 'border-warning shadow-sm'],
    ]);

    echo($c);
}
```

**Flujo:**
1. Recibe datos del formulario
2. Busca el registro a actualizar
3. Si existe → Actualiza y muestra éxito
4. Si no existe → Muestra advertencia
5. Limpia caché de búsqueda

---

### 5.4 validator.php (Validación)

**Ubicación final:** `app/Modules/{Module}/Views/{Component}/_Editor/validator.php`

**Responsabilidad:**
- Validar datos antes de procesar
- Mostrar errores si la validación falla
- Llamar a `processor.php` si la validación pasa

**Características principales:**

#### a) Definición de Reglas de Validación
```php
$f = service("forms", array("lang" => "{$module}_{$component}."));

// Se definen reglas según necesidad (comentadas por defecto)
// $f->set_ValidationRule("field1", "trim|required");
// $f->set_ValidationRule("field2", "trim|required|numeric");
// $f->set_ValidationRule("email", "trim|required|valid_email");
```

#### b) Ejecución de Validación
```php
if ($f->run_Validation()) {
    // Validación pasó → Procesar
    $c = view($component . '\processor', $parent->get_Array());
} else {
    // Validación falló → Mostrar errores
    $_icon_col = BS5::row([
        'attributes' => ['class' => 'text-center py-3'],
        'content' => BS5::icon(['icon' => 'triangle-exclamation', 'style' => 'duotone', 'size' => '2xl'])
    ]);
    
    $_msg_col = BS5::row([
        'attributes' => ['class' => 'text-center pb-2'],
        'htmlContent' => lang('App.validator-errors-message')
    ]);
    
    $_errors_col = BS5::row([
        'attributes' => ['class' => 'pb-2'],
        'htmlContent' => $f->validation->listErrors()
    ]);
    
    $_content = BS5::col([
        'attributes' => ['class' => 'justify-content-center'],
        'htmlContent' => $_icon_col . $_msg_col . $_errors_col
    ]);
    
    $c = BS5::card([
        'headerTitle' => lang('App.validator-errors-title'),
        'headerClass' => 'bg-danger text-white',
        'content' => ['htmlContent' => $_content],
        'attributes' => ['class' => 'border-danger shadow-sm'],
    ]);
    
    // Mostrar formulario nuevamente para que corrija
    $c .= view($component . '\form', $parent->get_Array());
}

echo($c);
```

**Características:**
- Las reglas están comentadas por defecto para máxima flexibilidad
- Los desarrolladores pueden descomentar y personalizar según necesidad
- Muestra errores con ícono y mensaje
- Vuelve a mostrar el formulario si falla
- Procesa si la validación pasa

---

### 5.5 breadcrumb.php (Navegación)

**Ubicación final:** `app/Modules/{Module}/Views/{Component}/_Editor/breadcrumb.php`

**Responsabilidad:**
- Mostrar ruta de navegación actual
- Permitir navegación hacia atrás
- Indicar el contexto actual (edición)

**Estructura:**
```php
use Higgs\Frontend\Bootstrap\v5_3_3\Bootstrap as BS5;

echo BS5::breadcrumb(['items' => [
    ['label' => '{module}', 'href' => '/{module}/'],
    ['label' => lang('App.{component}'), 'href' => '/{module}/{component}/home/'.lpk(), 'active' => true],
]]);
```

**Características:**
- Usa Bootstrap 5.3.3
- Navega al módulo y al componente
- Marca el último elemento como activo
- Mantiene contexto consistente

---

### 5.6 deny.php (Control de Acceso)

**Ubicación final:** `app/Modules/{Module}/Views/{Component}/_Editor/deny.php`

**Responsabilidad:**
- Mostrar pantalla de acceso denegado
- Diferencia entre usuario no autenticado vs. sin permisos
- Mostrar permisos requeridos si está autenticado

**Lógica:**
```php
use Higgs\Frontend\Bootstrap\v5_3_3\Bootstrap as BS5;

$continue = "/{$module}/{$component}/list/".lpk();

if ($authentication->get_LoggedIn()) {
    // Usuario AUTENTICADO pero SIN PERMISOS o SIN AUTORIDAD
    $_icon = (string)BS5::icon(['icon' => 'ban', 'style' => 'duotone', 'size' => '4x']);
    $_body = '<div class="text-center py-3">' . $_icon . '</div>'
        . '<p class="text-center pb-2">' . lang('App.Access-denied-message') . '</p>';
    $_permissions = "<p class=\"text-center pb-2\">Permisos requeridos: " . implode(" - ", $permissions) . "</p>";
    $_continue = BS5::button([
        'content' => lang('App.Continue'),
        'variant' => 'danger',
        'size' => 'md',
        'attributes' => ['href' => $continue]
    ]);
    
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
        'attributes' => ['class' => 'border-danger shadow-sm']
    ]);
} else {
    // Usuario NO AUTENTICADO
    $_icon = (string)BS5::icon(['icon' => 'lock', 'style' => 'duotone', 'size' => '4x']);
    $_body = '<div class="text-center py-3">' . $_icon . '</div>'
        . '<p class="text-center pb-2">' . lang('App.login-required-message') . '</p>';
    $_continue = BS5::button([
        'content' => lang('App.Continue'),
        'variant' => 'danger',
        'size' => 'md',
        'attributes' => ['href' => $continue]
    ]);
    
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
        'attributes' => ['class' => 'border-danger shadow-sm']
    ]);
}

echo($card);
```

**Características:**
- Detecta si el usuario está autenticado
- Muestra diferentes mensajes según el caso
- Explica los permisos requeridos
- Botón para volver al listado
- Diseño consistente con Bootstrap 5.3.3

---

## 6. Convenciones de Nombres

### 6.1 Rutas de Archivos

```
app/Modules/{ModuleName}/
├── Views/
│   └── {ComponentName}/
│       └── _Editor/
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
URL: /firewall/iprange/{id}
Permisos: 
  - firewall-iprange-edit (singular - autoridad)
  - firewall-iprange-edit-all (plural - administrador)
```

**Para 3 componentes (`firewall_iprange_log`):**
```php
Namespace: App\Modules\Firewall\Models
Clase: Firewall_Iprange_Log
URL: /firewall/iprange/log/{id}
Permisos:
  - firewall-iprange-log-edit (singular - autoridad)
  - firewall-iprange-log-edit-all (plural - administrador)
```

### 6.3 Permisos

```
Singular (Requiere autoridad): {module}-{component}-edit
Plural (Admin total): {module}-{component}-edit-all
Combinación: (singular AND es_autoridad) OR plural
```

---

## 7. Constantes y Variables Disponibles

### 7.1 En los Coders

```php
COMMENT_HR_VARS                    // Comentario separador de variables
COMMENT_MODULECONTROLER_VARS       // Documentación de variables heredadas
COMMENT_HR_BUILD                   // Comentario separador de construcción
COMMENT_HR_MODELS                  // Comentario separador de modelos
COMMENT_HR_FIELDS                  // Comentario separador de campos
COMMENT_HR_GROUPS                  // Comentario separador de grupos
COMMENT_HR_BUTTONS                 // Comentario separador de botones
```

### 7.2 Variables de Instancia

```php
$parent                // Instancia de ModuleController
$authentication        // Servicio de autenticación
$request               // Servicio de solicitud (GET/POST)
$bootstrap             // Servicio Bootstrap
$dates                 // Servicio de fechas
$strings               // Servicio de cadenas
$server                // Servicio de servidor (referer, etc)
$oid                   // ID del registro a editar
$data                  // Array con datos globales del módulo
$model                 // Modelo de datos del componente
```

### 7.3 Funciones de Ayuda

```php
safe_ucfirst($text)               // Convertir a PascalCase
safe_strtolower($text)            // Convertir a minúsculas
safe_get_user()                   // Obtener ID del usuario actual
lpk()                             // Get locale/partner key
```

---

## 8. Uso Paso a Paso

### 8.1 Acceder al Generador

```
1. URL: /development/generators/editor/
2. Seleccionar tabla: firewall_iprange (del combo)
3. El generador carga automáticamente
```

### 8.2 Revisar el Código Generado

```
1. El formulario muestra el código PHP combinado
2. Copiar/revisar la ruta de destino
3. Editar si es necesario (código personalizado)
```

### 8.3 Guardar los Archivos

```
1. Click en "Guardar Editor"
2. Validación de campos requeridos
3. Creación de archivos en:
   app/Modules/{Module}/Views/{Component}/_Editor/
4. Mensaje de éxito/advertencia
```

### 8.4 Verificar los Archivos Creados

```bash
ls -la app/Modules/Firewall/Views/IpRange/_Editor/
```

Debería mostrar:
```
-rw-rw-r-- index.php
-rw-rw-r-- form.php
-rw-rw-r-- processor.php
-rw-rw-r-- validator.php
-rw-rw-r-- breadcrumb.php
-rw-rw-r-- deny.php
```

### 8.5 Agregar Rutas en el Controlador

En el controlador del módulo, agregar rutas para el editor:

```php
$routes->get("{component}/edit/(:num)", "Components\{Component}Controller::edit/$1");
$routes->post("{component}/edit/(:num)", "Components\{Component}Controller::edit/$1");
```

### 8.6 Acceder al Editor

```
URL: /{module}/{component}/edit/{id}
```

Por ejemplo:
```
/firewall/iprange/edit/123
```

---

## 9. Personalización

### 9.1 Modificar Campos del Formulario

En `form.php`, personalizar los campos según necesidad:

```php
// Campos de texto simples
$f->fields["name"] = $f->get_FieldText("name", array(
    "value" => $r["name"],
    "proportion" => "col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12"
));

// Campo de área de texto
$f->fields["description"] = $f->get_FieldArea("description", array(
    "value" => $r["description"],
    "rows" => 4,
    "proportion" => "col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12"
));

// Campo select
$f->fields["status"] = $f->get_FieldSelect("status", array(
    "value" => $r["status"],
    "options" => ["active" => "Activo", "inactive" => "Inactivo"],
    "proportion" => "col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12"
));
```

### 9.2 Personalizar Validación

En `validator.php`, descomentar y personalizar:

```php
$f->set_ValidationRule("name", "trim|required|min_length[3]|max_length[100]");
$f->set_ValidationRule("email", "trim|required|valid_email");
$f->set_ValidationRule("age", "trim|required|numeric|greater_than[0]|less_than[150]");
```

### 9.3 Agregar Lógica Personalizada en Processor

Antes de actualizar, agregar validaciones adicionales:

```php
if (is_array($row)) {
    // Validaciones personalizadas
    if ($d['new_field'] == 'restricted_value') {
        // Mostrar error personalizado
        return;
    }
    
    // Transformar datos si es necesario
    $d['field'] = strtoupper($d['field']);
    
    // Actualizar
    $edit = $model->update($d['id'], $d);
    
    // Operaciones post-actualización
    $model->invalidateSearchCache();
    // ... más lógica
}
```

### 9.4 Cambiar Mensajes

Crear archivos de idioma en `app/Language/{lang}/`:

```php
// app/Language/es/Firewall_IpRange.php
return [
    'edit-title' => 'Editar Rango de IP',
    'edit-success-title' => 'Rango actualizado exitosamente',
    'edit-success-message' => 'El rango de IP se ha actualizado correctamente.',
    'edit-noexist-title' => 'Error: Registro no encontrado',
    'edit-noexist-message' => 'El rango de IP con ID %s no existe.',
];
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

### 10.2 Modelo de Datos

El modelo debe tener métodos específicos:

```php
// En el modelo:
$model->get{SingularComponent}($id);      // Obtener un registro
$model->find($id);                        // Verificar existencia
$model->update($id, $data);               // Actualizar registro
$model->getAuthority($id, $userId);       // Verificar autoridad (autor del registro)
$model->invalidateSearchCache();          // Limpiar caché
```

### 10.3 Validación con CodeIgniter

El servicio de formularios usa las reglas de validación de CodeIgniter:

```php
// Reglas comunes disponibles:
$f->set_ValidationRule("field", "trim|required");                    // Requerido
$f->set_ValidationRule("email", "trim|required|valid_email");       // Email válido
$f->set_ValidationRule("age", "trim|required|numeric");             // Numérico
$f->set_ValidationRule("name", "trim|required|min_length[3]");      // Mínimo 3 caracteres
$f->set_ValidationRule("password", "trim|required|min_length[8]");  // Mínimo 8 caracteres
$f->set_ValidationRule("url", "trim|required|valid_url");           // URL válida
```

### 10.4 URL Encoding

Los coders generan código que será:
1. URL encoded antes de guardarse en campos ocultos
2. URL decoded antes de escribirse en archivos

```php
// En form.php (encoding)
$f->add_HiddenField("cform", urlencode($cform));

// En processor.php (decoding)
"{$pathfiles}/form.php" => urldecode($cform)
```

### 10.5 Autoridad del Registro

El sistema permite que:
- Usuario con permiso **singular** + siendo **autoridad**: Edite su propio registro
- Usuario con permiso **plural**: Edite cualquier registro (sin necesidad de autoridad)

```php
$singular = $authentication->has_Permission("{$module}-{$component}-edit");
$plural = $authentication->has_Permission("{$module}-{$component}-edit-all");
$author = $model->getAuthority($oid, safe_get_user());
$authority = ($singular && $author) ? true : false;

if ($plural || $authority) {
    // Permitir edición
}
```

---

## 11. Errores Comunes

| Error | Causa | Solución |
|-------|-------|----------|
| "Permiso denegado" | Usuario sin permiso/autoridad | Asignar permiso `nexus-access` y configurar autoridad |
| Archivos no creados | Permisos del servidor | Verificar `chmod` en directorio `_Editor` |
| Tabla vacía | OID incorrecto | Verificar nombre exacto de tabla |
| Campo "author" no se asigna | No usar `safe_get_user()` | Usar `safe_get_user()` para campos author |
| Caché no se limpia | Olvidar invalidar caché | Llamar `$model->invalidateSearchCache()` |
| Datos viejos se muestran | Caché no fue limpiado | Verificar que processor.php limpie caché |
| Validación no funciona | Reglas comentadas | Descomentar y personalizar reglas en validator.php |
| Referer inválido | No capturar retorno | Usar `$server->get_Referer()` para capturar URL de origen |

---

## 12. Ejemplo Completo

### Generar Editor para Módulo Firewall - Tabla IP Ranges

**1. Ingresar al generador:**
```
URL: /development/generators/editor/
```

**2. Seleccionar tabla:**
```
OID: firewall_iprange
```

**3. Revisar:**
```
Ruta: app/Modules/Firewall/Views/IpRange/_Editor
Permisos:
  - firewall-iprange-edit (singular)
  - firewall-iprange-edit-all (plural)
```

**4. Personalizar (opcional):**
```php
// En validator.php (descomentar)
$f->set_ValidationRule("name", "trim|required|min_length[3]");
$f->set_ValidationRule("cidr", "trim|required|valid_cidr");
```

**5. Guardar**

**6. Archivos creados:**
```
app/Modules/Firewall/Views/IpRange/_Editor/
├── index.php        (Verificación de permisos y autoridad)
├── form.php         (Formulario de edición)
├── processor.php    (Actualización en BD)
├── validator.php    (Validación de datos)
├── breadcrumb.php   (Navegación)
└── deny.php         (Acceso denegado)
```

**7. Agregar rutas en el controlador:**
```php
$routes->get("iprange/edit/(:num)", "Components\IpRangeController::edit/$1");
$routes->post("iprange/edit/(:num)", "Components\IpRangeController::edit/$1");
```

**8. Acceder a:**
```
/firewall/iprange/edit/1
/firewall/iprange/edit/2
/firewall/iprange/edit/3
```

**9. El flujo será:**
```
Usuario visita /firewall/iprange/edit/1
    ↓
index.php verifica permisos
    ↓
¿Tiene permiso plural O (singular AND autoridad)?
    ├─ SÍ: Muestra form.php
    │   ├─ Usuario edita datos
    │   └─ Envía formulario
    │       ↓
    │   validator.php valida
    │       ├─ Si falla: Muestra errores y form.php nuevamente
    │       └─ Si pasa: Llama a processor.php
    │           ↓
    │       processor.php actualiza BD
    │           ├─ Si existe registro: Muestra éxito
    │           └─ Si no existe: Muestra error
    │
    └─ NO: Muestra deny.php (acceso denegado)
```

---

## 13. Resumen Operacional

```
┌─────────────────────────────────────────────────────────────┐
│  Acceso: /development/generators/editor/                   │
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
      │ app/Modules/{M}/Views/{C}/_Editor/     │
      │ ├── index.php                         │
      │ ├── form.php                          │
      │ ├── processor.php                     │
      │ ├── validator.php                     │
      │ ├── breadcrumb.php                    │
      │ └── deny.php                          │
      └────────┬─────────────────────────────┘
               │
               ↓
      ┌────────────────────┐
      │ Mensaje de éxito   │
      └────────────────────┘
               │
               ↓
      ┌────────────────────────────────────────┐
      │ Usuario accede a:                      │
      │ /{module}/{component}/edit/{id}        │
      └────────┬─────────────────────────────┘
               │
               ↓
      ┌────────────────────────────────────────┐
      │ index.php valida permisos y autoridad  │
      └────────┬─────────────────────────────┘
               │
               ├──────────────────────────────┬─────────────┐
               │ (¿Permiso & Autoridad?)      │             │
              SÍ                              NO            │
               │                               │             │
               ↓                               ↓             ↓
      ┌────────────────┐          ┌──────────────────┐
      │ form.php       │          │ deny.php         │
      │ (formulario)   │          │ (acceso denegado)│
      └────────┬───────┘          └──────────────────┘
               │
               ↓
      ┌────────────────────────────────────────┐
      │ Usuario llena formulario               │
      └────────┬─────────────────────────────┘
               │
               ↓
      ┌────────────────────────────────────────┐
      │ validator.php valida datos             │
      └────────┬─────────────────────────────┘
               │
               ├──────────────────┬──────────────┐
               │ ¿Validación ok?  │              │
              SÍ                  NO             │
               │                  │              │
               ↓                  ↓              ↓
      ┌────────────────────┐  ┌─────────────────────┐
      │ processor.php      │  │ Muestra errores     │
      │ (actualiza BD)     │  │ y form.php nuevamente
      └────────┬───────────┘  └─────────────────────┘
               │
               ├──────────────┬─────────────┐
               │ ¿Existe?     │             │
              SÍ              NO            │
               │               │            │
               ↓               ↓            ↓
      ┌─────────────────┐  ┌─────────────────────┐
      │ Pantalla éxito  │  │ Pantalla de error   │
      │ (edición ok)    │  │ (registro no existe)│
      └─────────────────┘  └─────────────────────┘
```

---

## 14. Flujo de Autorización Detallado

```
┌─────────────────────────────────────────┐
│ Usuario accede a /module/component/edit/5
└──────────────┬──────────────────────────┘
               │
               ↓
       ┌──────────────────┐
       │ index.php ejecuta │
       └────────┬─────────┘
                │
                ↓
       ┌──────────────────────────────┐
       │ Obtiene permisos del usuario │
       └────────┬─────────────────────┘
                │
                ├─ singular = "module-component-edit"
                ├─ plural = "module-component-edit-all"
                │
                ↓
       ┌──────────────────────────────┐
       │ Obtiene autoridad del record  │
       │ (¿Es el propietario?)        │
       └────────┬─────────────────────┘
                │
                ├─ $author = $model->getAuthority(5, $userId)
                │
                ↓
       ┌──────────────────────────────┐
       │ Evalúa lógica de acceso       │
       └────────┬─────────────────────┘
                │
       ┌────────┴─────────────────┬─────────┐
       │                          │         │
       ↓                          ↓         ↓
   (plural) OR          (singular AND    (other)
   (singular AND         author)
    author)              ✓ Permitir     ✗ Denegar
       │                  │               │
      ✓ Permitir          │               │
       │                  │               │
       └────────┬─────────┘               │
                │                         │
                ↓                         ↓
        ┌────────────────┐      ┌──────────────┐
        │ form.php       │      │ deny.php     │
        │ (mostrar)      │      │ (mostrar)    │
        └────────────────┘      └──────────────┘
```

---

## 15. Referencias

- **Ubicación del código:** `/www/wwwroot/_development/app/Modules/Development/Views/Generators/Editor/`
- **Framework:** Higgs (CodeIgniter 4 fork)
- **Bootstrap:** v5.3.3 con componentes customizados
- **Estándar de código:** PSR-12 (PHP)
- **PHP mínimo:** 8.2

---

## 16. Comparativa: Editor vs Creator vs Lister

| Aspecto | Lister | Creator | Editor |
|---------|--------|---------|--------|
| **Propósito** | Listar registros | Crear nuevos | Editar existentes |
| **Archivos** | 4 | 4 | 6 |
| **Permisos** | {-view-all} | {-create-all} | {-edit}, {-edit-all} |
| **Autoridad** | No | No | Sí |
| **Flujo principal** | Ver → Grid | Ver → Form → Validar → Procesar | Ver → Form → Validar → Procesar |
| **Campos cargados** | N/A | Nuevos (vacíos) | Existentes (precargados) |
| **BD - Operación** | SELECT | INSERT | UPDATE |
| **Redirección** | N/A | Nuevo item | Listado o anterior |
| **Caché** | Consulta y paginación | N/A | Búsqueda y caché |

---

**Última actualización:** 2026-05-06  
**Versión Editor:** 1.5.0  
**Autor:** Jose Alexis Correa Valencia
