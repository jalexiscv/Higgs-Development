# Guía Completa: Generador de Cruds "Lister" en Higgs Framework

## 1. Introducción

El **Generador de Listers** es una herramienta automatizada que genera vistas completas para listar/visualizar datos en tablas. Crea cuatro archivos PHP principales que manejan:

- **Pantalla de listado** con tabla interactiva (Grid)
- **Control de permisos** (Deny)
- **Navegación de breadcrumb**
- **Lógica de validación y procesamiento**

---

## 2. Arquitectura General del Generador

```
/Lister/
├── index.php                 ← Punto de entrada (verifica permisos)
├── form.php                  ← Formulario que muestra el código a generar
├── validator.php             ← Valida los campos del formulario
├── processor.php             ← Procesa y escribe los archivos generados
├── breadcrumb.php            ← Breadcrumb de la página del generador
├── deny.php                  ← Página de acceso denegado
└── coders/
    ├── index.php             ← Genera código para index.php
    ├── grid.php              ← Genera código para la tabla (grid.php)
    ├── deny.php              ← Genera código para deny.php
    ├── breadcrumb.php        ← Genera código para breadcrumb.php
    └── json.php              ← Genera código para respuestas JSON (no utilizado actualmente)
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
   - Ejemplo: `app/Modules/Firewall/Views/IpRange/_List`
   - Se construye con: `APPPATH . "Modules/{ModuleName}/Views/{ComponentName}/_List"`

2. **Código PHP a generar** (área editable):
   - Contiene el código combinado de los 4 coders
   - Usuario puede revisar y editar antes de guardar

3. **Campos ocultos** que contienen:
   - `pathfiles` → Ruta destino
   - `cindex` → Código del index.php (URL encoded)
   - `cdeny` → Código del deny.php (URL encoded)
   - `cgrid` → Código del grid.php (URL encoded)
   - `cbreadcrumb` → Código del breadcrumb.php (URL encoded)

---

### 3.3 Etapa 3: Validación (validator.php)

Se validan los campos requeridos:

```php
$f->set_ValidationRule("pathfiles", "trim|required");
$f->set_ValidationRule("cindex", "trim|required");
$f->set_ValidationRule("cdeny", "trim|required");
$f->set_ValidationRule("cgrid", "trim|required");
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
    "{$pathfiles}/grid.php" => urldecode($cgrid),
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

**Ubicación final:** `app/Modules/{Module}/Views/{Component}/_List/index.php`

**Responsabilidad:**
- Punto de entrada para visualizar el listado
- Verifica el permiso `{module}-{component}-view-all` (plural)
- Redirige a `grid.php` si tiene permiso
- Redirige a `deny.php` si no tiene permiso

**Variables disponibles:**
```php
$data['permissions']['plural'] = "firewall-iprange-view-all";
$plural = $authentication->has_Permission($data['permissions']['plural']);
```

**Estructura:**
```php
if ($plural) {
    if (!empty($submited)) {
        // Muestra validador
        $json = [...view($validator, $data)...]
    } else {
        // Muestra tabla
        $json = [...view($table, $data)...]
    }
} else {
    // Acceso denegado
    $json = [...view($deny, $data)...]
}
echo json_encode($json);
```

---

### 5.2 grid.php (La Tabla Principal)

**Ubicación final:** `app/Modules/{Module}/Views/{Component}/_List/grid.php`

**Responsabilidad:**
- Genera la tabla de datos con DataTable
- Maneja paginación servidor-side
- Genera botones de acciones (Ver, Editar, Eliminar)
- Muestra alertas e información

**Características principales:**

#### a) Consulta de Datos
```php
$currentPage = max(1, (int)($request->getVar("page") ?? 1));
$perPage = (int)($request->getVar("per_page") ?? 10);
$offset = ($currentPage - 1) * $perPage;
$search = $request->getVar("search") ?? "";
$field = $request->getVar("field") ?? "";

$rows = $m{component}->getCachedSearch($conditions, $limit, $offset, "id DESC");
$total = $m{component}->getCountAllResults($conditions);
```

#### b) Construcción de Datos para la Tabla
```php
foreach ($rows["data"] as $row) {
    // Crear URLs
    $hrefView = "$component/view/{$row['id']}";
    $hrefEdit = "$component/edit/{$row['id']}";
    $hrefDelete = "$component/delete/{$row['id']}";
    
    // Crear botones
    $btnView = (new Button([...]))->render();
    $btnEdit = (new Button([...]))->render();
    $btnDelete = (new Button([...]))->render();
    
    // Agrupar en grupo de botones
    $options = $bootstrap->get_BtnGroup("btn-group", [
        "content" => $btnView . $btnEdit . $btnDelete
    ]);
    
    // Añadir fila a tabla
    $tableData[] = [
        'count' => ['value' => $count, ...],
        'options' => ['value' => $options, ...],
    ];
}
```

#### c) Configuración de DataTable
```php
$dataTable = new DataTable([
    'id' => '{component}-datatable',
    'columns' => [
        'count' => ["title" => "#"],
        'options' => ["title" => "Opciones"]
    ],
    'data' => $tableData,
    'searchable' => true,
    'pagination' => true,
    'perPage' => $perPage,
    'perPageOptions' => [10, 25, 50, 100, 250, 500],
    'serverSide' => true,
    'totalRecords' => $total,
    'currentPage' => $currentPage
]);
```

#### d) Construcción de Tarjeta Bootstrap
```php
$btnBack = (new Button([...]))->render();
$btnAdd = (new Button([...]))->render();

$alert = (new Alert([
    'type' => 'info',
    'icon' => ICON_INFO,
    'htmlContent' => $alertContent
]));

$card = (new Card([
    'headerTitle' => lang('{Module}_{Component}.list-title'),
    'headerButtons' => [$btnBack, $btnAdd],
    'content' => [
        'htmlContent' => $alert->render() . $dataTable->render()
    ]
]))->render();

echo($card);
```

---

### 5.3 deny.php (Control de Acceso)

**Ubicación final:** `app/Modules/{Module}/Views/{Component}/_List/deny.php`

**Responsabilidad:**
- Mostrar pantalla de acceso denegado
- Diferencia entre usuario no autenticado vs. sin permisos

**Lógica:**
```php
if ($authentication->get_LoggedIn()) {
    // Usuario AUTENTICADO pero SIN PERMISOS
    $_icon = BS5::icon(['icon' => 'ban', ...]);
    $_body = "<!-- Mensaje de acceso denegado -->";
    $_permissions = "<!-- Lista de permisos requeridos -->";
    
    $card = BS5::card([
        'header' => ['title' => lang('App.Access-denied-title'), ...],
        'content' => ['htmlContent' => $_body . $_permissions, ...],
        ...
    ]);
} else {
    // Usuario NO AUTENTICADO
    $_icon = BS5::icon(['icon' => 'lock', ...]);
    $_body = "<!-- Mensaje de login requerido -->";
    
    $card = BS5::card([
        'header' => ['title' => lang('App.login-required-title'), ...],
        'content' => ['htmlContent' => $_body, ...],
        ...
    ]);
}

echo($card);
```

---

### 5.4 breadcrumb.php (Navegación)

**Ubicación final:** `app/Modules/{Module}/Views/{Component}/_List/breadcrumb.php`

**Responsabilidad:**
- Mostrar ruta de navegación actual
- Permitir navegación hacia atrás

**Estructura:**
```php
echo BS5::breadcrumb(['items' => [
    ['label' => '{module}', 'href' => '/{module}/'],
    ['label' => lang('App.{component}'), 
     'href' => '/{module}/{component}/home/'.lpk(), 
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
│       └── _List/
│           ├── index.php
│           ├── grid.php
│           ├── deny.php
│           └── breadcrumb.php
```

### 6.2 Nombres de Clases y Espacios

**Para 2 componentes (`firewall_iprange`):**
```php
Namespace: App\Modules\Firewall\Models
Clase: Firewall_Iprange
URL: /firewall/iprange/
Permiso: firewall-iprange-view-all
```

**Para 3 componentes (`firewall_iprange_log`):**
```php
Namespace: App\Modules\Firewall\Models
Clase: Firewall_Iprange_Log
URL: /firewall/iprange/log/
Permiso: firewall-iprange-log-view-all
```

### 6.3 Permisos

```
Singular (No usado en Lister): {module}-{component}-[access]
Plural (Principal): {module}-{component}-view-all
```

---

## 7. Constantes y Variables Disponibles

### 7.1 En los Coders

```php
COMMENT_HR_VARS          // Comentario separador de variables
COMMENT_MODULECONTROLER_VARS  // Documentación de variables heredadas
COMMENT_HR_BUILD         // Comentario separador de construcción
COMMENT_HR_MODELS        // Comentario separador de modelos
ICON_VIEW               // Ícono de visualizar
ICON_EDIT               // Ícono de editar
ICON_DELETE             // Ícono de eliminar
ICON_ADD                // Ícono de añadir
ICON_BACK               // Ícono de atrás
ICON_INFO               // Ícono de información
```

### 7.2 Variables de Instancia

```php
$parent          // Instancia de ModuleController
$authentication  // Servicio de autenticación
$request         // Servicio de solicitud (GET/POST)
$bootstrap       // Servicio Bootstrap
$dates           // Servicio de fechas
$strings         // Servicio de cadenas
$oid             // Identificador de objeto (ej: firewall_iprange)
$data            // Array con datos globales del módulo
```

---

## 8. Uso Paso a Paso

### 8.1 Acceder al Generador

```
1. URL: /development/generators/list/
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
1. Click en "Guardar"
2. Validación de campos requeridos
3. Creación de archivos en:
   app/Modules/{Module}/Views/{Component}/_List/
4. Mensaje de éxito/advertencia
```

### 8.4 Verificar los Archivos Creados

```bash
ls -la app/Modules/Firewall/Views/IpRange/_List/
```

Debería mostrar:
```
-rw-rw-r-- breadcrumb.php
-rw-rw-r-- deny.php
-rw-rw-r-- grid.php
-rw-rw-r-- index.php
```

---

## 9. Personalización

### 9.1 Modificar Campos de la Tabla

En `grid.php`, descomentar y personalizar el array `$fields`:

```php
$fields = array(
    "id" => lang("App.id"),
    "name" => lang("App.name"),
    "description" => lang("App.description"),
);
```

Y en la construcción de datos:
```php
'name' => [
    "value" => $row['name'],
    "class" => "text-left align-middle"
],
```

### 9.2 Agregar Más Botones de Acción

En `grid.php`, crear nuevos botones:

```php
$btnCustom = (new Button([
    "size" => "sm",
    "icon" => ICON_CUSTOM,
    "variant" => "info",
    "attributes" => ["href" => "{$component}/custom/{$row['id']}", ...]
]))->render();

$options = $bootstrap->get_BtnGroup("btn-group", array(
    "content" => $btnView . $btnEdit . $btnDelete . $btnCustom
));
```

### 9.3 Cambiar Paginación

En `grid.php`, modificar:

```php
'perPage' => 25,  // Cambiar de 10 a 25
'perPageOptions' => [10, 25, 50, 100, 250],  // Opciones disponibles
```

### 9.4 Personalizar Mensajes de Alertas

En `grid.php`, editar:

```php
$alertContent = '<strong>' . lang('{Module}_{Component}.list-title') . '</strong>'
    . '<p class="mb-0">' . lang('{Module}_{Component}.list-description') . '</p>';
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

### 10.2 Caching

Grid.php usa caching:

```php
$rows = $m{component}->getCachedSearch($conditions, $limit, $offset, "id DESC");
// Borrar caché si es necesario:
// $m{component}->clear_AllCache();
```

### 10.3 Paginación Servidor-Side

Manejo automático de:
- Página actual
- Registros por página
- Offset para consultas
- Total de registros

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

---

## 11. Errores Comunes

| Error | Causa | Solución |
|-------|-------|----------|
| "Permiso denegado" | Usuario sin acceso | Asignar permiso `nexus-access` |
| Archivos no creados | Permisos del servidor | Verificar `chmod` en directorio |
| Tabla vacía | OID incorrecto | Verificar nombre exacto de tabla |
| Código malformado | Especiales en nombre | Usar solo alfanuméricos y guiones |
| 404 en rutas | Archivo no existe | Regenerar archivos con Lister |

---

## 12. Ejemplo Completo

### Generar Lister para Módulo Firewall - Tabla IP Ranges

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
Ruta: app/Modules/Firewall/Views/IpRange/_List
Permiso: firewall-iprange-view-all
```

**4. Guardar**

**5. Archivos creados:**
```
app/Modules/Firewall/Views/IpRange/_List/
├── index.php        (Punto de entrada)
├── grid.php         (Tabla de datos)
├── deny.php         (Acceso denegado)
└── breadcrumb.php   (Navegación)
```

**6. Acceder a:**
```
/firewall/iprange/
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
      │ Escribir 4 archivos en:                │
      │ app/Modules/{M}/Views/{C}/_List/      │
      │ ├── index.php                         │
      │ ├── grid.php                          │
      │ ├── deny.php                          │
      │ └── breadcrumb.php                    │
      └────────┬─────────────────────────────┘
               │
               ↓
      ┌────────────────────┐
      │ Mensaje de éxito   │
      └────────────────────┘
```

---

## 14. Referencias

- **Ubicación del código:** `/www/wwwroot/_development/app/Modules/Development/Views/Generators/Lister/`
- **Framework:** Higgs (CodeIgniter 4 fork)
- **Bootstrap:** v5.3.3 con componentes customizados
- **Estándar de código:** PSR-12 (PHP)

---

**Última actualización:** 2026-05-06  
**Versión Lister:** 1.5.0  
**Autor:** Jose Alexis Correa Valencia
