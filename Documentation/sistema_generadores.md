# Sistema de Generadores de Código

> Documentación detallada del sistema de generadores de código del módulo Development.

---

## 📋 Tabla de Contenidos

1. [Descripción General](#descripción-general)
2. [Arquitectura del Sistema](#arquitectura-del-sistema)
3. [Tipos de Generadores](#tipos-de-generadores)
4. [Proceso de Generación](#proceso-de-generación)
5. [Crear un Generador Personalizado](#crear-un-generador-personalizado)
6. [Plantillas y Templates](#plantillas-y-templates)
7. [Configuración Avanzada](#configuración-avanzada)

---

## 🎯 Descripción General

El sistema de generadores permite crear automáticamente archivos de código basados en información de tablas de base de datos.

### Objetivos

- ✅ **Acelerar desarrollo**: Generar código boilerplate automáticamente
- ✅ **Mantener consistencia**: Seguir patrones estándar
- ✅ **Reducir errores**: Código validado y probado
- ✅ **Facilitar mantenimiento**: Cambios en estructura generan cambios en código

### Casos de Uso

```
Tabla en BD → Generador → Código PHP → Desarrollo Manual
```

Ejemplo:
```
tabla: employees → GenerateModel → Models/Employees.php
                → GenerateController → Controllers/Employees.php
                → GenerateViewer → Views/Employees/Viewer/index.php
```

---

## 🏗️ Arquitectura del Sistema

### Componentes

```
┌─────────────────────────────────────────────┐
│         Base: BaseCommand                    │  (Higgs CLI)
├─────────────────────────────────────────────┤
│  GenerateController                          │
│  GenerateModel                               │  Comandos CLI
│  GenerateMigration                           │  (Commands/)
│  GenerateViewer, etc.                        │
├─────────────────────────────────────────────┤
│  Helpers:                                    │
│  - get_development_code_copyright()          │  Utilidades
│  - buildController()                         │  Comunes
│  - buildModel()                              │
└─────────────────────────────────────────────┘
```

### Estructura de un Comando

```php
class GenerateXyz extends BaseCommand {
    // Metadatos del comando
    protected $group = 'Development';
    protected $name = 'development:generate-xyz';
    protected $description = 'Generates XYZ file...';
    
    // Ejecutar comando
    public function run(array $params): int {
        // 1. Validar parámetros
        // 2. Procesar input
        // 3. Construir contenido
        // 4. Escribir archivo
        // 5. Retornar estado
        return EXIT_SUCCESS;
    }
    
    // Métodos privados para construir contenido
    private function buildXyz(...) {
        // Generar contenido del archivo
    }
}
```

---

## 🎯 Tipos de Generadores

### 1. GenerateModel

**Propósito**: Crear clase Model para acceso a datos

**Input**:
```
Tabla: access_users
```

**Output**:
```php
// app/Modules/Access/Models/Users.php
namespace App\Modules\Access\Models;

use CodeIgniter\Model;

class Users extends Model {
    protected $table = 'access_users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'email', ...];
    
    // Métodos de validación
    protected $validationRules = [...];
    
    // Métodos personalizados
    public function getActive() { ... }
}
```

### 2. GenerateController

**Propósito**: Crear clase Controller con métodos CRUD

**Input**:
```
Tabla: access_users
```

**Output**:
```php
// app/Modules/Access/Controllers/_Users.php
namespace App\Modules\Access\Controllers;

class _Users extends ModuleController {
    public function __construct() { ... }
    
    public function index() { ... }
    public function view($id) { ... }
    public function create() { ... }
    public function update($id) { ... }
    public function delete($id) { ... }
}
```

### 3. GenerateMigration

**Propósito**: Crear archivo de migración para BD

**Input**:
```
Tabla: create_users_table
```

**Output**:
```php
// app/Modules/Database/Migrations/2026-04-04-xxxxx_CreateUsersTable.php
namespace App\Modules\Database\Migrations;

class CreateUsersTable extends Migration {
    public function up() {
        $this->forge->createTable('users', function($table) {
            $table->increments('id');
            $table->string('name');
            // ...
        });
    }
    
    public function down() {
        $this->forge->dropTable('users');
    }
}
```

### 4-8. GenerateViewer, Creator, Editor, Deleter, Lister

**Propósito**: Crear vistas para CRUD

Cada uno genera una vista específica:
- **Viewer**: Mostrar un registro
- **Creator**: Formulario de creación
- **Editor**: Formulario de edición
- **Deleter**: Confirmación de eliminación
- **Lister**: Tabla de registros

### 9. GenerateLang

**Propósito**: Crear archivo de idioma

**Input**:
```
Componente: users
```

**Output**:
```php
// app/Language/es/Users.php
return [
    'title' => 'Usuarios',
    'name' => 'Nombre',
    'email' => 'Email',
    // ...
];
```

---

## 🔄 Proceso de Generación

### Flujo Detallado

```
1. ENTRADA
   ↓
2. Usuario ejecuta comando
   $ php spark development:generate-model access_users
   ↓
3. Framework cargar comando (BaseCommand)
   ↓
4. run() es ejecutado
   ↓
5. Validación de parámetros
   - Verificar tabla
   - Extraer módulo y componente
   ↓
6. Procesamiento
   - Conectar a BD
   - Obtener información de tabla
   - Calcular rutas de archivos
   ↓
7. Construcción de contenido
   - buildModel() crea código PHP
   - Agregar cabecera de copyright
   - Agregar métodos base
   ↓
8. Escritura de archivo
   - Crear directorios si no existen
   - Escribir archivo
   - Establecer permisos
   ↓
9. Feedback
   - Mostrar mensaje de éxito
   - Indicar ubicación del archivo
   ↓
10. SALIDA (EXIT_SUCCESS)
```

### Ejemplos de Ejecución

#### Ejemplo 1: Generar Modelo

```bash
$ php spark development:generate-model sales_orders

Parsing table: sales_orders...
  Module: sales
  Component: orders

Creating directory: /app/Modules/Sales/Models/
  Created: /app/Modules/Sales/Models/Orders.php
Model file generated successfully.
```

#### Ejemplo 2: Generar Controlador

```bash
$ php spark development:generate-controller sales_orders

Creating directory: /app/Modules/Sales/Controllers/
  Created: /app/Modules/Sales/Controllers/_Orders.php
Controller file generated successfully.

Remember to add these to your Routes.php:
  'sales-orders' => 'Sales\Controllers\_Orders@...'
```

---

## 🛠️ Crear un Generador Personalizado

### Paso 1: Crear Archivo de Comando

Crear `Commands/GenerateCustom.php`:

```php
<?php

namespace App\Modules\Development\Commands;

use Higgs\CLI\BaseCommand;
use Higgs\CLI\CLI;

class GenerateCustom extends BaseCommand
{
    protected $group = 'Development';
    protected $name = 'development:generate-custom';
    protected $description = 'Generates custom component files';
    protected $usage = 'development:generate-custom <table>';
    protected $arguments = [
        'table' => 'Table name (e.g. module_component)',
    ];

    public function run(array $params): int
    {
        $table = array_shift($params);
        
        if (empty($table)) {
            CLI::error('Usage: php spark development:generate-custom <table>');
            return EXIT_ERROR;
        }

        // Parsear nombre de tabla
        $eid = explode('_', $table);
        if (count($eid) < 2) {
            CLI::error('Table name must be in format: module_component');
            return EXIT_ERROR;
        }

        $ucfModule = ucfirst($eid[0]);
        $ucfComponent = ucfirst($eid[1]);

        // Construir ruta del archivo
        $dir = APPPATH . "Modules/{$ucfModule}/Custom";
        $file = "{$dir}/_{$ucfComponent}.php";

        // Crear directorio
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
            CLI::write("Created directory: {$dir}", 'green');
        }

        // Construir contenido
        $content = $this->buildCustom($ucfModule, $ucfComponent);

        // Escribir archivo
        file_put_contents($file, $content);
        CLI::write("  Created: {$file}", 'yellow');
        CLI::write('Custom file generated successfully.', 'green');

        return EXIT_SUCCESS;
    }

    private function buildCustom(string $module, string $component): string
    {
        $copyright = generate_development_code_copyright([
            'path' => "Modules/{$module}/Custom/_{$component}.php"
        ]);

        $c = "<?php\n";
        $c .= "\n";
        $c .= "namespace App\\Modules\\{$module}\\Custom;\n";
        $c .= $copyright;
        $c .= "\n";
        $c .= "class {$component} {\n";
        $c .= "    // Implementar funcionalidad personalizada\n";
        $c .= "}\n";
        $c .= "?>\n";

        return $c;
    }
}
```

### Paso 2: Registrar el Comando

El comando se registra automáticamente si está en la carpeta `Commands/`.

### Paso 3: Probar el Comando

```bash
php spark development:generate-custom sales_orders
```

---

## 📝 Plantillas y Templates

### Sistema de Plantillas

Las plantillas se almacenan en `Views/Generators/{ComponentType}/coders/`:

```
Views/Generators/
├── Controller/
│   ├── coders/
│   │   ├── breadcrumb.php
│   │   ├── form.php
│   │   ├── processor.php
│   │   └── validator.php
│   └── index.php
├── Creator/
│   ├── coders/
│   │   ├── breadcrumb.php
│   │   ├── form.php
│   │   ├── processor.php
│   │   └── validator.php
│   └── index.php
└── ...
```

### Personalizar Plantillas

Para personalizar el código generado, editar las vistas correspondientes:

```php
// Views/Generators/Controller/coders/form.php
// Este archivo contiene la plantilla para generar controladores
```

---

## ⚙️ Configuración Avanzada

### Variables de Configuración

En los comandos, se pueden usar variables:

```php
$eid = explode('_', $table);
$ucfModule = ucfirst($eid[0]);      // Módulo en PascalCase
$slcModule = strtolower($eid[0]);   // Módulo en minúsculas
$ucfComponent = ucfirst($eid[1]);   // Componente en PascalCase
$slcComponent = strtolower($eid[1]); // Componente en minúsculas
```

### Convenciones de Nombres

| Patrón | Ejemplo | Uso |
|--------|---------|-----|
| `$table` | `access_users` | Nombre original de tabla |
| `$ucfModule` | `Access` | PascalCase para namespaces |
| `$slcModule` | `access` | lowercase para rutas |
| `$ucfComponent` | `Users` | PascalCase para clases |
| `$slcComponent` | `users` | lowercase para archivos |

### Copyright Automático

Todos los archivos incluyen encabezado de copyright:

```php
generate_development_code_copyright([
    'path' => "Modules/Access/Models/Users.php"
])
```

Genera:
```php
/**
 * █ ─────────────────────────────────────────────────
 * █ ░FRAMEWORK                                  2026-04-04
 * █ [Modules/Access/Models/Users.php]
 * █ Copyright 2023 - CloudEngine S.A.S., Inc. <admin@cgine.com>
 * █ ─────────────────────────────────────────────────
 * ...
 */
```

---

## 🧪 Probar Generadores

### Prueba Manual

```bash
# Generar cada componente
php spark development:generate-model test_users
php spark development:generate-controller test_users
php spark development:generate-migration create_test_users_table
php spark development:generate-viewer test_users
php spark development:generate-creator test_users
php spark development:generate-editor test_users
php spark development:generate-deleter test_users
php spark development:generate-lister test_users
php spark development:generate-lang users

# Verificar archivos creados
find app/Modules/Test -name "*Users*" -o -name "*users*"
```

### Verificación de Sintaxis

```bash
# Después de generar, verificar sintaxis
php -l app/Modules/Test/Models/Users.php
php -l app/Modules/Test/Controllers/_Users.php
```

### Ejecutar Pruebas

```bash
./vendor/bin/phpunit tests/Module/Test/
```

---

## 🔧 Troubleshooting de Generadores

### Problema: "Table not found"

```bash
# Solución: Asegurarse que la tabla existe
mysql> SHOW TABLES LIKE 'access_users';

# Si no existe, crear primero
php spark development:generate-migration create_access_users_table
php spark migrate
```

### Problema: "Permission denied"

```bash
# Solución: Verificar permisos
chmod -R 755 app/Modules/
chmod -R 755 app/Language/

# Si aún falla, usar sudo
sudo chmod -R 755 app/Modules/
```

### Problema: Clase duplicada

```bash
# Solución: Si el archivo ya existe, eliminarlo primero
rm app/Modules/Access/Models/Users.php

# O renombrar el módulo
php spark development:generate-model different_users
```

---

## 📚 Documentos Relacionados

- [README.md](../README.md) - Descripción general
- [comandos_cli.md](comandos_cli.md) - Referencia de comandos
- [architecture.md](architecture.md) - Arquitectura del sistema
- [ejemplos_uso.md](ejemplos_uso.md) - Ejemplos prácticos

---

**Última Actualización**: 2026-04-04
