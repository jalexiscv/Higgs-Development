# Módulo Development - Higgs Framework

> **Módulo de desarrollo avanzado para el framework Higgs (CodeIgniter 4 fork)**

Este módulo proporciona un conjunto completo de herramientas y generadores de código para acelerar el desarrollo de aplicaciones con Higgs. Incluye generadores de CRUD, componentes UI interactivos, herramientas de desarrollo y un IDE integrado.

---

## 📋 Tabla de Contenidos

1. [Descripción General](#descripción-general)
2. [Características Principales](#características-principales)
3. [Requisitos](#requisitos)
4. [Instalación](#instalación)
5. [Estructura del Módulo](#estructura-del-módulo)
6. [Guía Rápida](#guía-rápida)
7. [Documentación Detallada](#documentación-detallada)
8. [Comandos CLI](#comandos-cli)
9. [Contribución](#contribución)

---

## 📖 Descripción General

El módulo **Development** es un complemento esencial del framework Higgs que proporciona:

- **Generadores de Código CRUD**: Crea automáticamente controladores, modelos, migraciones y vistas
- **Interfaz Web**: Panel interactivo para explorar y generar componentes
- **Componentes UI**: Ejemplos y demostraciones de componentes Bootstrap 5
- **Herramientas de Desarrollo**: Utilidades para gestión de módulos, idiomas y más
- **IDE Integrado**: Entorno de desarrollo con capacidades avanzadas
- **Integración con IA**: Soporte para funcionalidades basadas en inteligencia artificial

---

## ✨ Características Principales

### 1. **Generadores de Código**
- Generador de Modelos
- Generador de Controladores
- Generador de Migraciones de Base de Datos
- Generador de Vistas (Viewer, Creator, Editor, Deleter, Lister)
- Generador de Archivos de Idioma
- Generador de módulos completos

### 2. **Herramientas de Desarrollo**
- **Module Generator**: Crea módulos completos automáticamente
- **Poeditor Integration**: Integración con herramientas de traducción
- **Text to PHP**: Convierte archivos de texto a código PHP
- **Webpack Manager**: Gestión de bundling y assets

### 3. **Interfaz de Usuario**
- Demostración de componentes Bootstrap 5
- Showcase de botones, formularios, tarjetas y más
- Componentes interactivos y responsivos

### 4. **Administración**
- Gestión de módulos registrados
- Gestión de clientes y usuarios
- Sistema de permisos y control de acceso

---

## 📦 Requisitos

- **PHP**: ≥ 8.2
- **Framework**: Higgs (CodeIgniter 4 fork)
- **Composer**: Para gestión de dependencias
- **Navegador**: Moderno (Chrome, Firefox, Safari, Edge)

```bash
# Verificar versión de PHP
php -v

# Verificar instalación de Composer
composer -v
```

---

## 🚀 Instalación

### Opción 1: Instalación Manual

1. **Clonar el repositorio** en la carpeta de módulos:
```bash
cd app/Modules
git clone https://github.com/jalexiscv/Higgs-Development.git Development
```

2. **Verificar la estructura** de carpetas:
```bash
ls -la Development/
```

3. **Autorizar el módulo** en la configuración de la aplicación principal

### Opción 2: Instalación vía Composer

```bash
composer require jalexiscv/higgs-development
```

---

## 📂 Estructura del Módulo

```
Development/
├── README.md                        # Este archivo
├── DOCUMENTATION_PROTOCOL.md        # Protocolo de documentación
├── CHANGELOG_PROTOCOL.md            # Protocolo de changelog
├── HTML_BOOTSTRAP_PROTOCOL.md       # Protocolo de componentes Bootstrap
│
├── Commands/                        # Comandos CLI (php spark)
│   ├── GenerateController.php
│   ├── GenerateCreator.php
│   ├── GenerateDeleter.php
│   ├── GenerateEditor.php
│   ├── GenerateLang.php
│   ├── GenerateLister.php
│   ├── GenerateMigration.php
│   ├── GenerateModel.php
│   └── GenerateViewer.php
│
├── Config/
│   └── Routes.php                   # Definición de rutas del módulo
│
├── Controllers/                     # Controladores HTTP
│   ├── Development.php              # Controlador principal
│   ├── Generators.php               # Generadores de código
│   ├── Tools.php                    # Herramientas de desarrollo
│   ├── UI.php                       # Componentes UI
│   ├── Webpack.php                  # Gestión de webpack
│   ├── Ide.php                      # IDE integrado
│   ├── AI.php                       # Funcionalidades IA
│   ├── Api.php                      # Endpoints API
│   └── Router.php                   # Enrutador dinámico
│
├── Helpers/
│   └── Development_helper.php       # Funciones auxiliares del módulo
│
├── Models/
│   ├── Development_Modules.php
│   ├── Development_Clients_Modules.php
│   ├── Development_Users.php
│   └── Development_Users_Fields.php
│
├── Language/
│   └── es/                          # Archivos de idioma (Español)
│       ├── Development.php
│       ├── Development_Modules.php
│       ├── Development_Tools.php
│       └── Generators.php
│
├── Views/
│   ├── index.php                    # Vista principal
│   ├── Home/                        # Vistas de inicio
│   ├── Generators/                  # Vistas de generadores
│   ├── Tools/                       # Vistas de herramientas
│   ├── UI/                          # Vistas de componentes UI
│   ├── Ide/                         # Vistas del IDE
│   ├── Webpack/                     # Vistas de webpack
│   └── E404/                        # Vistas de error
│
├── Documentation/                   # Documentación detallada
│   ├── architecture.md
│   ├── guia_instalacion.md
│   ├── comandos_cli.md
│   ├── controladores.md
│   ├── modelos.md
│   ├── sistema_generadores.md
│   └── ejemplos_uso.md
│
└── .git/                            # Repositorio Git
```

---

## 🎯 Guía Rápida

### Acceder al Panel

1. Abre tu navegador y ve a:
```
http://tu-app.local/development/
```

2. Si tienes permisos, verás el panel principal con:
   - Generadores de Código
   - Herramientas de Desarrollo
   - Componentes UI
   - IDE Integrado

### Generar un Modelo

1. Navega a **Generadores → Modelo**
2. Selecciona una tabla de la base de datos
3. Configura las opciones deseadas
4. Haz clic en "Generar"
5. El archivo se creará en `Modules/{MóduloDestino}/Models/`

### Generar un Controlador

1. Navega a **Generadores → Controlador**
2. Selecciona una tabla
3. Define el nombre del controlador
4. El archivo se creará automáticamente

### Usar Comandos CLI

```bash
# Generar un modelo
php spark development:generate-model access_users

# Generar un controlador
php spark development:generate-controller access_users

# Generar una migración
php spark development:generate-migration create_users_table

# Generar un archivo de idioma
php spark development:generate-lang users
```

---

## 📚 Documentación Detallada

Para documentación más profunda, consulta:

| Documento | Contenido |
|-----------|-----------|
| **[Arquitectura](Documentation/architecture.md)** | Decisiones de arquitectura, patrones y diseño |
| **[Guía de Instalación](Documentation/guia_instalacion.md)** | Pasos detallados de instalación y configuración |
| **[Comandos CLI](Documentation/comandos_cli.md)** | Referencia completa de comandos Spark |
| **[Controladores](Documentation/controladores.md)** | Documentación de cada controlador |
| **[Modelos](Documentation/modelos.md)** | Esquemas y funcionalidad de modelos |
| **[Sistema de Generadores](Documentation/sistema_generadores.md)** | Cómo funcionan los generadores de código |
| **[Ejemplos de Uso](Documentation/ejemplos_uso.md)** | Ejemplos prácticos y casos de uso |

---

## ⚙️ Comandos CLI

El módulo proporciona varios comandos Spark para acelerar el desarrollo:

```bash
# Generar Modelo (Model)
php spark development:generate-model {tabla}

# Generar Controlador (Controller)
php spark development:generate-controller {tabla}

# Generar Migración (Migration)
php spark development:generate-migration {tabla}

# Generar Vista (Viewer)
php spark development:generate-viewer {tabla}

# Generar Creador (Creator - formulario de creación)
php spark development:generate-creator {tabla}

# Generar Editor (Editor - formulario de edición)
php spark development:generate-editor {tabla}

# Generar Eliminador (Deleter - formulario de eliminación)
php spark development:generate-deleter {tabla}

# Generar Listador (Lister - vista de lista)
php spark development:generate-lister {tabla}

# Generar Archivo de Idioma
php spark development:generate-lang {componente}
```

**Ejemplo completo:**
```bash
php spark development:generate-model employees
php spark development:generate-controller employees
php spark development:generate-migration create_employees_table
```

---

## 🔒 Control de Acceso

El módulo requiere permisos específicos:

- `development-access`: Acceso general al módulo

Estos permisos se definen en el sistema de autorización de la aplicación (típicamente en DISA).

---

## 🧪 Verificación de Instalación

Para verificar que el módulo está correctamente instalado:

```bash
# Verificar sintaxis PHP
php -l app/Modules/Development/Controllers/Development.php

# Ejecutar pruebas unitarias
./vendor/bin/phpunit --stop-on-failure 2>&1 | tail -20

# Acceder a la ruta
curl -i http://tu-app.local/development/
```

---

## 📝 Convenciones y Estándares

Este módulo sigue estándares específicos documentados en:

- **DOCUMENTATION_PROTOCOL.md**: Cómo escribir documentación
- **HTML_BOOTSTRAP_PROTOCOL.md**: Cómo construir interfaces HTML
- **CHANGELOG_PROTOCOL.md**: Cómo registrar cambios

---

## 🤝 Contribución

Para contribuir al desarrollo de este módulo:

1. Fork del repositorio
2. Crea una rama con tu feature: `git checkout -b feature/nueva-feature`
3. Commit tus cambios: `git commit -m "Agregar nueva feature"`
4. Push a la rama: `git push origin feature/nueva-feature`
5. Abre un Pull Request

---

## 📄 Licencia

Este código es parte de CloudEngine S.A.S., Inc. Consulta la licencia distribuida con el código fuente para obtener los términos completos.

---

## 📞 Contacto y Soporte

- **Autor**: Jose Alexis Correa Valencia
- **Email**: jalexiscv@gmail.com
- **Sitio Web**: https://www.codehiggs.com
- **Versión**: 2.0.0
- **Última Actualización**: 2026-04-04

---

## 🔄 Historial de Cambios

Ver [CHANGELOG_PROTOCOL.md](CHANGELOG_PROTOCOL.md) para el registro de cambios y versiones.

---

**Higgs Development Module** · Desarrollado con ❤️ para el framework Higgs
