# Extracción de OID parsing duplicado a _shared.php en 5 generadores

**Fecha:** 2026-05-20
**Módulo:** Development > Views/Generators

## Descripción

Refactorización de los generadores Creator, Editor, Deleter, Viewer y Lister para extraer el parsing OID duplicado a un archivo `coders/_shared.php` centralizado. Cada generador tenía bloques idénticos de parsing OID repetidos en cada coder.

## Tipo de Cambio

- `Cambiado` — Refactorización estructural
- `Agregado` — Archivos coders/_shared.php en 5 generadores

## Archivos Afectados

### NUEVO `Views/Generators/{Creator,Editor,Deleter,Viewer,Lister}/coders/_shared.php`
- Parseo OID centralizado en objeto $g con propiedades tipadas
- Conexión a base de datos y obtención de campos

### MODIFICADO `Views/Generators/{Creator,Editor,Deleter,Viewer,Lister}/coders/*.php`
- Sustitución de variables $ucf_module, $slc_component, etc. por $g->propiedades
- Eliminación de código OID duplicado
- Corrección de 12 bugs de namespace copy-paste

## Impacto

- Reducción de 532→330 líneas en Creator (-38%)
- Reducción de 568→363 líneas en Editor (-36%)
- Reducción de 483→297 líneas en Deleter (-39%)
- Reducción de 501→308 líneas en Viewer (-38.5%)
- Reducción de 610→409 líneas en Lister (-33%)
