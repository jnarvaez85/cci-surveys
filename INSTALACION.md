# Guía de Instalación - CCI Surveys

## Requisitos Previos

### Software Necesario
- **PHP 8.0 o superior** con las siguientes extensiones:
  - PDO MySQL
  - JSON
  - Session
  - Hash
  - OpenSSL
- **MySQL 5.7+** o **MariaDB 10.3+**
- **Apache 2.4+** o **Nginx 1.18+**
- **XAMPP** (para instalación local en Windows)
- **Docker** (opcional, para base de datos)

### Hardware Mínimo
- 1 GB RAM
- 1 GB espacio en disco
- Procesador x64

## Instalación en XAMPP (Windows) - RECOMENDADO PARA DESARROLLO LOCAL

### 1. Descargar e Instalar XAMPP

1. **Descargar XAMPP:**
   - Ir a [https://www.apachefriends.org/download.html](https://www.apachefriends.org/download.html)
   - Descargar la versión más reciente de XAMPP para Windows
   - Seleccionar la versión con PHP 8.0 o superior

2. **Instalar XAMPP:**
   - Ejecutar el instalador como administrador
   - Seleccionar los componentes: Apache, MySQL, PHP, phpMyAdmin
   - Instalar en la ubicación por defecto: `C:\xampp`
   - Marcar "Start Control Panel" al finalizar la instalación

3. **Iniciar Servicios:**
   - Abrir XAMPP Control Panel
   - Hacer clic en "Start" junto a Apache
   - Hacer clic en "Start" junto a MySQL
   - Verificar que ambos servicios estén ejecutándose (color verde)

### 2. Configurar el Proyecto en XAMPP

1. **Ubicar el proyecto:**
   - El proyecto ya debe estar en: `C:\xampp\htdocs\cci-surveys`
   - Si no está ahí, copiar toda la carpeta del proyecto a `C:\xampp\htdocs\`

2. **Verificar estructura:**
   ```
   C:\xampp\htdocs\cci-surveys\
   ├── admin\
   ├── api\
   ├── auth\
   ├── classes\
   ├── config\
   ├── database\
   ├── includes\
   ├── surveyor\
   ├── index.php
   ├── install.php
   └── INSTALACION.md
   ```

### 3. Configurar Base de Datos en XAMPP

1. **Acceder a phpMyAdmin:**
   - Abrir navegador web
   - Ir a: `http://localhost/phpmyadmin`
   - Usuario: `root`
   - Contraseña: (dejar vacío por defecto)

2. **Crear Base de Datos (Automático):**
   - El script de instalación creará automáticamente la base de datos
   - No es necesario crearla manualmente

3. **Configurar Usuario (Opcional):**
   ```sql
   -- Crear usuario específico (opcional)
   CREATE USER 'cci_user'@'localhost' IDENTIFIED BY 'cci_password';
   GRANT ALL PRIVILEGES ON surveys.* TO 'cci_user'@'localhost';
   FLUSH PRIVILEGES;
   ```

### 4. Ejecutar Instalación

1. **Acceder al Instalador:**
   - Abrir navegador web
   - Ir a: `http://localhost/cci-surveys/install.php`
   - El instalador se ejecutará automáticamente

2. **Verificar Instalación:**
   - El instalador creará todas las tablas necesarias
   - Insertará datos de muestra
   - Creará el usuario administrador por defecto

3. **Credenciales por Defecto:**
   - **Usuario:** admin
   - **Contraseña:** admin123

### 5. Acceder al Sistema

1. **URL del Sistema:**
   - Ir a: `http://localhost/cci-surveys/`

2. **Iniciar Sesión:**
   - Usuario: admin
   - Contraseña: admin123
   - Cambiar contraseña después del primer login

### 6. Configuración Adicional para XAMPP

#### Configurar Virtual Host (Opcional)

1. **Editar archivo hosts:**
   - Abrir `C:\Windows\System32\drivers\etc\hosts` como administrador
   - Agregar línea: `127.0.0.1 cci-surveys.local`

2. **Crear archivo de configuración Apache:**
   - Crear archivo: `C:\xampp\apache\conf\extra\cci-surveys.conf`
   ```apache
   <VirtualHost *:80>
       ServerName cci-surveys.local
       DocumentRoot C:/xampp/htdocs/cci-surveys
       <Directory "C:/xampp/htdocs/cci-surveys">
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

3. **Incluir configuración en httpd.conf:**
   - Editar `C:\xampp\apache\conf\httpd.conf`
   - Agregar línea: `Include conf/extra/cci-surveys.conf`
   - Reiniciar Apache desde XAMPP Control Panel

4. **Acceder con dominio personalizado:**
   - URL: `http://cci-surveys.local`

### 7. Solución de Problemas Comunes en XAMPP

#### Error: "Apache no inicia"
- Verificar que el puerto 80 no esté ocupado
- Cambiar puerto en `C:\xampp\apache\conf\httpd.conf`: `Listen 8080`
- Reiniciar Apache

#### Error: "MySQL no inicia"
- Verificar que el puerto 3306 no esté ocupado
- Cambiar puerto en `C:\xampp\mysql\bin\my.ini`: `port=3307`
- Reiniciar MySQL

#### Error: "Permission denied" en archivos
- Verificar permisos de la carpeta del proyecto
- Ejecutar XAMPP Control Panel como administrador

#### Error: "PDO extension not loaded"
- Verificar que la extensión PDO esté habilitada en PHP
- Editar `C:\xampp\php\php.ini`
- Descomentar: `extension=pdo_mysql`
- Reiniciar Apache

### 8. Configuración de Seguridad para XAMPP

1. **Proteger phpMyAdmin:**
   - Cambiar contraseña de root de MySQL
   - Crear usuario específico para la aplicación

2. **Configurar .htaccess:**
   - El instalador crea automáticamente archivo .htaccess
   - Protege archivos de configuración y base de datos

3. **Configurar PHP para producción:**
   - Editar `C:\xampp\php\php.ini`
   ```ini
   display_errors = Off
   log_errors = On
   expose_php = Off
   ```

### 9. Respaldos en XAMPP

1. **Respaldar Base de Datos:**
   - Usar phpMyAdmin: Export → Custom → Seleccionar base de datos
   - O usar línea de comandos:
   ```cmd
   C:\xampp\mysql\bin\mysqldump -u root -p surveys > backup.sql
   ```

2. **Respaldar Archivos:**
   - Copiar carpeta completa: `C:\xampp\htdocs\cci-surveys`

### 10. Actualización en XAMPP

1. **Respaldar sistema actual**
2. **Descargar nueva versión**
3. **Reemplazar archivos** (excepto config/)
4. **Ejecutar scripts de migración** si existen
5. **Verificar funcionamiento**

---

## Instalación en Servidores de Producción

## Instalación Paso a Paso

### Opción 1: Instalación con Docker (Recomendada)

#### 1. Verificar Docker
```bash
docker --version
docker-compose --version
```

#### 2. Configurar Base de Datos Docker
Crear archivo `docker-compose.yml`:
```yaml
version: '3.8'
services:
  mysql:
    image: mysql:8.0
    container_name: cci-surveys-db
    environment:
      MYSQL_ROOT_PASSWORD: test123
      MYSQL_DATABASE: surveys
      MYSQL_USER: cci_user
      MYSQL_PASSWORD: cci_password
    ports:
      - "3306:3306"
    volumes:
      - mysql_data:/var/lib/mysql
      - ./database:/docker-entrypoint-initdb.d
    command: --default-authentication-plugin=mysql_native_password

volumes:
  mysql_data:
```

#### 3. Iniciar Base de Datos
```bash
docker-compose up -d
```

#### 4. Verificar Conexión
```bash
docker exec -it cci-surveys-db mysql -u root -p
# Contraseña: test123
```

### Opción 2: Instalación Manual

#### 1. Instalar MySQL
**Ubuntu/Debian:**
```bash
sudo apt update
sudo apt install mysql-server
sudo mysql_secure_installation
```

**CentOS/RHEL:**
```bash
sudo yum install mysql-server
sudo systemctl start mysqld
sudo mysql_secure_installation
```

**Windows:**
- Descargar MySQL Installer desde [mysql.com](https://dev.mysql.com/downloads/installer/)
- Instalar MySQL Server y MySQL Workbench

#### 2. Configurar Base de Datos
```sql
-- Conectar como root
mysql -u root -p

-- Crear base de datos
CREATE DATABASE surveys CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Crear usuario
CREATE USER 'cci_user'@'localhost' IDENTIFIED BY 'cci_password';
GRANT ALL PRIVILEGES ON surveys.* TO 'cci_user'@'localhost';
FLUSH PRIVILEGES;
```

## Configuración del Servidor Web

### Apache

#### 1. Habilitar Módulos
```bash
sudo a2enmod rewrite
sudo a2enmod php8.1
sudo systemctl restart apache2
```

#### 2. Configurar Virtual Host
Crear archivo `/etc/apache2/sites-available/cci-surveys.conf`:
```apache
<VirtualHost *:80>
    ServerName cci-surveys.local
    DocumentRoot /var/www/html/cci-surveys
    <Directory /var/www/html/cci-surveys>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/cci-surveys_error.log
    CustomLog ${APACHE_LOG_DIR}/cci-surveys_access.log combined
</VirtualHost>
```

#### 3. Activar Sitio
```bash
sudo a2ensite cci-surveys.conf
sudo systemctl reload apache2
```

### Nginx

#### 1. Configurar Sitio
Crear archivo `/etc/nginx/sites-available/cci-surveys`:
```nginx
server {
    listen 80;
    server_name cci-surveys.local;
    root /var/www/html/cci-surveys;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

#### 2. Activar Sitio
```bash
sudo ln -s /etc/nginx/sites-available/cci-surveys /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## Instalación de la Aplicación

### 1. Descargar Proyecto
```bash
# Clonar repositorio
git clone [URL_DEL_REPOSITORIO] /var/www/html/cci-surveys

# O descargar y extraer ZIP
cd /var/www/html
wget [URL_DEL_ARCHIVO_ZIP]
unzip cci-surveys.zip
```

### 2. Configurar Permisos
```bash
sudo chown -R www-data:www-data /var/www/html/cci-surveys
sudo chmod -R 755 /var/www/html/cci-surveys
sudo chmod 644 /var/www/html/cci-surveys/.htaccess
```

### 3. Configurar Base de Datos
Editar `config/database.php`:
```php
<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');  // O IP del contenedor Docker
define('DB_PORT', '3306');
define('DB_NAME', 'surveys');
define('DB_USER', 'cci_user');     // O 'root' para Docker
define('DB_PASS', 'cci_password'); // O 'test123' para Docker
define('APP_ENV', 'production');
```

### 4. Ejecutar Instalador
1. Abrir navegador
2. Ir a `http://cci-surveys.local/install.php`
3. Seguir instrucciones del instalador
4. Verificar que todas las tablas se crearon correctamente

### 5. Verificar Instalación
```bash
# Verificar tablas creadas
mysql -u cci_user -p surveys -e "SHOW TABLES;"

# Verificar datos de muestra
mysql -u cci_user -p surveys -e "SELECT COUNT(*) FROM users;"
mysql -u cci_user -p surveys -e "SELECT COUNT(*) FROM surveys;"
```

### 6. Configurar DNS Local (Opcional)
Editar `/etc/hosts`:
```
127.0.0.1 cci-surveys.local
```

## Configuración de Seguridad

### 1. Configurar SSL (Recomendado)
```bash
# Instalar Certbot
sudo apt install certbot python3-certbot-apache

# Generar certificado
sudo certbot --apache -d cci-surveys.local
```

### 2. Configurar Firewall
```bash
# UFW (Ubuntu)
sudo ufw allow 22
sudo ufw allow 80
sudo ufw allow 443
sudo ufw enable

# Firewalld (CentOS)
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --permanent --add-service=https
sudo firewall-cmd --permanent --add-service=ssh
sudo firewall-cmd --reload
```

### 3. Optimizar PHP
Editar `/etc/php/8.1/apache2/php.ini`:
```ini
# Seguridad
expose_php = Off
allow_url_fopen = Off
allow_url_include = Off

# Rendimiento
memory_limit = 256M
max_execution_time = 300
upload_max_filesize = 10M
post_max_size = 10M

# Sesiones
session.cookie_httponly = 1
session.use_only_cookies = 1
session.cookie_secure = 1  # Solo si usas HTTPS
```

## Verificación Post-Instalación

### 1. Acceso al Sistema
- URL: `http://cci-surveys.local` (o `https://` si configuraste SSL)
- Usuario: `admin`
- Contraseña: `admin123`

### 2. Verificar Funcionalidades
- [ ] Login funciona correctamente
- [ ] Dashboard carga sin errores
- [ ] Formulario de encuesta funciona
- [ ] Reportes muestran datos
- [ ] Responsive design funciona en móvil

### 3. Verificar Logs
```bash
# Logs de Apache
tail -f /var/log/apache2/cci-surveys_error.log

# Logs de MySQL
tail -f /var/log/mysql/error.log

# Logs de PHP
tail -f /var/log/php8.1-fpm.log
```

## Mantenimiento

### Respaldos Automáticos
Crear script `/usr/local/bin/backup-cci-surveys.sh`:
```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/cci-surveys"

# Crear directorio de respaldos
mkdir -p $BACKUP_DIR

# Respaldar base de datos
mysqldump -u cci_user -pcci_password surveys > $BACKUP_DIR/db_backup_$DATE.sql

# Respaldar archivos
tar -czf $BACKUP_DIR/files_backup_$DATE.tar.gz /var/www/html/cci-surveys/

# Mantener solo los últimos 7 días
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete

echo "Backup completed: $DATE"
```

Configurar cron para respaldos diarios:
```bash
# Editar crontab
crontab -e

# Agregar línea para respaldo diario a las 2 AM
0 2 * * * /usr/local/bin/backup-cci-surveys.sh
```

### Monitoreo
```bash
# Verificar espacio en disco
df -h

# Verificar uso de memoria
free -h

# Verificar procesos MySQL
ps aux | grep mysql

# Verificar logs de errores
grep -i error /var/log/apache2/cci-surveys_error.log
```

## Solución de Problemas

### Error: "Connection refused" en MySQL
```bash
# Verificar estado del servicio
sudo systemctl status mysql

# Reiniciar servicio
sudo systemctl restart mysql

# Verificar puerto
netstat -tlnp | grep 3306
```

### Error: "Permission denied" en archivos
```bash
# Corregir permisos
sudo chown -R www-data:www-data /var/www/html/cci-surveys
sudo chmod -R 755 /var/www/html/cci-surveys
```

### Error: "Module rewrite not enabled" en Apache
```bash
# Habilitar módulo
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Error: "PHP Fatal error" en logs
1. Verificar versión de PHP: `php -v`
2. Verificar extensiones: `php -m | grep -E "(pdo|mysql|json)"`
3. Verificar configuración en `php.ini`

## Actualización

### 1. Respaldar Sistema Actual
```bash
# Respaldar base de datos
mysqldump -u cci_user -p surveys > backup_pre_update.sql

# Respaldar archivos
tar -czf backup_files_pre_update.tar.gz /var/www/html/cci-surveys/
```

### 2. Actualizar Archivos
```bash
# Descargar nueva versión
cd /tmp
wget [URL_NUEVA_VERSION]

# Extraer archivos (excepto config/)
cd /var/www/html/cci-surveys
tar --exclude='config' -xzf /tmp/nueva_version.tar.gz
```

### 3. Actualizar Base de Datos
```bash
# Ejecutar scripts de migración si existen
mysql -u cci_user -p surveys < migration_scripts.sql
```

### 4. Verificar Actualización
- Probar login
- Verificar funcionalidades principales
- Revisar logs de errores

## Soporte

Para problemas de instalación:
1. Revisar logs de error
2. Verificar requisitos del sistema
3. Consultar documentación
4. Contactar soporte técnico

**Contacto:**
- Email: soporte@cci-surveys.com
- Documentación: [URL_DOCUMENTACION]
- Issues: [URL_REPOSITORIO]/issues

---

**CCI Surveys** - Guía de Instalación v1.0
