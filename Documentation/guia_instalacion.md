# Guía de Instalación y Configuración

> Pasos detallados para instalar, configurar y verificar el módulo Development en tu entorno Higgs.

---

## 📋 Tabla de Contenidos

1. [Requisitos Previos](#requisitos-previos)
2. [Instalación Paso a Paso](#instalación-paso-a-paso)
3. [Configuración Inicial](#configuración-inicial)
4. [Verificación de Instalación](#verificación-de-instalación)
5. [Solución de Problemas](#solución-de-problemas)
6. [Desinstalación](#desinstalación)

---

## ✅ Requisitos Previos

### Software Requerido

```bash
# Verificar versión de PHP (mínimo 8.2)
php -v
# Output esperado: PHP 8.2.x o superior

# Verificar Composer
composer -v
# Output esperado: Composer version X.X.X

# Verificar Git
git --version
# Output esperado: git version X.X.X
```

### Acceso a la Aplicación Higgs

- Acceso a la carpeta `app/Modules/`
- Permisos de escritura en el servidor
- Acceso a la base de datos
- Usuario con permisos administrativos

### Dependencias del Proyecto

El módulo requiere que Higgs esté instalado y funcional:

```bash
cd tu-proyecto-higgs
composer install
php spark migrate
```

---

## 🚀 Instalación Paso a Paso

### Opción 1: Instalación vía Git Clone (Recomendado)

**Paso 1: Navegar a la carpeta de módulos**
```bash
cd tu-proyecto-higgs/app/Modules
pwd
# Output: /tu-ruta/tu-proyecto-higgs/app/Modules
```

**Paso 2: Clonar el repositorio**
```bash
git clone https://github.com/jalexiscv/Higgs-Development.git Development
cd Development
git log --oneline -n 3
# Verificar que el clone fue exitoso
```

**Paso 3: Verificar estructura**
```bash
ls -la
# Debe mostrar carpetas: Commands, Config, Controllers, Models, Views, Language, Documentation
```

**Paso 4: Volver a la raíz del proyecto**
```bash
cd ../../../
```

### Opción 2: Instalación Manual (Descarga ZIP)

**Paso 1: Descargar archivo ZIP**
- Ir a: https://github.com/jalexiscv/Higgs-Development
- Hacer clic en "Code" → "Download ZIP"
- Descargar archivo

**Paso 2: Extraer en la ubicación correcta**
```bash
unzip Higgs-Development-main.zip
mv Higgs-Development-main app/Modules/Development
```

**Paso 3: Verificar permisos**
```bash
chmod -R 755 app/Modules/Development
```

### Opción 3: Instalación vía Composer

```bash
composer require jalexiscv/higgs-development
```

---

## ⚙️ Configuración Inicial

### 1. Registrar el Módulo en la Aplicación

Editar `app/Config/Modules.php`:

```php
public $modules = [
    'Development' => [
        'namespace' => 'App\Modules\Development',
        'path'      => APPPATH . 'Modules/Development',
    ],
    // ... otros módulos
];
```

### 2. Crear Base de Datos (si es necesario)

Si el módulo requiere tablas en la base de datos:

```bash
# Ejecutar migraciones
php spark migrate --namespace App\\Modules\\Development

# Verificar migraciones aplicadas
php spark migrate:status
```

### 3. Configurar Permisos

En tu sistema de control de acceso (DISA), crear los permisos:

```php
// Agregar al archivo de permisos
'development-access' => [
    'description' => 'Acceso al módulo Development',
    'module'      => 'development',
]
```

O usar el helper del módulo:

```php
// En el controlador o CLI
generate_development_permissions();
```

### 4. Configurar el Idioma

El módulo incluye archivos de idioma en español (`Language/es/`).

Editar `app/Config/App.php`:

```php
public string $language = 'es';
public array $supportedLocales = ['es', 'en'];
```

---

## 🔍 Verificación de Instalación

### Verificación 1: Verificar Archivos

```bash
# Verificar que el módulo existe
test -d app/Modules/Development && echo "✓ Módulo instalado" || echo "✗ Módulo no encontrado"

# Verificar controladores
test -f app/Modules/Development/Controllers/Development.php && echo "✓ Controllers OK"

# Verificar vistas
test -d app/Modules/Development/Views && echo "✓ Views OK"
```

### Verificación 2: Verificar Sintaxis PHP

```bash
# Verificar controladores
php -l app/Modules/Development/Controllers/Development.php
php -l app/Modules/Development/Controllers/Generators.php
php -l app/Modules/Development/Controllers/Tools.php

# Verificar modelos
php -l app/Modules/Development/Models/Development_Modules.php

# Verificar helpers
php -l app/Modules/Development/Helpers/Development_helper.php
```

Resultado esperado para cada archivo:
```
No syntax errors detected in [archivo]
```

### Verificación 3: Verificar Rutas

```bash
# Listar rutas del módulo
php spark routes

# Buscar rutas que contengan 'development'
php spark routes | grep -i development
```

Rutas esperadas:
```
development                       App\Modules\Development\Controllers\Development::index
development/home                  App\Modules\Development\Controllers\Development::index
development/home/(:any)           App\Modules\Development\Controllers\Development::home/$1
development/(:any)/(:any)/(:any)  App\Modules\Development\Controllers\Router::route/$1/$2/$3
```

### Verificación 4: Verificar Acceso Web

En tu navegador:

```
http://tu-app.local/development/
```

Resultados posibles:
- ✅ **Redirige a `/development/home/`**: Módulo funcionando
- ❌ **Error 404**: Rutas no registradas correctamente
- ❌ **Error de acceso**: Permisos no configurados
- ❌ **Error 500**: Problema en controlador o ayudante

### Verificación 5: Ejecutar Pruebas

```bash
# Si hay pruebas unitarias
./vendor/bin/phpunit tests/Modules/Development

# Ejecutar todas las pruebas
./vendor/bin/phpunit

# Ver últimas 20 líneas de output
./vendor/bin/phpunit --stop-on-failure 2>&1 | tail -20
```

### Verificación 6: Verificar Comandos Spark

```bash
# Listar comandos del grupo 'Development'
php spark list | grep -i development

# Probar un comando
php spark development:generate-model --help
```

Output esperado:
```
Development Modules and Utilities

Available Commands:
  development:generate-controller  Generates the Controllers/_{Component}.php controller...
  development:generate-creator     Generates the Views/_{Component}/Creator/index.php view...
  development:generate-model       Generates the Models/_{Component}.php model file...
  ...
```

---

## 🧪 Verificación Completa del Sistema

Crear un script de verificación `verify_development.sh`:

```bash
#!/bin/bash

echo "=== VERIFICACIÓN DEL MÓDULO DEVELOPMENT ==="
echo ""

# Verificar módulo
echo "1. Verificando instalación del módulo..."
test -d app/Modules/Development && echo "   ✓ Módulo instalado" || { echo "   ✗ Módulo NO encontrado"; exit 1; }

# Verificar sintaxis
echo "2. Verificando sintaxis PHP..."
php -l app/Modules/Development/Controllers/Development.php > /dev/null 2>&1 && echo "   ✓ Sintaxis OK" || { echo "   ✗ Error de sintaxis"; exit 1; }

# Verificar rutas
echo "3. Verificando rutas..."
php spark routes | grep -q "development" && echo "   ✓ Rutas registradas" || { echo "   ✗ Rutas no encontradas"; exit 1; }

# Verificar comandos
echo "4. Verificando comandos Spark..."
php spark list | grep -q "development:generate" && echo "   ✓ Comandos disponibles" || { echo "   ✗ Comandos no encontrados"; exit 1; }

# Verificar acceso
echo "5. Verificando acceso web..."
curl -s http://localhost:8080/development/ > /dev/null && echo "   ✓ Acceso OK" || echo "   ⚠ No se pudo acceder (puede ser normal en desarrollo)"

echo ""
echo "=== VERIFICACIÓN COMPLETADA ==="
```

Ejecutar:
```bash
chmod +x verify_development.sh
./verify_development.sh
```

---

## ❌ Solución de Problemas

### Problema 1: Módulo No Encontrado (Error 404)

**Síntoma**: Error 404 al acceder a `/development/`

**Soluciones**:

a) Verificar que la carpeta existe:
```bash
ls -d app/Modules/Development
```

b) Registrar el módulo en `app/Config/Modules.php`:
```php
public $modules = [
    'Development' => [
        'namespace' => 'App\Modules\Development',
        'path'      => APPPATH . 'Modules/Development',
    ],
];
```

c) Limpiar caché:
```bash
php spark cache:clear
```

### Problema 2: Error de Sintaxis PHP

**Síntoma**: Error 500 o Parse error

**Solución**:
```bash
php -l app/Modules/Development/Controllers/Development.php
# Buscar la línea del error y corregir
```

### Problema 3: Permisos Insuficientes

**Síntoma**: "Acceso Denegado" al acceder al módulo

**Soluciones**:

a) Verificar permisos en base de datos:
```php
// En tu controlador
$authorized = service('platform')->getAuthorizedModule('development');
dd($authorized); // Ver resultado
```

b) Registrar permisos:
```bash
php spark development:generate-permissions
# O manualmente crear el permiso 'development-access'
```

### Problema 4: Comandos No Funcionan

**Síntoma**: `php spark development:generate-model` no funciona

**Soluciones**:

a) Verificar que el comando existe:
```bash
php spark list | grep -i development
```

b) Verificar sintaxis del comando:
```bash
php -l app/Modules/Development/Commands/GenerateModel.php
```

c) Limpiar caché de spark:
```bash
rm -rf app/Cache/*
php spark spark:cache:clear
```

### Problema 5: Vistas No Se Renderizan

**Síntoma**: Página en blanco o contenido vacío

**Soluciones**:

a) Verificar que las vistas existen:
```bash
ls -la app/Modules/Development/Views/
```

b) Verificar rutas en controlador:
```php
// En el controlador
echo $this->views;        // Verificar ruta
echo $this->viewer;       // Verificar vista
echo $this->component;    // Verificar componente
```

c) Verificar que Bootstrap está disponible:
```php
use Higgs\Frontend\Bootstrap\v5_3_3\Bootstrap as BS5;
// Si da error, revisar paquete Frontend
```

---

## 🗑️ Desinstalación

### Paso 1: Eliminar Carpeta del Módulo

```bash
rm -rf app/Modules/Development
```

### Paso 2: Desregistrar del Config

Editar `app/Config/Modules.php` y comentar o eliminar:

```php
// 'Development' => [
//     'namespace' => 'App\Modules\Development',
//     'path'      => APPPATH . 'Modules/Development',
// ],
```

### Paso 3: Revertir Migraciones (si aplica)

```bash
php spark migrate:rollback --namespace App\\Modules\\Development
```

### Paso 4: Limpiar Permisos

Eliminar el permiso `development-access` de la base de datos (en tu sistema DISA).

### Paso 5: Limpiar Caché

```bash
php spark cache:clear
php spark routes:cache:clear
```

---

## 📋 Checklist de Instalación

- [ ] Clonar o descargar repositorio en `app/Modules/Development`
- [ ] Registrar módulo en `app/Config/Modules.php`
- [ ] Verificar sintaxis: `php -l app/Modules/Development/Controllers/*.php`
- [ ] Ejecutar migraciones si es necesario
- [ ] Crear permisos en la base de datos
- [ ] Acceder a `http://tu-app.local/development/`
- [ ] Verificar que los comandos funcionen
- [ ] Probar generadores de código
- [ ] Revisar logs en `writable/logs/`
- [ ] Documentar cualquier ajuste realizado

---

## 🆘 Contacto para Soporte

Si encuentras problemas:

1. **Revisar documentación**: Consulta `README.md` y otros documentos
2. **Verificar logs**: `writable/logs/log-*.log`
3. **Probar con CLI**: Ejecuta comandos directamente
4. **Contactar al autor**: jalexiscv@gmail.com

---

## 📚 Documentos Relacionados

- [README.md](../README.md) - Descripción general
- [architecture.md](architecture.md) - Arquitectura técnica
- [comandos_cli.md](comandos_cli.md) - Referencia de comandos

---

**Última Actualización**: 2026-04-04
