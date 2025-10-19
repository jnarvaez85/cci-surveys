<?php
/**
 * Script para cambiar entre modo desarrollo y producción
 */

$modo = $_GET['modo'] ?? 'desarrollo';

echo "<h1>Cambiar Modo - CCI Surveys</h1>\n";

if ($modo === 'produccion') {
    // Cambiar a modo producción
    if (file_exists(__DIR__ . '/.htaccess.production')) {
        if (copy(__DIR__ . '/.htaccess.production', __DIR__ . '/.htaccess')) {
            echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
            echo "<h3 style='color: #155724; margin-top: 0;'>✅ Modo Producción Activado</h3>";
            echo "<p style='color: #155724; margin-bottom: 0;'>El sistema está ahora en modo producción con máxima seguridad.</p>";
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
            echo "<h3 style='color: #721c24; margin-top: 0;'>❌ Error</h3>";
            echo "<p style='color: #721c24; margin-bottom: 0;'>No se pudo cambiar a modo producción.</p>";
            echo "</div>";
        }
    } else {
        echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
        echo "<h3 style='color: #856404; margin-top: 0;'>⚠️ Advertencia</h3>";
        echo "<p style='color: #856404; margin-bottom: 0;'>No se encontró archivo .htaccess.production. Usando configuración por defecto.</p>";
        echo "</div>";
    }
} else {
    // Cambiar a modo desarrollo
    if (file_exists(__DIR__ . '/.htaccess.development')) {
        if (copy(__DIR__ . '/.htaccess.development', __DIR__ . '/.htaccess')) {
            echo "<div style='background: #d1ecf1; border: 1px solid #bee5eb; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
            echo "<h3 style='color: #0c5460; margin-top: 0;'>✅ Modo Desarrollo Activado</h3>";
            echo "<p style='color: #0c5460; margin-bottom: 0;'>El sistema está ahora en modo desarrollo con configuración relajada.</p>";
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
            echo "<h3 style='color: #721c24; margin-top: 0;'>❌ Error</h3>";
            echo "<p style='color: #721c24; margin-bottom: 0;'>No se pudo cambiar a modo desarrollo.</p>";
            echo "</div>";
        }
    } else {
        echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
        echo "<h3 style='color: #721c24; margin-top: 0;'>❌ Error</h3>";
        echo "<p style='color: #721c24; margin-bottom: 0;'>No se encontró archivo .htaccess.development.</p>";
        echo "</div>";
    }
}

echo "<h3>Opciones:</h3>";
echo "<ul>";
echo "<li><a href='cambiar_modo.php?modo=desarrollo'>Cambiar a Modo Desarrollo</a></li>";
echo "<li><a href='cambiar_modo.php?modo=produccion'>Cambiar a Modo Producción</a></li>";
echo "<li><a href='auth/login.php'>Ir al Login</a></li>";
echo "</ul>";

echo "<h3>Diferencias entre modos:</h3>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Característica</th><th>Desarrollo</th><th>Producción</th></tr>";
echo "<tr><td>Errores PHP</td><td>Mostrados</td><td>Ocultos</td></tr>";
echo "<tr><td>Content Security Policy</td><td>Relajada</td><td>Estricta</td></tr>";
echo "<tr><td>Bootstrap CDN</td><td>Permitido</td><td>Permitido</td></tr>";
echo "<tr><td>Seguridad</td><td>Básica</td><td>Máxima</td></tr>";
echo "</table>";
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
table {
    margin: 20px 0;
}
th, td {
    padding: 8px;
    text-align: left;
}
th {
    background-color: #f2f2f2;
}
</style>

