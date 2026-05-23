# Normalización de salida generada por coders: PSR-12 + short arrays + single quotes

**Fecha:** 2026-05-23
**Módulo:** Development > Views/Generators (todos los coders)

## Descripción

Normalización del código que los generadores PRODUCEN como salida. CS Fixer había normalizado las plantillas (el código PHP de los coders), pero no el código generado como strings dentro de `$code .= "..."`. Este cambio asegura que todo el código nuevo generado de aquí en adelante cumpla PSR-12 + short arrays + single quotes.

## Tipo de Cambio

- `Cambiado` — 29 archivos coder en 8 generadores

## Reglas aplicadas al código generado

| Regla | Ejemplo |
|-------|---------|
| `array()` → `[]` | `array('key' => 'val')` → `['key' => 'val']` |
| `"string"` → `'string'` | `"primary"` → `'primary'` (sin interpolación) |
| Trailing comma | `['a', 'b']` → `['a', 'b',]` en multilínea |
| Se preserva `"` con `{$var}` | `"path/{$id}"` se mantiene |

## Archivos Afectados por Generador

| Generador | Archivos modificados |
|-----------|---------------------|
| Controller | 0 (ya estaba normalizado) |
| Lang | `coders/lang.php` — 45 language keys con single quotes |
| Migration | 0 (delega a librería externa) |
| Model | `coders/model.php` — allowedFields, timestamps, DBGroup |
| Creator | `coders/{form,processor,validator,deny,index}.php` (5 archivos) |
| Deleter | `coders/{form,processor,validator,deny,index}.php` (5 archivos) |
| Editor | `coders/{form,processor,validator,deny,index}.php` (5 archivos) |
| Viewer | `coders/{form,processor,validator,deny,index}.php` (5 archivos) |
| Lister | `coders/{grid,json,table,deny,index}.php` (5 archivos) |

**Total: 29 archivos, 349 inserciones, 343 eliminaciones**

## Impacto

- **0 errores de sintaxis** en los 34 archivos coder (verificado con `php -l`)
- **0 cambios funcionales** — el código generado es semánticamente idéntico
- El código nuevo generado desde ahora cumplirá CS Fixer sin necesidad de re-formateo
