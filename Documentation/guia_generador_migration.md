# Guía Completa: Generador de Migraciones en Higgs Framework

## 1. Introducción

El **Generador de Migraciones** es una herramienta automatizada que genera archivos de migración basados en la estructura existente de una tabla de base de datos. Crea un único archivo PHP que contiene:

- **Clase de migración** que extiende `Migration`
- **Método `up()`** que crea la tabla con su estructura (campos, tipos, restricciones)
- **Método `down()`** que revierte los cambios eliminando la tabla
- **Metadatos de configuración** (DBGroup, tabla asociada)

Las migraciones permiten versionear cambios en la base de datos y aplicarlos de manera reproducible en diferentes entornos.

---

## 2. Arquitectura General del Generador

```
/Migration/
├── index.php                 ← Punto de entrada (verifica permisos)
├── form.php                  ← Formulario que muestra el código a generar
├── validator.php             ← Valida los campos del formulario
├── processor.php             ← Procesa y escribe el archivo generado
├── breadcrumb.php            ← Breadcrumb de la página del generador
├── deny.php                  ← Página de acceso denegado
└── coders/
    └── migration.php         ← Genera código usando App\Libraries\Migrations
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

1. **Ruta de destino** (readonly):
   - Ejemplo: `app/Modules/Firewall/Database/Migrations/2025-05-06_120530_Firewall_IpRange.php`
   - Se construye con: `APPPATH . "Modules/{ModuleName}/Database/Migrations/"`
   - Nombre del archivo: `{timestamp}_{ModuleName}_{ComponentName}.php`

2. **Código PHP a generar** (área editable):
   - Contiene la clase migración completa
   - Usuario puede revisar y editar antes de guardar

3. **Campos ocultos** que contienen:
   - `file` → Nombre del archivo a crear
   - `path` → Ruta destino donde guardar

---

### 3.3 Etapa 3: Validación (validator.php)

Se validan los campos requeridos:

```php
$f->set_ValidationRule("file", "trim|required");
$f->set_ValidationRule("path", "trim|required");
$f->set_ValidationRule("code", "trim|required");
$f->set_ValidationRule("uri", "trim|required");
```

**Si la validación falla:**
- Muestra tarjeta de error con los mensajes de validación

**Si la validación pasa:**
- Llama a `processor.php` para escribir el archivo

---

### 3.4 Etapa 4: Procesamiento y Creación de Archivo (processor.php)

```php
$files->mkDir($path, true, 0775);  // Crear directorio si no existe
$files->open($uri, "writeOnly")->write($code);  // Escribir archivo
chmod($uri, 0664);  // Asignar permisos
```

**Proceso:**
1. Crea el directorio `$path` si no existe con permisos `0775`
2. Escribe el contenido en `$uri` (ruta completa al archivo)
3. Asigna permisos al archivo: `0664`
4. Muestra mensaje de éxito o advertencia

---

## 4. Estructura de Identificadores (OID)

El generador usa un identificador compuesto llamado **OID** (Object ID) que representa el nombre de la tabla:

```
{table_name}
```

**Ejemplos:**
- `firewall_iprange` → Tabla: `firewall_iprange`
- `users` → Tabla: `users`
- `account_settings` → Tabla: `account_settings`

**Transformaciones:**
```php
$eid = explode("_", $oid);                    // Divide el OID
$ucf_module = safe_ucfirst($eid[0]);          // "Firewall"
$slc_module = safe_strtolower($eid[0]);       // "firewall"
$ucf_component = safe_ucfirst($eid[1]);       // "Iprange"
$slc_component = safe_strtolower($eid[1]);    // "iprange"
```

---

## 5. Archivo Generado

### 5.1 Estructura de la Migración

**Ubicación final:** `app/Modules/{Module}/Database/Migrations/{timestamp}_{Module}_{Component}.php`

**Responsabilidad:**
- Definir la estructura de la tabla (campos, tipos, restricciones)
- Proporcionar método `up()` para crear la tabla
- Proporcionar método `down()` para revertir cambios
- Mantener integridad referencial y validación de datos

**Estructura de la clase:**

```php
<?php
// Copyright y cabecera de archivo

namespace App\Modules\{Module}\Database\Migrations;

use Higgs\Database\Migration;

class migration_{table}_{timestamp} extends Migration
{
    protected $table = '{table_name}';
    protected $DBGroup = 'authentication';  // O el grupo DB configurado

    public function up(): void
    {
        if ($this->db->tableExists($this->table)) {
            return;
        }

        $fields = [
            // Definición de campos aquí
        ];

        $this->forge->addField($fields);
        $this->forge->addKey('id', true);  // Clave primaria
        $this->forge->createTable($this->table);
    }

    public function down(): void
    {
        if ($this->db->tableExists($this->table)) {
            $this->forge->dropTable($this->table);
        }
    }
}
?>
```

---

## 6. Detalles de la Clase Generada

### 6.1 Propiedades de la Clase

```php
protected $table = '{table_name}';        // Nombre de la tabla
protected $DBGroup = 'authentication';    // Grupo de base de datos
```

### 6.2 Método `up()` - Crear Tabla

**Responsabilidad:**
- Verificar si la tabla ya existe (idempotencia)
- Definir estructura de campos
- Crear clave primaria
- Crear la tabla en la base de datos

**Flujo:**
```php
public function up(): void
{
    // 1. Verificar existencia
    if ($this->db->tableExists($this->table)) {
        return;  // Tabla ya existe, no hacer nada
    }

    // 2. Definir campos
    $fields = [
        'id' => [
            'type' => 'INT',
            'constraint' => 11,
            'null' => false,
        ],
        'name' => [
            'type' => 'VARCHAR',
            'constraint' => 255,
            'null' => false,
        ],
        'created_at' => [
            'type' => 'TIMESTAMP',
            'null' => true,
        ],
        // ... más campos
    ];

    // 3. Añadir campos al forge
    $this->forge->addField($fields);

    // 4. Añadir clave primaria
    $this->forge->addKey('id', true);

    // 5. Crear tabla
    $this->forge->createTable($this->table);
}
```

### 6.3 Método `down()` - Revertir Tabla

**Responsabilidad:**
- Eliminar la tabla de la base de datos
- Permitir rollback completo de la migración

```php
public function down(): void
{
    if ($this->db->tableExists($this->table)) {
        $this->forge->dropTable($this->table);
    }
}
```

### 6.4 Tipos de Campos Soportados

| Tipo BD | Tipo Higgs | Ejemplo | Notas |
|---------|-----------|---------|-------|
| INT | INT | `'constraint' => 11` | Entero |
| VARCHAR | VARCHAR | `'constraint' => 255` | Cadena variable |
| CHAR | CHAR | `'constraint' => 10` | Cadena fija |
| TEXT | TEXT | - | Texto largo |
| ENUM | ENUM | `'constraint' => ['a','b']` | Valores fijos |
| DECIMAL | DECIMAL | `'constraint' => '10,2'` | Precisión,escala |
| TIMESTAMP | TIMESTAMP | - | Fecha y hora |
| BOOLEAN | BOOLEAN | - | Verdadero/falso |
| LONGTEXT | LONGTEXT | - | Texto muy largo |
| MEDIUMTEXT | MEDIUMTEXT | - | Texto medio |

---

## 7. Convenciones de Nombres

### 7.1 Rutas de Archivos

```
app/Modules/{ModuleName}/Database/
└── Migrations/
    ├── 2025-05-06_120530_Firewall_IpRange.php
    ├── 2025-05-06_120545_Firewall_User.php
    └── 2025-05-07_083000_Users_Profile.php
```

### 7.2 Nombres de Clases

**Formato:** `migration_{table}_{timestamp}`

**Ejemplos:**
- `migration_firewall_iprange_20250506120530`
- `migration_users_20250506120545`

### 7.3 Nombres de Tablas

**Formato:** Utiliza el OID directamente (guiones bajos permiten múltiples palabras)

**Ejemplos:**
- `firewall_iprange`
- `account_settings`
- `users`

### 7.4 Nombres de Campos

**Convenciones Higgs:**
- snake_case en minúsculas
- Nombres descriptivos
- `id` como clave primaria (convención)
- `created_at`, `updated_at` para timestamps
- `deleted_at` para soft delete

**Ejemplos válidos:**
- `user_id`
- `first_name`
- `is_active`
- `created_at`
- `deleted_at`

---

## 8. Constantes y Variables Disponibles

### 8.1 En el Coder (migration.php)

```php
$path              // Ruta destino: "APPPATH . Modules/{M}/Database/Migrations/"
$oid               // Identificador de tabla (ejemplo: "firewall_iprange")
$migrations        // Instancia de \App\Libraries\Migrations
$code_migration    // Código generado por Migrations::generate()
```

### 8.2 En form.php

```php
$bootstrap         // Servicio Bootstrap para componentes UI
$f                 // Servicio de formularios
$ucf_module        // Nombre de módulo en PascalCase
$ucf_component     // Nombre de componente en PascalCase
$slc_module        // Nombre de módulo en snake_case
$slc_component     // Nombre de componente en snake_case
$classname         // Nombre de clase generada
$timestamp         // Timestamp actual: date('Y-m-d_His')
```

### 8.3 Servicios de Framework Disponibles

```php
$authentication    // Validación de permisos
$request           // Datos GET/POST
$bootstrap         // Componentes UI
$dates             // Servicio de fechas
$forms             // Servicio de formularios
$files             // Servicio de archivos
```

---

## 9. Proceso Interno: App\Libraries\Migrations

La clase `App\Libraries\Migrations` realiza el trabajo pesado:

### 9.1 Inicialización

```php
$migrations = new \App\Libraries\Migrations("frontend", $oid);
```

**Parámetros:**
- `$module` (string): "frontend" u otro nombre de módulo
- `$table` (string): Nombre de la tabla (OID)

### 9.2 Generación de Código

```php
$code = $migrations->generate($oid);
```

**Qué hace:**
1. Se conecta a la base de datos
2. Obtiene estructura de campos con `$db->getFieldData($table)`
3. Lee cada campo y genera su definición
4. Construye plantilla de clase migración
5. Retorna código PHP completo

### 9.3 Procesamiento de Campos

Para cada campo en la tabla, genera:

```php
'field_name' => [
    'type' => 'TIPO_DATOS',           // Requerido
    'constraint' => valor,             // Opcional (tamaño, precision)
    'null' => true/false,              // Por defecto false
    'default' => 'valor',              // Opcional
],
```

**Ejemplos:**

```php
// Campo INT
'id' => [
    'type' => 'INT',
    'constraint' => 11,
    'null' => false,
],

// Campo VARCHAR
'name' => [
    'type' => 'VARCHAR',
    'constraint' => 255,
    'null' => false,
],

// Campo ENUM
'status' => [
    'type' => 'ENUM',
    'constraint' => ['active', 'inactive', 'pending'],
    'null' => false,
],

// Campo DECIMAL
'price' => [
    'type' => 'DECIMAL',
    'constraint' => '10,2',
    'null' => false,
],
```

### 9.4 Detección de Clave Primaria

```php
$primaryKey = $this->getPrimaryKey();  // Retorna 'id' o null
```

La clase consulta `INFORMATION_SCHEMA` para identificar la PK.

---

## 10. Uso Paso a Paso

### 10.1 Acceder al Generador

```
1. URL: /development/generators/migration/
2. Seleccionar tabla: firewall_iprange (del combo)
3. El generador carga automáticamente el código
```

### 10.2 Revisar el Código Generado

```
1. El formulario muestra el código PHP de la migración
2. Verificar ruta destino:
   app/Modules/Firewall/Database/Migrations/2025-05-06_120530_Firewall_IpRange.php
3. Editar si es necesario (código personalizado)
```

**Puntos a revisar:**
- ¿Todos los campos se generaron correctamente?
- ¿Las restricciones (constraints) son exactas?
- ¿Los tipos de datos son correctos?
- ¿La clave primaria está identificada?
- ¿Los campos nullable están bien marcados?

### 10.3 Guardar el Archivo

```
1. Click en "Guardar"
2. Validación de campos requeridos
3. Creación del archivo en:
   app/Modules/{Module}/Database/Migrations/
4. Mensaje de éxito/advertencia
```

### 10.4 Ejecutar la Migración

```bash
# Ver migraciones pendientes
php spark migrate --show

# Ejecutar migraciones
php spark migrate

# Rollback última migración
php spark migrate:rollback

# Rollback todas
php spark migrate:refresh
```

### 10.5 Verificar el Archivo Creado

```bash
ls -la app/Modules/Firewall/Database/Migrations/

# Debería mostrar:
-rw-rw-r-- 2025-05-06_120530_Firewall_IpRange.php
```

---

## 11. Personalización

### 11.1 Editar Estructura de Campos

En el formulario, antes de guardar, puedes editar manualmente:

**Cambiar tipo de dato:**
```php
// Original
'name' => [
    'type' => 'VARCHAR',
    'constraint' => 255,
],

// Personalizado
'name' => [
    'type' => 'VARCHAR',
    'constraint' => 500,  // Aumentar tamaño
],
```

**Añadir campo nuevo:**
```php
'last_modified' => [
    'type' => 'TIMESTAMP',
    'null' => true,
    'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
],
```

**Modificar constraints:**
```php
// Cambiar NULL
'email' => [
    'type' => 'VARCHAR',
    'constraint' => 255,
    'null' => true,  // Permitir NULL
],
```

### 11.2 Agregar Índices Adicionales

En el método `up()`, después de crear campos:

```php
$this->forge->addField($fields);
$this->forge->addKey('id', true);  // Clave primaria
$this->forge->addKey('email', false);  // Índice único
$this->forge->addUniqueKey('user_name');  // Índice único
$this->forge->addKey('created_at', false);  // Índice normal
$this->forge->createTable($this->table);
```

### 11.3 Agregar Claves Foráneas

```php
// Después de crear la tabla
$this->forge->addForeignKey(
    'user_id',              // Campo local
    'users',                // Tabla referenciada
    'id',                   // Campo de referencia
    'CASCADE',              // ON DELETE
    'CASCADE'               // ON UPDATE
);
```

### 11.4 Cambiar Grupo de Base de Datos

```php
protected $DBGroup = 'secondary';  // Cambiar a otro grupo DB
```

---

## 12. Detalles Técnicos

### 12.1 Conectividad a Base de Datos

El generador se conecta a la BD por defecto:

```php
$db = Database::connect("default");
$fields = $db->getFieldData($this->table);
```

**Requisitos:**
- Tabla debe existir en la BD configurada
- Usuario BD debe tener permisos SELECT en `INFORMATION_SCHEMA`

### 12.2 Timestamp en Nombre de Archivo

Se genera automáticamente:

```php
$timestamp = date('Y-m-d_His');  // Formato: 2025-05-06_120530
$file = "{$timestamp}_{$ucf_module}_{$ucf_component}.php";
```

**Ventajas:**
- Garantiza nombres únicos
- Permite ordenamiento cronológico
- Compatible con sistema de migraciones Higgs

### 12.3 Idempotencia en `up()`

La migración es segura para ejecución múltiple:

```php
if ($this->db->tableExists($this->table)) {
    return;  // Tabla ya existe, no hacer nada
}
```

Esto permite ejecutar migraciones sin riesgo de errores si ya fueron aplicadas.

### 12.4 Rollback en `down()`

Verifica existencia antes de eliminar:

```php
if ($this->db->tableExists($this->table)) {
    $this->forge->dropTable($this->table);
}
```

Evita errores cuando se intenta revertir una migración que nunca fue aplicada.

### 12.5 Detección de Tipos Especiales

La clase `Migrations` maneja tipos complejos:

**ENUM:**
```php
// De tabla: status ENUM('active','inactive')
'status' => [
    'type' => 'ENUM',
    'constraint' => ['active', 'inactive'],
]
```

**DECIMAL/NUMERIC:**
```php
// De tabla: price DECIMAL(10,2)
'price' => [
    'type' => 'DECIMAL',
    'constraint' => '10,2',
]
```

### 12.6 URL Encoding de Código

El código generado se URL-encodes antes de guardarse en campos ocultos:

```php
// En form.php (encoding)
$r["code"] = $f->get_Value("code", $code);

// En processor.php (decoding)
$code = $f->get_Value("code");
// Se escribe directamente sin decodificar adicional
```

---

## 13. Errores Comunes

| Error | Causa | Solución |
|-------|-------|----------|
| "La tabla no existe" | Tabla no encontrada en BD | Verificar nombre exacto de tabla en BD |
| "Permiso denegado" | Usuario sin acceso | Asignar permiso `development-access` |
| Archivo no creado | Permisos del servidor | Verificar `chmod` en directorio Migrations |
| Clase no encontrada | Namespace incorrecto | Verificar nombre de módulo en config |
| Error en migración | Tipos de datos inválidos | Editar tipos en form antes de guardar |
| "DBGroup no existe" | Grupo DB incorrecto | Cambiar en propiedad `$DBGroup` |
| Campos con NULL no detectados | Estructura BD diferente | Regenerar con datos más recientes |
| Clave primaria no encontrada | Tabla sin PK | Añadir clave primaria a tabla |
| Constraint incorrecto | MAX_LENGTH no capturado | Editar manualmente en form |

---

## 14. Ejemplo Completo

### Generar Migración para Módulo Firewall - Tabla IP Ranges

**1. Estructura actual de tabla (en BD):**
```sql
CREATE TABLE firewall_iprange (
    id INT(11) NOT NULL AUTO_INCREMENT,
    ip_start VARCHAR(45) NOT NULL,
    ip_end VARCHAR(45) NOT NULL,
    is_blocked BOOLEAN NOT NULL DEFAULT FALSE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    INDEX idx_created_at (created_at)
);
```

**2. Ingresar al generador:**
```
URL: /development/generators/migration/
```

**3. Seleccionar tabla:**
```
OID: firewall_iprange
```

**4. Código generado automáticamente:**
```php
<?php
// Copyright y cabecera...

namespace App\Modules\Firewall\Database\Migrations;

use Higgs\Database\Migration;

class migration_firewall_iprange_20250506120530 extends Migration
{
    protected $table = 'firewall_iprange';
    protected $DBGroup = 'authentication';

    public function up(): void
    {
        if ($this->db->tableExists($this->table)) {
            return;
        }

        $fields = [
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'ip_start' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => false,
            ],
            'ip_end' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => false,
            ],
            'is_blocked' => [
                'type' => 'BOOLEAN',
                'null' => false,
                'default' => '0',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ];

        $this->forge->addField($fields);
        $this->forge->addKey('id', true);
        $this->forge->createTable($this->table);
    }

    public function down(): void
    {
        if ($this->db->tableExists($this->table)) {
            $this->forge->dropTable($this->table);
        }
    }
}
?>
```

**5. Personalización (opcional):**

Añadir índice en `ip_start` y `ip_end` para búsquedas rápidas:

```php
// Después de addKey('id', true)
$this->forge->addKey('ip_start');
$this->forge->addKey('ip_end');
$this->forge->addKey('created_at');
```

**6. Guardar** → Archivo creado en:
```
app/Modules/Firewall/Database/Migrations/2025-05-06_120530_Firewall_IpRange.php
```

**7. Ejecutar migración:**
```bash
php spark migrate
```

**8. Verificar resultado:**
```bash
php spark db:seed FirewallSeeder  # Si existe
mysql -u root -p development -e "SHOW COLUMNS FROM firewall_iprange;"
```

---

## 15. Resumen Operacional

```
┌─────────────────────────────────────────────────────────────┐
│  Acceso: /development/generators/migration/                 │
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
      │ Sistema analiza    │
      │ estructura BD      │
      │ con App\Libraries\ │
      │ Migrations         │
      └────────┬───────────┘
               │
               ↓
      ┌────────────────────┐
      │ Ver código PHP     │
      │ migración          │
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
      │ app/Modules/{M}/Database/Migrations/  │
      │ └── 2025-05-06_120530_{M}_{C}.php     │
      └────────┬─────────────────────────────┘
               │
               ↓
      ┌────────────────────────────────────────┐
      │ Ejecutar: php spark migrate            │
      │ Resultado: Tabla creada en BD          │
      └────────────────────────────────────────┘
```

---

## 16. Ciclo Completo: Desarrollo con Migraciones

### 16.1 Flujo Recomendado

**Fase 1: Crear tabla en BD**
```sql
CREATE TABLE firewall_iprange (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_start VARCHAR(45) NOT NULL,
    ip_end VARCHAR(45) NOT NULL
    -- ... más campos
);
```

**Fase 2: Generar migración**
```
URL: /development/generators/migration/
Tabla: firewall_iprange
Guardar → Archivo generado
```

**Fase 3: Aplicar migración**
```bash
php spark migrate
```

**Fase 4: Regenerar CRUD si es necesario**
```
Usar generador Lister, Creator, etc.
para generar vistas y controladores
```

**Fase 5: Deploy en otro entorno**
```bash
# En servidor de staging/producción
php spark migrate  # Aplica automáticamente
```

---

## 17. Integración con Otros Generadores

### 17.1 Orden de Generación Recomendado

1. **Migration** ← Primero (estructura BD)
2. **Lister** ← Segundo (vista de listado)
3. **Creator/Editor** ← Tercero (CRUD)
4. **Deleter** ← Cuarto (eliminación)

### 17.2 Dependencias

```
Migration
   ↓
   ├→ Lister (necesita tabla existente)
   ├→ Creator (necesita tabla existente)
   └→ Models (opcional, reference tablas)
```

---

## 18. Comandos CLI Relacionados

```bash
# Ver migraciones disponibles
php spark migrate --show

# Ejecutar todas las migraciones pendientes
php spark migrate

# Ejecutar migraciones de módulo específico
php spark migrate --namespace App\\Modules\\Firewall

# Revertir última migración
php spark migrate:rollback

# Revertir todas las migraciones
php spark migrate:refresh

# Revertir y volver a ejecutar (reset)
php spark migrate:refresh --seed

# Ver estado de migraciones
php spark migrate:status
```

---

## 19. Validación de Migraciones

### 19.1 Checklist Post-Generación

- [ ] Archivo creado en directorio correcto
- [ ] Nombre de archivo incluye timestamp
- [ ] Clase extiende `Migration`
- [ ] Namespace es correcto
- [ ] Método `up()` crea tabla
- [ ] Método `down()` elimina tabla
- [ ] Clave primaria está definida
- [ ] Tipos de datos son correctos
- [ ] Constraints (tamaños) son exactos
- [ ] Campos nullable están bien marcados

### 19.2 Validación de Ejecución

```bash
# 1. Verificar que no hay errores de sintaxis
php -l app/Modules/Firewall/Database/Migrations/2025-05-06_120530_Firewall_IpRange.php

# 2. Ejecutar migración
php spark migrate

# 3. Verificar tabla en BD
mysql -u root -p development -e "DESC firewall_iprange;"

# 4. Verificar que rollback funciona
php spark migrate:rollback
php spark migrate  # Ejecutar nuevamente
```

---

## 20. Referencias

- **Ubicación del código:** `/www/wwwroot/_development/app/Modules/Development/Views/Generators/Migration/`
- **Clase de generación:** `App\Libraries\Migrations`
- **Framework:** Higgs (CodeIgniter 4 fork)
- **Comando CLI:** `php spark migrate`
- **Documentación Higgs:** Database Migrations
- **Estándar de código:** PSR-12 (PHP)

---

**Última actualización:** 2026-05-06  
**Versión Migration Generator:** 1.5.0  
**Autor:** Jose Alexis Correa Valencia  
**Documentación creada por:** Claude Code Assistant
