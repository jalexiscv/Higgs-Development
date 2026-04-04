# Documentación de Controladores

> Referencia detallada de todos los controladores del módulo Development.

---

## 📋 Tabla de Contenidos

1. [Descripción General](#descripción-general)
2. [Development (Principal)](#development-principal)
3. [Generators](#generators)
4. [Tools](#tools)
5. [UI](#ui)
6. [Webpack](#webpack)
7. [Ide](#ide)
8. [AI](#ai)
9. [Api](#api)
10. [Router](#router)

---

## 🎯 Descripción General

Todos los controladores extienden `ModuleController` que proporciona:

```php
class Development extends ModuleController {
    public function __construct() {
        parent::__construct();
        // Propiedades disponibles:
        $this->authentication  // Usuario autenticado
        $this->request        // Solicitud HTTP
        $this->dates          // Información de fechas
        $this->prefix         // Prefijo para vistas
        $this->module         // Ruta del módulo
        $this->views          // Ruta de vistas
        $this->viewer         // Vista principal
        $this->component      // Componente a renderizar
        $this->oid            // Object ID (parámetro)
    }
}
```

---

## 🏠 Development (Principal)

**Archivo**: `Controllers/Development.php`

Controlador principal que maneja la navegación y acceso general al módulo.

### Métodos

#### `index()`
- **Descripción**: Punto de entrada al módulo
- **Acción**: Redirige a `/development/home/index.html`
- **Ruta**: `/development/`
- **Parámetros**: Ninguno
- **Return**: Redirect

```php
public function index() {
    $url = base_url('development/home/index.html');
    return (redirect()->to($url));
}
```

#### `home(string $rnd = "index")`
- **Descripción**: Página de inicio del módulo
- **Ruta**: `/development/home/(:any)`
- **Parámetros**: 
  - `$rnd` (string): Parámetro aleatorio (para cache busting)
- **Vista**: `Views/Home/index`

```php
public function home(string $rnd = "index") {
    $this->oid = null;
    $this->prefix = "{$this->prefix}-home";
    return (view($this->viewer, $this->get_Array()));
}
```

#### `denied(string $rnd = null)`
- **Descripción**: Página de acceso denegado
- **Ruta**: `/development/(:any)` (cuando no autorizado)
- **Parámetros**: 
  - `$rnd` (string, opcional): Parámetro aleatorio
- **Vista**: `Views/Denied/index`

```php
public function denied(string $rnd = null): string {
    $this->oid = null;
    $this->prefix = "{$this->prefix}-denied";
    return (view($this->viewer, $this->get_Array()));
}
```

#### `semantic(string $oid)`
- **Descripción**: Vista semántica (uso avanzado)
- **Parámetros**: 
  - `$oid` (string): Identificador de objeto
- **Vista**: `Views/Home/index`

```php
public function semantic(string $oid) {
    $this->oid = $oid;
    $this->prefix = "{$this->prefix}-home";
    return (view($this->viewer, $this->get_Array()));
}
```

---

## 🤖 Generators

**Archivo**: `Controllers/Generators.php`

Controlador para acceso a los generadores de código desde la interfaz web.

### Métodos

#### `index()`
- **Descripción**: Redirige a la lista de generadores
- **Ruta**: `/development/generators/`
- **Return**: Redirect a `generators/list/`

#### `list(string $rnd)`
- **Descripción**: Muestra lista de generadores disponibles
- **Ruta**: `/development/generators/list/(:any)`
- **Vista**: `Views/Generators/List/index`
- **Parámetros**: 
  - `$rnd`: Parámetro aleatorio

```php
public function list(string $rnd) {
    $this->oid = null;
    $this->prefix = "{$this->prefix}-list";
    $this->component = $this->views . '\Generators\List';
    return (view($this->viewer, $this->get_Array()));
}
```

#### `model(string $oid)`
- **Descripción**: Generador de modelos
- **Ruta**: `/development/generators/model/(:any)`
- **Vista**: `Views/Generators/Model/index`
- **Parámetros**: 
  - `$oid`: Tabla seleccionada

#### `controller(string $oid)`
- **Descripción**: Generador de controladores
- **Ruta**: `/development/generators/controller/(:any)`
- **Vista**: `Views/Generators/Controller/index`
- **Parámetros**: 
  - `$oid`: Tabla seleccionada

#### `creator(string $oid)`
- **Descripción**: Generador de formularios de creación
- **Ruta**: `/development/generators/creator/(:any)`
- **Vista**: `Views/Generators/Creator/index`

#### `editor(string $oid)`
- **Descripción**: Generador de formularios de edición
- **Ruta**: `/development/generators/editor/(:any)`
- **Vista**: `Views/Generators/Editor/index`

#### `viewer(string $oid)`
- **Descripción**: Generador de vistas de lectura
- **Ruta**: `/development/generators/viewer/(:any)`
- **Vista**: `Views/Generators/Viewer/index`

#### `deleter(string $oid)`
- **Descripción**: Generador de formularios de eliminación
- **Ruta**: `/development/generators/deleter/(:any)`
- **Vista**: `Views/Generators/Deleter/index`

#### `lister(string $oid)`
- **Descripción**: Generador de vistas de lista
- **Ruta**: `/development/generators/lister/(:any)`
- **Vista**: `Views/Generators/Lister/index`

#### `migration(string $oid)`
- **Descripción**: Generador de migraciones
- **Ruta**: `/development/generators/migration/(:any)`
- **Vista**: `Views/Generators/Migration/index`

#### `lang(string $oid)`
- **Descripción**: Generador de archivos de idioma
- **Ruta**: `/development/generators/lang/(:any)`
- **Vista**: `Views/Generators/Lang/index`

---

## 🛠️ Tools

**Archivo**: `Controllers/Tools.php`

Controlador para herramientas de desarrollo.

### Métodos

#### `index()`
- **Descripción**: Redirige a inicio de herramientas
- **Ruta**: `/development/tools/`
- **Return**: Redirect a `tools/home/`

#### `home(string $rnd)`
- **Descripción**: Página principal de herramientas
- **Ruta**: `/development/tools/home/(:any)`
- **Vista**: `Views/Tools/Home/index`

```php
public function home(string $rnd) {
    $this->oid = $rnd;
    $this->prefix = "{$this->prefix}-home";
    $this->component = $this->views . '\Tools\Home';
    return (view($this->viewer, $this->get_Array()));
}
```

#### `view(string $oid, string $rnd)`
- **Descripción**: Ver detalles de una herramienta
- **Ruta**: `/development/tools/view/(:any)/(:any)`
- **Parámetros**: 
  - `$oid`: ID de herramienta
  - `$rnd`: Parámetro aleatorio

---

## 🎨 UI

**Archivo**: `Controllers/Ui.php`

Controlador para demostración de componentes UI de Bootstrap 5.

### Métodos

#### `index()`
- **Descripción**: Redirige a inicio de UI
- **Return**: Redirect a `ui/home/`

#### `home(string $rnd)`
- **Descripción**: Página de inicio de componentes
- **Ruta**: `/development/ui/home/(:any)`
- **Vista**: `Views/Ui/Home/index`

#### `buttons(string $oid, string $rnd)`
- **Descripción**: Demostración de botones
- **Ruta**: `/development/ui/buttons/(:any)/(:any)`
- **Vista**: `Views/Ui/Buttons/index`

Muestra ejemplos de:
- Botones con diferentes variantes (primary, secondary, success, danger, etc.)
- Tamaños de botones (sm, md, lg)
- Botones deshabilitados
- Botones con iconos

#### `chatbox(string $oid, string $rnd)`
- **Descripción**: Componente de chat
- **Ruta**: `/development/ui/chatbox/(:any)/(:any)`
- **Vista**: `Views/Ui/Chatbox/index`

#### `uploaders(string $oid, string $rnd)`
- **Descripción**: Componentes de carga de archivos
- **Ruta**: `/development/ui/uploaders/(:any)/(:any)`
- **Vista**: `Views/Ui/Uploaders/index`

---

## 📦 Webpack

**Archivo**: `Controllers/Webpack.php`

Controlador para gestión de webpack y bundling de assets.

### Métodos

#### `index()`
- **Descripción**: Redirige a inicio de webpack
- **Return**: Redirect a `webpack/home/`

#### `home(string $rnd)`
- **Descripción**: Página de gestión de webpack
- **Ruta**: `/development/webpack/home/(:any)`
- **Vista**: `Views/Webpack/Home/index`

Permite:
- Ver estado de webpack
- Ejecutar builds
- Configurar opciones
- Ver logs de compilación

---

## 💻 IDE

**Archivo**: `Controllers/Ide.php`

Controlador para IDE integrado.

### Métodos

#### `index()`
- **Descripción**: Redirige a inicio de IDE
- **Return**: Redirect a `ide/home/`

#### `home(string $rnd)`
- **Descripción**: Interfaz principal del IDE
- **Ruta**: `/development/ide/home/(:any)`
- **Vista**: `Views/Ide/Home/index`

Características:
- Editor de código
- Vista de árbol de archivos
- Terminal integrada
- Depurador
- Integraciones de IA

---

## 🤖 AI

**Archivo**: `Controllers/AI.php`

Controlador para funcionalidades basadas en inteligencia artificial.

### Funcionalidades

- Generación automática de código con IA
- Análisis de código
- Sugerencias inteligentes
- Refactoring automático

---

## 🔌 Api

**Archivo**: `Controllers/Api.php`

Controlador para endpoints API REST del módulo.

### Características

- Endpoints JSON
- Autenticación API
- Rate limiting
- CORS handling

---

## 🧭 Router

**Archivo**: `Controllers/Router.php`

Controlador para enrutamiento dinámico.

### Métodos

#### `route(string $module, string $controller, string $action)`
- **Descripción**: Enrutamiento dinámico de solicitudes
- **Ruta**: `/development/(:any)/(:any)/(:any)`
- **Parámetros**: 
  - `$module`: Nombre del módulo
  - `$controller`: Nombre del controlador
  - `$action`: Acción a ejecutar

```php
public function route($module, $controller, $action) {
    // Enrutamiento dinámico
    // Permite llamar a controladores sin definir rutas explícitas
}
```

---

## 🔐 Control de Acceso

Todos los controladores verifican autorización:

```php
// En Config/Routes.php
if ($authorized === 'authorized') {
    // Registrar rutas
} else {
    // Solo rutas de acceso denegado
}
```

---

## 🧪 Pruebas de Controladores

Para probar un controlador manualmente:

```bash
# Acceder a través del navegador
http://tu-app.local/development/

# O usando curl
curl -i http://tu-app.local/development/generators/list/123
```

Para verificar sintaxis:

```bash
php -l app/Modules/Development/Controllers/Development.php
php -l app/Modules/Development/Controllers/Generators.php
```

---

## 📚 Documentos Relacionados

- [README.md](../README.md) - Descripción general
- [architecture.md](architecture.md) - Arquitectura
- [comandos_cli.md](comandos_cli.md) - Comandos Spark
- [modelos.md](modelos.md) - Documentación de modelos

---

**Última Actualización**: 2026-04-04
