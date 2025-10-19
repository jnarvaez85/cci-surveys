<?php
/**
 * Script de limpieza final para CCI Surveys
 * Este script elimina los archivos de instalación y restaura la seguridad
 */

echo "<h1>Limpieza Final - CCI Surveys</h1>\n";
echo "<p>Eliminando archivos de instalación y restaurando seguridad...</p>\n";

// Lista de archivos a eliminar
$archivos_a_eliminar = [
    'install.php',
    'install_simple.php',
    'verificar_instalacion.php',
    'limpiar_instalacion.php'
];

$archivos_eliminados = 0;
$archivos_no_encontrados = 0;

echo "<h2>Eliminando archivos de instalación...</h2>\n";

foreach ($archivos_a_eliminar as $archivo) {
    if (file_exists(__DIR__ . '/' . $archivo)) {
        if (unlink(__DIR__ . '/' . $archivo)) {
            echo "<p>✅ Archivo '$archivo' eliminado</p>\n";
            $archivos_eliminados++;
        } else {
            echo "<p>❌ No se pudo eliminar el archivo '$archivo'</p>\n";
        }
    } else {
        echo "<p>ℹ️ Archivo '$archivo' no encontrado (ya eliminado)</p>\n";
        $archivos_no_encontrados++;
    }
}

// Restaurar configuración de seguridad en .htaccess
echo "<h2>Restaurando configuración de seguridad...</h2>\n";

// Restaurar desde el archivo de producción
if (file_exists(__DIR__ . '/.htaccess.production')) {
    if (copy(__DIR__ . '/.htaccess.production', __DIR__ . '/.htaccess')) {
        echo "<p>✅ Configuración de seguridad restaurada desde .htaccess.production</p>\n";
        unlink(__DIR__ . '/.htaccess.production');
        echo "<p>✅ Archivo .htaccess.production eliminado</p>\n";
    } else {
        echo "<p>❌ Error al restaurar configuración de seguridad</p>\n";
    }
} else {
    // Crear configuración de seguridad si no existe el archivo de producción
    $htaccess_content = "# CCI Surveys - Configuración de seguridad y rendimiento

# Proteger archivos de configuración
<Files \"config/*\">
    Require all denied
</Files>

# Proteger clases PHP
<Files \"classes/*\">
    Require all denied
</Files>

# Proteger archivos de base de datos
<Files \"database/*\">
    Require all denied
</Files>

# Proteger archivos de instalación
<Files \"install*.php\">
    Require all denied
</Files>

# Proteger archivos de documentación
<Files \"*.md\">
    Require all denied
</Files>

# Proteger archivos de configuración específicos
<FilesMatch \"\\.(sql|log|txt)$\">
    Require all denied
</FilesMatch>

# Configuración de errores
php_flag display_errors Off
php_flag log_errors On

# Configuración de sesiones seguras
php_value session.cookie_httponly 1
php_value session.use_only_cookies 1
php_value session.cookie_secure 0

# Configuración de seguridad adicional
php_value expose_php Off
php_value allow_url_fopen Off
php_value allow_url_include Off

# Configuración de subida de archivos
php_value upload_max_filesize 10M
php_value post_max_size 10M

# Configuración de memoria y tiempo
php_value memory_limit 256M
php_value max_execution_time 300

# Compresión GZIP
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>

# Cache de archivos estáticos
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css \"access plus 1 month\"
    ExpiresByType application/javascript \"access plus 1 month\"
    ExpiresByType image/png \"access plus 1 month\"
    ExpiresByType image/jpg \"access plus 1 month\"
    ExpiresByType image/jpeg \"access plus 1 month\"
    ExpiresByType image/gif \"access plus 1 month\"
    ExpiresByType image/svg+xml \"access plus 1 month\"
</IfModule>

# Headers de seguridad
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection \"1; mode=block\"
    Header always set Referrer-Policy \"strict-origin-when-cross-origin\"
    Header always set Content-Security-Policy \"default-src 'self'; script-src 'self' 'unsafe-inline' cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' cdn.jsdelivr.net; img-src 'self' data:; font-src 'self' cdn.jsdelivr.net;\"
</IfModule>

# Redirección de URLs amigables
RewriteEngine On

# Prevenir acceso directo a archivos PHP excepto archivos del sistema
RewriteCond %{REQUEST_FILENAME} -f
RewriteCond %{REQUEST_URI} !^/cci-surveys/index\\.php$
RewriteCond %{REQUEST_URI} !^/cci-surveys/auth/(login|logout)\\.php$
RewriteCond %{REQUEST_URI} !^/cci-surveys/admin/(dashboard|reports)\\.php$
RewriteCond %{REQUEST_URI} !^/cci-surveys/surveyor/(dashboard|survey)\\.php$
RewriteCond %{REQUEST_URI} !^/cci-surveys/api/.*\\.php$
RewriteCond %{REQUEST_URI} \\.php$
RewriteRule ^(.*)$ - [F,L]

# Manejo de errores personalizados
ErrorDocument 404 /cci-surveys/includes/404.php
ErrorDocument 403 /cci-surveys/includes/403.php
ErrorDocument 500 /cci-surveys/includes/500.php

# Prevenir listado de directorios
Options -Indexes

# Proteger archivos sensibles
<FilesMatch \"\\.(htaccess|htpasswd|ini|log|sh|inc|bak)$\">
    Require all denied
</FilesMatch>

# Bloquear acceso a archivos de respaldo
<FilesMatch \"~$\">
    Require all denied
</FilesMatch>";

if (file_put_contents(__DIR__ . '/.htaccess', $htaccess_content)) {
    echo "<p>✅ Configuración de seguridad restaurada en .htaccess</p>\n";
} else {
    echo "<p>❌ Error al restaurar configuración de seguridad</p>\n";
}

echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
echo "<h3 style='color: #155724; margin-top: 0;'>✅ Limpieza Completada</h3>";
echo "<p style='color: #155724; margin-bottom: 10px;'>La limpieza se ha completado exitosamente:</p>";
echo "<ul style='color: #155724;'>";
echo "<li>Archivos eliminados: $archivos_eliminados</li>";
echo "<li>Archivos no encontrados: $archivos_no_encontrados</li>";
echo "<li>Configuración de seguridad restaurada</li>";
echo "</ul>";
echo "</div>";

echo "<h3>Estado del sistema:</h3>";
echo "<ul>";
echo "<li>✅ Archivos de instalación eliminados</li>";
echo "<li>✅ Configuración de seguridad restaurada</li>";
echo "<li>✅ Sistema listo para producción</li>";
echo "</ul>";

echo "<h3>Acceso al sistema:</h3>";
echo "<p>Puede acceder al sistema en: <a href='auth/login.php'>auth/login.php</a></p>";
echo "<p><strong>Credenciales:</strong> admin / admin123</p>";

echo "<p style='margin-top: 30px; font-style: italic;'>Este script se auto-elimina después de completar la limpieza.</p>";
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    line-height: 1.6;
}
h1, h2, h3 {
    color: #333;
}
</style>
