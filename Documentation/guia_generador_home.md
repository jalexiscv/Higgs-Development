# Guía Completa: Generador de Cruds "Home" en Higgs Framework

## 1. Introducción

El **Generador de Home** es una herramienta automatizada que genera vistas principales (home/dashboard) para componentes de módulos. Crea cuatro archivos PHP principales que manejan:

- **Página principal de componente** con visualización de contenido personalizado
- **Control de permisos** (Deny)
- **Navegación de breadcrumb**
- **Lógica de verificación y renderización**

El Home es la puerta de entrada a un componente y típicamente muestra información resumida, opciones de acciones rápidas y enlaces a funcionalidades relacionadas.

---

## 2. Arquitectura General del Generador

```
/Home/
├── index.php                 ← Punto de entrada (verifica permisos)
├── form.php                  ← Formulario que muestra el código a generar
├── validator.php             ← Valida los campos del formulario
├── processor.php             ← Procesa y escribe los archivos generados
├── breadcrumb.php            ← Breadcrumb de la página del generador
├── deny.php                  ← Página de acceso denegado
└── coders/
    ├── index.php             ← Genera código para index.php
    ├── view.php              ← Genera código para la vista principal (view.php)
    ├── deny.php              ← Genera código para deny.php
    └── breadcrumb.php        ← Genera código para breadcrumb.php
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
- Se comprueba el permiso: `nexus-access` (singular)
- Si falta permiso → Muestra `deny.php`
- Si tiene permiso → Muestra `form.php`

---

### 3.2 Etapa 2: Mostrar Formulario (form.php)

El formulario contiene:

1. **Ruta de destino** (readonly):
   - Ejemplo: `app/Modules/Firewall/Views/IpRange/Home`
   - Se construye con: `APPPATH . "Modules/{ModuleName}/Views/{ComponentName}/Home"`

2. **Código PHP a generar** (área editable):
   - Contiene el código combinado de los 4 coders
   - Usuario puede revisar y editar antes de guardar

3. **Campos ocultos** que contienen:
   - `pathfiles` → Ruta destino
   - `cindex` → Código del index.php (URL encoded)
   - `cdeny` → Código del deny.php (URL encoded)
   - `cview` → Código del view.php (URL encoded)
   - `cbreadcrumb` → Código del breadcrumb.php (URL encoded)

---

### 3.3 Etapa 3: Validación (validator.php)

Se validan los campos requeridos:

```php
$f->set_ValidationRule("pathfiles", "trim|required");
$f->set_ValidationRule("cindex", "trim|required");
$f->set_ValidationRule("cdeny", "trim|required");
$f->set_ValidationRule("cview", "trim|required");
$f->set_ValidationRule("cbreadcrumb", "trim|required");
```

**Si la validación falla:**
- Muestra tarjeta de error con los mensajes de validación

**Si la validación pasa:**
- Llama a `processor.php` para escribir los archivos

---

### 3.4 Etapa 4: Procesamiento y Creación de Archivos (processor.php)

```php
$generatedFiles = [
    "{$pathfiles}/index.php" => urldecode($cindex),
    "{$pathfiles}/deny.php" => urldecode($cdeny),
    "{$pathfiles}/view.php" => urldecode($cview),
    "{$pathfiles}/breadcrumb.php" => urldecode($cbreadcrumb),
];
```

**Proceso:**
1. Crea el directorio `$pathfiles` si no existe
2. Asigna permisos al directorio: `0775`
3. Escribe cada archivo con el contenido decodificado
4. Asigna permisos a cada archivo: `0664`
5. Muestra mensaje de éxito o advertencia

---

## 4. Estructura de Identificadores (OID)

El generador usa un identificador compuesto llamado **OID** (Object ID):

```
{module}_{component}_{options}
```

**Ejemplos:**
- `firewall_iprange` → 2 componentes (módulo_componente)
- `firewall_iprange_log` → 3 componentes (módulo_componente_opciones)

**Transformaciones:**
```php
$eid = explode("_", $oid);                    // Divide el OID
$ucf_module = safe_ucfirst($eid[0]);          // "Firewall"
$slc_module = safe_strtolower($eid[0]);       // "firewall"
$ucf_component = safe_ucfirst($eid[1]);       // "Iprange"
$slc_component = safe_strtolower($eid[1]);    // "iprange"
```

---

## 5. Archivos Generados

### 5.1 index.php

**Ubicación final:** `app/Modules/{Module}/Views/{Component}/Home/index.php`

**Responsabilidad:**
- Punto de entrada para visualizar el home del componente
- Verifica el permiso `{module}-{component}-view` (singular)
- Renderiza la vista principal si tiene permiso
- Redirige a `deny.php` si no tiene permiso
- Gestiona la sesión con datos de plantilla y contexto

**Variables disponibles:**
```php
$data['permissions']['singular'] = "firewall-iprange-view";
$singular = $authentication->has_Permission($data['permissions']['singular']);
```

**Estructura:**
```php
$data = $parent->get_Array();
$data['permissions'] = array('singular' => 'firewall-iprange-view', "plural" => false);
$singular = $authentication->has_Permission($data['permissions']['singular']);

if ($singular) {
    $c = view($component . '\view', $data);
} else {
    $c = view($deny, $data);
}

session()->set('page_template', 'page');
session()->set('page_header', $header);
session()->set('main_template', 'c9c3');
session()->set('messenger', true);
session()->set('main', $c);
session()->set('right', '');
```

---

### 5.2 view.php (La Vista Principal)

**Ubicación final:** `app/Modules/{Module}/Views/{Component}/Home/view.php`

**Responsabilidad:**
- Genera la visualización principal del home del componente
- Muestra información resumida/dashboard
- Proporciona enlaces rápidos a acciones
- Utiliza modelos para obtener datos
- Renderiza componentes visuales personalizados

**Características principales:**

#### a) Inicialización de Servicios
```php
$s = service('strings');
$b = service('bootstrap');
$mcases = model("App\Modules\Firewall\Models\Firewall_Iprange");
```

#### b) Construcción de HTML
```php
$html = "<div class=\"row row-cols-xxl-4 row-cols-xl-3 row-cols-lg-2 row-cols-md-1 row-cols-1 text-center\">";
```

#### c) Creación de Tarjetas de Componentes
```php
$card = service("smarty");
$card->set_Mode("bs5x");
$card->caching = 0;
$card->assign("type", "normal");
$card->assign("header", "Título de Sección");
$card->assign("body", "<i class=\"fa-solid fa-icon fa-4x\"></i>");
$card->assign("footer", "<a href=\"/module/component/action/\" class=\"btn btn-lg btn-primary\">Acción</a>");
$html .= $card->view('components/cards/index.tpl');
```

#### d) Consulta de Datos
```php
$rows = $m{component}->where('status', 'active')->findAll();
foreach ($rows as $row) {
    // Crear tarjetas dinámicas por cada resultado
}
```

#### e) Renderización Final
```php
echo $html;
```

---

### 5.3 deny.php (Control de Acceso)

**Ubicación final:** `app/Modules/{Module}/Views/{Component}/Home/deny.php`

**Responsabilidad:**
- Mostrar pantalla de acceso denegado
- Diferencia entre usuario no autenticado vs. sin permisos

**Lógica:**
```php
if ($authentication->get_LoggedIn()) {
    // Usuario AUTENTICADO pero SIN PERMISOS
    $_icon = BS5::icon(['icon' => 'ban', ...]);
    $_body = "<!-- Mensaje de acceso denegado -->";
    $_permissions = "<!-- Lista de permisos requeridos -->";
    
    $card = BS5::card([
        'header' => ['title' => lang('App.Access-denied-title'), ...],
        'content' => ['htmlContent' => $_body . $_permissions, ...],
        ...
    ]);
} else {
    // Usuario NO AUTENTICADO
    $_icon = BS5::icon(['icon' => 'lock', ...]);
    $_body = "<!-- Mensaje de login requerido -->";
    
    $card = BS5::card([
        'header' => ['title' => lang('App.login-required-title'), ...],
        'content' => ['htmlContent' => $_body, ...],
        ...
    ]);
}

echo($card);
```

---

### 5.4 breadcrumb.php (Navegación)

**Ubicación final:** `app/Modules/{Module}/Views/{Component}/Home/breadcrumb.php`

**Responsabilidad:**
- Mostrar ruta de navegación actual
- Permitir navegación hacia atrás a módulo

**Estructura:**
```php
echo BS5::breadcrumb(['items' => [
    ['label' => '{module}', 'href' => '/{module}/'],
    ['label' => lang('App.{component}'), 
     'href' => '/{module}/{component}/home/'.lpk(), 
     'active' => true],
]]);
```

---

## 6. Convenciones de Nombres

### 6.1 Rutas de Archivos

```
app/Modules/{ModuleName}/
├── Views/
│   └── {ComponentName}/
│       └── Home/
│           ├── index.php
│           ├── view.php
│           ├── deny.php
│           └── breadcrumb.php
```

### 6.2 Nombres de Clases y Espacios

**Para 2 componentes (`firewall_iprange`):**
```php
Namespace: App\Modules\Firewall\Models
Clase: Firewall_Iprange
URL: /firewall/iprange/home/
Permiso: firewall-iprange-view
```

**Para 3 componentes (`firewall_iprange_log`):**
```php
Namespace: App\Modules\Firewall\Models
Clase: Firewall_Iprange_Log
URL: /firewall/iprange/log/home/
Permiso: firewall-iprange-log-view
```

### 6.3 Permisos

```
Singular (Principal): {module}-{component}-view
Plural (No usado en Home): false
```

---

## 7. Constantes y Variables Disponibles

### 7.1 En los Coders

```php
COMMENT_HR_VARS          // Comentario separador de variables
COMMENT_MODULECONTROLER_VARS  // Documentación de variables heredadas
COMMENT_HR_BUILD         // Comentario separador de construcción
COMMENT_HR_MODELS        // Comentario separador de modelos
ICON_VIEW               // Ícono de visualizar
ICON_EDIT               // Ícono de editar
ICON_DELETE             // Ícono de eliminar
ICON_ADD                // Ícono de añadir
ICON_BACK               // Ícono de atrás
ICON_INFO               // Ícono de información
```

### 7.2 Variables de Instancia

```php
$parent          // Instancia de ModuleController
$authentication  // Servicio de autenticación
$request         // Servicio de solicitud (GET/POST)
$bootstrap       // Servicio Bootstrap
$dates           // Servicio de fechas
$strings         // Servicio de cadenas
$oid             // Identificador de objeto (ej: firewall_iprange)
$data            // Array con datos globales del módulo
$component       // Ruta a la vista actual
```

### 7.3 Variables de Sesión

```php
session()->set('page_template', 'page');      // Plantilla de página
session()->set('page_header', $header);       // Encabezado de página
session()->set('main_template', 'c9c3');      // Template principal
session()->set('messenger', true);            // Habilitar mensajería
session()->set('main', $content);             // Contenido principal
session()->set('right', $rightPanel);         // Panel derecho
```

---

## 8. Uso Paso a Paso

### 8.1 Acceder al Generador

```
1. URL: /development/generators/home/
2. Seleccionar tabla/componente: firewall_iprange (del combo)
3. El generador carga automáticamente
```

### 8.2 Revisar el Código Generado

```
1. El formulario muestra el código PHP combinado
2. Copiar/revisar la ruta de destino
3. Editar si es necesario (código personalizado)
```

### 8.3 Guardar los Archivos

```
1. Click en "Guardar"
2. Validación de campos requeridos
3. Creación de archivos en:
   app/Modules/{Module}/Views/{Component}/Home/
4. Mensaje de éxito/advertencia
```

### 8.4 Verificar los Archivos Creados

```bash
ls -la app/Modules/Firewall/Views/IpRange/Home/
```

Debería mostrar:
```
-rw-rw-r-- breadcrumb.php
-rw-rw-r-- deny.php
-rw-rw-r-- index.php
-rw-rw-r-- view.php
```

### 8.5 Acceder a la Vista Generada

```
URL: /firewall/iprange/home/
```

---

## 9. Personalización

### 9.1 Agregar Nuevas Secciones

En `view.php`, crear nuevas tarjetas:

```php
$card = service("smarty");
$card->set_Mode("bs5x");
$card->caching = 0;
$card->assign("type", "normal");
$card->assign("class", "mb-3");
$card->assign("header", "Mi Sección");
$card->assign("header_back", false);
$card->assign("body", "<i class=\"fa-solid fa-chart-line fa-4x\"></i>");
$card->assign("footer", "<a href=\"/firewall/iprange/list/\" class=\"w-100 btn btn-lg btn-primary\">Ver Todos</a>");
$html .= $card->view('components/cards/index.tpl');
```

### 9.2 Personalizar Datos Dinámicos

En `view.php`, modificar consultas:

```php
$rows = $m{component}->where('status', 'active')
                     ->where('created_at >=', date('Y-m-d', strtotime('-30 days')))
                     ->orderBy('created_at', 'DESC')
                     ->findAll();

foreach ($rows as $row) {
    // Crear contenido dinámico
}
```

### 9.3 Cambiar Estructura de Grid

Modificar el grid de tarjetas:

```php
// 4 columnas en desktop, 3 en xl, 2 en lg, 1 en md/sm
$html = "<div class=\"row row-cols-xxl-4 row-cols-xl-3 row-cols-lg-2 row-cols-md-1 row-cols-1\">";

// O más compacto:
// row-cols-1, row-cols-sm-2, row-cols-md-3, row-cols-lg-4
```

### 9.4 Añadir Información Alertas

En `view.php`, agregar alertas:

```php
$sinfo = service("smarty");
$sinfo->set_Mode("bs5x");
$sinfo->caching = 0;
$sinfo->assign("title", "Nota Importante");
$sinfo->assign("message", "Este es un mensaje informativo");
echo($sinfo->view('alerts/inline/info.tpl'));
```

### 9.5 Modificar Panel Derecho

En `index.php`, cambiar panel derecho:

```php
session()->set('right', get_custom_sidebar_content());
```

---

## 10. Detalles Técnicos

### 10.1 Modelos y Base de Datos

El generador puede consultar datos:

```php
$db = Database::connect("default");
$fields = $db->getFieldNames($oid);  // Obtiene nombres de campos
$pk = $fields[0] ?? 'id';            // Identifica la clave primaria

$m{component} = model('App\Modules\{Module}\Models\{Module}_{Component}');
$rows = $m{component}->findAll();
```

### 10.2 Servicios Disponibles

```php
$strings = service('strings');       // Servicio de cadenas
$bootstrap = service('bootstrap');   // Bootstrap v5.3.3
$smarty = service('smarty');         // Motor de plantillas Smarty
$dates = service('dates');           // Servicio de fechas
$auth = service('authentication');   // Autenticación
```

### 10.3 Templates Smarty

El sistema usa plantillas Smarty para componentes visuales:

```php
$card->view('components/cards/index.tpl');      // Tarjetas
$sinfo->view('alerts/inline/info.tpl');         // Alertas
$stats->view('lists/stats.tpl');                // Listas estadísticas
```

### 10.4 URL Encoding

Los coders generan código que será:
1. URL encoded antes de guardarse en campos ocultos
2. URL decoded antes de escribirse en archivos

```php
// En form.php (encoding)
$f->add_HiddenField("cindex", urlencode($cindex));

// En processor.php (decoding)
$generatedFiles["{$pathfiles}/index.php"] => urldecode($cindex)
```

### 10.5 Plantillas de Sesión

Las plantillas disponibles incluyen:
- `page` - Plantilla estándar de página
- `c9c3` - Template con sidebar completo
- `full` - Ancho completo

---

## 11. Errores Comunes

| Error | Causa | Solución |
|-------|-------|----------|
| "Permiso denegado" | Usuario sin acceso | Asignar permiso `nexus-access` |
| Archivos no creados | Permisos del servidor | Verificar `chmod` en directorio |
| Contenido vacío | Vista no trae datos | Revisar modelo y consultas en view.php |
| Iconos no se muestran | Font Awesome no cargado | Verificar CDN de FA en layout |
| Tarjetas desalineadas | CSS de grid incorrecto | Revisar clases row-cols-* |
| Permiso singular incorrecto | Nombre de permiso mal formado | Usar patrón: module-component-view |

---

## 12. Ejemplo Completo

### Generar Home para Módulo Firewall - Componente IP Ranges

**1. Ingresar al generador:**
```
URL: /development/generators/home/
```

**2. Seleccionar componente:**
```
OID: firewall_iprange
```

**3. Revisar:**
```
Ruta: app/Modules/Firewall/Views/IpRange/Home
Permiso: firewall-iprange-view
```

**4. Personalizar view.php:**

Agregar tarjeta de estadísticas:
```php
// Obtener estadísticas
$total = $m{iprange}->countAllResults();
$active = $m{iprange}->where('status', 'active')->countAllResults();
$blocked = $m{iprange}->where('status', 'blocked')->countAllResults();

// Crear tarjeta de estadísticas
$card = service("smarty");
$card->set_Mode("bs5x");
$card->assign("type", "normal");
$card->assign("header", "Estadísticas");
$card->assign("body", 
    "<p>Total: <strong>{$total}</strong></p>" .
    "<p>Activos: <strong>{$active}</strong></p>" .
    "<p>Bloqueados: <strong>{$blocked}</strong></p>"
);
$html .= $card->view('components/cards/index.tpl');
```

**5. Guardar**

**6. Archivos creados:**
```
app/Modules/Firewall/Views/IpRange/Home/
├── index.php        (Punto de entrada, manejo de sesión)
├── view.php         (Contenido principal del home)
├── deny.php         (Acceso denegado)
└── breadcrumb.php   (Navegación)
```

**7. Acceder a:**
```
/firewall/iprange/home/
```

---

## 13. Resumen Operacional

```
┌─────────────────────────────────────────────────────────────┐
│  Acceso: /development/generators/home/                      │
└──────────────┬──────────────────────────────────────────────┘
               │
               ↓
        ┌──────────────┐
        │ Seleccionar  │
        │ componente   │
        │ (OID)        │
        └──────┬───────┘
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
      ┌────────────────────────────────────────┐
      │ Escribir 4 archivos en:                │
      │ app/Modules/{M}/Views/{C}/Home/       │
      │ ├── index.php                         │
      │ ├── view.php                          │
      │ ├── deny.php                          │
      │ └── breadcrumb.php                    │
      └────────┬─────────────────────────────┘
               │
               ↓
      ┌────────────────────┐
      │ Mensaje de éxito   │
      └────────────────────┘
               │
               ↓
      ┌────────────────────────────────────────┐
      │ Acceder a:                             │
      │ /{module}/{component}/home/            │
      └────────────────────────────────────────┘
```

---

## 14. Ciclo de Vida del Home

```
[Usuario accede a /module/component/home/]
                    │
                    ↓
            [index.php carga]
                    │
        ┌───────────┴───────────┐
        │                       │
    ¿Tiene permiso?         ¿Tiene permiso?
     (module-component-view)
        │                       │
       SÍ                       NO
        │                       │
        ↓                       ↓
   [breadcrumb.php]        [deny.php]
   [view.php carga]
        │
        ├─ Obtiene datos
        ├─ Renderiza tarjetas
        ├─ Genera HTML
        │
        ↓
   [index.php asigna a sesión]
   [Plantilla renderiza resultado]
        │
        ↓
   [Usuario ve Home del componente]
```

---

## 15. Integración con Otros Generadores

El generador Home trabaja complementariamente con:

- **Controller** - Define la ruta al Home
- **Lister** - Genera listado completo (accesible desde Home)
- **Creator** - Genera formulario de creación (accesible desde Home)
- **Editor** - Genera formulario de edición (accesible desde Home)
- **Viewer** - Genera vista de detalle (accesible desde Home)

**Flujo típico:**
```
Home (inicio)
  ├─→ Lister (lista de todos)
  ├─→ Creator (crear nuevo)
  ├─→ Viewer (ver detalle)
  └─→ Editor (editar)
```

---

## 16. Best Practices

### 16.1 Estructura de Contenido

- Mantener el Home simple y enfocado en lo principal
- Mostrar máximo 3-4 tarjetas principales
- Ocultar opciones avanzadas en subvistas
- Usar colores consistentes con tema del módulo

### 16.2 Rendimiento

```php
// BUENO: Cachear datos frecuentes
$rows = $m{component}->getCachedSearch($conditions);

// EVITAR: Consultas sin límite
$rows = $m{component}->findAll();  // Sin paginación

// BUENO: Limitar resultados
$rows = $m{component}->limit(10)->orderBy('created_at', 'DESC')->find();
```

### 16.3 Accesibilidad

```php
// Siempre incluir títulos descriptivos
$card->assign("header", lang("Firewall_IpRange.home-title"));

// Usar íconos con etiquetas de texto
'<i class="fa-solid fa-icon"></i> ' . lang("Firewall_IpRange.action");

// Mantener contraste de colores
'class' => 'btn btn-primary'  // Colores estándar
```

### 16.4 Mantenibilidad

```php
// BUENO: Separar lógica y presentación
$data = $m{component}->getStats();
$view->render('stats', $data);

// EVITAR: Lógica mezclada con HTML
if ($rows) { echo "<div>..."; }
```

---

## 17. Referencias

- **Ubicación del código:** `/www/wwwroot/_development/app/Modules/Development/Views/Generators/Home/`
- **Framework:** Higgs (CodeIgniter 4 fork)
- **Bootstrap:** v5.3.3 con componentes customizados
- **Template Engine:** Smarty 3.x
- **Estándar de código:** PSR-12 (PHP)

---

## 18. Diagrama de Capas

```
┌─────────────────────────────────────────┐
│          Navegador Usuario              │
├─────────────────────────────────────────┤
│      /module/component/home/            │
├─────────────────────────────────────────┤
│ [ModuleController]                      │
│   └─ Carga index.php                   │
├─────────────────────────────────────────┤
│ [index.php]                             │
│   ├─ Verifica permiso                   │
│   ├─ Carga vista (view.php)            │
│   └─ Asigna a sesión                    │
├─────────────────────────────────────────┤
│ [view.php]                              │
│   ├─ Consulta modelos                   │
│   ├─ Procesa datos                      │
│   └─ Renderiza HTML                     │
├─────────────────────────────────────────┤
│ [breadcrumb.php]                        │
│   └─ Muestra navegación                 │
├─────────────────────────────────────────┤
│ [Plantilla Bootstrap + Smarty]          │
│   ├─ CSS (Bootstrap v5.3.3)            │
│   ├─ HTML generado                      │
│   └─ JS (DataTables, etc)              │
├─────────────────────────────────────────┤
│      Respuesta HTML renderizada         │
└─────────────────────────────────────────┘
```

---

## 19. Checklist de Implementación

- [ ] Permiso singular creado: `{module}-{component}-view`
- [ ] Ruta en Controller: `{module}/{component}/home/`
- [ ] Archivos generados en: `app/Modules/{Module}/Views/{Component}/Home/`
- [ ] Permisos de archivos: 664 (rw-rw-r--)
- [ ] Modelo disponible: `App\Modules\{Module}\Models\{Module}_{Component}`
- [ ] Idioma del Home: `app/Language/es/{Module}_{Component}.php`
- [ ] View.php trae datos correctos
- [ ] Breadcrumb navega correctamente
- [ ] Deny.php muestra mensajes apropiados
- [ ] Home accesible desde navegación del módulo

---

**Última actualización:** 2026-05-06  
**Versión Home:** 1.5.0  
**Autor:** Jose Alexis Correa Valencia

---

## Notas de Desarrollo

El generador Home es especialmente útil para:
- Crear dashboards de módulos
- Mostrar resúmenes y estadísticas
- Proporcionar acceso rápido a funciones principales
- Servir como puerta de entrada visual al módulo

Para máximo control, se recomienda personalizar `view.php` manualmente después de generar para adaptarlo a necesidades específicas del módulo.
