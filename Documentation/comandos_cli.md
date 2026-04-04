# Referencia Completa de Comandos CLI

> Documentación detallada de todos los comandos Spark disponibles en el módulo Development.

---

## 📋 Tabla de Contenidos

1. [Descripción General](#descripción-general)
2. [Sintaxis de Comandos](#sintaxis-de-comandos)
3. [Comandos Disponibles](#comandos-disponibles)
4. [Ejemplos Prácticos](#ejemplos-prácticos)
5. [Troubleshooting](#troubleshooting)

---

## 📌 Descripción General

El módulo Development proporciona 9 comandos principales para generar código automáticamente. Todos los comandos siguen el patrón:

```bash
php spark development:{tipo}-{componente} {tabla} [opciones]
```

### Beneficios de Usar Comandos

- ✅ **Generación Rápida**: Crea código en segundos
- ✅ **Consistencia**: Sigue patrones estándar del módulo
- ✅ **Menos Errores**: Código generado es validado
- ✅ **Escalabilidad**: Fácil crear múltiples componentes
- ✅ **Automatización**: Integrable en scripts de CI/CD

---

## 🔤 Sintaxis de Comandos

### Formato Básico

```bash
php spark <grupo>:<comando> <argumento> [opciones]
```

### Componentes

- **grupo**: `development` (siempre)
- **comando**: Tipo de componente a generar (model, controller, etc.)
- **argumento**: Nombre de tabla (ej: `access_users`)
- **opciones**: Parámetros adicionales (opcional)

### Nombres de Tablas

Convención: `{modulo}_{componente}`

Ejemplos:
- `access_users` → Módulo "access", componente "users"
- `employee_departments` → Módulo "employee", componente "departments"
- `inventory_products` → Módulo "inventory", componente "products"

---

## 🎯 Comandos Disponibles

### 1. `development:generate-model`

**Descripción**: Genera una clase Model para acceso a datos.

**Sintaxis**:
```bash
php spark development:generate-model <tabla>
```

**Ejemplo**:
```bash
php spark development:generate-model access_users
```

**Output**:
```
  Created: app/Modules/Access/Models/Users.php
  Model file generated successfully.
```

**Archivo Generado**:
```php
// app/Modules/Access/Models/Users.php
namespace App\Modules\Access\Models;

use CodeIgniter\Model;

class Users extends Model
{
    protected $table = 'access_users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'email', ...];
    // Métodos adicionales...
}
```

**Cuándo usarlo**:
- Crear un nuevo modelo desde una tabla existente
- Acceder a datos de la base de datos
- Implementar validación a nivel de modelo

---

### 2. `development:generate-controller`

**Descripción**: Genera una clase Controller con métodos CRUD.

**Sintaxis**:
```bash
php spark development:generate-controller <tabla>
```

**Ejemplo**:
```bash
php spark development:generate-controller access_users
```

**Output**:
```
  Created: app/Modules/Access/Controllers/_Users.php
  Controller file generated successfully.
```

**Archivo Generado**:
```php
// app/Modules/Access/Controllers/_Users.php
namespace App\Modules\Access\Controllers;

use App\Controllers\ModuleController;

class _Users extends ModuleController
{
    public function __construct()
    {
        parent::__construct();
        $this->prefix = 'access-users';
        $this->module = 'App\Modules\Access';
        // ...
    }
    
    // Métodos para CRUD
}
```

**Cuándo usarlo**:
- Crear controlador para gestionar un recurso
- Implementar lógica de negocio HTTP
- Manejar autenticación y autorización

---

### 3. `development:generate-migration`

**Descripción**: Genera un archivo de migración para crear o modificar tabla.

**Sintaxis**:
```bash
php spark development:generate-migration <tabla>
```

**Ejemplo**:
```bash
php spark development:generate-migration create_users_table
```

**Output**:
```
  Created: app/Modules/Database/Migrations/2026-04-04-120000_CreateUsersTable.php
  Migration file generated successfully.
```

**Archivo Generado**:
```php
// app/Modules/Database/Migrations/2026-04-04-120000_CreateUsersTable.php
namespace App\Modules\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->forge->createTable('users', function ($table) {
            $table->increments('id');
            $table->string('name');
            // Más campos...
        });
    }
    
    public function down()
    {
        $this->forge->dropTable('users');
    }
}
```

**Cuándo usarlo**:
- Crear nueva tabla en base de datos
- Modificar estructura de tabla
- Mantener historial de cambios de BD

---

### 4. `development:generate-viewer`

**Descripción**: Genera vista para mostrar un registro.

**Sintaxis**:
```bash
php spark development:generate-viewer <tabla>
```

**Ejemplo**:
```bash
php spark development:generate-viewer access_users
```

**Output**:
```
  Created: app/Modules/Access/Views/Users/Viewer/index.php
  Viewer file generated successfully.
```

**Ubicación del Archivo**:
```
app/Modules/Access/Views/Users/Viewer/index.php
```

**Propósito**: Mostrar información completa de un registro único.

---

### 5. `development:generate-creator`

**Descripción**: Genera vista con formulario para crear nuevo registro.

**Sintaxis**:
```bash
php spark development:generate-creator <tabla>
```

**Ejemplo**:
```bash
php spark development:generate-creator access_users
```

**Output**:
```
  Created: app/Modules/Access/Views/Users/Creator/index.php
  Creator file generated successfully.
```

**Ubicación del Archivo**:
```
app/Modules/Access/Views/Users/Creator/index.php
```

**Propósito**: Mostrar formulario para insertar nuevo registro.

---

### 6. `development:generate-editor`

**Descripción**: Genera vista con formulario para editar registro.

**Sintaxis**:
```bash
php spark development:generate-editor <tabla>
```

**Ejemplo**:
```bash
php spark development:generate-editor access_users
```

**Output**:
```
  Created: app/Modules/Access/Views/Users/Editor/index.php
  Editor file generated successfully.
```

**Ubicación del Archivo**:
```
app/Modules/Access/Views/Users/Editor/index.php
```

**Propósito**: Mostrar formulario para actualizar un registro.

---

### 7. `development:generate-deleter`

**Descripción**: Genera vista con confirmación para eliminar registro.

**Sintaxis**:
```bash
php spark development:generate-deleter <tabla>
```

**Ejemplo**:
```bash
php spark development:generate-deleter access_users
```

**Output**:
```
  Created: app/Modules/Access/Views/Users/Deleter/index.php
  Deleter file generated successfully.
```

**Ubicación del Archivo**:
```
app/Modules/Access/Views/Users/Deleter/index.php
```

**Propósito**: Mostrar confirmación antes de eliminar un registro.

---

### 8. `development:generate-lister`

**Descripción**: Genera vista para listar registros en tabla.

**Sintaxis**:
```bash
php spark development:generate-lister <tabla>
```

**Ejemplo**:
```bash
php spark development:generate-lister access_users
```

**Output**:
```
  Created: app/Modules/Access/Views/Users/Lister/index.php
  Lister file generated successfully.
```

**Ubicación del Archivo**:
```
app/Modules/Access/Views/Users/Lister/index.php
```

**Propósito**: Mostrar lista de registros en formato tabla.

---

### 9. `development:generate-lang`

**Descripción**: Genera archivo de idioma para un componente.

**Sintaxis**:
```bash
php spark development:generate-lang <componente>
```

**Ejemplo**:
```bash
php spark development:generate-lang users
```

**Output**:
```
  Created: app/Language/es/Users.php
  Language file generated successfully.
```

**Ubicación del Archivo**:
```
app/Language/es/Users.php
```

**Archivo Generado**:
```php
// app/Language/es/Users.php
return [
    'title' => 'Usuarios',
    'create' => 'Crear Usuario',
    'edit' => 'Editar Usuario',
    'delete' => 'Eliminar Usuario',
    'list' => 'Lista de Usuarios',
    // Más strings...
];
```

**Cuándo usarlo**:
- Crear archivos de traducción
- Mantener strings en idiomas múltiples
- Localizar interfaz de usuario

---

## 💡 Ejemplos Prácticos

### Ejemplo 1: Crear CRUD Completo para Tabla "employees"

```bash
# Paso 1: Generar modelo
php spark development:generate-model inventory_employees

# Paso 2: Generar controlador
php spark development:generate-controller inventory_employees

# Paso 3: Generar migraciones (si tabla no existe)
php spark development:generate-migration create_employees_table

# Paso 4: Generar vistas
php spark development:generate-viewer inventory_employees
php spark development:generate-creator inventory_employees
php spark development:generate-editor inventory_employees
php spark development:generate-deleter inventory_employees
php spark development:generate-lister inventory_employees

# Paso 5: Generar archivo de idioma
php spark development:generate-lang employees

# Paso 6: Ejecutar migraciones
php spark migrate
```

**Resultado**:
```
app/Modules/Inventory/
├── Models/
│   └── Employees.php
├── Controllers/
│   └── _Employees.php
├── Database/
│   └── Migrations/
│       └── 2026-04-04-xxxxx_CreateEmployeesTable.php
└── Views/
    └── Employees/
        ├── Viewer/
        ├── Creator/
        ├── Editor/
        ├── Deleter/
        └── Lister/

app/Language/es/
└── Employees.php
```

### Ejemplo 2: Actualizar Múltiples Tablas

```bash
# Crear generadores para tres tablas
for table in "sales_customers" "sales_invoices" "sales_products"; do
    php spark development:generate-model "$table"
    php spark development:generate-controller "$table"
    php spark development:generate-viewer "$table"
    php spark development:generate-lister "$table"
done
```

### Ejemplo 3: Script de Automatización

Crear archivo `generate_module.sh`:

```bash
#!/bin/bash

TABLE=$1
MODULE=$(echo $TABLE | cut -d'_' -f1)
COMPONENT=$(echo $TABLE | cut -d'_' -f2)

echo "Generando componentes para: $TABLE"

php spark development:generate-model "$TABLE"
php spark development:generate-controller "$TABLE"
php spark development:generate-migration "create_${TABLE}_table"
php spark development:generate-viewer "$TABLE"
php spark development:generate-creator "$TABLE"
php spark development:generate-editor "$TABLE"
php spark development:generate-deleter "$TABLE"
php spark development:generate-lister "$TABLE"
php spark development:generate-lang "$COMPONENT"

echo "✓ Generación completada"
```

Usar:
```bash
chmod +x generate_module.sh
./generate_module.sh sales_orders
```

---

## 🐛 Troubleshooting

### Problema: "Command not found"

**Causa**: El módulo no está registrado.

**Solución**:
```bash
# Verificar que exista
php spark list | grep development

# Si no aparece, editar app/Config/Modules.php
```

### Problema: "Table not found"

**Causa**: La tabla especificada no existe en la BD.

**Solución**:
```bash
# Crear tabla primero
php spark development:generate-migration create_table_name
php spark migrate

# Luego generar modelo
php spark development:generate-model module_table
```

### Problema: "Permission denied" al crear archivos

**Causa**: Permisos insuficientes en carpeta del módulo.

**Solución**:
```bash
chmod -R 755 app/Modules/
chmod -R 755 app/Language/
```

### Problema: Nombres de clases incorrectos

**Causa**: Formato incorrecto de nombre de tabla.

**Solución**:
- Usar formato: `modulo_componente`
- Ejemplos válidos: `access_users`, `sales_orders`, `inventory_products`
- Nombres inválidos: `users`, `access_users_roles` (más de 2 partes)

---

## ⚡ Tips y Buenas Prácticas

1. **Usar nomenclatura consistente**: Siempre usar `modulo_componente`
2. **Generar en orden**: Model → Controller → Migration → Views → Lang
3. **Personalizar después**: Los generadores crean código base, personalizar según necesidades
4. **Versionear migraciones**: Ejecutar `php spark migrate` después de generar
5. **Probar después de generar**: Verificar que los archivos funcionan correctamente
6. **Crear backup**: Guardar versión anterior antes de sobrescribir

---

## 📖 Documentos Relacionados

- [README.md](../README.md) - Descripción general
- [architecture.md](architecture.md) - Arquitectura del módulo
- [guia_instalacion.md](guia_instalacion.md) - Instalación y configuración
- [controladores.md](controladores.md) - Documentación de controladores

---

**Última Actualización**: 2026-04-04
