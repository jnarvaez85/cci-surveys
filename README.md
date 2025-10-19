# CCI Surveys - Sistema de Encuestas

## Descripción General

CCI Surveys es un sistema completo de gestión de encuestas desarrollado en PHP 8+ con Bootstrap 5.3.2 y MySQL. El sistema permite crear, administrar y analizar encuestas de manera escalable, con un enfoque especial en la recolección de datos por poblaciones específicas.

## Características Principales

### 🔐 Sistema de Autenticación Seguro
- Autenticación basada en sesiones con tokens CSRF
- Roles de usuario (Administrador y Encuestador)
- Hash de contraseñas con Argon2ID
- Protección contra ataques comunes
- Gestión de sesiones con expiración automática

### 📊 Gestión de Encuestas
- Creación y administración de múltiples encuestas
- Formularios dinámicos con preguntas personalizables
- Asociación de poblaciones a encuestas específicas
- Sistema escalable para diferentes tipos de encuestas

### 👥 Gestión de Poblaciones
- Administración de poblaciones/barrios
- Asociación flexible entre encuestas y poblaciones
- Estadísticas por población

### 📈 Reportes y Análisis
- Dashboard administrativo con métricas en tiempo real
- Gráficos interactivos con Chart.js
- Filtros avanzados por múltiples criterios
- Exportación de datos (en desarrollo)
- Análisis estadístico detallado

### 📱 Interfaz Responsive
- Diseño moderno con Bootstrap 5.3.2
- Interfaz responsive para dispositivos móviles
- UX optimizada con componentes interactivos
- Notificaciones en tiempo real
- Modales y componentes dinámicos

## Estructura del Proyecto

```
cci-surveys/
├── admin/                    # Panel administrativo
│   ├── dashboard.php        # Dashboard principal
│   ├── reports.php          # Reportes y estadísticas
│   ├── surveys.php          # Gestión de encuestas
│   ├── populations.php      # Gestión de poblaciones
│   ├── users.php           # Gestión de usuarios
│   └── responses.php       # Visualización de respuestas
├── api/                     # Endpoints API
│   ├── get-populations.php
│   ├── get-questions.php
│   └── get-response-details.php
├── auth/                    # Sistema de autenticación
│   ├── login.php
│   └── logout.php
├── classes/                 # Clases PHP
│   ├── Auth.php            # Autenticación y autorización
│   └── Survey.php          # Lógica de encuestas
├── config/                  # Configuración
│   ├── config.php          # Configuración general
│   └── database.php        # Configuración de BD
├── database/                # Scripts de base de datos
│   ├── schema.sql          # Estructura de tablas
│   ├── sample_data.sql     # Datos de ejemplo
│   └── additional_sample_data.sql
├── includes/                # Componentes compartidos
│   ├── header.php          # Cabecera común
│   └── footer.php          # Pie de página común
├── surveyor/                # Panel de encuestadores
│   ├── dashboard.php       # Dashboard de encuestador
│   ├── survey.php          # Formulario de encuesta
│   └── my-responses.php    # Respuestas del encuestador
├── .htaccess               # Configuración de seguridad
├── index.php               # Punto de entrada
├── install.php             # Script de instalación
└── README.md               # Este archivo
```

## Requisitos del Sistema

### Servidor Web
- Apache 2.4+ o Nginx 1.18+
- PHP 8.0 o superior
- Extensiones PHP requeridas:
  - PDO MySQL
  - JSON
  - Session
  - Hash
  - OpenSSL

### Base de Datos
- MySQL 5.7+ o MariaDB 10.3+
- Usuario con permisos de CREATE, INSERT, UPDATE, DELETE, SELECT

### Cliente
- Navegador web moderno (Chrome, Firefox, Safari, Edge)
- JavaScript habilitado
- Conexión a internet para CDNs de Bootstrap y Chart.js

## Instalación

### 1. Clonar o Descargar el Proyecto

```bash
# Si tienes Git instalado
git clone [URL_DEL_REPOSITORIO] cci-surveys

# O descargar y extraer el archivo ZIP
```

### 2. Configurar el Servidor Web

#### Apache
```apache
<VirtualHost *:80>
    ServerName cci-surveys.local
    DocumentRoot /ruta/a/cci-surveys
    <Directory /ruta/a/cci-surveys>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### Nginx
```nginx
server {
    listen 80;
    server_name cci-surveys.local;
    root /ruta/a/cci-surveys;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 3. Configurar Base de Datos

1. Crear la base de datos MySQL:
```sql
CREATE DATABASE surveys CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Crear usuario para la aplicación:
```sql
CREATE USER 'cci_user'@'localhost' IDENTIFIED BY 'password_seguro';
GRANT ALL PRIVILEGES ON surveys.* TO 'cci_user'@'localhost';
FLUSH PRIVILEGES;
```

3. Actualizar configuración en `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'surveys');
define('DB_USER', 'cci_user');
define('DB_PASS', 'password_seguro');
```

### 4. Ejecutar Instalación

1. Acceder a `http://tu-servidor/cci-surveys/install.php`
2. Seguir las instrucciones del instalador
3. Eliminar el archivo `install.php` después de la instalación

### 5. Configurar Permisos

```bash
# Establecer permisos correctos
chmod 755 cci-surveys/
chmod 644 cci-surveys/.htaccess
chmod -R 644 cci-surveys/config/
chmod -R 644 cci-surveys/classes/
chmod -R 644 cci-surveys/includes/
```

## Configuración

### Variables de Entorno

Editar `config/config.php`:

```php
// Configuración general
define('APP_NAME', 'CCI Surveys');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/cci-surveys');

// Configuración de entorno
define('APP_ENV', 'production'); // 'development' o 'production'
```

### Configuración de Seguridad

El archivo `.htaccess` incluye configuraciones de seguridad:
- Protección de archivos sensibles
- Configuración de sesiones seguras
- Ocultación de errores en producción

## Uso del Sistema

### Acceso Inicial

**Credenciales por defecto:**
- Usuario: `admin`
- Contraseña: `admin123`

⚠️ **Importante:** Cambiar estas credenciales inmediatamente después de la instalación.

### Roles de Usuario

#### Administrador
- Gestión completa del sistema
- Creación y administración de encuestas
- Gestión de poblaciones
- Administración de usuarios
- Acceso a reportes y estadísticas
- Visualización de todas las respuestas

#### Encuestador
- Acceso al formulario de encuestas
- Visualización de sus propias respuestas
- Dashboard con estadísticas personales

### Flujo de Trabajo

1. **Configuración Inicial (Admin)**
   - Crear poblaciones/barrios
   - Crear usuarios encuestadores
   - Configurar encuestas con preguntas

2. **Recolección de Datos (Encuestador)**
   - Acceder al formulario de encuesta
   - Seleccionar encuesta y población
   - Completar datos del encuestado
   - Responder preguntas de la encuesta
   - Enviar formulario

3. **Análisis y Reportes (Admin)**
   - Revisar respuestas en tiempo real
   - Generar reportes con filtros
   - Analizar estadísticas por población
   - Exportar datos para análisis externo

## Estructura de Base de Datos

### Tablas Principales

- **users**: Usuarios del sistema
- **surveys**: Encuestas disponibles
- **populations**: Poblaciones/barrios
- **survey_populations**: Relación encuesta-población
- **survey_questions**: Preguntas de las encuestas
- **question_options**: Opciones de respuesta
- **survey_responses**: Respuestas principales
- **response_answers**: Respuestas específicas a preguntas
- **system_logs**: Logs del sistema
- **user_sessions**: Gestión de sesiones

## API Endpoints

### GET `/api/get-populations.php`
Obtiene poblaciones asociadas a una encuesta.

**Parámetros:**
- `survey_id`: ID de la encuesta

**Respuesta:**
```json
{
  "success": true,
  "populations": [
    {
      "id": 1,
      "name": "Olaya",
      "description": "Barrio Olaya"
    }
  ]
}
```

### GET `/api/get-questions.php`
Obtiene preguntas y opciones de una encuesta.

**Parámetros:**
- `survey_id`: ID de la encuesta

### GET `/api/get-response-details.php`
Obtiene detalles completos de una respuesta.

**Parámetros:**
- `id`: ID de la respuesta

## Seguridad

### Medidas Implementadas

1. **Autenticación**
   - Hash de contraseñas con Argon2ID
   - Tokens CSRF en formularios
   - Gestión segura de sesiones
   - Protección contra ataques de fuerza bruta

2. **Autorización**
   - Control de acceso basado en roles
   - Verificación de permisos en cada página
   - Protección de rutas administrativas

3. **Validación de Datos**
   - Sanitización de entradas
   - Validación en servidor y cliente
   - Prepared statements para consultas SQL

4. **Protección de Archivos**
   - Archivos de configuración protegidos
   - Scripts de base de datos inaccesibles
   - Configuración .htaccess para seguridad

## Mantenimiento

### Respaldos

```bash
# Respaldar base de datos
mysqldump -u usuario -p surveys > backup_$(date +%Y%m%d).sql

# Respaldar archivos
tar -czf backup_files_$(date +%Y%m%d).tar.gz /ruta/a/cci-surveys/
```

### Logs

Los logs del sistema se almacenan en:
- Logs de aplicación: `system_logs` table
- Logs de PHP: configurados en `php.ini`
- Logs de servidor web: ubicación por defecto del servidor

### Actualizaciones

1. Respaldar sistema actual
2. Descargar nueva versión
3. Actualizar archivos (excepto `config/`)
4. Ejecutar scripts de migración si existen
5. Verificar funcionamiento

## Solución de Problemas

### Problemas Comunes

#### Error de Conexión a Base de Datos
```
Error de conexión a la base de datos: SQLSTATE[HY000] [2002] Connection refused
```

**Solución:**
1. Verificar que MySQL esté ejecutándose
2. Confirmar credenciales en `config/database.php`
3. Verificar permisos del usuario de BD

#### Error de Permisos
```
Warning: session_start(): open(/tmp/sess_xxx, O_RDWR) failed: Permission denied
```

**Solución:**
```bash
# Ajustar permisos de directorio de sesiones
chmod 755 /tmp
# O configurar directorio personalizado en php.ini
```

#### Páginas en Blanco
1. Verificar logs de PHP
2. Comprobar errores de sintaxis
3. Verificar extensiones PHP requeridas

### Logs de Depuración

Para habilitar logs detallados, editar `config/config.php`:

```php
define('APP_ENV', 'development');
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## Contribución

### Desarrollo Local

1. Clonar repositorio
2. Configurar entorno de desarrollo
3. Instalar dependencias
4. Configurar base de datos de desarrollo
5. Ejecutar tests (si existen)

### Estándares de Código

- PSR-12 para PHP
- Indentación con 4 espacios
- Comentarios en español para documentación
- Nombres de variables en inglés
- Comentarios JSDoc para JavaScript

## Licencia

Este proyecto está bajo la licencia MIT. Ver archivo `LICENSE` para más detalles.

## Soporte

Para soporte técnico o reportar bugs:
- Email: soporte@cci-surveys.com
- Documentación: [URL_DOCUMENTACION]
- Issues: [URL_REPOSITORIO]/issues

## Changelog

### v1.0.0 (2024-01-XX)
- Lanzamiento inicial
- Sistema completo de encuestas
- Dashboard administrativo
- Reportes y estadísticas
- Autenticación segura
- Interfaz responsive

---

**CCI Surveys** - Sistema profesional de gestión de encuestas
