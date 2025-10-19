# Manual de Usuario - CCI Surveys

## Introducción

CCI Surveys es un sistema completo de gestión de encuestas que permite crear, administrar y analizar encuestas de manera eficiente. Este manual te guiará a través de todas las funcionalidades del sistema.

## Acceso al Sistema

### URL de Acceso
- **Desarrollo:** `http://localhost/cci-surveys`
- **Producción:** `https://tu-dominio.com`

### Credenciales por Defecto
- **Usuario:** `admin`
- **Contraseña:** `admin123`

⚠️ **Importante:** Cambiar estas credenciales inmediatamente después del primer acceso.

## Tipos de Usuario

### Administrador
Tiene acceso completo al sistema:
- Gestión de encuestas y poblaciones
- Administración de usuarios
- Acceso a reportes y estadísticas
- Configuración del sistema

### Encuestador
Acceso limitado para recolección de datos:
- Formulario de encuestas
- Visualización de sus propias respuestas
- Dashboard personal

## Panel de Administración

### Dashboard Principal

Al acceder como administrador, verás el dashboard principal con:

#### Métricas Principales
- **Total Respuestas:** Número total de encuestas completadas
- **Hoy:** Respuestas del día actual
- **Esta Semana:** Respuestas de la semana
- **Usuarios Activos:** Número de usuarios activos

#### Gráficos Interactivos
- **Respuestas por Población:** Gráfico de barras
- **Distribución por Edad:** Gráfico circular
- **Distribución por Género:** Gráfico de pastel

#### Respuestas Recientes
Lista de las últimas 10 respuestas con:
- Datos del encuestado
- Encuestador responsable
- Población
- Fecha de envío

### Gestión de Encuestas

#### Crear Nueva Encuesta

1. **Acceder al módulo:**
   - Ir a "Encuestas" en el menú lateral
   - Hacer clic en "Nueva Encuesta"

2. **Datos básicos:**
   - **Nombre:** Título de la encuesta
   - **Descripción:** Descripción detallada del propósito

3. **Configurar preguntas:**
   - Hacer clic en "Agregar Pregunta"
   - Escribir el texto de la pregunta
   - Agregar opciones de respuesta
   - Definir el orden de la pregunta

4. **Asociar poblaciones:**
   - Seleccionar las poblaciones que aplican
   - Guardar configuración

#### Editar Encuesta Existente

1. Seleccionar la encuesta de la lista
2. Hacer clic en "Editar"
3. Modificar datos necesarios
4. Guardar cambios

#### Desactivar Encuesta

1. Seleccionar la encuesta
2. Hacer clic en "Desactivar"
3. Confirmar la acción

### Gestión de Poblaciones

#### Crear Nueva Población

1. **Acceder al módulo:**
   - Ir a "Poblaciones" en el menú lateral
   - Hacer clic en "Nueva Población"

2. **Datos requeridos:**
   - **Nombre:** Nombre del barrio/población
   - **Descripción:** Descripción del sector

3. **Guardar:** Hacer clic en "Guardar"

#### Editar Población

1. Seleccionar la población de la lista
2. Hacer clic en "Editar"
3. Modificar información
4. Guardar cambios

### Gestión de Usuarios

#### Crear Nuevo Usuario

1. **Acceder al módulo:**
   - Ir a "Usuarios" en el menú lateral
   - Hacer clic en "Nuevo Usuario"

2. **Datos del usuario:**
   - **Usuario:** Nombre de usuario único
   - **Email:** Correo electrónico
   - **Contraseña:** Contraseña segura
   - **Nombres:** Nombre completo
   - **Apellidos:** Apellidos
   - **Tipo de documento:** CC, TI, CE, etc.
   - **Número de documento:** Número de identificación
   - **Rol:** Administrador o Encuestador

3. **Guardar:** Hacer clic en "Guardar"

#### Editar Usuario

1. Seleccionar el usuario de la lista
2. Hacer clic en "Editar"
3. Modificar datos necesarios
4. Guardar cambios

#### Cambiar Estado de Usuario

- **Activar:** Hacer clic en el botón "Activar"
- **Desactivar:** Hacer clic en el botón "Desactivar"

### Reportes y Estadísticas

#### Acceder a Reportes

1. Ir a "Reportes" en el menú lateral
2. El sistema mostrará estadísticas generales

#### Filtros Disponibles

- **Encuesta:** Filtrar por encuesta específica
- **Población:** Filtrar por población/barrio
- **Grupo de Edad:** Joven, Adulto, Adulto Mayor, Otro
- **Género:** Mujer, Hombre
- **Rango de Fechas:** Desde y hasta fechas específicas

#### Aplicar Filtros

1. Seleccionar criterios de filtrado
2. Hacer clic en "Aplicar Filtros"
3. Los gráficos y estadísticas se actualizarán automáticamente

#### Exportar Reportes

1. Configurar filtros deseados
2. Hacer clic en "Exportar Reporte"
3. Seleccionar formato (Excel, PDF, CSV)
4. Descargar archivo

### Visualización de Respuestas

#### Lista de Respuestas

1. Ir a "Respuestas" en el menú lateral
2. Ver lista de todas las respuestas
3. Usar filtros para encontrar respuestas específicas

#### Detalles de Respuesta

1. Hacer clic en el ícono de "Ver" junto a cualquier respuesta
2. Se abrirá un modal con:
   - Datos del encuestado
   - Datos del encuestador
   - Información general
   - Respuestas a todas las preguntas

## Panel de Encuestador

### Dashboard de Encuestador

#### Métricas Personales
- **Total Encuestas:** Número de encuestas completadas por el encuestador
- **Hoy:** Encuestas del día actual
- **Semana Actual:** Encuestas de la semana

#### Acciones Rápidas
- **Nueva Encuesta:** Acceso directo al formulario
- **Mis Encuestas:** Ver historial de respuestas

### Formulario de Encuesta

#### Acceder al Formulario

1. Hacer clic en "Nueva Encuesta" desde el dashboard
2. O ir a "Encuesta" en el menú lateral

#### Completar Formulario

1. **Seleccionar Encuesta:**
   - Elegir el tipo de encuesta de la lista
   - Seleccionar la población correspondiente

2. **Datos del Encuestado:**
   - **Nombres y Apellidos:** (Obligatorio)
   - **Teléfono:** (Opcional)
   - **Grupo de Edad:** Seleccionar entre las opciones (Obligatorio)
   - **Dirección:** Dirección completa (Obligatorio)
   - **Sexo:** Mujer u Hombre (Obligatorio)
   - **Tipo y Número de Documento:** (Opcional)

3. **Datos del Encuestador:**
   - Se llenan automáticamente con los datos del usuario logueado
   - Verificar que el número de documento sea correcto

4. **Responder Preguntas:**
   - El sistema cargará automáticamente las preguntas de la encuesta seleccionada
   - Responder todas las preguntas (todas son obligatorias)
   - Solo se puede seleccionar una opción por pregunta

5. **Enviar Encuesta:**
   - Revisar todos los datos
   - Hacer clic en "Enviar Encuesta"
   - Confirmar el envío

#### Validaciones del Formulario

- Todos los campos obligatorios deben completarse
- Todas las preguntas de la encuesta deben responderse
- El sistema validará que los datos sean consistentes

### Mis Encuestas

#### Ver Historial

1. Ir a "Mis Encuestas" en el menú lateral
2. Ver lista de todas las encuestas enviadas
3. Filtrar por fecha o población

#### Detalles de Encuesta

1. Hacer clic en "Ver" junto a cualquier encuesta
2. Ver detalles completos de la respuesta
3. No se pueden editar encuestas ya enviadas

## Configuración Personal

### Mi Perfil

#### Acceder al Perfil

1. Hacer clic en el avatar del usuario (esquina superior derecha)
2. Seleccionar "Mi Perfil"

#### Editar Información

1. **Datos Personales:**
   - Modificar nombres y apellidos
   - Actualizar email
   - Cambiar tipo y número de documento

2. **Cambiar Contraseña:**
   - Introducir contraseña actual
   - Ingresar nueva contraseña
   - Confirmar nueva contraseña
   - Guardar cambios

### Configuración del Sistema

#### Acceder a Configuración

1. Hacer clic en el avatar del usuario
2. Seleccionar "Configuración"

#### Opciones Disponibles

- **Preferencias de Idioma:** Español (por defecto)
- **Zona Horaria:** Configurar según ubicación
- **Notificaciones:** Activar/desactivar notificaciones

## Mejores Prácticas

### Para Administradores

#### Gestión de Encuestas
- Crear preguntas claras y específicas
- Usar opciones de respuesta balanceadas
- Probar encuestas antes de activarlas
- Mantener encuestas organizadas por categorías

#### Gestión de Usuarios
- Asignar roles apropiados
- Mantener credenciales seguras
- Revisar actividad de usuarios regularmente
- Desactivar usuarios inactivos

#### Análisis de Datos
- Revisar reportes regularmente
- Usar filtros para análisis específicos
- Exportar datos para análisis externo
- Mantener respaldos de información

### Para Encuestadores

#### Recolección de Datos
- Verificar información antes de enviar
- Mantener confidencialidad de datos
- Completar encuestas de manera honesta
- Reportar problemas técnicos

#### Comunicación
- Mantener comunicación clara con encuestados
- Explicar el propósito de la encuesta
- Respetar la privacidad de los participantes
- Seguir protocolos de recolección

## Solución de Problemas

### Problemas Comunes

#### No puedo iniciar sesión
1. Verificar usuario y contraseña
2. Verificar que la cuenta esté activa
3. Contactar al administrador si persiste el problema

#### El formulario no carga las preguntas
1. Verificar conexión a internet
2. Recargar la página
3. Verificar que se haya seleccionado encuesta y población
4. Contactar soporte técnico

#### Error al enviar encuesta
1. Verificar que todos los campos obligatorios estén completos
2. Verificar que todas las preguntas estén respondidas
3. Intentar enviar nuevamente
4. Contactar soporte si persiste el error

#### No puedo ver reportes
1. Verificar permisos de usuario
2. Verificar que existan datos para mostrar
3. Contactar al administrador

### Contactar Soporte

Para problemas técnicos o consultas:
- **Email:** soporte@cci-surveys.com
- **Teléfono:** [Número de soporte]
- **Horario:** Lunes a Viernes, 8:00 AM - 6:00 PM

## Glosario

### Términos Técnicos

- **Encuesta:** Conjunto de preguntas sobre un tema específico
- **Población:** Barrio, sector o zona geográfica específica
- **Encuestador:** Persona responsable de recolectar datos
- **Encuestado:** Persona que responde la encuesta
- **Dashboard:** Panel de control con estadísticas y métricas
- **CSRF Token:** Token de seguridad para formularios
- **Responsive:** Diseño que se adapta a diferentes dispositivos

### Términos del Sistema

- **Rol:** Tipo de usuario (Administrador o Encuestador)
- **Estado:** Activo o Inactivo (para usuarios y encuestas)
- **Filtros:** Criterios para limitar la visualización de datos
- **Exportar:** Descargar datos en formato Excel, PDF o CSV
- **Modal:** Ventana emergente para mostrar información detallada

## Actualizaciones

### Notificaciones de Actualización

El sistema notificará automáticamente sobre:
- Nuevas versiones disponibles
- Nuevas funcionalidades
- Correcciones de errores
- Mejoras de seguridad

### Proceso de Actualización

1. **Notificación:** El sistema mostrará una notificación
2. **Respaldo:** Se recomienda respaldar datos importantes
3. **Actualización:** El administrador ejecutará la actualización
4. **Verificación:** Se verificará el funcionamiento correcto

---

**CCI Surveys** - Manual de Usuario v1.0

Para más información, consultar la documentación técnica o contactar soporte.
