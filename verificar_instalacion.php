<?php
/**
 * Script de verificación para CCI Surveys
 * Este script verifica que todos los componentes estén funcionando correctamente
 */

echo "<h1>Verificación de Instalación - CCI Surveys</h1>\n";
echo "<p>Verificando componentes del sistema...</p>\n";

$errores = [];
$advertencias = [];

// Verificar PHP
echo "<h2>1. Verificación de PHP</h2>\n";
echo "<p>Versión de PHP: " . phpversion() . "</p>\n";

if (version_compare(PHP_VERSION, '8.0.0', '<')) {
    $errores[] = "PHP 8.0 o superior es requerido. Versión actual: " . PHP_VERSION;
} else {
    echo "<p>✅ Versión de PHP compatible</p>\n";
}

// Verificar extensiones PHP
$extensiones_requeridas = ['pdo', 'pdo_mysql', 'json', 'session', 'hash', 'openssl'];
echo "<h3>Extensiones PHP requeridas:</h3>\n";
foreach ($extensiones_requeridas as $ext) {
    if (extension_loaded($ext)) {
        echo "<p>✅ $ext - Disponible</p>\n";
    } else {
        $errores[] = "Extensión PHP requerida no encontrada: $ext";
        echo "<p>❌ $ext - No disponible</p>\n";
    }
}

// Verificar archivos del proyecto
echo "<h2>2. Verificación de Archivos del Proyecto</h2>\n";
$archivos_requeridos = [
    'config/config.php',
    'config/database.php',
    'database/schema.sql',
    'database/sample_data.sql',
    'classes/Auth.php',
    'classes/Survey.php',
    'auth/login.php',
    'admin/dashboard.php',
    'surveyor/dashboard.php'
];

foreach ($archivos_requeridos as $archivo) {
    if (file_exists(__DIR__ . '/' . $archivo)) {
        echo "<p>✅ $archivo - Encontrado</p>\n";
    } else {
        $errores[] = "Archivo requerido no encontrado: $archivo";
        echo "<p>❌ $archivo - No encontrado</p>\n";
    }
}

// Verificar conexión a base de datos
echo "<h2>3. Verificación de Base de Datos</h2>\n";
try {
    require_once __DIR__ . '/config/database.php';
    $db = Database::getInstance();
    $connection = $db->getConnection();
    echo "<p>✅ Conexión a base de datos exitosa</p>\n";
    
    // Verificar que la base de datos existe
    $stmt = $connection->query("SELECT DATABASE() as db_name");
    $result = $stmt->fetch();
    if ($result && $result['db_name'] === 'surveys') {
        echo "<p>✅ Base de datos 'surveys' seleccionada</p>\n";
    } else {
        $errores[] = "Base de datos 'surveys' no está seleccionada";
        echo "<p>❌ Base de datos 'surveys' no encontrada</p>\n";
    }
    
    // Verificar tablas
    $stmt = $connection->query("SHOW TABLES");
    $tablas = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $tablas_requeridas = ['users', 'populations', 'surveys', 'survey_responses'];
    
    echo "<h3>Tablas en la base de datos:</h3>\n";
    foreach ($tablas_requeridas as $tabla) {
        if (in_array($tabla, $tablas)) {
            echo "<p>✅ Tabla '$tabla' existe</p>\n";
        } else {
            $errores[] = "Tabla requerida no encontrada: $tabla";
            echo "<p>❌ Tabla '$tabla' no existe</p>\n";
        }
    }
    
    // Verificar datos de muestra
    $stmt = $connection->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    if ($result['count'] > 0) {
        echo "<p>✅ Usuarios encontrados en la base de datos</p>\n";
    } else {
        $advertencias[] = "No se encontraron usuarios en la base de datos";
        echo "<p>⚠️ No se encontraron usuarios</p>\n";
    }
    
    $stmt = $connection->query("SELECT COUNT(*) as count FROM surveys");
    $result = $stmt->fetch();
    if ($result['count'] > 0) {
        echo "<p>✅ Encuestas encontradas en la base de datos</p>\n";
    } else {
        $advertencias[] = "No se encontraron encuestas en la base de datos";
        echo "<p>⚠️ No se encontraron encuestas</p>\n";
    }
    
} catch (Exception $e) {
    $errores[] = "Error de conexión a base de datos: " . $e->getMessage();
    echo "<p>❌ Error de conexión a base de datos: " . htmlspecialchars($e->getMessage()) . "</p>\n";
}

// Resumen final
echo "<h2>Resumen de Verificación</h2>\n";

if (empty($errores)) {
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h3 style='color: #155724; margin-top: 0;'>✅ Verificación Exitosa</h3>";
    echo "<p style='color: #155724; margin-bottom: 0;'>El sistema está correctamente instalado y configurado.</p>";
    echo "</div>";
    
    echo "<h3>Próximos pasos:</h3>";
    echo "<ol>";
    echo "<li>Acceder al sistema en: <a href='auth/login.php'>auth/login.php</a></li>";
    echo "<li>Iniciar sesión con usuario: admin / contraseña: admin123</li>";
    echo "<li>Cambiar la contraseña del administrador</li>";
    echo "<li>Configurar el sistema según sus necesidades</li>";
    echo "</ol>";
} else {
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h3 style='color: #721c24; margin-top: 0;'>❌ Errores Encontrados</h3>";
    echo "<p style='color: #721c24; margin-bottom: 10px;'>Se encontraron los siguientes errores:</p>";
    echo "<ul style='color: #721c24;'>";
    foreach ($errores as $error) {
        echo "<li>" . htmlspecialchars($error) . "</li>";
    }
    echo "</ul>";
    echo "</div>";
}

if (!empty($advertencias)) {
    echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h3 style='color: #856404; margin-top: 0;'>⚠️ Advertencias</h3>";
    echo "<ul style='color: #856404;'>";
    foreach ($advertencias as $advertencia) {
        echo "<li>" . htmlspecialchars($advertencia) . "</li>";
    }
    echo "</ul>";
    echo "</div>";
}

// Información del sistema
echo "<h2>Información del Sistema</h2>\n";
echo "<ul>";
echo "<li><strong>Versión de PHP:</strong> " . phpversion() . "</li>";
echo "<li><strong>Sistema Operativo:</strong> " . php_uname('s') . " " . php_uname('r') . "</li>";
echo "<li><strong>Servidor Web:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Desconocido') . "</li>";
echo "<li><strong>Directorio del proyecto:</strong> " . __DIR__ . "</li>";
echo "<li><strong>URL del proyecto:</strong> " . (isset($_SERVER['HTTP_HOST']) ? 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) : 'Desconocido') . "</li>";
echo "</ul>";

echo "<p><strong>Nota:</strong> Elimine este archivo después de la verificación por motivos de seguridad.</p>";
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 1000px;
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

