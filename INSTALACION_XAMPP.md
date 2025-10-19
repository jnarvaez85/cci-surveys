# Guía de Instalación Rápida - CCI Surveys en XAMPP

## Pasos Rápidos para Instalación en XAMPP

### 1. Verificar XAMPP
- ✅ XAMPP instalado en `C:\xampp`
- ✅ Apache ejecutándose (puerto 80)
- ✅ MySQL ejecutándose (puerto 3306)
- ✅ phpMyAdmin accesible en `http://localhost/phpmyadmin`

### 2. Ubicar Proyecto
- ✅ Proyecto ubicado en `C:\xampp\htdocs\cci-surveys`
- ✅ Todos los archivos presentes

### 3. Ejecutar Instalación
1. Abrir navegador web
2. Ir a: `http://localhost/cci-surveys/install.php`
3. Esperar a que se complete la instalación automática
4. Verificar mensajes de éxito

### 4. Acceder al Sistema
- **URL:** `http://localhost/cci-surveys/`
- **Usuario:** admin
- **Contraseña:** admin123

## Configuración Automática

El sistema está configurado para funcionar con la configuración por defecto de XAMPP:

- **Base de datos:** MySQL en localhost:3306
- **Usuario:** root (sin contraseña)
- **Base de datos:** surveys (se crea automáticamente)

## Solución de Problemas Rápidos

### Si Apache no inicia:
1. Cerrar otros programas que usen puerto 80 (Skype, IIS, etc.)
2. Cambiar puerto en XAMPP Control Panel
3. Reiniciar Apache

### Si MySQL no inicia:
1. Verificar que puerto 3306 no esté ocupado
2. Reiniciar MySQL desde XAMPP Control Panel

### Si hay errores de permisos:
1. Ejecutar XAMPP Control Panel como administrador
2. Verificar permisos de la carpeta del proyecto

### Si el instalador falla:
1. Verificar que los archivos `database/schema.sql` y `database/sample_data.sql` existan
2. Verificar conexión a MySQL en phpMyAdmin
3. Revisar logs de error de Apache

## Estructura del Proyecto Después de la Instalación

```
C:\xampp\htdocs\cci-surveys\
├── admin/              # Panel de administración
├── api/                # APIs del sistema
├── auth/               # Autenticación
├── classes/            # Clases PHP
├── config/             # Configuraciones
├── database/           # Scripts de base de datos
├── includes/           # Archivos incluidos
├── surveyor/           # Panel de encuestadores
├── .htaccess          # Configuración de seguridad (creado automáticamente)
├── index.php          # Página principal
└── install.php        # Instalador (eliminar después de usar)
```

## Datos de Muestra Incluidos

Después de la instalación, el sistema incluye:

- **Usuario administrador:** admin / admin123
- **2 encuestadores de prueba:** encuestador1, encuestador2
- **5 poblaciones:** Olaya, Villa del Sol, Parque Heredia, Ternera, San Fernando
- **1 encuesta:** "SEGURIDAD DE LA POBLACIÓN" con 7 preguntas
- **Múltiples respuestas de muestra** para pruebas

## Próximos Pasos

1. **Cambiar contraseña del administrador**
2. **Crear usuarios encuestadores reales**
3. **Configurar poblaciones según necesidades**
4. **Eliminar archivo install.php por seguridad**
5. **Configurar respaldos automáticos**

## Soporte

Si encuentras problemas durante la instalación:

1. Verificar que XAMPP esté correctamente instalado
2. Revisar logs de error en XAMPP Control Panel
3. Verificar que PHP tenga las extensiones necesarias
4. Consultar la guía completa en `INSTALACION.md`

---

**CCI Surveys** - Instalación XAMPP v1.0
