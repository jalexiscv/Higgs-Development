# Protocolo de Documentación (Documentation Protocol)

Este documento establece el protocolo para redactar, mantener y organizar toda la documentación relacionada con el código fuente del **módulo Development**. Este módulo es parte de una aplicación principal más grande, por lo que su documentación se mantiene contenida dentro de la estructura del propio módulo.

Este protocolo debe ser seguido por todos los desarrolladores y agentes de IA que trabajen en el proyecto para garantizar de este modo un código fácil de leer, mantener y escalar.

---

## Estructura de Archivos de Documentación

```text
Modules/Development/
├── DOCUMENTATION_PROTOCOL.md      ← Este archivo (protocolo de documentación)
├── CHANGELOG_PROTOCOL.md          ← Protocolo de registro de cambios
├── README.md                      ← Visión general del módulo y su propósito
└── Documentation/                          ← Directorio principal de documentación detallada
    ├── architecture.md            ← Decisiones de arquitectura y diseño
    ├── api_reference.md           ← Documentación de endpoints o interfaces públicas
    ├── setup.md                   ← Guía de configuración y dependencias
    └── ...
```

---

## 1. Documentación en el Código (Inline & DocBlocks)

Todo el código debe estar autodocumentado en la medida de lo posible mediante nombres claros de variables y métodos. Sin embargo, se requiere el uso de **DocBlocks** para clases, métodos y funciones complejas.

### Formato de DocBlocks (PHPDoc estándar)

```php
/**
 * Resumen corto de lo que hace la función o método.
 *
 * Descripción más detallada si es necesario, explicando el "por qué" 
 * o casos de uso particulares.
 *
 * @param string $parametro1 Descripción del parámetro 1.
 * @param int|null $parametro2 Descripción del parámetro 2 (opcional).
 * @return array|bool Descripción detallada de lo que retorna.
 * @throws Exception Descripción de en qué caso falla y lanza excepción.
 */
public function ejemploMetodo(string $parametro1, ?int $parametro2 = null)
{
    // ...
}
```

### Reglas para comentarios en línea
- **No explicar el "qué"** (eso lo dice el código), sino **explicar el "por qué"** (la razón detrás de una lógica compleja o decisión de negocio).
- Usar prefijos como `TODO:`, `FIXME:` o `NOTE:` para alertar sobre deudas técnicas o detalles relevantes.

---

## 2. Documentación a Nivel de Módulo (`README.md` y directorio `Docs/`)

### `README.md`
Cada módulo debe tener en su raíz un archivo `README.md` actualizado que incluya:
- Nombre y propósito general del módulo.
- Requisitos previos específicos del módulo.
- Estructura de carpetas a alto nivel.

### Directorio `Docs/`
Cualquier documentación técnica más extensa debe colocarse en la carpeta `Docs/`.
- Cada archivo debe enfocarse en un solo tema (p. ej. `database_schema.md`, `events.md`).
- Utilizar formato Markdown (GFM).
- Mantener los documentos actualizados y en sincronía con el código.

---

## 3. Tipos de Documentación y Cuándo Usarlos

| Tipo                 | Ubicación                  | Propósito                                                                 |
|:---------------------|:---------------------------|:--------------------------------------------------------------------------|
| **Código**           | Archivos `.php`, `.js`     | DocBlocks para firmas de métodos y comentarios para lógica de negocio compleja. |
| **Arquitectura**     | `Docs/architecture.md`     | Explicar cómo interactúan los componentes dentro del módulo Development.          |
| **Guías de Uso**     | `Docs/setup.md`            | Instrucciones para que un nuevo desarrollador despliegue o pruebe el módulo.|
| **Changelog**        | `Changelogs/`              | Registro histórico de qué cambió (ver CHANGELOG_PROTOCOL.md).             |

---

## 4. Reglas Generales

1. **La documentación es código:** Cualquier Pull Request o cambio significativo debe incluir su respectiva actualización en la documentación.
2. **Claridad sobre verbosidad:** Es preferible una documentación breve y precisa, que un texto largo y repetitivo.
3. **Idioma:** Toda la documentación (incluyendo DocBlocks y comentarios) debe estar escrita de manera clara y profesional en el idioma acordado para el proyecto (Español, u otro idioma, manteniendo consistencia).
4. **Markdown:** Para archivos de texto, usar siempre Markdown, aprovechando bloques de código, tablas y formato enriquecido.

---

*Este documento es la referencia para la IA y los desarrolladores sobre cómo mantener los estándares de documentación dentro del módulo Development.*
