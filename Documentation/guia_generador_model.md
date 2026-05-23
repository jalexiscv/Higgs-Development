# Guía Completa: Generador de Modelos en Higgs Framework

## 1. Introducción

El **Generador de Modelos** es una herramienta automatizada que genera clases Model completas basadas en tablas de base de datos. Crea un archivo PHP que implementa la clase Model extendiendo de `CachedModel5`, integrando:

- **Gestión de caché avanzada** con soporte para tags
- **Búsqueda paginada** con filtrado por términos
- **Métodos CRUD** heredados de CachedModel5
- **Métodos personalizados** para casos de uso específicos
- **Migraciones automáticas** en el constructor

El modelo generado está completamente integrado con el framework y listo para ser usado inmediatamente en controladores.

---

## 2. Arquitectura General del Generador

```
/Model/
├── index.php                 ← Punto de entrada (verifica permisos)
├── form.php                  ← Formulario que muestra el código a generar
├── validator.php             ← Valida los campos del formulario
├── processor.php             ← Procesa y escribe el archivo generado
├── breadcrumb.php            ← Breadcrumb de la página del generador
├── deny.php                  ← Página de acceso denegado
└── Methods/                  ← Coders para métodos generados
    ├── __construct.php       ← Genera constructor con migraciones
    ├── exec_Migrate.php      ← Genera método de migraciones
    ├── getAuthority.php      ← Verifica autoridad del usuario
    ├── getList.php           ← Obtiene lista paginada con búsqueda
    ├── getSelectData.php     ← Obtiene datos para elementos SELECT
    ├── get_Row.php           ← Obtiene un registro por ID
    └── [otros métodos]       ← Métodos opcionales (comentados)
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
- Se comprueba el permiso: `development-access` (singular)
- Si falta permiso → Muestra `deny.php`
- Si tiene permiso → Muestra `form.php`

---

### 3.2 Etapa 2: Mostrar Formulario (form.php)

El formulario contiene:

1. **Análisis de Tabla** (automático):
   - Se obtiene el OID (identificador de tabla)
   - Se extraen los nombres de campos de la tabla
   - Se identifica la clave primaria (primer campo)

2. **Ruta de destino** (readonly):
   - Ejemplo: `app/Modules/Firewall/Models/_Firewall_IpRange.php`
   - Se construye con: `APPPATH . "Modules/{ModuleName}/Models/_{ClassName}.php"`

3. **Código PHP a generar** (área editable):
   - Contiene la clase Model combinada con métodos coders
   - Usuario puede revisar y editar antes de guardar
   - Incluye documentación @method con anotaciones

4. **Campos ocultos** que contienen:
   - `pathfile` → Ruta destino del archivo
   - `mkdir` → Directorio a crear/verificar
   - `relative` → Ruta relativa desde APPPATH
   - `code` → Código PHP del modelo (URL encoded)

---

### 3.3 Etapa 3: Validación (validator.php)

Se validan los campos requeridos:

```php
$f->set_ValidationRule("pathfile", "trim|required");
$f->set_ValidationRule("mkdir", "trim|required");
$f->set_ValidationRule("relative", "trim|required");
$f->set_ValidationRule("code", "trim|required");
```

**Si la validación falla:**
- Muestra tarjeta de error con los mensajes de validación

**Si la validación pasa:**
- Llama a `processor.php` para escribir el archivo

---

### 3.4 Etapa 4: Procesamiento y Creación de Archivos (processor.php)

```php
$generatedFiles = [
    "{$pathfile}" => $code
];
```

**Proceso:**
1. Crea el directorio `$mkdir` si no existe
2. Asigna permisos al directorio: `0775`
3. Escribe el archivo PHP con el contenido decodificado
4. Asigna permisos al archivo: `0664`
5. Muestra mensaje de éxito o advertencia

---

## 4. Estructura de Identificadores (OID)

El generador usa un identificador compuesto llamado **OID** (Object ID) que representa el nombre de tabla:

```
{module}_{component}_{options}
```

**Ejemplos:**
- `firewall_iprange` → Tabla: firewall_iprange (2 componentes)
- `firewall_iprange_log` → Tabla: firewall_iprange_log (3 componentes)

**Transformaciones:**
```php
$eid = explode("_", $oid);                    // Divide el OID
$ucf_module = safe_ucfirst($eid[0]);          // "Firewall"
$slc_module = safe_strtolower($eid[0]);       // "firewall"
$ucf_component = safe_ucfirst($eid[1]);       // "Iprange"
$slc_component = safe_strtolower($eid[1]);    // "iprange"
$classname = "{$ucf_module}_{$ucf_component}"; // "Firewall_Iprange"
```

---

## 5. Archivos Generados

### 5.1 Clase Model Generada

**Ubicación final:** `app/Modules/{Module}/Models/_{ClassName}.php`

**Responsabilidad:**
- Modelo ORM completo que extiende `CachedModel5`
- Proporciona acceso a datos con caché automático
- Implementa búsqueda, filtrado y paginación
- Maneja autoridades y permisos por autor

**Estructura básica:**
```php
namespace App\Modules\{Module}\Models;

use App\Models\CachedModel5;
use Config\Database;

class {ClassName} extends CachedModel5
{
    protected $table = "{oid}";
    protected $primaryKey = "{primary_field}";
    protected $returnType = "array";
    protected $allowedFields = [/* campos */];
    protected $useTimestamps = true;
    protected $cache_time = 60;
    protected array $cacheTags = ['table:{oid}'];
    
    public function __construct() { /* ... */ }
    private function exec_Migrate(): void { /* ... */ }
    public function getList(/* ... */) { /* ... */ }
    public function getSelectData() { /* ... */ }
    public function get{PrimaryName}($id) { /* ... */ }
    public function getAuthority($id, $author): bool { /* ... */ }
}
```

---

## 6. Métodos Generados de la Clase

### 6.1 Constructor (`__construct`)

**Responsabilidad:**
- Inicializa el modelo
- Ejecuta migraciones automáticamente
- Asegura que la tabla exista en la BD

**Código generado:**
```php
public function __construct()
{
    parent::__construct();
    $this->exec_Migrate();
}
```

**Características:**
- Llama al constructor padre de CachedModel5
- Ejecuta `exec_Migrate()` automáticamente

---

### 6.2 Migraciones (`exec_Migrate`)

**Responsabilidad:**
- Ejecuta las migraciones del módulo
- Crea/actualiza tablas si es necesario
- Maneja errores silenciosamente

**Código generado:**
```php
private function exec_Migrate(): void
{
    $migrations = Config\Services::migrations();
    try {
        $migrations->setNamespace('App\\Modules\\{Module}');
        $migrations->latest();
        $all = $migrations->findMigrations();
    } catch(Throwable $e) {
        echo($e->getMessage());
    }
}
```

**Características:**
- Usa el sistema de servicios de Higgs
- Ejecuta las migraciones del módulo específico
- Captura y muestra errores de migración

---

### 6.3 Obtener Registro por ID (`get{PrimaryName}`)

**Responsabilidad:**
- Obtiene un registro específico por su clave primaria
- Utiliza caché automáticamente
- Devuelve array o false

**Código generado:**
```php
public function get{PrimaryName}($id): false|array
{
    $result = parent::getCached($id);
    if (is_array($result)) {
        return ($result);
    } else {
        return (false);
    }
}
```

**Ejemplo:** Para tabla con campo `id`:
```php
public function getId($id): false|array { /* ... */ }
```

**Características:**
- Utiliza `getCached()` de CachedModel5
- Beneficiario automático de caché de 60 segundos

---

### 6.4 Obtener Lista Paginada (`getList`)

**Responsabilidad:**
- Obtiene múltiples registros con paginación
- Busca en múltiples campos
- Retorna array ordenado o false

**Código generado:**
```php
public function getList(int $limit, int $offset, string $search = ""): array|false
{
    $result = $this
        ->groupStart()
        ->like("{primary}", "%{$search}%")
        ->orLike("{field1}", "%{$search}%")
        ->orLike("{field2}", "%{$search}%")
        ->groupEnd()
        ->orderBy("created_at", "DESC")
        ->findAll($limit, $offset);
    
    if (is_array($result)) {
        return $result;
    } else {
        return false;
    }
}
```

**Parámetros:**
- `$limit` → Cantidad de registros por página
- `$offset` → Registros a omitir (para paginación)
- `$search` → Término de búsqueda (opcional)

**Características:**
- Busca en la clave primaria y todos los campos excepto timestamps
- Agrupa condiciones con `groupStart()`/`groupEnd()`
- Ordena por `created_at DESC`
- Devuelve false si no hay resultados

**Uso:**
```php
$mIprange = model('App\Modules\Firewall\Models\Firewall_IpRange');
$list = $mIprange->getList(10, 0, "192.168");
```

---

### 6.5 Obtener Datos para SELECT (`getSelectData`)

**Responsabilidad:**
- Obtiene todos los registros formateados para un SELECT HTML
- Retorna array con estructura label/value
- Ideal para dropdowns

**Código generado:**
```php
public function getSelectData()
{
    $result = $this->select("`{$this->primaryKey}` AS `value`, `name` AS `label`")
        ->findAll();
    
    if (is_array($result)) {
        return ($result);
    } else {
        return (false);
    }
}
```

**Estructura retornada:**
```php
[
    ["value" => "1", "label" => "registro1"],
    ["value" => "2", "label" => "registro2"],
    ["value" => "3", "label" => "registro3"],
]
```

**Uso en formularios:**
```php
$mIprange = model('App\Modules\Firewall\Models\Firewall_IpRange');
$selectData = $mIprange->getSelectData();
$f->get_FieldSelect("iprange", array(
    "selected" => $r["iprange"],
    "data" => $selectData,
    "proportion" => "col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12"
));
```

---

### 6.6 Verificar Autoridad (`getAuthority`)

**Responsabilidad:**
- Valida si un usuario es el autor de un registro
- Útil para validar permisos de edición
- Devuelve booleano

**Código generado:**
```php
public function getAuthority($id, $author): bool
{
    $row = parent::getCachedFirst([$this->primaryKey => $id]);
    if (isset($row["author"]) && $row["author"] == $author) {
        return (true);
    } else {
        return (false);
    }
}
```

**Parámetros:**
- `$id` → ID del registro a verificar
- `$author` → ID del usuario a validar

**Características:**
- Solo funciona si la tabla tiene campo "author"
- Utiliza caché de CachedModel5
- Comparación exacta del ID del autor

**Uso:**
```php
$mIprange = model('App\Modules\Firewall\Models\Firewall_IpRange');
if ($mIprange->getAuthority($recordId, $userId)) {
    // El usuario puede editar
}
```

---

## 7. Convenciones de Nombres

### 7.1 Rutas de Archivos

```
app/Modules/{ModuleName}/
└── Models/
    └── _{ClassName}.php        (Prefijo _ opcional para generados)
```

### 7.2 Nombres de Clases y Espacios

**Para 2 componentes (`firewall_iprange`):**
```php
Namespace: App\Modules\Firewall\Models
Clase: Firewall_Iprange
Archivo: _Firewall_Iprange.php
OID: firewall_iprange
Tabla: firewall_iprange
```

**Para 3 componentes (`firewall_iprange_log`):**
```php
Namespace: App\Modules\Firewall\Models
Clase: Firewall_Iprange_Log
Archivo: _Firewall_Iprange_Log.php
OID: firewall_iprange_log
Tabla: firewall_iprange_log
```

### 7.3 Métodos Generados

```
get{PrimaryKeyName}()      → Obtiene por clave primaria
getList()                  → Obtiene múltiples registros
getSelectData()            → Obtiene datos para SELECT
getAuthority()             → Valida autoridad
exec_Migrate()             → Ejecuta migraciones
__construct()              → Constructor
```

**Ejemplos con tabla que tiene `user_id` como clave primaria:**
```
getId()                    → public function getId($id)
getSelectData()            → public function getSelectData()
```

---

## 8. Constantes y Variables Disponibles

### 8.1 En form.php

```php
$oid                    // Identificador de tabla (ej: firewall_iprange)
$fields                 // Array de nombres de campos de la tabla
$db                     // Instancia de base de datos
$datas                  // Datos de tipos de campos
$code                   // Código PHP generado
$mkdir                  // Directorio a crear
$pathfile              // Ruta completa del archivo
$relative              // Ruta relativa desde APPPATH
```

### 8.2 Propiedades de la Clase Generada

```php
protected $table                = "{oid}";
protected $primaryKey           = "{first_field}";
protected $returnType           = "array";
protected $useSoftDeletes       = true;
protected $allowedFields        = [/* campos */];
protected $useTimestamps        = true;
protected $createdField         = "created_at";
protected $updatedField         = "updated_at";
protected $deletedField         = "deleted_at";
protected $validationRules      = [];
protected $validationMessages   = [];
protected $skipValidation       = false;
protected $DBGroup              = "authentication";
protected $version              = '1.0.1';
protected $cache_time           = 60;      // Caché de 60 segundos
protected array $cacheTags      = ['table:{oid}'];
```

### 8.3 Variables Heredadas de CachedModel5

```php
// Métodos heredados disponibles:
insert(array $data, bool $returnID = true, bool $protect = true): int|string
update(string $id, array $data): bool
delete(string $id, bool $purge = false): bool
getCached(mixed $id): array|object|null
getCachedFirst(array $conditions, string $orderBy = 'created_at DESC'): array|object|null
getCachedSearch(array $conditions = [], int $limit = 10, int $offset = 0, string $orderBy = '', int $page = 1): array
getCachedCustomQuery(callable $queryBuilder, string $cacheKeySuffix, ?int $ttl = null): array
withTags(array $tags): static
invalidateTag(string $tag): int
invalidateSearchCache(): void
getSelectDataCached(?string $labelColumn = 'name', int $ttl = 3600): array
```

---

## 9. Uso Paso a Paso

### Paso 1: Acceder al Generador

```
URL: /development/generators/model/
```

### Paso 2: Seleccionar Tabla

El generador muestra un formulario donde puedes seleccionar la tabla:

```
OID: firewall_iprange
```

### Paso 3: Revisar Parámetros

El sistema automáticamente calcula:

```
Módulo: Firewall
Componente: IpRange
Tabla: firewall_iprange
Clase: Firewall_IpRange
Archivo: _Firewall_IpRange.php
Namespace: App\Modules\Firewall\Models
```

### Paso 4: Revisar Código Generado

Verifica que:
- Los campos detectados sean correctos
- La clave primaria esté bien identificada
- Los métodos generados sean apropiados

Puedes editar el código antes de guardar si es necesario.

### Paso 5: Guardar

Haz clic en "Crear" para:
1. Validar los campos requeridos
2. Crear el directorio si no existe
3. Escribir el archivo PHP

### Paso 6: Verificar Creación

Accede a la ruta generada para confirmar:

```
app/Modules/Firewall/Models/_Firewall_IpRange.php
```

### Paso 7: Usar en Controladores

```php
// En un controlador
$mIprange = model('App\Modules\Firewall\Models\Firewall_IpRange');

// Obtener un registro
$record = $mIprange->getId(1);

// Obtener lista paginada
$list = $mIprange->getList(10, 0, "search term");

// Obtener datos para SELECT
$selectData = $mIprange->getSelectData();

// Verificar autoridad
if ($mIprange->getAuthority($id, $userId)) {
    // El usuario puede editar
}

// Usar métodos heredados de CachedModel5
$record = $mIprange->find(1);
$mIprange->insert(['name' => 'New IP Range']);
$mIprange->update(1, ['name' => 'Updated']);
```

---

## 10. Personalización

### 10.1 Modificar Métodos Generados

Después de generar, puedes editar el archivo:

```php
// Personalizar getList
public function getList(int $limit, int $offset, string $search = ""): array|false
{
    $result = $this
        ->groupStart()
        ->like("name", "%{$search}%")  // Solo busca en name
        ->groupEnd()
        ->where('status', 'active')     // Agregar filtro adicional
        ->orderBy("created_at", "DESC")
        ->findAll($limit, $offset);
    
    return is_array($result) ? $result : false;
}
```

### 10.2 Agregar Métodos Personalizados

```php
// Agregar método nuevo
public function getActiveRecords(): array|false
{
    $result = $this
        ->where('status', 'active')
        ->orderBy('name', 'ASC')
        ->findAll();
    
    return is_array($result) ? $result : false;
}
```

### 10.3 Cambiar Caché

```php
// Aumentar tiempo de caché a 300 segundos
protected $cache_time = 300;

// Cambiar tags de caché
protected array $cacheTags = ['table:firewall_iprange', 'firewall'];
```

### 10.4 Modificar Campos Permitidos

```php
// Restringir campos que se pueden actualizar
protected $allowedFields = ['name', 'description'];  // Solo estos campos
```

### 10.5 Agregar Validación

```php
// Agregar reglas de validación
protected $validationRules = [
    'name' => 'required|string|max_length[255]',
    'description' => 'string|max_length[1000]',
];

protected $validationMessages = [
    'name' => [
        'required' => 'El nombre es obligatorio',
        'max_length' => 'El nombre no puede exceder 255 caracteres',
    ],
];
```

### 10.6 Usar Diferentes Bases de Datos

```php
// Cambiar grupo de BD
protected $DBGroup = "other_database";  // default, authentication, etc.
```

---

## 11. Detalles Técnicos

### 11.1 CachedModel5

El modelo generado extiende `CachedModel5`, que proporciona:

**Caché automática:**
- Todos los `find()` se cachean automáticamente
- TTL configurable (60 segundos por defecto)
- Tags para invalidación selectiva

**Métodos especializados:**
- `getCached()` → Obtiene con caché
- `getCachedFirst()` → Obtiene primer registro con caché
- `getCachedSearch()` → Búsqueda completa con caché
- `invalidateTag()` → Invalida caché por tag

**Ventajas:**
- Reduce carga de BD significativamente
- Mejora rendimiento de lectura
- Mantiene datos frescos automáticamente

### 11.2 Migraciones Automáticas

Cuando se instancia el modelo:

```php
$mIprange = model('App\Modules\Firewall\Models\Firewall_IpRange');
// Automáticamente ejecuta exec_Migrate()
// La tabla se crea/actualiza si es necesario
```

**Beneficios:**
- No requiere llamadas manuales
- Asegura que la tabla exista
- Proporciona feedback de errores

### 11.3 Búsqueda Multi-campo

`getList()` busca automáticamente en:
- Clave primaria
- Todos los campos excepto: `created_at`, `updated_at`, `deleted_at`

```php
// Busca en: id, name, description, status, etc.
$list = $mIprange->getList(10, 0, "search");
```

### 11.4 Soft Deletes

Por defecto, los modelos usan soft deletes:

```php
protected $useSoftDeletes = true;      // Los registros no se eliminan realmente
protected $deletedField = "deleted_at"; // Campo que marca como eliminado
```

**Comportamiento:**
- `delete()` establece `deleted_at`
- `find()` automáticamente excluye eliminados
- Se puede recuperar con `withDeleted()`

### 11.5 Timestamps Automáticos

```php
protected $useTimestamps = true;
protected $createdField = "created_at";
protected $updatedField = "updated_at";
```

**Comportamiento:**
- `insert()` establece `created_at` automáticamente
- `update()` establece `updated_at` automáticamente

---

## 12. Errores Comunes

| Error | Causa | Solución |
|-------|-------|----------|
| "Permiso denegado" | Usuario sin `development-access` | Asignar permiso al usuario |
| "Directorio no existe" | No hay permisos de escritura | Verificar permisos `0775` |
| "Tabla no encontrada" | OID incorrecto | Verificar nombre exacto de tabla |
| "Clase no encontrada" | Namespace incorrecto | Verificar ruta generada |
| Caché no funciona | `$cache_time = 0` | Asegurar `$cache_time > 0` |
| getSelectData() vacío | No hay campo `name` | Usar tabla con columna `name` |
| Métodos no existen | Código incompleto | Regenerar desde el generador |
| Migraciones fallan | Tabla no existe en BD | Crear tabla manualmente o migración |

---

## 13. Ejemplo Completo

### Generar Modelo para Módulo Firewall - Tabla IP Ranges

**1. Ingresar al generador:**
```
URL: /development/generators/model/
```

**2. Seleccionar tabla:**
```
OID: firewall_iprange
```

**3. Revisar parámetros generados:**
```
Módulo: Firewall
Componente: IpRange
Clase: Firewall_IpRange
Archivo: _Firewall_IpRange.php
Namespace: App\Modules\Firewall\Models
Tabla: firewall_iprange
```

**4. Revisar métodos a generar:**
```
__construct()           ← Constructor con migraciones
exec_Migrate()          ← Ejecuta migraciones del módulo
getId()                 ← Obtiene por ID (caché)
getList()              ← Obtiene lista paginada con búsqueda
getSelectData()        ← Obtiene datos para SELECT
getAuthority()         ← Valida autoridad del usuario
```

**5. Guardar**

**6. Archivo creado:**
```
app/Modules/Firewall/Models/_Firewall_IpRange.php
```

**7. Usar en controlador:**

```php
<?php

namespace App\Modules\Firewall\Controllers;

use App\Controllers\ModuleController;

class IpRange extends ModuleController
{
    public function view($id = null)
    {
        // Instanciar modelo
        $mIprange = model('App\Modules\Firewall\Models\Firewall_IpRange');
        
        // Obtener registro
        $data['record'] = $mIprange->getId($id);
        
        if (!$data['record']) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Not found');
        }
        
        // Verificar autoridad
        if (!$mIprange->getAuthority($id, $this->userId)) {
            // Usuario no es propietario
            return $this->showAccessDenied();
        }
        
        return view('app/Modules/Firewall/Views/IpRange/view', $data);
    }
    
    public function list()
    {
        $request = service("request");
        $mIprange = model('App\Modules\Firewall\Models\Firewall_IpRange');
        
        $page = (int)($request->getVar("page") ?? 1);
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        $search = $request->getVar("search") ?? "";
        
        // Obtener lista con búsqueda
        $data['records'] = $mIprange->getList($perPage, $offset, $search);
        $data['selectData'] = $mIprange->getSelectData();
        
        return view('app/Modules/Firewall/Views/IpRange/list', $data);
    }
}
```

**8. Usar datos en formulario:**

```php
<?php
// En view de formulario

$f = service("forms");
$mIprange = model('App\Modules\Firewall\Models\Firewall_IpRange');

// Obtener datos para SELECT
$selectData = $mIprange->getSelectData();

// Crear campo SELECT
$f->fields["iprange"] = $f->get_FieldSelect("iprange", array(
    "selected" => $r["iprange"],
    "data" => $selectData,
    "proportion" => "col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12"
));
```

---

## 14. Métodos Opcionales (Desactivados)

El generador incluye comentarios para métodos adicionales que pueden activarse:

```php
// En form.php, línea 97-111, descomenta según necesite:

// get_CountAllResults()     → Obtiene total de registros
// get_TableExist()          → Verifica si tabla existe
// get_Total()               → Obtiene total con condiciones
// is_CacheValid()           → Valida si caché es válida
// get_CacheKey()            → Obtiene clave de caché
// get_CachedItem()          → Obtiene item del caché
// _exec_BeforeFind()        → Hook antes de buscar
// _exec_FindCache()         → Hook para cachear resultado
// _exec_UpdateCache()       → Hook para actualizar caché
// _exec_DeleteCache()       → Hook para eliminar caché
```

Para activar un método, descomenta la línea en `form.php`:

```php
// ANTES:
// $code .= view($component . '\Methods\get_Total', ...);

// DESPUÉS:
$code .= view($component . '\Methods\get_Total', ...);
```

---

## 15. Resumen Operacional

```
┌─────────────────────────────────────────────────────────────┐
│  Acceso: /development/generators/model/                     │
└──────────────┬──────────────────────────────────────────────┘
               │
               ↓
        ┌──────────────────┐
        │ Seleccionar      │
        │ tabla (OID)      │
        └──────┬───────────┘
               │
               ↓
      ┌────────────────────┐
      │ Sistema calcula:   │
      │ - Módulo           │
      │ - Componente       │
      │ - Clase            │
      │ - Campos           │
      └────────┬───────────┘
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
      ┌────────────────────────────────────┐
      │ Escribir archivo en:               │
      │ app/Modules/{M}/Models/_{C}.php    │
      │                                    │
      │ Incluye:                           │
      │ ├── Constructor con migraciones    │
      │ ├── getList() con búsqueda         │
      │ ├── getSelectData() para forms     │
      │ ├── get{ID}() por clave primaria   │
      │ ├── getAuthority() verificación    │
      │ └── Métodos heredados CachedModel5 │
      └────────┬─────────────────────────┘
               │
               ↓
      ┌────────────────────┐
      │ Mensaje de éxito   │
      │ o advertencia      │
      └────────────────────┘
```

---

## 16. Integración con el Framework

### 16.1 Autoloading

El archivo generado se carga automáticamente:

```php
// En cualquier controlador o modelo
$mIprange = model('App\Modules\Firewall\Models\Firewall_IpRange');
// El autoloader encuentra: app/Modules/Firewall/Models/_Firewall_IpRange.php
```

### 16.2 Servicios Disponibles

El modelo puede usar servicios de Higgs:

```php
$db = Database::connect("authentication");  // BD configurada
$cache = cache();                           // Caché sistema
$migrations = Config\Services::migrations(); // Migraciones
```

### 16.3 Compatibilidad CodeIgniter 4

El modelo generado es 100% compatible con CodeIgniter 4:

```php
// Utiliza sintaxis CI4 estándar
$this->select('*');
$this->where('status', 'active');
$this->orderBy('created_at', 'DESC');
$this->findAll(10, 0);
```

---

## 17. Referencias

- **Ubicación del código:** `/www/wwwroot/_development/app/Modules/Development/Views/Generators/Model/`
- **Framework:** Higgs (CodeIgniter 4 fork)
- **Clase Base:** CachedModel5 (Caché avanzada)
- **Bootstrap:** v5.3.3 con componentes customizados
- **Estándar de código:** PSR-12 (PHP)
- **Compatibilidad:** PHP >= 8.2

---

**Última actualización:** 2026-05-06  
**Versión Model Generator:** 1.5.0  
**Autor:** Jose Alexis Correa Valencia  
**Mantenedor Actual:** Development Module Team
