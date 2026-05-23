# Protocolo de Construcción HTML con Bootstrap 5

## Principio fundamental

**Todo elemento HTML construido en este proyecto debe adherirse a los componentes gráficos reconocibles de Bootstrap 5, y su implementación debe realizarse exclusivamente a través de las clases PHP del paquete:**

```
/www/wwwroot/_development/system/Frontend/src/Bootstrap/v5_3_3
```

**Namespace de uso:**

```php
use Higgs\Frontend\Bootstrap\v5_3_3\Bootstrap as BS5;
```

---

## Reglas del protocolo

### 1. Prohibición de HTML crudo ad hoc

No se permite construir estructuras HTML manualmente si existe un componente BS5 equivalente.

| Prohibido | Correcto |
|-----------|----------|
| `<button class="btn btn-primary">...</button>` | `BS5::button(['content' => '...', 'variant' => 'primary'])->render()` |
| `<div class="alert alert-success">...</div>` | `BS5::alert(['content' => '...', 'type' => 'success'])->render()` |
| `<div class="container">...</div>` | `BS5::container(['content' => '...'])->render()` |

### 2. Uso obligatorio del paquete Frontend

Todos los componentes visuales deben instanciarse mediante la clase estática `BS5::` o sus clases internas. No se deben escribir clases CSS de Bootstrap directamente en los atributos PHP sin pasar por los componentes.

### 3. Seguridad en contenido HTML

- Usar `content` para texto plano (se escapa automáticamente).
- Usar `htmlContent` o `Html::raw()` **solo con HTML hardcodeado y confiable**.
- **Nunca** pasar entrada del usuario sin sanitizar como `htmlContent`.

### 4. Atributos adicionales

Todo atributo HTML adicional (id, data-*, aria-*, etc.) debe pasarse a través del array `attributes` del componente, no concatenarse manualmente.

---

## Componentes disponibles por categoría

### Layout — `Higgs\Frontend\Bootstrap\v5_3_3\Layout\`

| Componente | Método | Descripción |
|------------|--------|-------------|
| Container | `BS5::container([])` | Contenedor responsivo (fluid, sm, md, lg, xl, xxl) |
| Row | `BS5::row([])` | Fila del sistema de grilla |
| Col | `BS5::col([])` | Columna del sistema de grilla |
| Grid | `BS5::grid([])` | Grid CSS experimental |

```php
// Layout básico en 2 columnas
BS5::container([
    'content' => BS5::row([
        'gutter' => '3',
        'content' => [
            BS5::col(['size' => '8', 'content' => 'Principal'])->render(),
            BS5::col(['size' => '4', 'content' => 'Lateral'])->render(),
        ]
    ])->render()
])->render();
```

---

### Interface — `Higgs\Frontend\Bootstrap\v5_3_3\Interface\`

| Componente | Método | Descripción |
|------------|--------|-------------|
| Accordion | `BS5::accordion([])` | Paneles colapsables |
| Alert | `BS5::alert([])` | Mensajes de retroalimentación |
| Badge | `BS5::badge([])` | Etiquetas y contadores |
| Button | `BS5::button([])` | Botones de acción |
| ButtonGroup | `BS5::buttonGroup([])` | Agrupación de botones |
| Card | `BS5::card([])` | Contenedores flexibles |
| CardGroup | `BS5::cardGroup([])` | Grupos de tarjetas |
| Carousel | `BS5::carousel([])` | Carrusel de imágenes |
| Collapse | `BS5::collapse([])` | Elementos colapsables |
| Dropdown | `BS5::dropdown([])` | Menús desplegables |
| ListGroup | `BS5::listGroup([])` | Listas de grupos |
| Modal | `BS5::modal([])` | Diálogos modales |
| Offcanvas | `BS5::offcanvas([])` | Paneles laterales |
| Popover | `BS5::popover([])` | Información ampliada |
| Progress | `BS5::progress([])` | Barras de progreso |
| Spinner | `BS5::spinner([])` | Indicadores de carga |
| Toast | `BS5::toast([])` | Notificaciones push |
| Tooltip | `BS5::tooltip([])` | Información contextual |

```php
// Botón con variantes
BS5::button(['content' => 'Guardar', 'variant' => 'success', 'type' => 'submit'])->render();
BS5::button(['content' => 'Eliminar', 'variant' => 'danger', 'size' => 'sm'])->render();
BS5::button(['content' => 'Cancelar', 'variant' => 'secondary', 'outline' => true])->render();

// Alerta descartable
BS5::alert(['content' => 'Operación exitosa', 'type' => 'success', 'dismissible' => true])->render();

// Card
BS5::card([
    'title'   => 'Título',
    'content' => 'Cuerpo de la tarjeta',
    'footer'  => 'Pie de tarjeta'
])->render();
```

---

### Form — `Higgs\Frontend\Bootstrap\v5_3_3\Form\`

| Componente | Método | Descripción |
|------------|--------|-------------|
| Form | `BS5::form([])` | Contenedor de formulario |
| FormControl | `BS5::formControl([])` | Control de formulario con label |
| Input | `BS5::input([])` | Campo de texto |
| Textarea | `BS5::textarea([])` | Área de texto |
| Select | `BS5::select([])` | Lista desplegable |
| Check | `BS5::check([])` | Checkbox y switch |
| Radio | `BS5::radio([])` | Botón de radio |
| File | `BS5::file([])` | Input de archivos |
| InputGroup | `BS5::inputGroup([])` | Grupo de inputs con addons |

```php
// Campo de texto
BS5::input(['name' => 'email', 'type' => 'email', 'required' => true])->render();

// Select
BS5::select(['name' => 'pais', 'options' => ['co' => 'Colombia', 'mx' => 'México']])->render();

// Checkbox
BS5::check(['name' => 'activo', 'label' => 'Activo', 'checked' => true])->render();
```

---

### Navigation — `Higgs\Frontend\Bootstrap\v5_3_3\Navigation\`

| Componente | Método | Descripción |
|------------|--------|-------------|
| Breadcrumb | `BS5::breadcrumb([])` | Ruta de navegación |
| Nav | `BS5::nav([])` | Navegación base |
| Navbar | `BS5::navbar([])` | Barra de navegación |
| Pagination | `BS5::pagination([])` | Paginación |

---

### Content — `Higgs\Frontend\Bootstrap\v5_3_3\Content\`

| Componente | Método | Descripción |
|------------|--------|-------------|
| Figure | `BS5::figure([])` | Figura con caption |
| Image | `BS5::image([])` | Imagen responsiva |
| Table | `BS5::table([])` | Tabla de datos |
| Typography | `BS5::typography([])` | Tipografía y texto |

---

### Extras — `Higgs\Frontend\Bootstrap\v5_3_3\Extras\`

| Componente | Método | Descripción |
|------------|--------|-------------|
| Indicator | `BS5::indicator([])` | Indicadores visuales de estado |
| Tabs | `BS5::tabs([])` | Pestañas de navegación |
| Croppie | `BS5::croppie([])` | Recortador de imágenes |
| SelectMultiple | `BS5::selectMultiple([])` | Select con selección múltiple |

---

## Patrón de implementación estándar

Todos los componentes siguen el mismo patrón:

```php
// 1. Instanciación con array de opciones
$componente = BS5::nombreComponente([
    'opcion1' => 'valor1',
    'opcion2' => 'valor2',
    'attributes' => [
        'id'        => 'mi-id',
        'data-foo'  => 'bar',
        'class'     => 'clase-extra',
    ]
]);

// 2. Opcionalmente: encadenamiento fluido
$componente->metodoFluido(true);

// 3. Renderizado a string HTML
echo $componente->render();
```

---

## Íconos

Los íconos deben pasarse como clases CSS (Bootstrap Icons, Font Awesome, etc.) a través de la opción `icon` cuando el componente lo soporte, o como `Html::raw()` dentro del `content`:

```php
use Higgs\Html\Html;

// Opción preferida: parámetro 'icon'
BS5::button([
    'content'      => 'Guardar',
    'variant'      => 'success',
    'icon'         => 'bi bi-save',
    'iconPosition' => 'start',
])->render();

// Opción alternativa: Html::raw() con HTML confiable
$icon = Html::raw('<i class="bi bi-pencil"></i>');
BS5::button(['content' => [$icon, ' Editar'], 'variant' => 'warning'])->render();
```

---

## Composición de vistas

Al construir vistas PHP completas, los componentes BS5 deben anidarse compositivamente:

```php
echo BS5::container([
    'content' => [
        BS5::row([
            'gutter' => '3',
            'content' => [
                BS5::col([
                    'size'    => '12',
                    'md'      => '6',
                    'content' => BS5::card([
                        'title'   => 'Panel A',
                        'content' => BS5::alert([
                            'content' => 'Todo en orden',
                            'type'    => 'success',
                        ])->render(),
                    ])->render(),
                ])->render(),
            ],
        ])->render(),
    ],
])->render();
```

---

## Excepciones permitidas

Se permite HTML manual únicamente en estos casos:

1. **Elementos sin equivalente en el paquete** (ej: `<canvas>`, `<svg>` inline, `<video>`).
2. **Salida de librerías de terceros** que generan su propio HTML y no pueden envolverse sin romper su funcionamiento.
3. **Microestructuras de una sola línea** dentro de contenido textual (ej: `<strong>`, `<em>`, `<br>`).

En todos los casos excepcionales, el HTML debe ir dentro de `Html::raw()` y documentarse con un comentario inline explicando la razón.

---

## Referencias

- **Paquete**: `/www/wwwroot/_development/system/Frontend/src/Bootstrap/v5_3_3/`
- **Índice de componentes**: `Bootstrap.md` en la raíz del paquete
- **Documentación por categoría**: carpeta `Docs/`
- **Ejemplos de uso**: carpeta `Examples/`
- **Bootstrap 5.3 oficial**: https://getbootstrap.com/docs/5.3/

---

**Versión**: 1.0.0
**Fecha**: 2026-03-21
