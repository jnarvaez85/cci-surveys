# CCI Surveys - Instalación en XAMPP

## Descripción
CCI Surveys es un sistema de gestión de encuestas diseñado para funcionar fácilmente en XAMPP para desarrollo local.

## Requisitos
- XAMPP instalado en Windows
- PHP 8.0 o superior
- MySQL 5.7 o superior
- Navegador web moderno

## Instalación Rápida

### 1. Verificar XAMPP
Asegúrate de que XAMPP esté instalado y funcionando:
- Apache ejecutándose en puerto 80
- MySQL ejecutándose en puerto 3306
- phpMyAdmin accesible en `http://localhost/phpmyadmin`

### 2. Ubicar Proyecto
El proyecto debe estar en: `C:\xampp\htdocs\cci-surveys`

### 3. Instalación Automática
1. Abrir navegador web
2. Ir a: `http://localhost/cci-surveys/install.php`
3. Esperar a que se complete la instalación
4. Verificar mensajes de éxito

### 4. Verificación
1. Ir a: `http://localhost/cci-surveys/verificar_instalacion.php`
2. Revisar que no haya errores
3. Eliminar archivos de instalación y verificación

### 5. Acceso al Sistema
- **URL:** `http://localhost/cci-surveys/`
- **Usuario:** admin
- **Contraseña:** admin123

## Características del Sistema

### Funcionalidades Principales
- ✅ Gestión de usuarios (administradores y encuestadores)
- ✅ Creación y gestión de poblaciones
- ✅ Diseño de encuestas con preguntas múltiples
- ✅ Captura de respuestas de encuestados
- ✅ Reportes y análisis de datos
- ✅ Interfaz responsive para móviles

### Datos de Muestra Incluidos
- Usuario administrador por defecto
- 2 encuestadores de prueba
- 5 poblaciones de ejemplo
- 1 encuesta completa sobre seguridad ciudadana
- Múltiples respuestas de muestra para pruebas

## Estructura del Proyecto

```
cci-surveys/
├── admin/              # Panel de administración
│   ├── dashboard.php
│   └── reports.php
├── api/                # APIs del sistema
│   ├── get-populations.php
│   ├── get-questions.php
│   └── get-response-details.php
├── auth/               # Sistema de autenticación
│   ├── login.php
│   └── logout.php
├── classes/            # Clases PHP del sistema
│   ├── Auth.php
│   └── Survey.php
├── config/             # Configuraciones
│   ├── config.php
│   └── database.php
├── database/           # Scripts de base de datos
│   ├── schema.sql
│   ├── sample_data.sql
│   └── additional_sample_data.sql
├── includes/           # Archivos incluidos
│   ├── header.php
│   ├── footer.php
│   ├── 403.php
│   ├── 404.php
│   └── 500.php
├── surveyor/           # Panel de encuestadores
│   ├── dashboard.php
│   └── survey.php
├── index.php          # Página principal
├── install.php        # Instalador (eliminar después)
├── verificar_instalacion.php  # Verificador (eliminar después)
└── .htaccess          # Configuración de seguridad
```

## Configuración para XAMPP

### Base de Datos
- **Host:** localhost
- **Puerto:** 3306
- **Usuario:** root (sin contraseña por defecto)
- **Base de datos:** surveys (se crea automáticamente)

### URLs del Sistema
- **Página principal:** `http://localhost/cci-surveys/`
- **Login:** `http://localhost/cci-surveys/auth/login.php`
- **Admin:** `http://localhost/cci-surveys/admin/dashboard.php`
- **Encuestador:** `http://localhost/cci-surveys/surveyor/dashboard.php`

## Solución de Problemas

### Apache no inicia
- Verificar que puerto 80 no esté ocupado
- Cerrar Skype, IIS u otros servicios web
- Cambiar puerto en XAMPP Control Panel

### MySQL no inicia
- Verificar que puerto 3306 no esté ocupado
- Reiniciar MySQL desde XAMPP Control Panel
- Verificar logs en XAMPP Control Panel

### Errores de permisos
- Ejecutar XAMPP Control Panel como administrador
- Verificar permisos de la carpeta del proyecto

### Extensiones PHP faltantes
- Verificar que PDO MySQL esté habilitado
- Editar `C:\xampp\php\php.ini`
- Descomentar `extension=pdo_mysql`
- Reiniciar Apache

## Seguridad

### Después de la Instalación
1. **Eliminar archivos de instalación:**
   - `install.php`
   - `verificar_instalacion.php`

2. **Cambiar contraseñas:**
   - Usuario administrador
   - Usuario root de MySQL (opcional)

3. **Configurar .htaccess:**
   - Ya creado automáticamente
   - Protege archivos sensibles

## Respaldos

### Base de Datos
```cmd
C:\xampp\mysql\bin\mysqldump -u root -p surveys > backup.sql
```

### Archivos del Proyecto
- Copiar carpeta completa: `C:\xampp\htdocs\cci-surveys`

## Soporte

Para problemas técnicos:
1. Revisar logs de error en XAMPP Control Panel
2. Verificar configuración de PHP
3. Consultar documentación completa en `INSTALACION.md`

## Licencia
Este proyecto está desarrollado para uso interno de CCI Surveys.

---

**CCI Surveys** - Sistema de Gestión de Encuestas  
Versión 1.0.0 - Compatible con XAMPP
