# Documentación de Modelos

> Referencia detallada de los modelos del módulo Development.

---

## 📋 Tabla de Contenidos

1. [Descripción General](#descripción-general)
2. [Development_Modules](#development_modules)
3. [Development_Clients_Modules](#development_clients_modules)
4. [Development_Users](#development_users)
5. [Development_Users_Fields](#development_users_fields)
6. [Ejemplos de Uso](#ejemplos-de-uso)

---

## 🎯 Descripción General

Los modelos del módulo Development extienden `CodeIgniter\Model` y proporcionan acceso a la base de datos.

### Propiedades Comunes

```php
class Development_Modules extends Model {
    protected $table = 'development_modules';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['field1', 'field2', ...];
    protected $validationRules = [...];
}
```

---

## 📚 Development_Modules

**Archivo**: `Models/Development_Modules.php`

Modelo para gestionar módulos registrados en el sistema Development.

### Tabla: `development_modules`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | int | Identificador único |
| `name` | string | Nombre del módulo |
| `namespace` | string | Namespace del módulo |
| `path` | string | Ruta del módulo en servidor |
| `status` | enum | Estado (activo/inactivo) |
| `created_at` | timestamp | Fecha de creación |
| `updated_at` | timestamp | Fecha de última actualización |

### Métodos Principales

#### `getAll()`
- **Descripción**: Obtiene todos los módulos
- **Return**: Array de módulos

```php
$modules = $model->getAll();
// Array([0] => ['id' => 1, 'name' => 'Development', ...], ...)
```

#### `getById($id)`
- **Descripción**: Obtiene módulo por ID
- **Parámetros**: 
  - `$id` (int): ID del módulo
- **Return**: Array|null

```php
$module = $model->getById(1);
// Array(['id' => 1, 'name' => 'Development', ...])
```

#### `getByName($name)`
- **Descripción**: Obtiene módulo por nombre
- **Parámetros**: 
  - `$name` (string): Nombre del módulo
- **Return**: Array|null

```php
$module = $model->getByName('Development');
```

#### `create($data)`
- **Descripción**: Crea nuevo módulo
- **Parámetros**: 
  - `$data` (array): Datos del módulo
- **Return**: int (ID insertado)

```php
$id = $model->create([
    'name' => 'NewModule',
    'namespace' => 'App\Modules\NewModule',
    'path' => '/app/Modules/NewModule',
    'status' => 'active'
]);
```

#### `update($id, $data)`
- **Descripción**: Actualiza módulo
- **Parámetros**: 
  - `$id` (int): ID del módulo
  - `$data` (array): Datos a actualizar

```php
$result = $model->update(1, [
    'status' => 'inactive'
]);
```

#### `delete($id)`
- **Descripción**: Elimina módulo
- **Parámetros**: 
  - `$id` (int): ID del módulo

---

## 🔗 Development_Clients_Modules

**Archivo**: `Models/Development_Clients_Modules.php`

Modelo para gestionar la asociación entre clientes y módulos.

### Tabla: `development_clients_modules`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | int | Identificador único |
| `client_id` | int | Referencia a cliente |
| `module_id` | int | Referencia a módulo (FK) |
| `access_level` | enum | Nivel de acceso (read/write/admin) |
| `assigned_at` | timestamp | Fecha de asignación |

### Métodos Principales

#### `getClientModules($clientId)`
- **Descripción**: Obtiene módulos asignados a cliente
- **Parámetros**: 
  - `$clientId` (int): ID del cliente
- **Return**: Array de módulos

```php
$modules = $model->getClientModules(123);
// Array([0] => ['module_id' => 1, 'access_level' => 'admin', ...], ...)
```

#### `assignModule($clientId, $moduleId, $accessLevel)`
- **Descripción**: Asigna módulo a cliente
- **Parámetros**: 
  - `$clientId` (int): ID del cliente
  - `$moduleId` (int): ID del módulo
  - `$accessLevel` (string): Nivel de acceso

```php
$result = $model->assignModule(123, 1, 'admin');
```

#### `revokeModule($clientId, $moduleId)`
- **Descripción**: Revoca acceso a módulo
- **Parámetros**: 
  - `$clientId` (int): ID del cliente
  - `$moduleId` (int): ID del módulo

```php
$result = $model->revokeModule(123, 1);
```

#### `hasAccess($clientId, $moduleId)`
- **Descripción**: Verifica si cliente tiene acceso
- **Parámetros**: 
  - `$clientId` (int): ID del cliente
  - `$moduleId` (int): ID del módulo
- **Return**: bool

```php
$hasAccess = $model->hasAccess(123, 1);
// true|false
```

---

## 👥 Development_Users

**Archivo**: `Models/Development_Users.php`

Modelo para gestionar usuarios del módulo Development.

### Tabla: `development_users`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | int | Identificador único |
| `name` | string | Nombre completo |
| `email` | string | Email (único) |
| `password` | string | Hash de contraseña |
| `role` | enum | Rol (admin/developer/viewer) |
| `status` | enum | Estado (active/inactive) |
| `last_login` | timestamp | Último acceso |
| `created_at` | timestamp | Fecha de creación |
| `updated_at` | timestamp | Fecha de última actualización |

### Métodos Principales

#### `getByEmail($email)`
- **Descripción**: Obtiene usuario por email
- **Parámetros**: 
  - `$email` (string): Email del usuario
- **Return**: Array|null

```php
$user = $model->getByEmail('user@example.com');
```

#### `authenticate($email, $password)`
- **Descripción**: Autentica usuario
- **Parámetros**: 
  - `$email` (string): Email
  - `$password` (string): Contraseña sin encriptar
- **Return**: Array|false

```php
$user = $model->authenticate('user@example.com', 'password123');
if ($user) {
    // Autenticación exitosa
}
```

#### `create($data)`
- **Descripción**: Crea nuevo usuario
- **Parámetros**: 
  - `$data` (array): Datos del usuario

```php
$id = $model->create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => password_hash('pass123', PASSWORD_BCRYPT),
    'role' => 'developer',
    'status' => 'active'
]);
```

#### `updateLastLogin($userId)`
- **Descripción**: Actualiza hora de último acceso
- **Parámetros**: 
  - `$userId` (int): ID del usuario

```php
$model->updateLastLogin(1);
```

#### `getActiveUsers()`
- **Descripción**: Obtiene usuarios activos
- **Return**: Array de usuarios

```php
$activeUsers = $model->getActiveUsers();
```

---

## 🏷️ Development_Users_Fields

**Archivo**: `Models/Development_Users_Fields.php`

Modelo para campos personalizados de usuarios.

### Tabla: `development_users_fields`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | int | Identificador único |
| `user_id` | int | Referencia a usuario (FK) |
| `field_name` | string | Nombre del campo |
| `field_value` | text | Valor del campo |
| `field_type` | enum | Tipo (text/number/date/etc) |

### Métodos Principales

#### `getFieldsByUser($userId)`
- **Descripción**: Obtiene campos de usuario
- **Parámetros**: 
  - `$userId` (int): ID del usuario
- **Return**: Array de campos

```php
$fields = $model->getFieldsByUser(1);
// Array(['field_name' => 'phone', 'field_value' => '555-1234'], ...)
```

#### `getField($userId, $fieldName)`
- **Descripción**: Obtiene valor de campo específico
- **Parámetros**: 
  - `$userId` (int): ID del usuario
  - `$fieldName` (string): Nombre del campo

```php
$phone = $model->getField(1, 'phone');
```

#### `setField($userId, $fieldName, $value, $type = 'text')`
- **Descripción**: Establece valor de campo
- **Parámetros**: 
  - `$userId` (int): ID del usuario
  - `$fieldName` (string): Nombre del campo
  - `$value` (mixed): Valor
  - `$type` (string): Tipo de campo

```php
$model->setField(1, 'phone', '555-1234', 'text');
```

---

## 💡 Ejemplos de Uso

### Ejemplo 1: Obtener Todos los Módulos

```php
// En el controlador
$model = new \App\Modules\Development\Models\Development_Modules();
$modules = $model->findAll();

// Pasar a la vista
return view('modules_list', ['modules' => $modules]);
```

### Ejemplo 2: Crear Nuevo Usuario

```php
$userModel = new \App\Modules\Development\Models\Development_Users();

$data = [
    'name' => 'Jane Smith',
    'email' => 'jane@example.com',
    'password' => password_hash('securepass123', PASSWORD_BCRYPT),
    'role' => 'developer',
    'status' => 'active'
];

$userId = $userModel->insert($data);
echo "Usuario creado con ID: {$userId}";
```

### Ejemplo 3: Validar Acceso de Cliente a Módulo

```php
$clientModulesModel = new \App\Modules\Development\Models\Development_Clients_Modules();

$clientId = 123;
$moduleId = 1;

if ($clientModulesModel->hasAccess($clientId, $moduleId)) {
    // Permitir acceso
} else {
    // Denegar acceso
    return response()->setStatusCode(403, 'Forbidden');
}
```

### Ejemplo 4: Obtener Información de Usuario con Campos

```php
$userModel = new \App\Modules\Development\Models\Development_Users();
$fieldsModel = new \App\Modules\Development\Models\Development_Users_Fields();

$user = $userModel->find(1);
$fields = $fieldsModel->getFieldsByUser($user['id']);

// Combinar datos
$userData = array_merge($user, ['custom_fields' => $fields]);

return view('user_profile', $userData);
```

### Ejemplo 5: Actualizar Estado de Múltiples Módulos

```php
$model = new \App\Modules\Development\Models\Development_Modules();

$modulesToUpdate = [1, 2, 3];
foreach ($modulesToUpdate as $moduleId) {
    $model->update($moduleId, ['status' => 'active']);
}
```

---

## 🔒 Validación en Modelos

Los modelos incluyen reglas de validación:

```php
protected $validationRules = [
    'name'  => 'required|string|max_length[255]',
    'email' => 'required|email|valid_email|is_unique[development_users.email]',
    'role'  => 'required|in_list[admin,developer,viewer]',
];
```

Usar validación:

```php
if (!$model->validate($data)) {
    $errors = $model->errors();
    // Manejar errores
}
```

---

## 📊 Relaciones Entre Modelos

```
Development_Users (1) ──→ (N) Development_Users_Fields
                                    |
Development_Users (1) ──→ (N) Development_Clients_Modules
                                    |
                         Development_Modules
```

---

## 📈 Performance Tips

1. **Usar indexes**: Las columnas con FK deben tener índices
2. **Lazy loading**: Cargar relaciones solo si se necesitan
3. **Cachear**: Cachear resultados frecuentemente consultados

```php
// Ejemplo con cache
$key = 'modules_' . md5($query);
if ($cached = cache($key)) {
    return $cached;
}

$result = $model->findAll();
cache()->save($key, $result, 3600); // 1 hora
return $result;
```

---

## 📚 Documentos Relacionados

- [README.md](../README.md) - Descripción general
- [architecture.md](architecture.md) - Arquitectura
- [controladores.md](controladores.md) - Documentación de controladores
- [sistema_generadores.md](sistema_generadores.md) - Sistema de generadores

---

**Última Actualización**: 2026-04-04
