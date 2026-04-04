# Arquitectura del Módulo Development

> Documento que describe la arquitectura, patrones de diseño y decisiones técnicas del módulo Development.

---

## 📋 Tabla de Contenidos

1. [Visión General](#visión-general)
2. [Patrones Arquitectónicos](#patrones-arquitectónicos)
3. [Capas de la Aplicación](#capas-de-la-aplicación)
4. [Flujo de Solicitud](#flujo-de-solicitud)
5. [Componentes Principales](#componentes-principales)
6. [Sistema de Generadores](#sistema-de-generadores)
7. [Integración con Bootstrap](#integración-con-bootstrap)
8. [Seguridad y Autorización](#seguridad-y-autorización)

---

## 🏗️ Visión General

El módulo Development sigue una arquitectura modular basada en el patrón **MVC (Model-View-Controller)** de Higgs (CodeIgniter 4). Se divide en capas claramente separadas:

```
┌─────────────────────────────────────────────┐
│         Interfaz de Usuario (Vistas)        │  → Views/
├─────────────────────────────────────────────┤
│       Controladores HTTP (Lógica)           │  → Controllers/
├─────────────────────────────────────────────┤
│    Modelos (Acceso a Datos)                 │  → Models/
├─────────────────────────────────────────────┤
│    Base de Datos                            │  → Database/
└─────────────────────────────────────────────┘
```

---

## 🎯 Patrones Arquitectónicos

### 1. **Model-View-Controller (MVC)**
- **Models**: Encapsulan la lógica de acceso a datos
- **Views**: Presentan información al usuario
- **Controllers**: Orquestan la lógica de negocio

### 2. **Inyección de Dependencias**
Higgs utiliza un contenedor de servicios para inyectar dependencias:

```php
$service = service('service_name');
$platform = service('platform');
```

### 3. **Enrutamiento Modular**
Cada módulo define sus propias rutas en `Config/Routes.php`:

```php
$routes->group('development', ['namespace' => 'App\Modules\Development\Controllers'], function($routes) {
    $routes->add('/', 'Development::index');
    $routes->add('/generators/list/(:any)', 'Generators::list/$1');
});
```

### 4. **Generadores de Código**
Patrón Builder para crear código dinámicamente:

```php
// Ejemplo: GenerateController.php
$content = $this->buildController($ucfModule, $ucfComponent, ...);
file_put_contents($pathfile, $content);
```

### 5. **Control de Acceso Basado en Roles (RBAC)**
Utiliza el servicio `platform` para verificar permisos:

```php
$authorized = $platform->getAuthorizedModule('development');
```

---

## 🔷 Capas de la Aplicación

### Capa de Presentación (Views)

**Ubicación**: `Views/`

Responsabilidades:
- Renderizar componentes Bootstrap 5
- Mostrar datos al usuario
- Capturar entrada del usuario

Estructura:
```
Views/
├── index.php              ← Vista principal envolvente
├── Home/                  ← Vistas de inicio
├── Generators/            ← Vistas de generadores de código
├── Tools/                 ← Vistas de herramientas
├── UI/                    ← Vistas de componentes UI
└── E404/                  ← Vistas de error
```

**Protocolo**: Todas las vistas usan componentes del paquete `Higgs\Frontend\Bootstrap\v5_3_3`

### Capa de Lógica (Controllers)

**Ubicación**: `Controllers/`

Responsabilidades:
- Procesar solicitudes HTTP
- Coordinar Models y Views
- Aplicar reglas de negocio
- Gestionar autenticación y autorización

**Controladores Principales**:

| Controlador | Responsabilidad |
|-------------|-----------------|
| `Development.php` | Punto de entrada, redirecciones |
| `Generators.php` | Interfaz de generadores de código |
| `Tools.php` | Herramientas de desarrollo |
| `UI.php` | Demostraciones de componentes |
| `Webpack.php` | Gestión de bundling |
| `Ide.php` | IDE integrado |
| `AI.php` | Funcionalidades IA |
| `Api.php` | Endpoints API |
| `Router.php` | Enrutamiento dinámico |

### Capa de Datos (Models)

**Ubicación**: `Models/`

Responsabilidades:
- Acceder a la base de datos
- Validar datos
- Implementar lógica de negocio a nivel de datos

**Modelos Disponibles**:

```php
Development_Modules.php              // Información de módulos
Development_Clients_Modules.php      // Asociación cliente-módulo
Development_Users.php                // Usuarios del desarrollo
Development_Users_Fields.php         // Campos de usuario
```

---

## 🔄 Flujo de Solicitud

### Flujo Típico

```
1. Usuario solicita /development/generators/list/123
   ↓
2. Higgs Router → Development\Config\Routes.php
   ↓
3. Ruta matchea: Generators::list/$1
   ↓
4. Controlador: Generators->list('123')
   ↓
5. Controlador establece propiedades:
   - $this->oid = '123'
   - $this->prefix = 'development-generators-list'
   - $this->component = 'Views\Generators\List'
   ↓
6. Controlador llama: view($this->viewer, $this->get_Array())
   ↓
7. Vista principal (Views/index.php) incluye el componente
   ↓
8. HTML se renderiza y se envía al usuario
```

### Verificación de Autorización

```
Request → ModuleController->__construct()
              ↓
         service('platform')->getAuthorizedModule($module)
              ↓
         if ($authorized === 'authorized')
            // Procesar solicitud
         else
            // Mostrar vista de acceso denegado
```

---

## 🧩 Componentes Principales

### 1. ModuleController (Clase Base)

Todos los controladores extienden `ModuleController`:

```php
class Generators extends ModuleController {
    public function __construct() {
        parent::__construct();
        $this->prefix = 'development-generators';
        $this->module = 'App\Modules\Development';
        $this->views = $this->module . '\Views';
        $this->viewer = $this->views . '\index';
    }
}
```

**Propiedades Heredadas**:
- `$authentication`: Usuario autenticado
- `$request`: Solicitud HTTP actual
- `$dates`: Información de fechas
- `$parent`: Módulo padre

### 2. Comandos CLI (Generadores)

**Ubicación**: `Commands/`

Generadores de código ejecutados via CLI:

```bash
php spark development:generate-controller users
php spark development:generate-model users
php spark development:generate-migration create_users
```

Estructura de un comando:

```php
class GenerateController extends BaseCommand {
    protected $name = 'development:generate-controller';
    
    public function run(array $params): int {
        // Lógica de generación
        return EXIT_SUCCESS;
    }
    
    private function buildController(...) {
        // Construir contenido PHP
    }
}
```

### 3. Helper Development

**Ubicación**: `Helpers/Development_helper.php`

Funciones auxiliares:

```php
generate_development_permissions()      // Registra permisos del módulo
get_development_sidebar($active_url)    // Barra lateral de navegación
get_development_code_copyright($args)   // Cabecera de copyright para código generado
```

---

## 🤖 Sistema de Generadores

### Funcionamiento General

Los generadores crean archivos PHP basados en información de la base de datos.

### Generadores Disponibles

| Generador | Salida | Ubicación |
|-----------|--------|-----------|
| Model | Clase Model | `Modules/{Module}/Models/` |
| Controller | Clase Controller | `Modules/{Module}/Controllers/` |
| Migration | Clase Migration | `Modules/{Module}/Database/Migrations/` |
| Viewer | Vista de lectura | `Modules/{Module}/Views/{Component}/` |
| Creator | Formulario de creación | `Modules/{Module}/Views/{Component}/` |
| Editor | Formulario de edición | `Modules/{Module}/Views/{Component}/` |
| Deleter | Formulario de eliminación | `Modules/{Module}/Views/{Component}/` |
| Lister | Vista de lista | `Modules/{Module}/Views/{Component}/` |
| Lang | Archivo de idioma | `Modules/{Module}/Language/es/` |

### Proceso de Generación

```
1. Usuario selecciona tabla en la interfaz
   ↓
2. Controlador procesa formulario
   ↓
3. Comando CLI es invocado (o se ejecuta directamente)
   ↓
4. Generador parsea información de tabla
   ↓
5. Generador construye código PHP
   ↓
6. Archivo se escribe en el sistema de archivos
   ↓
7. Usuario es notificado del éxito
```

### Ejemplo: Generar un Controlador

```php
// Input: tabla = "access_users"
// Output: Clase Controller_Access_Users en Controllers/Access_Users.php

$eid = explode('_', $table);           // ['access', 'users']
$ucfModule = ucfirst($eid[0]);         // 'Access'
$ucfComponent = ucfirst($eid[1]);      // 'Users'

$classname = "{$ucfModule}_{$ucfComponent}";  // 'Access_Users'
$pathfile = "Modules/{$ucfModule}/Controllers/_{$ucfComponent}.php";
```

---

## 🎨 Integración con Bootstrap 5

### Protocolo de HTML Bootstrap

Todas las vistas siguen el **HTML_BOOTSTRAP_PROTOCOL.md**:

```php
use Higgs\Frontend\Bootstrap\v5_3_3\Bootstrap as BS5;

// ✅ Correcto: Usar componente BS5
BS5::button(['content' => 'Guardar', 'variant' => 'success'])->render();

// ❌ Incorrecto: HTML manual
echo '<button class="btn btn-success">Guardar</button>';
```

### Categorías de Componentes

- **Layout**: Container, Row, Col, Grid
- **Interface**: Button, Card, Modal, Alert, Dropdown, Toast
- **Form**: Input, Select, Textarea, Checkbox, Radio
- **Navigation**: Navbar, Breadcrumb, Pagination
- **Content**: Table, Image, Typography
- **Extras**: Tabs, Accordion, Croppie

---

## 🔒 Seguridad y Autorización

### Niveles de Control de Acceso

1. **Nivel Módulo**: ¿Puede el usuario acceder al módulo Development?
   ```php
   $authorized = $platform->getAuthorizedModule('development');
   ```

2. **Nivel Funcionalidad**: Permisos específicos del módulo
   ```php
   $permission = 'development-access';
   ```

### Validación de Entrada

- Todos los datos POST se validan en el controlador
- Se sanitizan HTML y JavaScript
- Se implementa CSRF protection via Higgs

### Autenticación

El módulo requiere usuario autenticado:

```php
if (!authentication()->user()) {
    return redirect()->to('login');
}
```

---

## 📊 Diagrama de Dependencias

```
Views/
  ├── Compone con → Bootstrap Components
  └── Renderiza → Controllers

Controllers/
  ├── Extiende → ModuleController
  ├── Usa → Models
  ├── Usa → Helpers
  └── Autoriza via → Platform Service

Models/
  ├── Extienden → BaseModel
  └── Acceden a → Database

Commands/
  ├── Extienden → BaseCommand
  ├── Generan → Controllers, Models, etc.
  └── Usan → Helpers
```

---

## 🔧 Extensibilidad

### Crear un Nuevo Generador

1. Extender `Higgs\CLI\BaseCommand`
2. Definir propiedades: `$name`, `$description`, `$usage`
3. Implementar método `run()`
4. Incluir método privado para construir contenido

```php
namespace App\Modules\Development\Commands;

use Higgs\CLI\BaseCommand;

class GenerateCustom extends BaseCommand {
    protected $name = 'development:generate-custom';
    
    public function run(array $params): int {
        // Implementar lógica
    }
}
```

### Agregar un Nuevo Controlador

1. Crear clase que extienda `ModuleController`
2. Definir métodos de acción
3. Registrar rutas en `Config/Routes.php`
4. Crear vistas en `Views/{ControllerName}/`

---

## 📈 Escalabilidad

El módulo está diseñado para:

- **Crecer con nuevos generadores**: Fácil agregar más tipos de generadores
- **Mantener múltiples módulos**: Sistema RBAC permite control granular
- **Extenderse con funcionalidades**: Arquitectura modular permite plugins

---

## 🎯 Decisiones de Diseño

| Decisión | Justificación |
|----------|---------------|
| MVC Modular | Separación de responsabilidades |
| Bootstrap 5 | Componentes modernos y responsivos |
| Generadores de Código | Acelera desarrollo repetitivo |
| CLI Commands | Automatización sin interfaz web |
| RBAC | Control granular de acceso |
| Helpers | Reutilización de funcionalidad |

---

## 📚 Referencias

- [CodeIgniter 4 Documentation](https://codeigniter.com/user_guide/)
- [Bootstrap 5 Documentation](https://getbootstrap.com/docs/5.0/)
- [PHP PSR Standards](https://www.php-fig.org/)
- [SOLID Principles](https://en.wikipedia.org/wiki/SOLID)

---

**Última Actualización**: 2026-04-04
