# Ejemplos de Uso Prácticos

> Casos de uso reales y ejemplos de cómo usar el módulo Development en situaciones comunes.

---

## 📋 Tabla de Contenidos

1. [Crear un Módulo Completo](#crear-un-módulo-completo)
2. [Gestionar Permisos y Usuarios](#gestionar-permisos-y-usuarios)
3. [Usar Generadores en Flujo de Trabajo](#usar-generadores-en-flujo-de-trabajo)
4. [Integración con CI/CD](#integración-con-cicd)
5. [Ejemplos Avanzados](#ejemplos-avanzados)

---

## 🚀 Crear un Módulo Completo

### Escenario

Necesitas crear un módulo de **Inventario** que gestione productos en la base de datos.

### Paso 1: Estructura Base

Crear la estructura del módulo:

```bash
mkdir -p app/Modules/Inventory/{Config,Controllers,Models,Views,Language/es,Database/Migrations}
```

### Paso 2: Crear Tabla en Base de Datos

```sql
-- app/Modules/Inventory/Database/Migrations/2026-04-04-000001_CreateProductsTable.php

CREATE TABLE inventory_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    quantity INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE INDEX idx_code ON inventory_products(code);
CREATE INDEX idx_status ON inventory_products(status);
```

### Paso 3: Generar Componentes

```bash
# Generar modelo
php spark development:generate-model inventory_products

# Generar controlador
php spark development:generate-controller inventory_products

# Generar vistas
php spark development:generate-viewer inventory_products
php spark development:generate-creator inventory_products
php spark development:generate-editor inventory_products
php spark development:generate-deleter inventory_products
php spark development:generate-lister inventory_products

# Generar archivo de idioma
php spark development:generate-lang products
```

### Paso 4: Registrar Rutas

Editar `app/Modules/Inventory/Config/Routes.php`:

```php
<?php

use Config\Services;

$platform = service('platform');

if ($platform->getCandidate(__FILE__)) {
    $routes = $routes ?? Services::routes(true);
    $module = 'inventory';
    $namespace = 'App\Modules\Inventory\Controllers';
    $authorized = $platform->getAuthorizedModule($module);
    
    $routes->group($module, ['namespace' => $namespace], function ($subroutes) use ($authorized) {
        if ($authorized === 'authorized') {
            $subroutes->add('/', 'Products::index');
            $subroutes->add('/products', 'Products::list');
            $subroutes->add('/products/view/(:num)', 'Products::view/$1');
            $subroutes->add('/products/create', 'Products::create');
            $subroutes->add('/products/edit/(:num)', 'Products::edit/$1');
            $subroutes->add('/products/delete/(:num)', 'Products::delete/$1');
        }
    });
}
```

### Paso 5: Registrar Módulo

Editar `app/Config/Modules.php`:

```php
public $modules = [
    'Inventory' => [
        'namespace' => 'App\Modules\Inventory',
        'path'      => APPPATH . 'Modules/Inventory',
    ],
];
```

### Paso 6: Configurar Permisos

```bash
# Registrar permisos del módulo
generate_inventory_permissions();
```

O manualmente en la BD:
```php
INSERT INTO permissions (name, description, module)
VALUES ('inventory-access', 'Acceso al módulo Inventario', 'inventory');
```

### Paso 7: Verificar

```bash
# Verificar sintaxis
php -l app/Modules/Inventory/Controllers/_Products.php

# Acceder en el navegador
http://tu-app.local/inventory/

# Ver lista de productos
http://tu-app.local/inventory/products
```

---

## 👥 Gestionar Permisos y Usuarios

### Crear Nuevo Usuario del Módulo

```php
// En un controlador o comando
$userModel = new \App\Modules\Development\Models\Development_Users();

$userData = [
    'name' => 'Juan Pérez',
    'email' => 'juan@example.com',
    'password' => password_hash('SecurePassword123!', PASSWORD_BCRYPT),
    'role' => 'developer',
    'status' => 'active'
];

if ($userModel->validate($userData)) {
    $userId = $userModel->insert($userData);
    echo "Usuario creado con ID: {$userId}";
} else {
    print_r($userModel->errors());
}
```

### Asignar Módulo a Cliente

```php
// En un controlador
$clientModulesModel = new \App\Modules\Development\Models\Development_Clients_Modules();

$clientModulesModel->assignModule(
    clientId: 123,      // ID del cliente
    moduleId: 5,        // ID del módulo Development
    accessLevel: 'admin' // read, write, admin
);

echo "Módulo asignado exitosamente";
```

### Verificar Acceso de Usuario

```php
// Middleware o controlador
$clientModulesModel = new \App\Modules\Development\Models\Development_Clients_Modules();

$hasAccess = $clientModulesModel->hasAccess(
    clientId: $user['client_id'],
    moduleId: $module['id']
);

if (!$hasAccess) {
    return response()->setStatusCode(403, 'Acceso Denegado');
}
```

### Actualizar Rol de Usuario

```php
$userModel = new \App\Modules\Development\Models\Development_Users();

$userModel->update(userId: 42, ['role' => 'admin', 'status' => 'active']);

echo "Usuario actualizado";
```

---

## 🔄 Usar Generadores en Flujo de Trabajo

### Escenario: Ciclo de Desarrollo

#### 1. Sprint Planning

```
Tarea: Crear módulo de Recursos Humanos
Tabla: hr_employees
Campos: id, name, email, position, salary, department_id
```

#### 2. Generación Automática

```bash
#!/bin/bash
# Script: generate_hr_module.sh

echo "Generando módulo HR..."

php spark development:generate-model hr_employees
php spark development:generate-controller hr_employees
php spark development:generate-viewer hr_employees
php spark development:generate-creator hr_employees
php spark development:generate-editor hr_employees
php spark development:generate-deleter hr_employees
php spark development:generate-lister hr_employees
php spark development:generate-lang employees

echo "✓ Módulo generado"
echo ""
echo "Próximos pasos:"
echo "1. Editar Controllers/_Employees.php"
echo "2. Agregar validaciones en Models/Employees.php"
echo "3. Personalizar vistas en Views/Employees/"
echo "4. Registrar rutas en Config/Routes.php"
```

#### 3. Personalización Manual

Editar `app/Modules/Hr/Models/Employees.php`:

```php
class Employees extends Model {
    // ... código generado ...
    
    // Agregar método personalizado
    public function getByDepartment($departmentId) {
        return $this->where('department_id', $departmentId)
                    ->where('status', 'active')
                    ->findAll();
    }
    
    // Agregar validaciones
    protected $validationRules = [
        'name'          => 'required|string|max_length[255]',
        'email'         => 'required|email|is_unique[hr_employees.email]',
        'position'      => 'required|string',
        'salary'        => 'required|numeric|greater_than[0]',
        'department_id' => 'required|numeric|is_not_unique[hr_departments.id]',
    ];
}
```

#### 4. Testing

```bash
# Verificar modelo
php -l app/Modules/Hr/Models/Employees.php

# Ejecutar pruebas
./vendor/bin/phpunit tests/Modules/Hr/

# Probar en navegador
http://localhost/hr/employees
```

---

## 🔗 Integración con CI/CD

### GitHub Actions Example

Crear `.github/workflows/generate-code.yml`:

```yaml
name: Generate Development Code

on:
  push:
    branches: [main]
    paths:
      - 'database/migrations/**'
  workflow_dispatch:

jobs:
  generate:
    runs-on: ubuntu-latest
    
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
      
      - name: Install dependencies
        run: composer install
      
      - name: Generate Models
        run: |
          php spark development:generate-model app_users
          php spark development:generate-model app_roles
          php spark development:generate-model app_permissions
      
      - name: Generate Controllers
        run: |
          php spark development:generate-controller app_users
          php spark development:generate-controller app_roles
      
      - name: Generate Views
        run: |
          php spark development:generate-viewer app_users
          php spark development:generate-creator app_users
          php spark development:generate-editor app_users
          php spark development:generate-lister app_users
      
      - name: Run tests
        run: ./vendor/bin/phpunit --stop-on-failure
      
      - name: Commit generated files
        run: |
          git config --global user.email "ci@example.com"
          git config --global user.name "CI Bot"
          git add app/Modules/*/
          git commit -m "Auto-generate code from migrations" || echo "No changes"
          git push
```

### GitLab CI Example

Crear `.gitlab-ci.yml`:

```yaml
stages:
  - generate
  - test

generate_code:
  stage: generate
  image: php:8.2
  script:
    - composer install
    - php spark development:generate-model app_users
    - php spark development:generate-controller app_users
    - php -l app/Modules/App/Models/Users.php
  artifacts:
    paths:
      - app/Modules/App/
    expire_in: 1 day

run_tests:
  stage: test
  image: php:8.2
  script:
    - composer install
    - ./vendor/bin/phpunit --stop-on-failure
  dependencies:
    - generate_code
```

---

## 💡 Ejemplos Avanzados

### Crear Generador Personalizado para tu Proyecto

Crear `app/Modules/Development/Commands/GenerateApi.php`:

```php
<?php

namespace App\Modules\Development\Commands;

use Higgs\CLI\BaseCommand;
use Higgs\CLI\CLI;

/**
 * Genera endpoint API REST automáticamente
 */
class GenerateApi extends BaseCommand
{
    protected $group = 'Development';
    protected $name = 'development:generate-api';
    protected $description = 'Generates REST API endpoint for a table';
    
    public function run(array $params): int
    {
        $table = array_shift($params);
        
        if (empty($table)) {
            CLI::error('Usage: php spark development:generate-api <table>');
            return EXIT_ERROR;
        }
        
        $eid = explode('_', $table);
        $ucfModule = ucfirst($eid[0]);
        $ucfComponent = ucfirst($eid[1]);
        
        // Generar archivo API
        $dir = APPPATH . "Modules/{$ucfModule}/Controllers/Api";
        $file = "{$dir}/{$ucfComponent}.php";
        
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        $content = $this->buildApi($ucfModule, $ucfComponent, $table);
        file_put_contents($file, $content);
        
        CLI::write("  Created: {$file}", 'yellow');
        CLI::write('API endpoint generated successfully.', 'green');
        
        return EXIT_SUCCESS;
    }
    
    private function buildApi($module, $component, $table): string
    {
        $lcComponent = strtolower($component);
        
        $code = <<<EOT
<?php

namespace App\\Modules\\{$module}\\Controllers\\Api;

use CodeIgniter\\RESTful\\ResourceController;

class {$component} extends ResourceController
{
    protected \$modelName = 'App\\\\Modules\\\\{$module}\\\\Models\\\\{$component}';
    protected \$format = 'json';
    
    public function index()
    {
        return \$this->respond(\$this->model->findAll());
    }
    
    public function show(\$id = null)
    {
        \$data = \$this->model->find(\$id);
        if (! \$data) return \$this->failNotFound('No record found');
        return \$this->respond(\$data);
    }
    
    public function create()
    {
        if (! \$this->validate(\$this->model->validationRules)) {
            return \$this->fail(\$this->validator->getErrors());
        }
        
        \$this->model->insert(\$this->request->getJSON(true));
        return \$this->respondCreated();
    }
    
    public function update(\$id = null)
    {
        \$this->model->update(\$id, \$this->request->getRawInput());
        return \$this->respond(['id' => \$id, 'status' => 'updated']);
    }
    
    public function delete(\$id = null)
    {
        \$this->model->delete(\$id);
        return \$this->respondDeleted(['id' => \$id]);
    }
}
EOT;
        
        return $code;
    }
}
```

### Usar el Generador Personalizado

```bash
php spark development:generate-api sales_orders

# Resultado:
# Created: app/Modules/Sales/Controllers/Api/Orders.php
# API endpoint generated successfully.
```

### Ejemplo: Batch Generation

```bash
# Script: generate_all.sh

tables=(
    "sales_customers"
    "sales_invoices"
    "sales_products"
    "inventory_warehouses"
    "inventory_stock"
)

for table in "${tables[@]}"; do
    echo "Generating for: $table"
    php spark development:generate-model "$table"
    php spark development:generate-controller "$table"
    php spark development:generate-lister "$table"
done

echo "✓ Generation complete"
```

Ejecutar:
```bash
chmod +x generate_all.sh
./generate_all.sh
```

---

## 📊 Monitoreo y Logging

### Registrar Generaciones

```php
// En un controlador
$log = service('log');
$log->info("Generated model for table: {$table}", [
    'timestamp' => date('Y-m-d H:i:s'),
    'user' => $user['id'],
    'table' => $table,
]);
```

Ver logs:
```bash
tail -f writable/logs/log-*.log | grep "Generated"
```

---

## 🎓 Tips y Mejores Prácticas

1. **Generar primero, personalizar después**: Usar generadores para código base, luego adaptarlo
2. **Versionear cambios**: Usar Git para rastrear cambios antes y después de generar
3. **Automatizar**: Usar scripts para generar múltiples componentes
4. **Documentar personalizaciones**: Anotar cambios manuales en el código generado
5. **Probar después de generar**: Verificar sintaxis y ejecutar pruebas

---

## 🔗 Documentos Relacionados

- [README.md](../README.md) - Descripción general
- [comandos_cli.md](comandos_cli.md) - Referencia de comandos
- [sistema_generadores.md](sistema_generadores.md) - Sistema de generadores
- [architecture.md](architecture.md) - Arquitectura

---

**Última Actualización**: 2026-04-04
