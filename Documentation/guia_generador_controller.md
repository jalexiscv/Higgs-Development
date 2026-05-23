# Guía Completa: Generador de Controladores en Higgs Framework

## 1. Introducción

El **Generador de Controladores** es una herramienta automatizada que genera un controlador PHP completo que actúa como punto de entrada para un módulo. Este controlador:

- **Implementa 6 métodos principales** (index, home, view, list, create, edit, delete)
- **Extiende ModuleController** para heredar funcionalidades comunes
- **Configura automáticamente variables de instancia** (prefix, module, views, viewer)
- **Establece la estructura MVC** para toda la navegación de un componente
- **Genera código listo para producción** con estructura de permisos integrada

---

## 2. Arquitectura General del Generador

```
/Controller/
├── index.php                 ← Punto de entrada (verifica permisos)
├── form.php                  ← Formulario que muestra el código a generar
├── validator.php             ← Valida los campos del formulario
├── processor.php             ← Procesa y escribe el archivo generado
├── breadcrumb.php            ← Breadcrumb de la página del generador
└── deny.php                  ← Página de acceso denegado
```

**Nota:** A diferencia del generador Lister que crea múltiples archivos (index, grid, deny, breadcrumb), el generador Controller crea **un solo archivo PHP** que contiene toda la clase controladora.

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
   - Ejemplo: `app/Modules/Firewall/Controllers/_IpRange.php`
   - Se construye con: `APPPATH . "Modules/{ModuleName}/Controllers/_{ComponentName}.php"`

2. **Código PHP a generar** (área editable):
   - Contiene el código de la clase controladora completa
   - Usuario puede revisar y editar antes de guardar

3. **Campos ocultos** que contienen:
   - `pathfile` → Ruta destino completa del archivo
   - `mkdir` → Directorio a crear si no existe
   - `relative` → Ruta relativa del modelo (no usado en Controller)

---

### 3.3 Etapa 3: Validación (validator.php)

Se validan los campos requeridos:

```php
$f->set_ValidationRule("pathfile", "trim|required");
$f->set_ValidationRule("mkdir", "trim|required");
$f->set_ValidationRule("relative", "trim|required");
$f->set_ValidationRule("uri_save", "trim|required");
$f->set_ValidationRule("code", "trim|required");
```

**Si la validación falla:**
- Muestra tarjeta de error con los mensajes de validación

**Si la validación pasa:**
- Llama a `processor.php` para escribir el archivo

---

### 3.4 Etapa 4: Procesamiento y Creación del Archivo (processor.php)

```php
$files->mkDir($mkdir);
chmod($mkdir, 0775);
$files->open($pathfile, "writeOnly")->write($code);
chmod($pathfile, 0664);
```

**Proceso:**
1. Crea el directorio `Modules/{ModuleName}/Controllers` si no existe
2. Asigna permisos al directorio: `0775`
3. Escribe el archivo `_{ComponentName}.php` con el contenido generado
4. Asigna permisos al archivo: `0664`
5. Muestra mensaje de éxito o advertencia

---

## 4. Estructura de Identificadores (OID)

El generador usa un identificador compuesto llamado **OID** (Object ID):

```
{module}_{component}
```

**Ejemplos:**
- `firewall_iprange` → Módulo: Firewall, Componente: IpRange
- `security_user` → Módulo: Security, Componente: User

**Transformaciones:**
```php
$eid = explode("_", $oid);                    // ["firewall", "iprange"]
$ucf_module = safe_ucfirst($eid[0]);          // "Firewall"
$slc_module = safe_strtolower($eid[0]);       // "firewall"
$ucf_component = safe_ucfirst($eid[1]);       // "Iprange"
$slc_component = safe_strtolower($eid[1]);    // "iprange"
```

---

## 5. Archivo Generado

### 5.1 Ubicación y Nombre

**Ubicación final:** `app/Modules/{Module}/Controllers/_{Component}.php`

**Ejemplo:** `app/Modules/Firewall/Controllers/_IpRange.php`

---

### 5.2 Estructura de la Clase Generada

```php
<?php

namespace App\Modules\{Module}\Controllers;

use App\Controllers\ModuleController;

class {Component} extends ModuleController {

    //[{Module}/Config/Routes]
    // [{Component}]----------------------------------------------------------------------------------------
    //"module-component-home"=>"{$views\{Component}\Home\index",
    //"module-component-list"=>"{$views\{Component}\List\index",
    //"module-component-view"=>"{$views\{Component}\View\index",
    //"module-component-create"=>"{$views\{Component}\Create\index",
    //"module-component-edit"=>"{$views\{Component}\Edit\index",
    //"module-component-delete"=>"{$views\{Component}\Delete\index",

    //[{Component}]----------------------------------------------------------------------------------------
    //    "module-component-access",
    //    "module-component-view",
    //    "module-component-view-all",
    //    "module-component-create",
    //    "module-component-edit",
    //    "module-component-edit-all",
    //    "module-component-delete",
    //    "module-component-delete-all",

    public function __construct() {
       parent::__construct();
       $this->prefix = 'module-component';
       $this->module = 'App\Modules\{Module}';
       $this->views = $this->module . '\Views';
       $this->viewer = $this->views . '\index';
       helper($this->module.'\Helpers\{Module}');
    }

    public function index() {
        $url = base_url('module/component/home/' . lpk());
        return (redirect()->to($url));
    }

    public function home(string $rnd) {
        $this->oid = $rnd;
        $this->prefix = "{$this->prefix}-home";
        $this->component = $this->views . '\{Component}\Home';
        return (view($this->viewer, $this->get_Array()));
    }

    public function view(string $oid) {
        $this->oid = $oid;
        $this->prefix = "{$this->prefix}-view";
        $this->component = $this->views . '\{Component}\View';
        return (view($this->viewer, $this->get_Array()));
    }

    public function list(string $rnd) {
        $this->oid = $rnd;
        $this->prefix = "{$this->prefix}-list";
        $this->component = $this->views . '\{Component}\List';
        return (view($this->viewer, $this->get_Array()));
    }

    public function create(string $rnd) {
        $this->oid = $rnd;
        $this->prefix = "{$this->prefix}-create";
        $this->component = $this->views . '\{Component}\Create';
        return (view($this->viewer, $this->get_Array()));
    }

    public function edit(string $oid) {
        $this->oid = $oid;
        $this->prefix = "{$this->prefix}-edit";
        $this->component = $this->views . '\{Component}\Edit';
        return (view($this->viewer, $this->get_Array()));
    }

    public function delete(string $oid) {
        $this->oid = $oid;
        $this->prefix = "{$this->prefix}-delete";
        $this->component = $this->views . '\{Component}\Delete';
        return (view($this->viewer, $this->get_Array()));
    }

}
?>
```

---

## 6. Detalles de la Clase Generada

### 6.1 Responsabilidades

La clase controladora tiene 4 responsabilidades principales:

1. **Enrutamiento y Despacho:** Recibe solicitudes HTTP y las dirige a vistas específicas
2. **Configuración de Variables:** Establece `$prefix`, `$module`, `$views`, `$viewer`
3. **Gestión de OID:** Almacena el identificador de objeto (`$this->oid`)
4. **Carga de Vistas:** Utiliza `view()` con datos configurados en `$this->get_Array()`

### 6.2 Métodos Principales

#### a) `__construct()`
- Inicializa variables de instancia hereditarias
- Establece el prefijo para permisos: `module-component`
- Define las rutas de módulo y vistas
- Carga el helper del módulo

```php
public function __construct() {
   parent::__construct();
   $this->prefix = 'firewall-iprange';
   $this->module = 'App\Modules\Firewall';
   $this->views = $this->module . '\Views';
   $this->viewer = $this->views . '\index';
   helper($this->module.'\Helpers\Firewall');
}
```

#### b) `index()`
- Punto de entrada del controlador
- Redirige a `home()` con un valor aleatorio (lpk)
- Propósito: Inicializar sesión y cargar la vista principal

```php
public function index() {
    $url = base_url('firewall/iprange/home/' . lpk());
    return (redirect()->to($url));
}
```

#### c) `home(string $rnd)`
- Muestra la pantalla principal del componente
- Recibe parámetro `$rnd` (número aleatorio para seguridad)
- Carga vista: `Views\IpRange\Home\index`
- Actualiza prefix a: `firewall-iprange-home`

```php
public function home(string $rnd) {
    $this->oid = $rnd;
    $this->prefix = "{$this->prefix}-home";
    $this->component = $this->views . '\IpRange\Home';
    return (view($this->viewer, $this->get_Array()));
}
```

#### d) `view(string $oid)`
- Muestra detalles de un registro específico
- Recibe parámetro `$oid` (identificador del objeto)
- Carga vista: `Views\IpRange\View\index`
- Actualiza prefix a: `firewall-iprange-view`

#### e) `list(string $rnd)`
- Muestra el listado de todos los registros
- Recibe parámetro `$rnd` (número aleatorio)
- Carga vista: `Views\IpRange\List\index`
- Actualiza prefix a: `firewall-iprange-list`

#### f) `create(string $rnd)`
- Muestra formulario para crear nuevo registro
- Recibe parámetro `$rnd` (número aleatorio)
- Carga vista: `Views\IpRange\Create\index`
- Actualiza prefix a: `firewall-iprange-create`

#### g) `edit(string $oid)`
- Muestra formulario para editar un registro
- Recibe parámetro `$oid` (identificador del objeto)
- Carga vista: `Views\IpRange\Edit\index`
- Actualiza prefix a: `firewall-iprange-edit`

#### h) `delete(string $oid)`
- Muestra formulario de confirmación para eliminar
- Recibe parámetro `$oid` (identificador del objeto)
- Carga vista: `Views\IpRange\Delete\index`
- Actualiza prefix a: `firewall-iprange-delete`

---

### 6.3 Variables de Instancia Heredadas

Estas variables se heredan de `ModuleController` y se configuran en el constructor:

```php
$this->prefix = 'module-component'     // Prefijo para permisos (ej: firewall-iprange)
$this->module = 'App\Modules\{Module}' // Ruta del módulo
$this->views = '...\Views'             // Ruta de vistas del módulo
$this->viewer = '...\Views\index'      // Archivo principal de visualización
$this->oid = null                      // Identificador de objeto (se actualiza por método)
$this->component = null                // Ruta actual de vista
```

---

## 7. Convenciones de Nombres

### 7.1 Rutas de Archivos

```
app/Modules/{ModuleName}/
├── Controllers/
│   └── _{ComponentName}.php
├── Views/
│   └── {ComponentName}/
│       ├── Home/
│       ├── List/
│       ├── View/
│       ├── Create/
│       ├── Edit/
│       └── Delete/
└── Helpers/
    └── {ModuleName}.php
```

### 7.2 Nombres de Clases

**Estructura general:**
```
Namespace: App\Modules\{ModuleName}\Controllers
Clase: {ComponentName}
Archivo: _{ComponentName}.php
```

**Ejemplo (firewall_iprange):**
```php
Namespace: App\Modules\Firewall\Controllers
Clase: IpRange
Archivo: _IpRange.php
```

### 7.3 Convención de Prefijo para Permisos

El generador crea automáticamente comentarios con los permisos necesarios:

```
Acceso general:        module-component-access
Visualización:         module-component-view
Visualización masiva:  module-component-view-all
Creación:             module-component-create
Edición:              module-component-edit
Edición masiva:       module-component-edit-all
Eliminación:          module-component-delete
Eliminación masiva:   module-component-delete-all
```

**Ejemplo para firewall_iprange:**
```
firewall-iprange-access
firewall-iprange-view
firewall-iprange-view-all
firewall-iprange-create
firewall-iprange-edit
firewall-iprange-edit-all
firewall-iprange-delete
firewall-iprange-delete-all
```

---

## 8. Constantes y Variables Disponibles

### 8.1 En form.php (Coder)

```php
APPPATH                        // Ruta de aplicación
$oid                           // Identificador de objeto (ej: firewall_iprange)
$db->getFieldNames($oid)       // Campos de la tabla
$ucf_module                    // Nombre módulo PascalCase (Firewall)
$ucf_component                 // Nombre componente PascalCase (IpRange)
$slc_module                    // Nombre módulo lowercase (firewall)
$slc_component                 // Nombre componente lowercase (iprange)
$classname                     // Nombre de clase con guiones (Firewall_IpRange)
$mkdir                         // Directorio a crear
$pathfile                      // Ruta completa del archivo
$relative                      // Ruta relativa del modelo
$namespaced                    // Ruta con namespace para comentario
```

### 8.2 Variables de Instancia en la Clase Generada

```php
$this->prefix          // Prefijo para permisos (actualizado por método)
$this->module          // Ruta del módulo
$this->views           // Ruta de vistas
$this->viewer          // Archivo principal de vista
$this->oid             // Identificador actual
$this->component       // Componente/vista actual
```

### 8.3 Métodos Disponibles de ModuleController

```php
$this->get_Array()          // Obtiene array con datos de la instancia
$this->get_Prefix()         // Obtiene el prefijo actual
$this->get_Module()         // Obtiene el módulo actual
$this->get_Component()      // Obtiene el componente actual
```

---

## 9. Uso Paso a Paso

### 9.1 Acceder al Generador

```
1. URL: /development/generators/
2. Seleccionar: Generador de Controladores
3. O ir directamente a: /development/generators/controller/
```

### 9.2 Ingresar el OID

```
1. El generador solicita el OID (Object Identifier)
2. Formato: module_component (ej: firewall_iprange)
3. El generador valida que la tabla exista en la base de datos
```

### 9.3 Revisar el Código Generado

```
1. El formulario muestra el código PHP del controlador
2. Copiar/revisar la ruta de destino: 
   app/Modules/{Module}/Controllers/_{Component}.php
3. El código incluye comentarios con las rutas de configuración a registrar
4. Editar si es necesario (personalización)
```

### 9.4 Guardar el Archivo

```
1. Click en "Guardar Controlador"
2. Validación de campos requeridos (pathfile, mkdir, code, etc.)
3. Creación del directorio Controllers si no existe
4. Escritura del archivo en: app/Modules/{Module}/Controllers/_{Component}.php
5. Mensaje de éxito o advertencia
```

### 9.5 Verificar el Archivo Creado

```bash
ls -la app/Modules/Firewall/Controllers/
```

Debería mostrar:
```
-rw-rw-r-- _IpRange.php
```

### 9.6 Registrar Rutas

El archivo generado incluye comentarios indicando dónde registrar las rutas en:
`app/Modules/{Module}/Config/Routes.php`

```php
// En la sección del módulo:
$subroutes->add('iprange', 'IpRange::index');
$subroutes->add('iprange/home/(:any)', 'IpRange::home/$1');
$subroutes->add('iprange/list/(:any)', 'IpRange::list/$1');
$subroutes->add('iprange/view/(:any)', 'IpRange::view/$1');
$subroutes->add('iprange/create/(:any)', 'IpRange::create/$1');
$subroutes->add('iprange/edit/(:any)', 'IpRange::edit/$1');
$subroutes->add('iprange/delete/(:any)', 'IpRange::delete/$1');
```

### 9.7 Registrar Vistas

El archivo generado también incluye comentarios indicando dónde registrar las vistas en:
`app/Modules/{Module}/Views/index.php`

```php
// En la sección del componente:
"firewall-iprange-home" => "$views\IpRange\Home\index",
"firewall-iprange-list" => "$views\IpRange\List\index",
"firewall-iprange-view" => "$views\IpRange\View\index",
"firewall-iprange-create" => "$views\IpRange\Create\index",
"firewall-iprange-edit" => "$views\IpRange\Edit\index",
"firewall-iprange-delete" => "$views\IpRange\Delete\index",
```

---

## 10. Personalización

### 10.1 Modificar Nombre del Componente

Si necesitas cambiar el nombre después de generar:

1. Editar el archivo `_{ComponentName}.php`
2. Cambiar el nombre de la clase
3. Actualizar el namespace si es necesario
4. Renombrar el archivo si cambias el nombre de la clase

### 10.2 Agregar Métodos Adicionales

Después de generar el controlador, puedes agregar nuevos métodos:

```php
public function custom(string $oid) {
    $this->oid = $oid;
    $this->prefix = "{$this->prefix}-custom";
    $this->component = $this->views . '\IpRange\Custom';
    return (view($this->viewer, $this->get_Array()));
}
```

Y registrar la ruta correspondiente:
```php
$subroutes->add('iprange/custom/(:any)', 'IpRange::custom/$1');
```

### 10.3 Personalizar Constructor

Modificar la inicialización de variables si tu módulo tiene estructura diferente:

```php
public function __construct() {
   parent::__construct();
   $this->prefix = 'custom-prefix';
   $this->module = 'App\Modules\CustomModule';
   $this->views = $this->module . '\MyCustomViews';  // Ruta personalizada
   $this->viewer = $this->views . '\custom_index';   // Archivo personalizado
   helper($this->module.'\Helpers\Custom');
}
```

### 10.4 Cambiar Estructura de Vistas

Si tu proyecto no usa la estructura `Views\Component\Action\index`:

```php
// En lugar de:
$this->component = $this->views . '\IpRange\Home';

// Puedes usar:
$this->component = 'Modules/Firewall/Views/IpRange/home';
```

---

## 11. Detalles Técnicos

### 11.1 Seguridad en Parámetros

El generador utiliza 2 tipos de parámetros según la acción:

**Con `$rnd` (números aleatorios):**
- `home()`, `list()`, `create()`
- Propósito: Inicializar o listar (no requieren ID específico)
- Ejemplo: `/firewall/iprange/home/abc123def456`

**Con `$oid` (identificadores):**
- `view()`, `edit()`, `delete()`
- Propósito: Acceder a un registro específico
- Ejemplo: `/firewall/iprange/view/42`

### 11.2 Flujo de Datos (MVC)

```
Controlador                    Vista
    │                           │
    ├─ $this->prefix ─────────> Determina permisos
    ├─ $this->oid ──────────────> ID del registro
    ├─ $this->component ───────> Ruta de vista
    └─ $this->get_Array() ────> Array con todos los datos
                                    │
                                    ↓
                              Archivo view
                              (carga datos y renderiza)
```

### 11.3 Herencia de ModuleController

El controlador generado hereda de `ModuleController`, que proporciona:

- `get_Array()` - Retorna array con todas las propiedades
- Gestión automática de permisos basada en `$this->prefix`
- Integración con sistema de autenticación
- Variables globales ($authentication, $request, $dates, etc.)

### 11.4 Gestión de Archivos

El generador utiliza la clase `Files` para escribir:

```php
$files = new Files();
$files->mkDir($mkdir);                           // Crea directorio
$files->open($pathfile, "writeOnly")->write($code);  // Escribe archivo
chmod($pathfile, 0664);                         // Permisos rw-rw-r--
```

---

## 12. Errores Comunes

| Error | Causa | Solución |
|-------|-------|----------|
| "Permiso denegado" | Usuario sin acceso | Asignar permiso `nexus-access` |
| Archivo no creado | Permisos del servidor | Verificar `chmod` en directorio Controllers |
| OID incorrecto | Tabla no existe | Verificar nombre exacto de tabla en base de datos |
| Clase no se carga | Namespace incorrecto | Verificar namespace coincida con estructura de carpetas |
| Métodos sin vistas | Vistas no creadas | Generar vistas con otros generadores (Home, List, etc.) |
| Rutas no funcionan | Rutas no registradas | Registrar rutas en Config/Routes.php del módulo |
| Variables no disponibles | ViewerIndex incorrecto | Configurar archivo índice principal en constructor |

---

## 13. Ejemplo Completo

### Generar Controlador para Módulo Firewall - Tabla IP Ranges

**Paso 1: Acceder al generador**
```
URL: /development/generators/controller/
```

**Paso 2: Ingresar el OID**
```
OID: firewall_iprange
```

**Paso 3: Revisar información**
```
Archivo: app/Modules/Firewall/Controllers/_IpRange.php
Clase: IpRange
Namespace: App\Modules\Firewall\Controllers
```

**Paso 4: Guardar**

**Paso 5: Archivo creado**
```
app/Modules/Firewall/Controllers/_IpRange.php
- Clase: IpRange extends ModuleController
- Constructor: inicializa prefix, module, views, viewer
- 7 métodos: index, home, view, list, create, edit, delete
```

**Paso 6: Registrar rutas en `app/Modules/Firewall/Config/Routes.php`**
```php
$subroutes->add('iprange', 'IpRange::index');
$subroutes->add('iprange/home/(:any)', 'IpRange::home/$1');
$subroutes->add('iprange/list/(:any)', 'IpRange::list/$1');
$subroutes->add('iprange/view/(:any)', 'IpRange::view/$1');
$subroutes->add('iprange/create/(:any)', 'IpRange::create/$1');
$subroutes->add('iprange/edit/(:any)', 'IpRange::edit/$1');
$subroutes->add('iprange/delete/(:any)', 'IpRange::delete/$1');
```

**Paso 7: Registrar vistas en `app/Modules/Firewall/Views/index.php`**
```php
"firewall-iprange-home" => "$views\IpRange\Home\index",
"firewall-iprange-list" => "$views\IpRange\List\index",
"firewall-iprange-view" => "$views\IpRange\View\index",
"firewall-iprange-create" => "$views\IpRange\Create\index",
"firewall-iprange-edit" => "$views\IpRange\Edit\index",
"firewall-iprange-delete" => "$views\IpRange\Delete\index",
```

**Paso 8: Generar vistas**
- Usar generadores Home, List, View, Create, Edit, Delete para crear las vistas correspondientes

**Paso 9: Acceder al controlador**
```
/firewall/iprange/
```
Se redirige automáticamente a:
```
/firewall/iprange/home/{random}
```

---

## 14. Resumen Operacional

```
┌─────────────────────────────────────────────────────────────┐
│  Acceso: /development/generators/controller/                │
└──────────────┬──────────────────────────────────────────────┘
               │
               ↓
        ┌──────────────┐
        │ Ingresar OID │
        │ (ej: firewall│
        │  _iprange)   │
        └──────┬───────┘
               │
               ↓
      ┌────────────────────┐
      │ Ver código PHP     │
      │ del controlador    │
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
      │ Escribir archivo en:                   │
      │ app/Modules/{Module}/Controllers/      │
      │ _{Component}.php                       │
      └────────┬─────────────────────────────┘
               │
               ↓
      ┌────────────────────┐
      │ Mensaje de éxito   │
      └────────┬───────────┘
               │
               ↓
      ┌────────────────────────────────────────┐
      │ Registrar rutas en:                    │
      │ {Module}/Config/Routes.php             │
      └────────┬─────────────────────────────┘
               │
               ↓
      ┌────────────────────────────────────────┐
      │ Registrar vistas en:                   │
      │ {Module}/Views/index.php               │
      └────────┬─────────────────────────────┘
               │
               ↓
      ┌────────────────────────────────────────┐
      │ Generar vistas (Home, List, etc.)      │
      │ para cada método del controlador       │
      └────────┬─────────────────────────────┘
               │
               ↓
      ┌────────────────────┐
      │ Controlador listo  │
      │ en /module/        │
      │ component/         │
      └────────────────────┘
```

---

## 15. Diferencias con Otros Generadores

### vs. Generador Lister
- **Controller:** Genera 1 archivo (controlador)
- **Lister:** Genera 4 archivos (vistas)
- **Controller:** Enruta a vistas
- **Lister:** Renderiza tabla de datos

### vs. Generador Model
- **Controller:** Maneja solicitudes HTTP
- **Model:** Accede a base de datos
- **Ambos:** Necesarios para CRUD completo

### vs. Generador View
- **Controller:** Coordina lógica
- **View:** Renderiza HTML
- **Ambos:** Se necesitan mutuamente

---

## 16. Ciclo de Vida Completo de un Componente

```
1. Crear Modelo          → Generador Model
2. Crear Controlador     → Generador Controller
3. Registrar Rutas       → Config/Routes.php
4. Crear Vistas:
   - Home               → Generador Home
   - List               → Generador Lister
   - View               → Generador View
   - Create             → Generador Create
   - Edit               → Generador Edit
   - Delete             → Generador Delete
5. Registrar Vistas      → Views/index.php
6. Registrar Permisos    → Sistema de autenticación
7. Acceder a /module/component/
```

---

## 17. Referencias

- **Ubicación del código:** `/www/wwwroot/_development/app/Modules/Development/Views/Generators/Controller/`
- **Framework:** Higgs (CodeIgniter 4 fork)
- **Bootstrap:** v5.3.3 con componentes customizados
- **Estándar de código:** PSR-12 (PHP)
- **Clase base:** `App\Controllers\ModuleController`

---

**Última actualización:** 2026-05-06  
**Versión Controller:** 1.5.0  
**Autor:** Jose Alexis Correa Valencia
