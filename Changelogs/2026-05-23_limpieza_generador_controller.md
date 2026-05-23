# Limpieza del generador Controller: headers, DB innecesaria, breadcrumb y language key

**Fecha:** 2026-05-23
**Módulo:** Development > Views/Generators/Controller

## Descripción

Corrección de 4 problemas en el generador Controller: eliminación de conexión DB innecesaria, limpieza de headers ASCII art en index.php y breadcrumb.php, bug de language key en processor.php, y adición de claves de idioma faltantes.

## Tipo de Cambio

- `Corregido` — Bug de language key en processor.php que referenciaba `model--success-title`
- `Cambiado` — Limpieza de headers ASCII art en index.php y breadcrumb.php
- `Agregado` — Claves de idioma `controller-success-title` y `controller-success-text`
- `Eliminado` — Conexión DB innecesaria en coders/_shared.php

## Archivos Afectados

### MODIFICADO `Views/Generators/Controller/coders/_shared.php`
- Eliminada conexión a base de datos y `$g->fields` (no usados por controller.php)
- Reducción de 25→21 líneas

### MODIFICADO `Views/Generators/Controller/index.php`
- Eliminado header ASCII art de 32 líneas con copyright de framework
- Reemplazado por doc comments limpios `/** @var */` (patrón deny.php)
- Reducción de 65→49 líneas

### MODIFICADO `Views/Generators/Controller/breadcrumb.php`
- Eliminado header ASCII art de 27 líneas con path incorrecto (`Account\Views\Processes\Creator\deny.php`)
- Migración de `$b->get_Breadcrumb()` legacy a `BS5::breadcrumb()`
- Reducción de 33→9 líneas

### MODIFICADO `Views/Generators/Controller/processor.php`
- Corregido `lang("Development.model--success-title")` → `lang("Development.controller-success-title")`
- Corregido texto del body a `lang("Development.controller-success-text")`

### MODIFICADO `Language/es/Development.php`
- Agregadas claves `controller-success-title` y `controller-success-text`

## Impacto

- **Reducción neta:** -65 líneas entre los 4 archivos de vistas
- **breadcrumb.php:** de 33→9 líneas con migración completa a BS5
- **0 conexiones DB innecesarias** en el generador Controller
- **Language key corregida:** ya no usa la clave del generador Model
