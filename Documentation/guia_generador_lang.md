# Guía Completa: Generador de Archivos de Idioma "Lang" en Higgs Framework

## 1. Introducción

El **Generador de Archivos de Idioma (Lang)** es una herramienta automatizada que genera archivos de traducción/localización completamente estructurados. Crea un archivo PHP que contiene:

- **Claves de traducción** para todas las operaciones CRUD (Crear, Leer, Actualizar, Eliminar)
- **Placeholders y etiquetas** para campos de formularios
- **Mensajes de validación y error**
- **Mensajes de confirmación y éxito**
- **Descripciones de funcionalidades**

El archivo se genera automáticamente basado en los campos de la tabla de base de datos, ahorrando tiempo significativo en la configuración de sistemas multiidioma.

---

## 2. Arquitectura General del Generador

```
/Lang/
├── index.php                 ← Punto de entrada (verifica permisos)
├── form.php                  ← Formulario que muestra el código a generar
├── validator.php             ← Valida los campos del formulario
├── processor.php             ← Procesa y escribe el archivo generado
├── breadcrumb.php            ← Breadcrumb de la página del generador
├── deny.php                  ← Página de acceso denegado
└── coders/
    └── lang.php              ← Genera código completo del archivo de idioma
```

**Diferencia clave con Lister:**
- Lister genera **4 archivos** (index, grid, deny, breadcrumb) en una **estructura de directorio**
- Lang genera **1 archivo único** (archivo de idioma) que centraliza **todas las traducciones**

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

1. **Ruta de destino del archivo** (readonly):
   - Ejemplo: `app/Modules/Firewall/Language/es/_Firewall_IpRange.php`
   - Se construye con: `APPPATH . "Modules/{ModuleName}/Language/es/_{ModuleName}_{ComponentName}.php"`

2. **Código PHP a generar** (área editable):
   - Contiene el código completo del archivo de idioma
   - Usuario puede revisar y editar antes de guardar

3. **Campos ocultos** que contienen:
   - `pathfile` → Ruta de destino completa
   - `mkdir` → Ruta del directorio a crear
   - `code` → Código PHP completo del archivo (editable)

4. **Información de creación:**
   - `date` → Fecha de generación
   - `time` → Hora de generación

---

### 3.3 Etapa 3: Validación (validator.php)

Se validan los campos requeridos:

```php
$f->set_ValidationRule("pathfile", "trim|required");
$f->set_ValidationRule("mkdir", "trim|required");
$f->set_ValidationRule("uri_save", "trim|required");
$f->set_ValidationRule("code", "trim|required");
```

**Si la validación falla:**
- Muestra tarjeta de error con los mensajes de validación
- Retorna al formulario para correcciones

**Si la validación pasa:**
- Llama a `processor.php` para escribir el archivo

---

### 3.4 Etapa 4: Procesamiento y Creación del Archivo (processor.php)

```php
$files = new Files();
$files->mkDir($mkdir);                    // Crear directorio si no existe
chmod($mkdir, 0775);                      // Asignar permisos al directorio
$files->open($pathfile, "writeOnly")      // Abrir archivo para escritura
    ->write($code);                       // Escribir código PHP
chmod($pathfile, 0664);                   // Asignar permisos al archivo
```

**Proceso:**
1. Crea el directorio `$mkdir` (Language/es/) si no existe
2. Asigna permisos al directorio: `0775` (rwxrwxr-x)
3. Abre el archivo en modo escritura
4. Escribe el contenido del código PHP
5. Asigna permisos al archivo: `0664` (rw-rw-r--)
6. Muestra mensaje de éxito o advertencia

---

## 4. Estructura de Identificadores (OID)

El generador usa un identificador compuesto llamado **OID** (Object ID):

```
{module}_{component}_{options}
```

**Ejemplos:**
- `firewall_iprange` → 2 componentes (Módulo_Componente)
- `firewall_iprange_log` → 3 componentes (Módulo_Componente_Opción)

**Transformaciones automáticas:**
```php
$oid = "firewall_iprange";
$eid = explode("_", $oid);                    // ["firewall", "iprange"]
$ucf_module = safe_ucfirst($eid[0]);          // "Firewall"
$slc_module = safe_strtolower($eid[0]);       // "firewall"
$ucf_component = safe_ucfirst($eid[1]);       // "Iprange"
$slc_component = safe_strtolower($eid[1]);    // "iprange"
$ucf_options = safe_ucfirst(@$eid[2]);        // "Log" (si existe)
$slc_options = safe_strtolower(@$eid[2]);     // "log" (si existe)
```

**Construir nombre de clase:**
```php
// Para 2 componentes
$classname = "{$ucf_module}_{$ucf_component}";        // "Firewall_Iprange"

// Para 3 componentes
$classname = "{$ucf_module}_{$ucf_component}_{$ucf_options}";  // "Firewall_Iprange_Log"
```

---

## 5. Archivos Generados

### 5.1 Archivo de Idioma Principal

**Ubicación final:** `app/Modules/{Module}/Language/es/_{Module}_{Component}.php`

**Ejemplo:** `app/Modules/Firewall/Language/es/_Firewall_IpRange.php`

**Responsabilidad:**
- Centralizar todas las claves de traducción del módulo/componente
- Proporcionar mensajes en idioma español (es)
- Organizar claves por funcionalidad (crear, ver, editar, eliminar, listar)
- Permitir referencias dinámicas a campos (#singular, #plural)

**Estructura general:**
```php
<?php
return [
    // Claves de campos
    "label_{field}" => "{field}",
    "placeholder_{field}" => "{field}",
    "help_{field}" => "{field}",
    
    // Mensajes de operación CRUD
    "create-denied-title" => "...",
    "create-success-message" => "...",
    
    "edit-denied-title" => "...",
    "edit-success-message" => "...",
    
    "delete-denied-title" => "...",
    "delete-success-message" => "...",
    
    "view-denied-title" => "...",
    "view-success-message" => "...",
    
    "list-title" => "...",
    "list-description" => "...",
];
```

---

## 6. Convenciones de Nombres

### 6.1 Rutas de Archivos

**Para 2 componentes:**
```
app/Modules/{ModuleName}/
├── Language/
│   └── es/
│       └── _{ModuleName}_{ComponentName}.php
```

**Ejemplo completo:**
```
app/Modules/Firewall/
├── Language/
│   └── es/
│       └── _Firewall_IpRange.php
```

### 6.2 Nombres de Módulos y Componentes

**Para 2 componentes (`firewall_iprange`):**
```php
Módulo: Firewall (App\Modules\Firewall)
Componente: IpRange
Archivo: _Firewall_IpRange.php
Clase modelo: Firewall_Iprange
```

**Para 3 componentes (`firewall_iprange_log`):**
```php
Módulo: Firewall (App\Modules\Firewall)
Componente: IpRange
Opción: Log
Archivo: _Firewall_IpRange_Log.php
Clase modelo: Firewall_Iprange_Log
```

### 6.3 Convención de Claves de Traducción

```
Formato: {operacion}-{tipo}

Operaciones: create, view, edit, delete, list
Tipos: title, message, denied-title, denied-message, errors-message, success-title, success-message, noexist-title, noexist-message

Ejemplos:
"create-denied-title"
"create-success-message"
"edit-errors-title"
"delete-noexist-message"
"list-title"
```

### 6.4 Variables Dinámicas en Mensajes

```php
#singular    → Nombre singular del componente
#plural      → Nombre plural del componente

Ejemplo en código:
"create-success-message" => "La #singular se registró exitosamente!"

Convertido en tiempo de ejecución:
"La IP Range se registró exitosamente!"
```

---

## 7. Constantes y Variables Disponibles

### 7.1 En el Coder (lang.php)

**Variables de configuración:**
```php
Database::connect("default")     // Conexión a base de datos
$db->getFieldNames($id)          // Obtiene nombres de campos de tabla
```

**Servicios disponibles:**
```php
service("forms")                 // Servicio de formularios
service("dates")                 // Servicio de fechas y horas
get_development_code_copyright() // Función que genera encabezado de copyright
```

**Funciones de formato:**
```php
safe_ucfirst($string)    // Primera letra mayúscula con seguridad
safe_strtolower($string) // Convertir a minúsculas con seguridad
```

### 7.2 Variables de Instancia

```php
$oid                 // Identificador de objeto (ej: firewall_iprange)
$id                  // Alias de $oid
$parent              // Instancia del controlador padre
$data                // Array con datos globales heredados
$fields              // Array de nombres de campos de tabla
$ucf_module          // Nombre módulo con primera letra mayúscula
$slc_module          // Nombre módulo en minúsculas
$ucf_component       // Nombre componente con primera letra mayúscula
$slc_component       // Nombre componente en minúsculas
$ucf_options         // Nombre opción con primera letra mayúscula (si existe)
$slc_options         // Nombre opción en minúsculas (si existe)
$model               // Namespace completo de clase modelo
$classname           // Nombre de clase construido dinámicamente
```

### 7.3 Constantes Globales del Framework

```php
APPPATH              // Ruta a directorio app/
FCPATH               // Ruta al directorio raíz del proyecto
```

---

## 8. Uso Paso a Paso

### 8.1 Acceder al Generador

```
1. URL: /development/generators/lang/
2. Ingresa automáticamente a la página del generador
```

### 8.2 Seleccionar Componente

El acceso puede ser directo o mediante formulario de selección:

```
1. Parámetro OID: firewall_iprange
2. El sistema genera automáticamente:
   - Nombre de módulo: Firewall
   - Nombre de componente: IpRange
   - Ruta de archivo: app/Modules/Firewall/Language/es/_Firewall_IpRange.php
```

### 8.3 Revisar el Código Generado

```
1. El formulario muestra el archivo de idioma completo
2. Verificar la ruta de destino
3. Editar si es necesario:
   - Modificar mensajes según necesidad
   - Cambiar placeholders
   - Añadir claves adicionales
```

### 8.4 Entender la Estructura del Código Generado

El coder genera automáticamente:

```php
// 1. Campos de tabla como claves
"label_id"           => "id"
"label_name"         => "name"
"label_description"  => "description"
"placeholder_id"     => "id"
"help_id"            => "id"

// 2. Mensajes de creación
"create-denied-title" => "Acceso denegado!"
"create-success-title" => "¡IP Range registrada exitosamente!"

// 3. Mensajes de edición
"edit-denied-title" => "¡Advertencia!"
"edit-success-message" => "Los datos de #singular se actualizaron exitosamente"

// 4. Mensajes de eliminación
"delete-denied-title" => "¡Advertencia!"
"delete-success-message" => "La #singular se elimino exitosamente"

// 5. Mensajes de visualización
"view-denied-title" => "¡Acceso denegado!"
"view-noexist-title" => "¡No existe!"

// 6. Mensajes de listado
"list-title" => "Listado de #plural"
"list-description" => "Descripción de #plural"
```

### 8.5 Guardar el Archivo

```
1. Click en "Guardar"
2. Validación de campos requeridos:
   - pathfile (obligatorio)
   - mkdir (obligatorio)
   - uri_save (obligatorio)
   - code (obligatorio)
3. Creación del directorio Language/es/ si no existe
4. Creación del archivo de idioma
5. Asignación de permisos (664)
6. Mensaje de éxito/advertencia
```

### 8.6 Verificar el Archivo Creado

```bash
# Ver contenido del archivo
cat app/Modules/Firewall/Language/es/_Firewall_IpRange.php

# Verificar permisos
ls -la app/Modules/Firewall/Language/es/_Firewall_IpRange.php
```

Debería mostrar:
```
-rw-rw-r-- _Firewall_IpRange.php
```

### 8.7 Usar en el Controlador

```php
// En el controlador del módulo
$translation = lang("Firewall_IpRange.create-title");
// Resultado: "Crear nuevo IP Range"

$fieldLabel = lang("Firewall_IpRange.label_name");
// Resultado: "name"

$successMsg = lang("Firewall_IpRange.create-success-message");
// Resultado: "La IP Range se registró exitosamente!"
```

---

## 9. Personalización

### 9.1 Modificar Mensajes de Error

En el código generado, cambiar:

```php
// Original
"create-errors-message" => "Los datos proporcionados son incorrectos o están incompletos, por favor verifique eh inténtelo nuevamente."

// Personalizado
"create-errors-message" => "Verifique los datos ingresados. Campos requeridos: nombre, descripción"
```

### 9.2 Cambiar Placeholders de Campos

En el código generado:

```php
// Original
"placeholder_name" => "name"

// Personalizado
"placeholder_name" => "Ingrese el nombre del rango IP"
```

### 9.3 Añadir Claves Adicionales

Después de generar el archivo, agregar claves personalizadas:

```php
// Añadir en la sección correspondiente
"custom_message" => "Mensaje personalizado",
"custom_title" => "Título personalizado",
"custom_help" => "Ayuda adicional",
```

### 9.4 Crear Archivos para Otros Idiomas

Una vez creado el archivo en español (_Firewall_IpRange.php):

```
1. Duplicar el archivo
2. Cambiar nombre: _Firewall_IpRange.php -> _Firewall_IpRange_en.php
3. Traducir los mensajes al inglés
4. Usar: lang("Firewall_IpRange_en.create-title")
```

Estructura de directorios multiidioma:
```
app/Modules/Firewall/Language/
├── es/
│   ├── _Firewall_IpRange.php      (Español)
│   └── _Firewall_IpRange_en.php   (Inglés)
├── en/
│   ├── _Firewall_IpRange.php      (Estructura para inglés)
```

### 9.5 Modificar Referencias Dinámicas

En tiempo de ejecución, el sistema busca las referencias y las reemplaza:

```php
// En el archivo de idioma
"create-success-message" => "¡#singular registrada!"

// En el código de la aplicación
$component = "IP Range";  // Asignado en controlador
$msg = lang("Firewall_IpRange.create-success-message");
// Se reemplaza #singular por "IP Range" si está configurado
```

---

## 10. Detalles Técnicos

### 10.1 Obtención de Campos de Base de Datos

El coder consulta la tabla especificada:

```php
$db = Database::connect("default");
$fields = $db->getFieldNames($id);  // Obtiene array de nombres de campos

// Ejemplo de resultado:
// Array ( [0] => id, [1] => name, [2] => description, [3] => created_at )
```

### 10.2 Generación de Copyright

Cada archivo incluye encabezado de copyright automático:

```php
$code = get_development_code_copyright(array(
    "path" => $namespaced  // Ruta documentada en encabezado
));
```

El encabezado incluye:
- Copyright de Higgs Bigdata
- Información de licencia
- Ruta del archivo
- Versión del framework

### 10.3 Formato de Array de Retorno

El archivo generado es un array asociativo que retorna todas las claves:

```php
<?php
return [
    "label_id"              => "id",
    "label_name"            => "name",
    "placeholder_id"        => "id",
    ...
];
```

### 10.4 Validación de Campos

El validador usa reglas estándar de CodeIgniter:

```php
"trim"          // Elimina espacios al inicio y final
"required"      // Campo obligatorio
```

### 10.5 Manejo de Permisos de Archivos

```php
chmod($mkdir, 0775);    // rwxrwxr-x (lectura-escritura para grupo)
chmod($pathfile, 0664); // rw-rw-r-- (lectura-escritura para grupo)
```

Los permisos 0775 y 0664 aseguran que:
- El servidor web (usuario www-data) pueda escribir
- El grupo pueda acceder
- Otros usuarios tengan solo lectura

---

## 11. Errores Comunes

| Error | Causa | Solución |
|-------|-------|----------|
| "Permiso denegado" | Usuario sin acceso | Asignar permiso `development-access` |
| Archivo no creado | Permisos del servidor | Verificar `chmod` en directorio app/Modules |
| Tabla no encontrada | OID incorrecto | Verificar nombre exacto en base de datos |
| Código malformado | Caracteres especiales en nombre | Usar solo alfanuméricos y guiones |
| Campos no generados | Tabla sin campos | Asegurar que la tabla existe en BD |
| Ruta incorrecta | Nombre módulo/componente inconsistente | Usar PascalCase para módulo/componente |
| Archivo en blanco | Validación rechazada | Revisar mensaje de error de validación |
| No se reconocen cambios | Caché activo | Limpiar caché con `clear_AllCache()` |

---

## 12. Ejemplo Completo

### Generar archivo de idioma para Módulo Firewall - Tabla IP Ranges

**1. Ingresar al generador:**
```
URL: /development/generators/lang/
```

**2. Especificar componente:**
```
OID: firewall_iprange
```

**3. El sistema genera automáticamente:**

```
Módulo: Firewall
Componente: IpRange
Ruta destino: app/Modules/Firewall/Language/es/_Firewall_IpRange.php
Directorio: app/Modules/Firewall/Language/es/
```

**4. Revisar código generado:**

El formulario mostrará un archivo como este:

```php
<?php
return [
    // - IpRange fields 
    "label_id"            => "id",
    "label_name"          => "name",
    "label_description"   => "description",
    "label_ip_start"      => "ip_start",
    "label_ip_end"        => "ip_end",
    
    "placeholder_id"      => "id",
    "placeholder_name"    => "name",
    "placeholder_description" => "description",
    
    "help_id"             => "id",
    "help_name"           => "name",
    
    // - IpRange creator 
    "create-denied-title"       => "Acceso denegado!",
    "create-denied-message"     => "Su rol en la plataforma no posee los privilegios...",
    "create-title"              => "Crear nuevo #singular",
    "create-errors-title"       => "¡Advertencia!",
    "create-errors-message"     => "Los datos proporcionados son incorrectos...",
    "create-success-title"      => "¡#singular registrada exitosamente!",
    "create-success-message"    => "La #singular se registró exitosamente...",
    
    // - IpRange editor 
    "edit-denied-title"         => "¡Advertencia!",
    "edit-denied-message"       => "Los roles asignados a su perfil...",
    "edit-title"                => "¡Actualizar #singular!",
    "edit-errors-title"         => "¡Advertencia!",
    "edit-errors-message"       => "Los datos proporcionados son incorrectos...",
    "edit-success-title"        => "¡#singular actualizada!",
    "edit-success-message"      => "Los datos de #singular se actualizaron exitosamente...",
    
    // - IpRange deleter 
    "delete-denied-title"       => "¡Advertencia!",
    "delete-denied-message"     => "Los roles asignados a su perfil...",
    "delete-title"              => "¡Eliminar #singular!",
    "delete-message"            => "Para confirmar la eliminación del #singular...",
    "delete-success-title"      => "¡#Singular eliminad@ exitosamente!",
    "delete-success-message"    => "La #singular se elimino exitosamente...",
    
    // - IpRange list 
    "list-title"                => "Listado de #plural",
    "list-description"          => "Descripción de #plural",
];
```

**5. Personalizar si es necesario:**

```php
// Cambiar algún mensaje específico
"list-title"        => "Rango de IPs registradas",
"list-description"  => "Gestione los rangos de IP permitidas en el sistema",

// Cambiar placeholders
"placeholder_name"  => "Ej: Rango local",
"placeholder_description" => "Descripción del rango",
```

**6. Guardar**

**7. Archivo creado:**
```
app/Modules/Firewall/Language/es/_Firewall_IpRange.php
```

**8. Usar en el módulo:**

```php
// En el controlador
$title = lang("Firewall_IpRange.create-title");
// Resultado: "Crear nuevo IP Range"

$label = lang("Firewall_IpRange.label_name");
// Resultado: "name"

// En la vista
<label><?= lang("Firewall_IpRange.label_ip_start") ?></label>
<input type="text" placeholder="<?= lang("Firewall_IpRange.placeholder_ip_start") ?>">
<small><?= lang("Firewall_IpRange.help_ip_start") ?></small>
```

---

## 13. Resumen Operacional

```
┌─────────────────────────────────────────────────────────────┐
│  Acceso: /development/generators/lang/                      │
└──────────────┬──────────────────────────────────────────────┘
               │
               ↓
        ┌──────────────┐
        │ Especificar  │
        │ componente   │
        │ (OID)        │
        └──────┬───────┘
               │
               ↓
    ┌──────────────────────┐
    │ Coder genera código: │
    │ 1. Obtiene campos BD │
    │ 2. Crea etiquetas    │
    │ 3. Crea placeholders │
    │ 4. Crea mensajes CRUD│
    └──────┬───────────────┘
           │
           ↓
    ┌──────────────────────┐
    │ Ver código PHP       │
    │ generado             │
    │ (editable)           │
    └──────┬───────────────┘
           │
           ↓
    ┌──────────────────────┐
    │ Validar campos:      │
    │ - pathfile           │
    │ - mkdir              │
    │ - uri_save           │
    │ - code               │
    └──────┬───────────────┘
           │
           ↓
    ┌──────────────────────────────────────┐
    │ Escribir archivo único en:           │
    │ app/Modules/{M}/Language/es/         │
    │ _{M}_{C}.php                         │
    │                                      │
    │ Ejemplo:                             │
    │ _Firewall_IpRange.php                │
    └──────┬───────────────────────────────┘
           │
           ↓
    ┌──────────────────────┐
    │ Mensaje de éxito     │
    │ Archivo disponible   │
    └──────────────────────┘
```

---

## 14. Diferencias clave: Lang vs Lister

| Aspecto | Lang Generator | Lister Generator |
|---------|---|---|
| **Archivos generados** | 1 archivo | 4 archivos |
| **Propósito** | Traducción/Localización | Visualización de datos |
| **Ubicación** | `Language/es/` | `Views/{Component}/_List/` |
| **Contenido** | Claves de texto | Vistas HTML + lógica |
| **Entrada usuario** | Especificar OID | Seleccionar tabla |
| **Campos dinámicos** | Basado en campos de tabla | Basado en campos de tabla |
| **Editable** | Código PHP (array) | Código PHP (múltiples archivos) |
| **Extensión** | `.php` (archivo de idioma) | `.php` (múltiples vistas) |
| **Estructura** | Plana (un array) | Jerárquica (múltiples archivos) |

---

## 15. Referencia de Claves Generadas

### 15.1 Claves de Campos

```
label_{fieldname}       → Etiqueta del campo
placeholder_{fieldname} → Texto placeholder del campo
help_{fieldname}        → Texto de ayuda del campo
```

**Ejemplo para campo "name":**
```php
"label_name" => "name",
"placeholder_name" => "name",
"help_name" => "name"
```

### 15.2 Claves de Creación

```
create-denied-title         → Título cuando acceso denegado
create-denied-message       → Mensaje cuando acceso denegado
create-title                → Título del formulario
create-errors-title         → Título de errores de validación
create-errors-message       → Mensaje de errores
create-duplicate-title      → Título si existe duplicado
create-duplicate-message    → Mensaje si existe duplicado
create-success-title        → Título de éxito
create-success-message      → Mensaje de éxito
```

### 15.3 Claves de Visualización

```
view-denied-title       → Acceso denegado
view-denied-message     → Mensaje de acceso denegado
view-title              → Título de vista
view-errors-title       → Título de errores
view-errors-message     → Mensaje de errores
view-noexist-title      → Título si no existe
view-noexist-message    → Mensaje si no existe
view-success-title      → Título de éxito
view-success-message    → Mensaje de éxito
```

### 15.4 Claves de Edición

```
edit-denied-title       → Acceso denegado
edit-denied-message     → Mensaje de acceso denegado
edit-title              → Título del formulario
edit-errors-title       → Título de errores
edit-errors-message     → Mensaje de errores
edit-noexist-title      → Título si no existe
edit-noexist-message    → Mensaje si no existe
edit-success-title      → Éxito al editar
edit-success-message    → Mensaje de éxito
```

### 15.5 Claves de Eliminación

```
delete-denied-title         → Acceso denegado
delete-denied-message       → Mensaje de acceso denegado
delete-title                → Título de confirmación
delete-message              → Mensaje de confirmación
delete-errors-title         → Título de errores
delete-errors-message       → Mensaje de errores
delete-noexist-title        → Título si no existe
delete-noexist-message      → Mensaje si no existe
delete-success-title        → Éxito al eliminar
delete-success-message      → Mensaje de éxito
```

### 15.6 Claves de Listado

```
list-denied-title       → Acceso denegado al listar
list-denied-message     → Mensaje de acceso denegado
list-title              → Título de la lista
list-description        → Descripción de la lista
```

---

## 16. Casos de Uso Avanzados

### 16.1 Integración con Validación de Formularios

```php
// En el controlador, usar claves de error
if (!$this->validation->run()) {
    $errors = $this->validation->getErrors();
    foreach ($errors as $field => $error) {
        $label = lang("Firewall_IpRange.label_{$field}");
        echo "Error en $label: $error";
    }
}
```

### 16.2 Mostrar Mensajes con Placeholders

```php
// En la vista, reemplazar #singular y #plural
$message = lang("Firewall_IpRange.create-success-message");

// Si el sistema tiene $singular y $plural configurados:
$message = str_replace("#singular", "IP Range", $message);
$message = str_replace("#plural", "IP Ranges", $message);

echo $message;  // "La IP Range se registró exitosamente!"
```

### 16.3 Crear Variantes por Idioma

```php
// Archivo base (español)
app/Modules/Firewall/Language/es/_Firewall_IpRange.php

// Crear para inglés
app/Modules/Firewall/Language/en/_Firewall_IpRange.php

// Usar según idioma configurado
$lang = config('App')->supportedLocales[0];  // 'es' o 'en'
$translation = lang("Firewall_IpRange.create-title", [], 'es');
```

### 16.4 Extender el Archivo Generado

Después de generar, agregar claves personalizadas:

```php
<?php
return [
    // Claves generadas automáticamente
    "label_id" => "id",
    
    // Claves personalizadas agregadas manualmente
    "custom_status_active" => "Activo",
    "custom_status_inactive" => "Inactivo",
    "custom_filter_by_range" => "Filtrar por rango",
];
```

---

## 17. Referencias

- **Ubicación del código:** `/www/wwwroot/_development/app/Modules/Development/Views/Generators/Lang/`
- **Framework:** Higgs (CodeIgniter 4 fork)
- **Librería de archivos:** `App\Libraries\Files`
- **Servicio de formularios:** `Forms` (Nexus)
- **Estándar de código:** PSR-12 (PHP)
- **Permisos de archivo:** POSIX (0775, 0664)
- **Encoding:** UTF-8 (espacio de nombres PHP)

---

**Última actualización:** 2026-05-06  
**Versión Lang Generator:** 1.5.0  
**Autor:** Jose Alexis Correa Valencia  
**Licencia:** Higgs Bigdata Framework 7.1
