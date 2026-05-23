# Reorganización de documentación en directorio Documentation/

**Fecha:** 2026-05-22
**Módulo:** Development > Documentation

## Descripción

Movidos todos los archivos .md de la raíz del módulo al directorio Documentation/ con nombres en minúsculas y guiones bajos, siguiendo la convención existente. Los planes de mejora de Views/Generators/ también se movieron a Documentation/.

## Tipo de Cambio

- `Cambiado` — Reorganización de archivos de documentación

## Archivos Afectados

### MOVIDO `*_GENERATOR_GUIDE.md` → `Documentation/guia_generador_*.md` (11 archivos)
- CONTROLLER_GENERATOR_GUIDE.md → guia_generador_controller.md
- CREATOR_GENERATOR_GUIDE.md → guia_generador_creator.md
- DELETER_GENERATOR_GUIDE.md → guia_generador_deleter.md
- EDITOR_GENERATOR_GUIDE.md → guia_generador_editor.md
- HOME_GENERATOR_GUIDE.md → guia_generador_home.md
- LANG_GENERATOR_GUIDE.md → guia_generador_lang.md
- LIST_GENERATOR_GUIDE.md → guia_generador_list.md
- LISTER_GENERATOR_GUIDE.md → guia_generador_lister.md
- MIGRATION_GENERATOR_GUIDE.md → guia_generador_migration.md
- MODEL_GENERATOR_GUIDE.md → guia_generador_model.md
- VIEWER_GENERATOR_GUIDE.md → guia_generador_viewer.md

### MOVIDO `*_PROTOCOL.md` → `Documentation/protocolo_*.md` (3 archivos)
- CHANGELOG_PROTOCOL.md → protocolo_changelog.md
- DOCUMENTATION_PROTOCOL.md → protocolo_documentacion.md
- HTML_BOOTSTRAP_PROTOCOL.md → protocolo_html_bootstrap.md

### MOVIDO `Views/Generators/*/IMPROVEMENT_PLAN.md` → `Documentation/plan_mejora_*.md` (2 archivos)
- Views/Generators/IMPROVEMENT_PLAN.md → plan_mejora_generadores.md
- Views/Generators/Creator/IMPROVEMENT_PLAN.md → plan_mejora_creator.md

## Impacto

- Documentación consolidada en un solo directorio (24 archivos .md)
- Nombres consistentes con la convención existente en Documentation/
- README.md conservado en raíz del módulo (ubicación estándar)
