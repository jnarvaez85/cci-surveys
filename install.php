<?php
/**
 * Script de instalación para CCI Surveys
 * Este script crea la base de datos y las tablas necesarias
 */

// Configuración de la base de datos para XAMPP
$db_config = [
    'host' => 'localhost',
    'port' => '3306',
    'username' => 'root',      // Usuario por defecto de XAMPP
    'password' => '',          // Contraseña vacía por defecto en XAMPP
    'database' => 'surveys'
];

echo "<h1>Instalación de CCI Surveys</h1>\n";
echo "<p>Iniciando proceso de instalación...</p>\n";

try {
    // Conectar a MySQL sin especificar base de datos
    $dsn = "mysql:host={$db_config['host']};port={$db_config['port']};charset=utf8mb4";
    $pdo = new PDO($dsn, $db_config['username'], $db_config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    echo "<p>✓ Conexión a MySQL establecida</p>\n";
    
    // Crear base de datos si no existe
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db_config['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p>✓ Base de datos '{$db_config['database']}' creada o verificada</p>\n";
    
    // Conectar a la base de datos específica
    $dsn = "mysql:host={$db_config['host']};port={$db_config['port']};dbname={$db_config['database']};charset=utf8mb4";
    $pdo = new PDO($dsn, $db_config['username'], $db_config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    // Leer y ejecutar el schema SQL
    $schema_sql = file_get_contents(__DIR__ . '/database/schema.sql');
    
    if ($schema_sql === false) {
        throw new Exception("No se pudo leer el archivo schema.sql");
    }
    
    // Dividir el SQL en statements individuales
    $statements = array_filter(
        array_map('trim', explode(';', $schema_sql)),
        function($stmt) {
            return !empty($stmt) && !preg_match('/^--/', $stmt);
        }
    );
    
    if (!empty($statements)) {
        $pdo->beginTransaction();
        
        foreach ($statements as $statement) {
            if (!empty(trim($statement))) {
                $pdo->exec($statement);
            }
        }
        
        $pdo->commit();
        echo "<p>✓ Todas las tablas creadas exitosamente</p>\n";
    } else {
        echo "<p>⚠️ No se encontraron statements SQL para ejecutar</p>\n";
    }
    
    // Insertar datos de muestra
    $sample_sql = file_get_contents(__DIR__ . '/database/sample_data.sql');
    
    if ($sample_sql !== false) {
        $statements = array_filter(
            array_map('trim', explode(';', $sample_sql)),
            function($stmt) {
                return !empty($stmt) && !preg_match('/^--/', $stmt);
            }
        );
        
        if (!empty($statements)) {
            $pdo->beginTransaction();
            
            foreach ($statements as $statement) {
                if (!empty(trim($statement))) {
                    $pdo->exec($statement);
                }
            }
            
            $pdo->commit();
            echo "<p>✓ Datos de muestra insertados</p>\n";
        } else {
            echo "<p>⚠️ No se encontraron datos de muestra para insertar</p>\n";
        }
    } else {
        echo "<p>⚠️ No se pudo leer el archivo sample_data.sql</p>\n";
    }
    
    // Crear usuario administrador por defecto
    try {
        $admin_password = password_hash('admin123', PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3
        ]);
        
        $stmt = $pdo->prepare("
            INSERT INTO users (username, email, password_hash, first_name, last_name, role, is_active) 
            VALUES ('admin', 'admin@cci-surveys.com', :password, 'Administrador', 'Sistema', 'admin', 1)
            ON DUPLICATE KEY UPDATE 
            password_hash = :password,
            is_active = 1
        ");
        $stmt->bindParam(':password', $admin_password);
        $stmt->execute();
        
        echo "<p>✓ Usuario administrador creado/actualizado</p>\n";
    } catch (Exception $e) {
        echo "<p>⚠️ Advertencia: No se pudo crear/actualizar el usuario administrador: " . htmlspecialchars($e->getMessage()) . "</p>\n";
    }
    
    // Crear archivo .htaccess para seguridad
    $htaccess_content = "# CCI Surveys - Configuración de seguridad\n\n";
    $htaccess_content .= "# Proteger archivos de configuración\n";
    $htaccess_content .= "<Files \"config/*\">\n";
    $htaccess_content .= "    Require all denied\n";
    $htaccess_content .= "</Files>\n\n";
    $htaccess_content .= "<Files \"classes/*\">\n";
    $htaccess_content .= "    Require all denied\n";
    $htaccess_content .= "</Files>\n\n";
    $htaccess_content .= "# Proteger archivos de base de datos\n";
    $htaccess_content .= "<Files \"database/*\">\n";
    $htaccess_content .= "    Require all denied\n";
    $htaccess_content .= "</Files>\n\n";
    $htaccess_content .= "# Proteger archivos de instalación\n";
    $htaccess_content .= "<Files \"install.php\">\n";
    $htaccess_content .= "    Require all denied\n";
    $htaccess_content .= "</Files>\n\n";
    $htaccess_content .= "# Configuración de errores\n";
    $htaccess_content .= "php_flag display_errors Off\n";
    $htaccess_content .= "php_flag log_errors On\n\n";
    $htaccess_content .= "# Configuración de sesiones\n";
    $htaccess_content .= "php_value session.cookie_httponly 1\n";
    $htaccess_content .= "php_value session.use_only_cookies 1\n\n";
    
    file_put_contents(__DIR__ . '/.htaccess', $htaccess_content);
    echo "<p>✓ Archivo .htaccess creado para seguridad</p>\n";
    
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h3 style='color: #155724; margin-top: 0;'>¡Instalación Completada!</h3>";
    echo "<p style='color: #155724; margin-bottom: 0;'>El sistema CCI Surveys ha sido instalado exitosamente.</p>";
    echo "</div>";
    
    echo "<h3>Credenciales por defecto:</h3>";
    echo "<ul>";
    echo "<li><strong>Usuario:</strong> admin</li>";
    echo "<li><strong>Contraseña:</strong> admin123</li>";
    echo "</ul>";
    
    echo "<h3>Próximos pasos:</h3>";
    echo "<ol>";
    echo "<li>Acceda al sistema en: <a href='auth/login.php'>auth/login.php</a></li>";
    echo "<li>Cambie la contraseña del administrador</li>";
    echo "<li>Configure las poblaciones según sus necesidades</li>";
    echo "<li>Crear usuarios encuestadores</li>";
    echo "<li>Eliminar este archivo de instalación por seguridad</li>";
    echo "</ol>";
    
    echo "<p><strong>Importante:</strong> Elimine este archivo install.php después de la instalación por motivos de seguridad.</p>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h3 style='color: #721c24; margin-top: 0;'>Error en la Instalación</h3>";
    echo "<p style='color: #721c24; margin-bottom: 0;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
    
    echo "<h3>Posibles soluciones:</h3>";
    echo "<ul>";
    echo "<li>Verifique que MySQL esté ejecutándose</li>";
    echo "<li>Confirme las credenciales de la base de datos</li>";
    echo "<li>Asegúrese de que el usuario tenga permisos para crear bases de datos</li>";
    echo "<li>Verifique que los archivos schema.sql y sample_data.sql existan</li>";
    echo "<li>Intente con el instalador simplificado: <a href='install_simple.php'>install_simple.php</a></li>";
    echo "</ul>";
}
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
code {
    background: #f4f4f4;
    padding: 2px 4px;
    border-radius: 3px;
}
</style>