# Guía Completa: Generador de Vistas "List" en Higgs Framework

## 1. Introducción

El **Generador de Vistas List** es el componente de salida del **Generador Lister**. Genera cuatro archivos PHP principales que manejan la visualización y listado de datos de entidades:

- **Punto de entrada** con verificación de permisos (index.php)
- **Tabla interactiva** con paginación servidor-side (grid.php)
- **Pantalla de acceso denegado** (deny.php)
- **Navegación de breadcrumb** (breadcrumb.php)

Estos archivos trabajan en conjunto para proporcionar una interfaz completa de visualización de datos con control de acceso granular.

---

## 2. Arquitectura General

### 2.1 Ubicación de los Archivos Generados

```
app/Modules/{ModuleName}/Views/{ComponentName}/
└── _List/
    ├── index.php        ← Punto de entrada (router de permisos)
    ├── grid.php         ← Tabla de datos con DataTable
    ├── deny.php         ← Pantalla de acceso denegado
    └── breadcrumb.php   ← Navegación de migas de pan
```

### 2.2 Diagrama de Relaciones

```
┌──────────────────────────────────────────────────────────┐
│ ModuleController                                         │
│ /{module}/{component}/                                  │
└─────────────┬──────────────────────────────────────────┘
              │
              ↓
         ┌─────────────┐
         │ index.php   │  (Verifica permisos)
         └─────┬───────┘
               │
        ┌──────┴──────┐
        │             │
    ¿Tiene        ¿NO tiene
    permiso?      permiso?
        │             │
        ↓             ↓
    ┌────────┐   ┌──────────┐
    │grid.php│   │ deny.php │
    └────────┘   └──────────┘
        │
        ↓
   ┌──────────────┐
   │ breadcrumb   │ (En ambas rutas)
   └──────────────┘
```

### 2.3 Relación con el Generador Lister

El **Generador Lister** es la herramienta que **genera** estos archivos:

```
┌──────────────────────────────────────┐
│ Generador Lister                     │
│ /development/generators/lister/      │
├──────────────────────────────────────┤
│ form.php          (Formulario)       │
│ processor.php     (Guarda archivos)  │
│ validator.php     (Valida entrada)   │
│ coders/           (Generadores)      │
│   ├── index.php   (Coder)            │
│   ├── grid.php    (Coder)            │
│   ├── deny.php    (Coder)            │
│   └── breadcrumb.php (Coder)         │
└──────────────────────────────────────┘
                ↓↓↓ GENERA
         (app/Modules/{M}/Views/{C}/_List)
┌──────────────────────────────────────┐
│ Vistas List Generadas                │
│ /app/Modules/{Module}/{Component}/   │
│ Views/{Component}/_List/             │
└──────────────────────────────────────┘
```

---

## 3. Flujo de Trabajo del Generador List

### 3.1 Etapa 1: Acceso al Generador Lister

```
┌─────────────────────────────────┐
│ Usuario accede a:               │
│ /development/generators/lister/ │
└────────────┬────────────────────┘
             │
             ↓
      ┌──────────────┐
      │ ¿Tiene       │
      │ permiso      │
      │ nexus-access?│
      └──┬───────┬──┘
        SI│       │NO
         ↓       ↓
    [Form]   [Deny]
```

**Verificación:**
- Se comprueba el permiso: `nexus-access` (acceso singular)
- Si falta permiso → Muestra `deny.php`
- Si tiene permiso → Muestra `form.php`

### 3.2 Etapa 2: Seleccionar Tabla y Generar Código (form.php)

El formulario del Lister contiene:

1. **Campo OID (Object ID):**
   - Selector de tabla de base de datos
   - Ejemplo: `firewall_iprange`
   - Determina los nombres de módulo y componente

2. **Ruta de destino** (readonly):
   - Se construye automáticamente
   - Ejemplo: `app/Modules/Firewall/Views/IpRange/_List`

3. **Vista previa de código** (editable):
   - Muestra el código PHP que será generado
   - Resultado de los 4 coders combinados
   - Usuario puede revisar y personalizar

4. **Campos ocultos con código encodificado:**
   - `cindex` → Código para index.php (URL encoded)
   - `cdeny` → Código para deny.php (URL encoded)
   - `cgrid` → Código para grid.php (URL encoded)
   - `cbreadcrumb` → Código para breadcrumb.php (URL encoded)

### 3.3 Etapa 3: Validación de Formulario (validator.php del Lister)

Se validan los campos requeridos:

```php
$f->set_ValidationRule("pathfiles", "trim|required");
$f->set_ValidationRule("cindex", "trim|required");
$f->set_ValidationRule("cdeny", "trim|required");
$f->set_ValidationRule("cgrid", "trim|required");
$f->set_ValidationRule("cbreadcrumb", "trim|required");
```

**Si la validación falla:**
- Muestra tarjeta de error con mensajes de validación
- Usuario puede corregir y reintentar

**Si la validación pasa:**
- Llama a `processor.php` para escribir los archivos

### 3.4 Etapa 4: Procesamiento y Creación de Archivos (processor.php del Lister)

```php
$generatedFiles = [
    "{$pathfiles}/index.php" => urldecode($cindex),
    "{$pathfiles}/deny.php" => urldecode($cdeny),
    "{$pathfiles}/grid.php" => urldecode($cgrid),
    "{$pathfiles}/breadcrumb.php" => urldecode($cbreadcrumb),
];
```

**Proceso:**
1. Crea el directorio `_List` si no existe
2. Asigna permisos al directorio: `chmod 0775`
3. Escribe cada archivo con el contenido decodificado
4. Asigna permisos a cada archivo: `chmod 0664`
5. Muestra mensaje de éxito o advertencia

---

## 4. Coders: Los Generadores de Código

Los "coders" son archivos PHP que **generan código PHP como texto**. Cada coder se encarga de generar uno de los cuatro archivos finales.

### 4.1 Proceso de los Coders

```
┌────────────────────┐
│ form.php carga     │
│ coders/index.php   │  ← Lee OID, extrae módulo/componente
│ coders/grid.php    │  ← Genera código para grid.php
│ coders/deny.php    │  ← Genera código para deny.php
│ coders/breadcrumb  │  ← Genera código para breadcrumb.php
└────────┬───────────┘
         │
         ↓
    ┌─────────────────────────────────┐
    │ $code = "<?php\n";              │
    │ $code .= "use Higgs...          │
    │ $code .= "echo(\$data);         │
    │ ...                             │
    │ ?>"                             │
    └────────┬────────────────────────┘
             │
             ↓
    ┌──────────────────────────┐
    │ urlencode($code)         │
    │ Guardado en campo oculto │
    └──────────────────────────┘
```

### 4.2 Variables Disponibles en los Coders

```php
$oid                // "firewall_iprange" o "firewall_iprange_log"
$eid                // ["firewall", "iprange"] o ["firewall", "iprange", "log"]
$ucf_module         // "Firewall"
$ucf_component      // "Iprange"
$ucf_options        // "Log" (si aplica)
$slc_module         // "firewall"
$slc_component      // "iprange"
$slc_options        // "log" (si aplica)
$model              // "App\\Modules\\Firewall\\Models\\Firewall_Iprange"
$path               // "/firewall/iprange"
$plural             // "firewall-iprange-view-all"
$pathfiles          // "app/Modules/Firewall/Views/Iprange/_List"
$db                 // Conexión a base de datos
$fields             // Array de campos de la tabla
$pk                 // Clave primaria identificada
```

---

## 5. Archivos Generados - Estructura Detallada

### 5.1 index.php

**Ubicación final:** `app/Modules/{Module}/Views/{Component}/_List/index.php`

**Responsabilidad:**
- Punto de entrada para visualizar el listado
- Router de acceso basado en permisos
- Combina las diferentes vistas según autenticación

**Lógica de flujo:**

```php
// Obtiene datos del controlador
$data = $parent->get_Array();

// Define permisos
$data['permissions'] = [
    'singular' => false,
    'plural' => 'firewall-iprange-view-all'
];

// Verifica permiso
$plural = $authentication->has_Permission(
    $data['permissions']['plural']
);

// Define vistas
$breadcrumb = $component . '\breadcrumb';
$validator = $component . '\validator';
$table = $component . '\grid';
$deny = $component . '\deny';

// Router de vistas
if ($plural) {
    // Usuario tiene permiso
    $json = [
        'breadcrumb' => view($breadcrumb, $data),
        'main' => view($table, $data),
        'right' => ""
    ];
} else {
    // Usuario sin permisos
    $json = [
        'breadcrumb' => view($breadcrumb, $data),
        'main' => view($deny, $data),
        'right' => ""
    ];
}
echo(json_encode($json));
```

**Características:**
- Evaluación de permiso plural: `{module}-{component}-view-all`
- Usa JSON como respuesta estándar
- Template de columna completa: `c12` (12 columnas Bootstrap)
- Incluye breadcrumb en ambas rutas

---

### 5.2 grid.php (La Tabla Principal)

**Ubicación final:** `app/Modules/{Module}/Views/{Component}/_List/grid.php`

**Responsabilidad:**
- Genera la tabla de datos interactiva
- Maneja paginación servidor-side
- Crea botones de acciones (Ver, Editar, Eliminar)
- Renderiza alertas e información de contexto

#### a) Carga de Datos

```php
// Instancia del modelo
$miprange = model('App\Modules\Firewall\Models\Firewall_Iprange');

// Variables de paginación servidor-side
$currentPage = max(1, (int)($request->getVar("page") ?? 1));
$perPage     = (int)($request->getVar("per_page") ?? 10);
$offset      = ($currentPage - 1) * $perPage;
$search      = !empty($request->getVar("search")) ? $request->getVar("search") : "";
$field       = !empty($request->getVar("field"))  ? $request->getVar("field")  : "";
$limit       = $perPage;

// Campos de búsqueda
$fields = [
    // "name" => lang("App.name"),
    // "description" => lang("App.description"),
];

// Consulta con caché
$conditions = [];
$rows  = $miprange->getCachedSearch($conditions, $limit, $offset, "id DESC");
$total = $miprange->getCountAllResults($conditions);
```

#### b) Construcción de Datos para la Tabla

```php
$tableData = [];
$count = $offset;

foreach ($rows["data"] as $row) {
    if (!empty($row["id"])) {
        $count++;
        
        // URLs de acciones
        $hrefView   = "$component/view/{$row['id']}";
        $hrefEdit   = "$component/edit/{$row['id']}";
        $hrefDelete = "$component/delete/{$row['id']}";
        
        // Botones individuales
        $btnView = (new Button([
            "size"       => "sm",
            "icon"       => ICON_VIEW,
            "variant"    => "primary",
            "attributes" => [
                "href" => $hrefView,
                "class" => "btn-sm ml-1",
                "title" => lang("App.View")
            ]
        ]))->render();
        
        $btnEdit = (new Button([
            "size"       => "sm",
            "icon"       => ICON_EDIT,
            "variant"    => "warning",
            "attributes" => [
                "href" => $hrefEdit,
                "class" => "btn-sm ml-1",
                "title" => lang("App.Edit")
            ]
        ]))->render();
        
        $btnDelete = (new Button([
            "size"       => "sm",
            "icon"       => ICON_DELETE,
            "variant"    => "danger",
            "attributes" => [
                "href" => $hrefDelete,
                "class" => "btn-sm ml-1",
                "title" => lang("App.Delete")
            ]
        ]))->render();
        
        // Grupo de botones
        $options = $bootstrap->get_BtnGroup("btn-group", [
            "content" => $btnView . $btnEdit . $btnDelete
        ]);
        
        // Fila de tabla
        $tableData[] = [
            'count' => [
                "value" => $count,
                "class" => "text-center align-middle",
                "style" => "width: 80px;"
            ],
            // 'name' => [
            //     "value" => $row['name'],
            //     "class" => "text-left align-middle"
            // ],
            'options' => [
                "value" => $options,
                "class" => "text-center align-middle text-nowrap",
                "style" => "width: 120px;"
            ]
        ];
    }
}
```

#### c) Configuración de DataTable

```php
$dataTable = new DataTable([
    'id'              => 'iprange-datatable',
    'columns'         => [
        'count'   => [
            "title" => "#",
            "class" => "text-center align-middle"
        ],
        // 'name' => [
        //     "title" => lang("App.name"),
        //     "class" => "text-center align-middle"
        // ],
        'options' => [
            "title" => lang("App.Options"),
            "class" => "text-center align-middle"
        ]
    ],
    'data'            => $tableData,
    'searchable'      => true,
    'pagination'      => true,
    'perPage'         => $perPage,
    'perPageOptions'  => [10, 25, 50, 100, 250, 500],
    'tableAttributes' => ['class' => 'table-sm'],
    'serverSide'      => true,
    'totalRecords'    => $total,
    'currentPage'     => $currentPage
]);
```

#### d) Construcción de Tarjeta Bootstrap

```php
$headerButtons = [];

// Botón Atrás
$btnBack = (new Button([
    "size"       => "sm",
    "icon"       => ICON_BACK,
    "variant"    => "secondary",
    "attributes" => [
        "href" => "/firewall",
        "class" => "ml-1",
        "title" => lang("App.Back")
    ]
]))->render();
$headerButtons[] = $btnBack;

// Botón Añadir
$btnAdd = (new Button([
    "size"       => "sm",
    "icon"       => ICON_ADD,
    "variant"    => "success",
    "attributes" => [
        "href" => "/firewall/iprange/create/" . lpk(),
        "class" => "ml-1",
        "title" => lang("App.Add")
    ]
]))->render();
$headerButtons[] = $btnAdd;

// Alerta informativa
$alertContent = '<strong>' . lang('Firewall_Iprange.list-title') . '</strong>'
    . '<p class="mb-0">' . lang('Firewall_Iprange.list-description') . '</p>';

$alert = (new Alert([
    'type'        => 'info',
    'icon'        => ICON_INFO,
    'htmlContent' => $alertContent
]));

// Tarjeta final
$card = (new Card([
    'attributes'    => ['class' => 'card-grid shadow-sm'],
    'headerTitle'   => lang('Firewall_Iprange.list-title'),
    'headerButtons' => $headerButtons,
    'content'       => [
        'htmlContent' => $alert->render() . $dataTable->render(),
        'class'       => 'p-0'
    ]
]))->render();

echo($card);
```

---

### 5.3 deny.php (Control de Acceso)

**Ubicación final:** `app/Modules/{Module}/Views/{Component}/_List/deny.php`

**Responsabilidad:**
- Mostrar pantalla amigable de acceso denegado
- Diferencia entre usuario no autenticado vs. sin permisos suficientes
- Proporciona instrucciones claras

**Lógica:**

```php
use Higgs\Frontend\Bootstrap\v5_3_3\Bootstrap as BS5;

$continue = "/firewall/iprange/list/" . lpk();

if ($authentication->get_LoggedIn()) {
    // Usuario AUTENTICADO pero SIN PERMISOS SUFICIENTES
    
    $_icon = (string)BS5::icon([
        'icon' => 'ban',
        'style' => 'duotone',
        'size' => '4x'
    ]);
    
    $_body = '<div class="text-center py-3">' . $_icon . '</div>'
        . '<p class="text-center pb-2">' 
        . lang('App.Access-denied-message') 
        . '</p>';
    
    $_permissions = "<p class=\"text-center pb-2\">Permisos requeridos: "
        . implode(" - ", $permissions)
        . "</p>";
    
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
        'attributes' => [
            'class' => 'border-danger shadow-sm'
        ],
    ]);
    
} else {
    // Usuario NO AUTENTICADO
    
    $_icon = (string)BS5::icon([
        'icon' => 'lock',
        'style' => 'duotone',
        'size' => '4x'
    ]);
    
    $_body = '<div class="text-center py-3">' . $_icon . '</div>'
        . '<p class="text-center pb-2">' 
        . lang('App.login-required-message') 
        . '</p>';
    
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
        'attributes' => [
            'class' => 'border-danger shadow-sm'
        ],
    ]);
}

echo($card);
```

**Características:**
- Icono de prohibición (ban) para acceso denegado
- Icono de candado (lock) para login requerido
- Botón "Continuar" para volver al módulo
- Estilos Bootstrap rojo puro (danger)
- Información clara del estado

---

### 5.4 breadcrumb.php (Navegación)

**Ubicación final:** `app/Modules/{Module}/Views/{Component}/_List/breadcrumb.php`

**Responsabilidad:**
- Mostrar ruta de navegación actual
- Permitir navegación hacia niveles superiores
- Indicar ubicación actual en la jerarquía

**Estructura:**

```php
use Higgs\Frontend\Bootstrap\v5_3_3\Bootstrap as BS5;

echo BS5::breadcrumb([
    'items' => [
        [
            'label' => 'firewall',
            'href' => '/firewall/'
        ],
        [
            'label' => lang('App.iprange'),
            'href' => '/firewall/iprange/home/' . lpk(),
            'active' => true
        ],
    ]
]);
```

**Para 3 componentes (`firewall_iprange_log`):**

```php
echo BS5::breadcrumb([
    'items' => [
        [
            'label' => 'firewall',
            'href' => '/firewall/'
        ],
        [
            'label' => lang('App.iprange'),
            'href' => '/firewall/iprange/home/' . lpk()
        ],
        [
            'label' => lang('App.log'),
            'href' => '/firewall/iprange/log/home/' . lpk(),
            'active' => true
        ],
    ]
]);
```

**Características:**
- Estructura automática según jerarquía de componentes
- Último item marcado como "active"
- Integración con sistema de lenguaje (lang files)
- Token de seguridad (lpk()) en URLs

---

## 6. Convenciones de Nombres

### 6.1 Rutas de Archivos

**Para 2 componentes:**
```
app/Modules/Firewall/Views/IpRange/_List/
├── index.php
├── grid.php
├── deny.php
└── breadcrumb.php
```

**Para 3 componentes:**
```
app/Modules/Firewall/Views/IpRange/Log/_List/
├── index.php
├── grid.php
├── deny.php
└── breadcrumb.php
```

### 6.2 Nomenclatura de Variables

```php
// OID parsing
$eid = explode("_", $oid);  // ["firewall", "iprange"]

// Caso superior (PascalCase)
$ucf_module = "Firewall"
$ucf_component = "Iprange"
$ucf_options = "Log"

// Caso inferior (lowercase)
$slc_module = "firewall"
$slc_component = "iprange"
$slc_options = "log"
```

### 6.3 Nombres de Clases y Namespaces

**Para 2 componentes (`firewall_iprange`):**
```php
Namespace: App\Modules\Firewall\Models
Clase: Firewall_Iprange
URL: /firewall/iprange/
Permiso: firewall-iprange-view-all
Ruta: app/Modules/Firewall/Views/Iprange/_List
```

**Para 3 componentes (`firewall_iprange_log`):**
```php
Namespace: App\Modules\Firewall\Models
Clase: Firewall_Iprange_Log
URL: /firewall/iprange/log/
Permiso: firewall-iprange-log-view-all
Ruta: app/Modules/Firewall/Views/Iprange/Log/_List
```

### 6.4 Permisos Requeridos

```
Permiso singular (no usado en List): {module}-{component}-[access]
Permiso plural (principal): {module}-{component}-view-all
Generador: nexus-access
```

---

## 7. Constantes y Variables Disponibles

### 7.1 Constantes de Íconos

```php
ICON_VIEW               // Icono para visualizar (eye)
ICON_EDIT               // Icono para editar (pencil)
ICON_DELETE             // Icono para eliminar (trash)
ICON_ADD                // Icono para añadir (plus)
ICON_BACK               // Icono para atrás (arrow-left)
ICON_INFO               // Icono para información (circle-info)
```

### 7.2 Variables de Instancia Inyectadas por ModuleController

```php
$parent              // Instancia de ModuleController
$authentication      // Servicio de autenticación
$request             // Servicio de solicitud HTTP (GET/POST)
$bootstrap           // Servicio Bootstrap para componentes UI
$dates               // Servicio de fechas
$strings             // Servicio de cadenas
$oid                 // Object ID (ej: firewall_iprange)
$component           // URI del componente (ej: /firewall/iprange)
$data                // Array con datos globales del módulo
```

### 7.3 Variables de Configuración del Grid

```php
$currentPage         // Página actual (1-indexed)
$perPage             // Registros por página (default: 10)
$offset              // Offset de la consulta SQL
$search              // Término de búsqueda
$field               // Campo de búsqueda
$limit               // Límite de resultados
$total               // Total de registros
$pk                  // Clave primaria identificada automáticamente
```

### 7.4 Clases Bootstrap Disponibles

```php
use Higgs\Frontend\Bootstrap\v5_3_3\Bootstrap as BS5;
use Higgs\Frontend\Bootstrap\v5_3_3\Extras\DataTable;
use Higgs\Frontend\Bootstrap\v5_3_3\Interface\Button;
use Higgs\Frontend\Bootstrap\v5_3_3\Interface\Card;
use Higgs\Frontend\Bootstrap\v5_3_3\Interface\Alert;
```

---

## 8. Uso Paso a Paso

### 8.1 Acceder al Generador Lister

```
1. URL: /development/generators/lister/
2. El generador carga si tienes permiso "nexus-access"
3. Si no tienes permiso, se muestra deny.php
```

### 8.2 Seleccionar Tabla

```
1. Selector de tabla: Elegir de la lista (ejemplo: firewall_iprange)
2. El generador carga automáticamente
3. Los coders generan el código PHP
```

### 8.3 Revisar el Código Generado

```
1. El formulario muestra:
   - Ruta destino: app/Modules/Firewall/Views/IpRange/_List
   - Código PHP combinado de los 4 archivos
2. Puedes editar el código si necesitas personalizarlo
3. Verifica que la ruta sea correcta
```

### 8.4 Guardar los Archivos

```
1. Click en "Guardar"
2. Validación de campos requeridos
   - pathfiles (requerido)
   - cindex (requerido)
   - cdeny (requerido)
   - cgrid (requerido)
   - cbreadcrumb (requerido)
3. Si falla: Se muestra tarjeta de error
4. Si pasa: Se escriben los archivos
```

### 8.5 Verificar los Archivos Creados

```bash
# Listar archivos
ls -la app/Modules/Firewall/Views/IpRange/_List/

# Resultado esperado
-rw-rw-r--  1  www  www  1234  May 06 10:30  index.php
-rw-rw-r--  1  www  www  5678  May 06 10:30  grid.php
-rw-rw-r--  1  www  www  2345  May 06 10:30  deny.php
-rw-rw-r--  1  www  www  1890  May 06 10:30  breadcrumb.php
```

### 8.6 Acceder a la Vista Generada

```
1. Asegúrate de tener el permiso: firewall-iprange-view-all
2. URL: /firewall/iprange/
3. Si tienes permiso: Se muestra grid.php con la tabla
4. Si no tienes permiso: Se muestra deny.php
```

---

## 9. Personalización

### 9.1 Modificar Campos de la Tabla

En `grid.php`, descomentar y personalizar el array `$fields`:

```php
$fields = [
    "id" => lang("App.id"),
    "name" => lang("App.name"),
    "description" => lang("App.description"),
    "created_at" => lang("App.created_at"),
    "updated_at" => lang("App.updated_at"),
];
```

Luego descomentar las columnas en la tabla:

```php
$tableData[] = [
    'count' => [
        "value" => $count,
        "class" => "text-center align-middle",
        "style" => "width: 80px;"
    ],
    'name' => [
        "value" => $row['name'],
        "class" => "text-left align-middle"
    ],
    'description' => [
        "value" => $row['description'],
        "class" => "text-left align-middle"
    ],
    'options' => [
        "value" => $options,
        "class" => "text-center align-middle text-nowrap",
        "style" => "width: 120px;"
    ]
];
```

Y en la configuración de DataTable:

```php
$dataTable = new DataTable([
    'columns' => [
        'count' => ["title" => "#", "class" => "text-center align-middle"],
        'name' => ["title" => lang("App.name"), "class" => "text-center align-middle"],
        'description' => ["title" => lang("App.description"), "class" => "text-center align-middle"],
        'options' => ["title" => lang("App.Options"), "class" => "text-center align-middle"]
    ],
    // ... resto de configuración
]);
```

### 9.2 Agregar Más Botones de Acción

En `grid.php`, crear nuevos botones antes de agruparlos:

```php
$btnCustom = (new Button([
    "size"       => "sm",
    "icon"       => ICON_CUSTOM,
    "variant"    => "info",
    "attributes" => [
        "href" => "$component/custom/{$row['id']}",
        "class" => "btn-sm ml-1",
        "title" => lang("App.Custom")
    ]
]))->render();

// Agrupar todos los botones
$options = $bootstrap->get_BtnGroup("btn-group", [
    "content" => $btnView . $btnEdit . $btnDelete . $btnCustom
]);
```

### 9.3 Cambiar Opciones de Paginación

En `grid.php`, modificar:

```php
'perPage'        => 25,              // Default: 10
'perPageOptions' => [5, 10, 25, 50], // Default: [10, 25, 50, 100, 250, 500]
```

### 9.4 Personalizar Mensajes de Alertas

En `grid.php`, editar el contenido de la alerta:

```php
$alertContent = '<strong>' . lang('Firewall_Iprange.list-title') . '</strong>'
    . '<p class="mb-0">' . lang('Firewall_Iprange.list-description') . '</p>'
    . '<p class="text-muted">Información adicional personalizada</p>';
```

### 9.5 Cambiar el Orden de los Registros

En `grid.php`, modificar el parámetro de orderBy:

```php
// Default: "id DESC"
$rows = $miprange->getCachedSearch(
    $conditions,
    $limit,
    $offset,
    "name ASC"  // Ordenar por nombre ascendente
);
```

### 9.6 Agregar Filtros Avanzados

En `grid.php`, construir condiciones dinámicamente:

```php
$conditions = [];

if (!empty($search)) {
    $conditions['name LIKE'] = "%$search%";
}

if (!empty($request->getVar("status"))) {
    $conditions['status'] = $request->getVar("status");
}

$rows = $miprange->getCachedSearch($conditions, $limit, $offset, "id DESC");
```

---

## 10. Detalles Técnicos

### 10.1 Flujo de Datos en la Paginación Servidor-Side

```
┌──────────────────────────────┐
│ Cliente solicita:            │
│ ?page=2&per_page=25         │
└─────────────┬────────────────┘
              │
              ↓
    ┌─────────────────────┐
    │ index.php          │
    │ (router)           │
    └────────┬────────────┘
             │
             ↓
    ┌─────────────────────┐
    │ grid.php            │
    │ Procesa parámetros  │
    └─────────┬───────────┘
              │
    ┌─────────┴──────────────┐
    │                        │
    ↓                        ↓
┌──────────┐          ┌───────────┐
│ page: 2  │          │ per_page: │
│ offset:  │          │ 25        │
│ (2-1)*25 │          └───────────┘
│ = 25     │
└──────────┘
    │
    ├─────────────┬──────────────────┐
    │             │                  │
    ↓             ↓                  ↓
┌─────────┐ ┌────────────┐  ┌──────────────┐
│Condiciones│ getCachedSearch(│LIMIT 25    │
│ $cond  │ $conditions,  │ OFFSET 25  │
└─────────┘ $limit, $offset  └──────────────┘
                │
                ↓
        ┌────────────────┐
        │ Base de Datos  │
        └────────┬───────┘
                 │
                 ↓
        ┌────────────────────┐
        │ 25 registros       │
        │ (filas 26-50)      │
        └─────────┬──────────┘
                  │
                  ↓
         ┌───────────────────┐
         │ Construir         │
         │ tableData[] array │
         └──────────┬────────┘
                    │
                    ↓
         ┌────────────────────┐
         │ Renderizar         │
         │ DataTable con:     │
         │ - Datos            │
         │ - Paginación       │
         │ - Total registros  │
         └────────────────────┘
```

### 10.2 Manejo de Caché

El modelo usa caching automático:

```php
// Con caché (recomendado)
$rows = $miprange->getCachedSearch(
    $conditions,
    $limit,
    $offset,
    "id DESC"
);

// Si necesitas borrar caché manualmente
$miprange->clear_AllCache();
```

**Ventajas:**
- Mejora rendimiento en consultas repetidas
- Automático y transparent
- Invalidado al insertar/actualizar/eliminar

### 10.3 Control de Acceso Granular

El sistema diferencia entre tres estados:

```
1. NO AUTENTICADO
   └─ deny.php muestra: "Iniciar sesión"

2. AUTENTICADO SIN PERMISOS
   └─ deny.php muestra: "Acceso denegado" + permisos requeridos

3. AUTENTICADO CON PERMISOS
   └─ grid.php muestra: tabla completa
```

### 10.4 Encoding/Decoding de Código

**En Lister form.php (Encoding):**
```php
$f->add_HiddenField("cindex", urlencode($cindex));
$f->add_HiddenField("cgrid", urlencode($cgrid));
```

**En Lister processor.php (Decoding):**
```php
$generatedFiles["{$pathfiles}/index.php"] = urldecode($cindex);
$generatedFiles["{$pathfiles}/grid.php"] = urldecode($cgrid);
```

**Razón:** Permite pasar código PHP con caracteres especiales a través de POST.

### 10.5 URL Encoding con Higgs

La constante `lpk()` genera un token de seguridad:

```php
// Genera algo como: /firewall/iprange/create/?tk=abc123def456

lpk()  // Token de protección CSRF

// Uso
['href' => "/firewall/iprange/create/" . lpk()]
```

---

## 11. Errores Comunes

| Error | Causa | Solución |
|-------|-------|----------|
| "Permiso denegado" en Lister | Sin permiso `nexus-access` | Asignar permiso al usuario |
| "Permiso denegado" en List | Sin permiso `{module}-{component}-view-all` | Asignar permiso al usuario |
| Archivos no creados | Permisos de escritura insuficientes | Verificar `chmod 775` en directorio módulo |
| Tabla vacía | Base de datos vacía o OID incorrecto | Verificar nombre exacto de tabla en base de datos |
| Código malformado | Caracteres especiales en nombres | Usar solo alfanuméricos y guiones bajos |
| Error de namespace | Estructura de módulo incorrecta | Verificar que el módulo existe en `app/Modules/` |
| 404 en rutas de botones | Vista Create/Editor/etc no generada | Generar vistas adicionales con otros generadores |
| "Class not found" | Modelo no existe | Generar modelo con Generador Model |
| Paginación no funciona | JavaScript DataTable no cargado | Verificar `<script>` tags en layout |
| Columnas no aparecen | Campos comentados | Descomentar campos en `$fields` y `tableData` |

---

## 12. Ejemplo Completo: Generar List para Firewall IPRange

### Paso 1: Acceder al Generador Lister

```
URL: /development/generators/lister/
```

Verifica que veas el formulario (tienes permiso `nexus-access`).

### Paso 2: Seleccionar Tabla

```
Combo box: firewall_iprange
```

El generador calcula automáticamente:
- Módulo: Firewall
- Componente: IpRange
- OID: firewall_iprange

### Paso 3: Revisar Ruta Destino

```
Ruta mostrada: app/Modules/Firewall/Views/IpRange/_List
Permiso requerido: firewall-iprange-view-all
```

### Paso 4: Revisar Código Generado

El área de código muestra aproximadamente 500 líneas de PHP:

```php
<?php
use Higgs\Frontend\Bootstrap\v5_3_3\Bootstrap as BS5;
use Higgs\Frontend\Bootstrap\v5_3_3\Extras\DataTable;
use Higgs\Frontend\Bootstrap\v5_3_3\Interface\Button;
use Higgs\Frontend\Bootstrap\v5_3_3\Interface\Card;
use Higgs\Frontend\Bootstrap\v5_3_3\Interface\Alert;

// ===== index.php =====
$data = $parent->get_Array();
$data['permissions'] = ['singular' => false, 'plural' => 'firewall-iprange-view-all'];
// ...

// ===== grid.php =====
$miprange = model('App\Modules\Firewall\Models\Firewall_Iprange');
// ...

// ===== deny.php =====
if ($authentication->get_LoggedIn()) {
    // Usuario sin permisos
} else {
    // Usuario no autenticado
}

// ===== breadcrumb.php =====
echo BS5::breadcrumb(['items' => [...]]);
?>
```

### Paso 5: Personalizar (Opcional)

Ejemplo: Descomentar campo "name" en grid.php:

```php
// ANTES:
//'name' => [
//    "value" => $row['name'],
//    "class" => "text-left align-middle"
//],

// DESPUÉS:
'name' => [
    "value" => $row['name'],
    "class" => "text-left align-middle"
],
```

### Paso 6: Guardar

```
Click en "Guardar"
```

**Validación automática:**
- pathfiles: ✓
- cindex: ✓
- cdeny: ✓
- cgrid: ✓
- cbreadcrumb: ✓

**Resultado:** Mensaje de éxito

```
Archivos creados exitosamente en:
app/Modules/Firewall/Views/IpRange/_List/
- index.php
- grid.php
- deny.php
- breadcrumb.php
```

### Paso 7: Verificar Archivos

```bash
ls -la app/Modules/Firewall/Views/IpRange/_List/
```

Output:
```
total 28
-rw-rw-r--  index.php
-rw-rw-r--  grid.php
-rw-rw-r--  deny.php
-rw-rw-r--  breadcrumb.php
```

### Paso 8: Acceder a la Vista

```
URL: /firewall/iprange/
```

**Si tienes permiso `firewall-iprange-view-all`:**
- Se carga breadcrumb
- Se carga grid.php con tabla de datos
- Botones: Ver, Editar, Eliminar funcionan

**Si NO tienes permiso:**
- Se carga breadcrumb
- Se carga deny.php con mensaje de acceso denegado

---

## 13. Resumen Operacional con Diagrama

### Flujo Completo desde Generador hasta Vista

```
┌─────────────────────────────────────────────────────────────────┐
│ 1. USUARIO ACCEDE A GENERADOR                                  │
│    URL: /development/generators/lister/                        │
│    Verifica: permiso "nexus-access"                            │
└──────────────┬──────────────────────────────────────────────────┘
               │
               ├─ ✗ Sin permiso → Muestra deny.php
               │
               └─ ✓ Con permiso
                  │
                  ↓
┌─────────────────────────────────────────────────────────────────┐
│ 2. FORMULARIO LISTER (form.php)                                │
│    - Combo box para seleccionar tabla (OID)                    │
│    - Campo URI: app/Modules/{Module}/{Component}/_List        │
│    - Área de código: Vista previa (editable)                   │
│    - Campos ocultos con código encodificado (4 archivos)       │
└──────────────┬──────────────────────────────────────────────────┘
               │
               ↓
┌─────────────────────────────────────────────────────────────────┐
│ 3. CODERS GENERAN CÓDIGO (coders/*.php)                        │
│                                                                 │
│    coders/index.php          → genera index.php               │
│    coders/grid.php           → genera grid.php                │
│    coders/deny.php           → genera deny.php                │
│    coders/breadcrumb.php     → genera breadcrumb.php          │
│                                                                 │
│    Cada coder construye string PHP con urlencode()            │
└──────────────┬──────────────────────────────────────────────────┘
               │
               ↓
┌─────────────────────────────────────────────────────────────────┐
│ 4. USUARIO REVISA CÓDIGO                                       │
│    (puede personalizar antes de guardar)                       │
└──────────────┬──────────────────────────────────────────────────┘
               │
               ↓
        ┌──────────────┐
        │ GUARDAR      │
        └──────┬───────┘
               │
               ↓
┌─────────────────────────────────────────────────────────────────┐
│ 5. VALIDACIÓN (validator.php)                                  │
│    - Verifica: pathfiles, cindex, cdeny, cgrid, cbreadcrumb  │
└──────────────┬──────────────────────────────────────────────────┘
               │
               ├─ ✗ Validación falla → Muestra errores
               │
               └─ ✓ Validación pasa
                  │
                  ↓
┌─────────────────────────────────────────────────────────────────┐
│ 6. PROCESAMIENTO (processor.php)                               │
│    - Crea directorio _List (mkdir)                            │
│    - Asigna permisos: chmod 0775                              │
│    - Decodifica: urldecode($cindex, $cdeny, etc)             │
│    - Escribe 4 archivos en _List/                            │
│    - Asigna permisos: chmod 0664 a cada archivo              │
└──────────────┬──────────────────────────────────────────────────┘
               │
               ↓
┌─────────────────────────────────────────────────────────────────┐
│ 7. ARCHIVOS CREADOS ✓                                          │
│                                                                 │
│    app/Modules/Firewall/Views/IpRange/_List/                 │
│    ├── index.php         (punto de entrada)                   │
│    ├── grid.php          (tabla de datos)                     │
│    ├── deny.php          (acceso denegado)                    │
│    └── breadcrumb.php    (navegación)                         │
│                                                                 │
│    Permisos: -rw-rw-r-- (0664)                               │
└──────────────┬──────────────────────────────────────────────────┘
               │
               ↓
        ┌──────────────────────┐
        │ USUARIO ACCEDE:      │
        │ /firewall/iprange/   │
        └──────┬───────────────┘
               │
        ┌──────┴───────────┐
        │                  │
    ¿Permiso?          ¿Permiso?
    view-all           view-all?
        │                  │
    SÍ │                   │ NO
        ↓                   ↓
   ┌────────┐         ┌────────────┐
   │breadcrumb        │breadcrumb  │
   │grid.php          │deny.php    │
   │(tabla)           │(bloqueado) │
   └────────┘         └────────────┘
```

### Estados Finales

```
┌──────────────────────────────────────┐
│ ESTADO 1: LISTA VISIBLE              │
│                                      │
│ Breadcrumb: firewall > iprange       │
│ Tabla: Datos con paginación          │
│ Botones: Ver, Editar, Eliminar       │
│ Botones header: Atrás, Añadir        │
│ Alerta: Información descriptiva      │
└──────────────────────────────────────┘

┌──────────────────────────────────────┐
│ ESTADO 2: ACCESO DENEGADO            │
│                                      │
│ Breadcrumb: firewall > iprange       │
│ Icono: Ban (prohibido)               │
│ Mensaje: "Acceso denegado"           │
│ Permisos: Lista de permisos faltantes│
│ Botón: Continuar (vuelve a módulo)  │
│ Color: Rojo (danger)                 │
└──────────────────────────────────────┘

┌──────────────────────────────────────┐
│ ESTADO 3: LOGIN REQUERIDO            │
│                                      │
│ Icono: Candado (lock)                │
│ Mensaje: "Iniciar sesión requerido"  │
│ Botón: Continuar (vuelve a módulo)  │
│ Color: Rojo (danger)                 │
└──────────────────────────────────────┘
```

---

## 14. Integración con Otros Generadores

El generador List trabaja en conjunto con otros generadores del framework:

```
┌─────────────────────────────────────────────────────────────┐
│ GENERADORES COMPLEMENTARIOS                                │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│ Model Generator                                            │
│ └─ Crea: App\Modules\{M}\Models\{M}_{C}                   │
│    Usado por: grid.php para getCachedSearch()             │
│                                                             │
│ Controller Generator                                       │
│ └─ Crea: App\Modules\{M}\Controllers\{C}                 │
│    Enruta a: /firewall/iprange/ → index.php              │
│                                                             │
│ Creator Generator                                          │
│ └─ Crea: Views/{Component}/_Create/                      │
│    Acceso desde: Botón "Añadir" en grid.php              │
│                                                             │
│ Viewer Generator                                           │
│ └─ Crea: Views/{Component}/_Viewer/                      │
│    Acceso desde: Botón "Ver" en grid.php                 │
│                                                             │
│ Editor Generator                                           │
│ └─ Crea: Views/{Component}/_Editor/                      │
│    Acceso desde: Botón "Editar" en grid.php              │
│                                                             │
│ Deleter Generator                                          │
│ └─ Crea: Views/{Component}/_Delete/                      │
│    Acceso desde: Botón "Eliminar" en grid.php            │
│                                                             │
│ Lang Generator                                             │
│ └─ Crea: Language/{module}_{component}.php               │
│    Usado por: lang() calls en todas las vistas            │
│                                                             │
│ Migration Generator                                        │
│ └─ Crea: Database/Migrations/...                         │
│    Define estructura de tabla                             │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

## 15. Checklist de Implementación

```
ANTES DE GENERAR:
☐ Verificar que el módulo existe: app/Modules/{Module}/
☐ Verificar que la tabla existe en la base de datos
☐ Verificar que tienes permiso "nexus-access"
☐ Tener permiso para crear directorios en Views/

DURANTE LA GENERACIÓN:
☐ Seleccionar el OID correcto
☐ Revisar la ruta destino
☐ Revisar el código generado
☐ Personalizar si es necesario

DESPUÉS DE GENERAR:
☐ Verificar que los 4 archivos fueron creados
☐ Verificar permisos: -rw-rw-r-- (0664)
☐ Generar archivo de lenguaje: {Module}_{Component}.php
☐ Asignar permiso: {module}-{component}-view-all a usuarios
☐ Probar acceso: /firewall/iprange/
☐ Probar botones: Ver, Editar, Eliminar
☐ Probar paginación: cambiar página, registros por página
☐ Probar búsqueda: ingresar término en buscador
☐ Probar acceso denegado: sin permisos view-all
```

---

## 16. Debugging y Troubleshooting

### Habilitar Debug en grid.php

```php
// Descomentar para ver SQL de consultas
// echo(safe_dump($rows['sql']));

// Descomentar para ver datos crudos
// echo(safe_dump($rows));

// Descomentar para ver DataTable config
// echo(safe_dump($dataTable));
```

### Limpiar Caché

```php
// En grid.php, descomentar:
$miprange->clear_AllCache();
```

### Verificar Permisos

```php
// Agregar temporalmente en index.php:
if ($plural) {
    echo "DEBUG: Tienes permiso view-all";
} else {
    echo "DEBUG: NO tienes permiso view-all";
}
```

### Verificar Variables Inyectadas

```php
// Agregar temporalmente en index.php:
echo "<pre>";
var_dump($parent->get_Array());
echo "</pre>";
```

---

## 17. Referencias Rápidas

### Archivos del Generador Lister

- **form.php** - Formulario del generador (ruta, código, campos)
- **processor.php** - Escribe los 4 archivos en disco
- **validator.php** - Valida que los campos requeridos estén presentes
- **coders/index.php** - Genera código para index.php
- **coders/grid.php** - Genera código para grid.php
- **coders/deny.php** - Genera código para deny.php
- **coders/breadcrumb.php** - Genera código para breadcrumb.php

### URLs Importantes

```
Generador Lister: /development/generators/lister/
Selector de tabla: Combo box en form.php
Lista generada: /firewall/iprange/ (una vez creada)
Generador Model: /development/generators/model/
Generador Controller: /development/generators/controller/
```

### Permisos Requeridos

```
Acceso al generador: nexus-access
Ver lista de datos: {module}-{component}-view-all
Ver registro: {module}-{component}-view
Crear registro: {module}-{component}-create
Editar registro: {module}-{component}-edit
Eliminar registro: {module}-{component}-delete
```

---

## 18. Notas Importantes

1. **Siempre genera el Modelo primero** - El grid.php necesita una clase modelo funcional

2. **Siempre crea el archivo de lenguaje** - Las vistas usan `lang()` para textos

3. **Los coders generan código como strings** - Usan `urlencode()` para pasar a través de POST

4. **El OID determina TODO** - Nombres de clases, rutas, permisos, etc. se derivan de él

5. **La paginación es servidor-side** - No carga todos los registros a la vez

6. **El caché es automático** - `getCachedSearch()` cachea resultados

7. **Los permisos son granulares** - Puedes tener view-all pero no edit

8. **Bootstrap v5.3.3 es requerido** - Las clases que genera dependen de esta versión

9. **Los archivos generados son editable** - No son regenerados automáticamente

10. **Token CSRF (lpk())** - Se requiere en todas las URLs para seguridad

---

## 19. Compatibilidad y Requisitos

```
Framework:      Higgs (CodeIgniter 4 fork)
PHP:            >= 8.2
Bootstrap:      v5.3.3 con componentes customizados
Base de datos:  MySQL/MariaDB (requiere tabla existente)
Permisos SO:    Write en app/Modules/*/Views/
```

---

**Última actualización:** 2026-05-06  
**Versión Generator List:** 1.5.0  
**Basado en:** LISTER_GENERATOR_GUIDE.md  
**Autor:** Jose Alexis Correa Valencia

---

## Apéndice A: Ejemplo de Código Generado Completo

Este es un ejemplo de los 4 archivos generados para `firewall_iprange`:

### index.php (Generado)

```php
<?php
$data = $parent->get_Array();
$data['permissions'] = ['singular' => false, 'plural' => 'firewall-iprange-view-all'];
$plural = $authentication->has_Permission($data['permissions']['plural']);
$submited = $request->getPost("submited");
$breadcrumb = $component . '\breadcrumb';
$validator = $component . '\validator';
$table = $component . '\grid';
$deny = $component . '\deny';

if ($plural) {
    if (!empty($submited)) {
        $json = ['breadcrumb' => view($breadcrumb, $data), 'main' => view($validator, $data), 'right' => ""];
    } else {
        $json = ['breadcrumb' => view($breadcrumb, $data), 'main' => view($table, $data), 'right' => ""];
    }
} else {
    $json = ['breadcrumb' => view($breadcrumb, $data), 'main' => view($deny, $data), 'right' => ""];
}
echo(json_encode($json));
?>
```

### grid.php (Fragmento)

```php
<?php
use Higgs\Frontend\Bootstrap\v5_3_3\Extras\DataTable;
use Higgs\Frontend\Bootstrap\v5_3_3\Interface\Button;
use Higgs\Frontend\Bootstrap\v5_3_3\Interface\Card;
use Higgs\Frontend\Bootstrap\v5_3_3\Interface\Alert;

$miprange = model('App\Modules\Firewall\Models\Firewall_Iprange');
$back = "/firewall";
$component = '/firewall/iprange';
$currentPage = max(1, (int)($request->getVar("page") ?? 1));
$perPage     = (int)($request->getVar("per_page") ?? 10);
// ... resto del código
?>
```

### deny.php (Fragmento)

```php
<?php
use Higgs\Frontend\Bootstrap\v5_3_3\Bootstrap as BS5;

if ($authentication->get_LoggedIn()) {
    $_icon = (string)BS5::icon(['icon' => 'ban', 'style' => 'duotone', 'size' => '4x']);
    // ... construir tarjeta roja con permisos requeridos
} else {
    $_icon = (string)BS5::icon(['icon' => 'lock', 'style' => 'duotone', 'size' => '4x']);
    // ... construir tarjeta con mensaje de login requerido
}
echo($card);
?>
```

### breadcrumb.php (Completo)

```php
<?php
use Higgs\Frontend\Bootstrap\v5_3_3\Bootstrap as BS5;

echo BS5::breadcrumb(['items' => [
    ['label' => 'firewall', 'href' => '/firewall/'],
    ['label' => lang('App.iprange'), 'href' => '/firewall/iprange/home/'.lpk(), 'active' => true],
]]);
?>
```

---

**FIN DEL DOCUMENTO**
