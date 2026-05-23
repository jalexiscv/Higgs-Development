# Protocolo de Documentación de Cambios (Changelog Protocol)

Este documento establece el protocolo para registrar todos los cambios realizados en el código fuente del **módulo Development**. Este módulo es parte de una aplicación principal más grande, por lo que los cambios se documentan de forma aislada dentro del directorio del módulo.

Este protocolo debe ser seguido por todos los desarrolladores y agentes de IA que trabajen en el proyecto.

El formato está basado en [Keep a Changelog](https://keepachangelog.com/es-ES/1.0.0/) y este proyecto adhiere al [Versionado Semántico](https://semver.org/lang/es/).

---

## Estructura de Archivos

```
Modules/Development/
├── CHANGELOG_PROTOCOL.md          ← Este archivo (protocolo)
└── Changelogs/
    ├── CHANGELOG.md                ← Índice numerado de todos los cambios
    ├── YYYY-MM-DD_descripcion.md   ← Archivo detallado por cambio
    ├── YYYY-MM-DD_descripcion.md
    └── ...
```

---

## 1. Índice de Cambios (`Changelogs/CHANGELOG.md`)

Este archivo es un **índice numerado y resumido** de todos los cambios. **No debe contener detalles**, solo una línea breve por cambio con enlace al archivo detallado correspondiente.

### Formato del índice

```markdown
# Changelog - Módulo Sie

| #  | Fecha      | Descripción breve                         | Detalle                                      |
|----|------------|-------------------------------------------|----------------------------------------------|
| 3  | 2026-02-18 | Tabla de notas agrupada por periodo       | [Ver detalle](2026-02-18_descripcion.md)     |
| 2  | 2026-02-18 | Extracción de lógica de tablas a grids    | [Ver detalle](2026-02-18_otra_descripcion.md)|
| 1  | 2026-02-17 | Corrección de sintaxis en formulario Sync | [Ver detalle](2026-02-17_descripcion.md)     |
```

> **Nota:** Las entradas se numeran de forma ascendente. La más reciente va primero (orden cronológico inverso).

---

## 2. Archivos Detallados por Cambio

Cada cambio significativo debe tener su **propio archivo** en `Changelogs/` con la descripción completa.

### Nomenclatura del archivo

```
YYYY-MM-DD_descripcion_breve.md
```

Ejemplo: `2026-02-18_transcript_grids_extraction.md`

### Contenido del archivo detallado

```markdown
# Título descriptivo del cambio

**Fecha:** YYYY-MM-DD
**Módulo:** Sie > [Componente] > [Subcomponente]

## Descripción

Breve explicación del cambio y su motivación.

## Tipo de Cambio

- `Agregado` | `Cambiado` | `Corregido` | `Removido` | `Obsoleto` | `Seguridad`

## Archivos Afectados

### [NUEVO|MODIFICADO|ELIMINADO] `ruta/al/archivo.php`
- Detalle de lo que se hizo en este archivo

### [NUEVO|MODIFICADO|ELIMINADO] `ruta/al/otro_archivo.php`
- Detalle de lo que se hizo

## Impacto

- Descripción del impacto funcional
- Notas sobre compatibilidad o breaking changes si aplica
```

---

## 3. Tipos de Cambios

| Tipo         | Uso                                          |
|:-------------|:---------------------------------------------|
| `Agregado`   | Nuevas funcionalidades o archivos             |
| `Cambiado`   | Modificaciones a funcionalidades existentes   |
| `Corregido`  | Corrección de bugs                            |
| `Removido`   | Funcionalidades o archivos eliminados         |
| `Obsoleto`   | Funcionalidades marcadas para futura remoción |
| `Seguridad`  | Correcciones de vulnerabilidades              |

---

## 4. Reglas Generales

1. **Todo cambio debe documentarse** al momento de realizarse, no después.
2. **Un archivo detallado por conjunto de cambios relacionados** — si en una sesión se hacen múltiples cambios al mismo componente, van en un solo archivo.
3. **Actualizar siempre el índice** (`Changelogs/CHANGELOG.md`) al crear un archivo detallado.
4. Los documentos deben ser **legibles y claros**. Usar bloques de código para mostrar diferencias críticas si es necesario, manteniendo la brevedad.
5. Este módulo es **parte de una aplicación principal** — los cambios documentados aquí son exclusivos del módulo Sie.

---

*Este documento es la referencia para la IA y los desarrolladores sobre cómo gestionar el historial de cambios del módulo Sie.*
