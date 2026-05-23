# Refactorización integral de generadores: _shared.php, BS5, deny.php

**Fecha:** 2026-05-22
**Módulo:** Development > Views/Generators

## Descripción

Aplicación del patrón `coders/_shared.php` a los 4 generadores pendientes (Controller, Lang, Migration, Model), migración completa a componentes BS5, estandarización de archivos deny.php, y eliminación de código muerto en todos los generadores.

## Tipo de Cambio

- `Cambiado` — Refactorización estructural sin cambios funcionales
- `Agregado` — Nuevos archivos coders/_shared.php y coders
- `Removido` — Código muerto y generador Home/ vacío
- `Corregido` — Bug de namespace en Lang/coders/lang.php

## Archivos Afectados

### NUEVO `Views/Generators/Controller/coders/_shared.php`
### NUEVO `Views/Generators/Controller/coders/controller.php`
### NUEVO `Views/Generators/Lang/coders/_shared.php`
### NUEVO `Views/Generators/Migration/coders/_shared.php`
### NUEVO `Views/Generators/Model/coders/_shared.php`
### NUEVO `Views/Generators/Model/coders/model.php`
- Extracción de lógica de generación de código a archivos coder independientes
- Centralización de parsing OID en _shared.php (objeto $g)
- Eliminación de duplicación OID en Lang y Migration

### MODIFICADO `Views/Generators/Controller/form.php`
- Reducción de 192→55 líneas. Delega generación a coders/controller.php
- 28 líneas de código comentado eliminadas
- Variables muertas ($action, $module, $component) eliminadas

### MODIFICADO `Views/Generators/Controller/processor.php`
- Migración de legacy get_Card() a BS5::card()

### MODIFICADO `Views/Generators/Controller/validator.php`
- Migración a BS5::card() con estructura consistente

### MODIFICADO `Views/Generators/Lang/coders/lang.php`
- Uso de $g desde _shared.php. Eliminación de variables muertas
- Corrección de bug: namespaced referenciaba Creator\index.php

### MODIFICADO `Views/Generators/Lang/form.php`
- Reducción de 68→55 líneas. Migración a BS5::card()

### MODIFICADO `Views/Generators/Lang/processor.php`
- Migración a BS5::card()

### MODIFICADO `Views/Generators/Lang/validator.php`
- Migración a BS5::card()

### MODIFICADO `Views/Generators/Migration/coders/migration.php`
- Uso de $g desde _shared.php. Eliminación de variables muertas

### MODIFICADO `Views/Generators/Migration/form.php`
- Reducción de 48→40 líneas. Migración a BS5::card()

### MODIFICADO `Views/Generators/Migration/processor.php`
- Migración a BS5::card()

### MODIFICADO `Views/Generators/Migration/validator.php`
- Migración a BS5::card()

### MODIFICADO `Views/Generators/Model/form.php`
- Reducción de 159→45 líneas. Delega generación a coders/model.php
- 18 líneas de métodos comentados eliminadas

### MODIFICADO `Views/Generators/Model/processor.php`
- Migración a BS5::card()

### MODIFICADO `Views/Generators/Model/validator.php`
- Migración a BS5::card()

### MODIFICADO `Views/Generators/{Controller,Creator,Deleter,Editor,Lang,Lister,Migration,Model,Viewer}/deny.php`
- Estandarización de los 9 archivos deny.php a una plantilla única
- 8/9 comparten checksum idéntico (solo Lang difiere en $continue)
- Eliminación de headers ASCII art y bloques de copyright duplicados

### MODIFICADO `Views/Generators/{Creator,Deleter,Editor,Lister,Viewer}/processor.php`
- Ajustes menores de consistencia en processors ya refactorizados

### ELIMINADO `Views/Generators/Home/index.php`
- Directorio Home/ completo eliminado (index.php vacío, generador nunca implementado)

## Impacto

- **Reducción neta:** -451 líneas (-36% en archivos modificados)
- **9/10 generadores ahora usan patrón _shared.php** (excepto List que es dashboard)
- **0 llamadas legacy get_Card()** — todas migradas a BS5::card()
- **deny.php:** de 5 variantes a 2 (8 idénticos + Lang único + List único)
- **Sin breaking changes:** la funcionalidad de cada generador se preserva
- **118 archivos PHP pasan php -l** con 0 errores de sintaxis
