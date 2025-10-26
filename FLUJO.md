# Flujo de Navegación del Sistema

## Diagrama de Páginas

```
┌─────────────────────────────────────────────────┐
│                  index.php                      │
│          (Redirige a login.php)                 │
└──────────────────┬──────────────────────────────┘
                   │
                   ▼
┌─────────────────────────────────────────────────┐
│               login.php                          │
│  - Formulario de usuario/contraseña             │
│  - Validación JavaScript                        │
│  - Autenticación segura con bcrypt              │
└──────────────────┬──────────────────────────────┘
                   │ (login exitoso)
                   ▼
┌─────────────────────────────────────────────────┐
│              dashboard.php                       │
│  [Accesible por todos los usuarios]             │
│  - Estadísticas de asistencia                   │
│  - Registro de entrada/salida                   │
│  - Tabla de asistencias recientes               │
│  - Navegación según rol                         │
└──────────────┬────────────┬─────────┬───────────┘
               │            │         │
     (admin)   │            │         │  (todos)
               │            │         │
               ▼            ▼         ▼
    ┌──────────────┐  ┌──────────┐  ┌──────────┐
    │ empleados.php│  │reportes  │  │logout.php│
    │              │  │.php      │  │          │
    │ [ADMIN SOLO] │  │[ADMIN]   │  │          │
    └──────────────┘  └──────────┘  └────┬─────┘
                                          │
                                          ▼
                                    login.php
```

## Descripción de Páginas

### 1. **index.php**
- Página de entrada
- Redirige automáticamente a login.php
- No requiere contenido visual

### 2. **pages/login.php**
- **Acceso**: Público
- **Funcionalidad**:
  - Formulario de inicio de sesión
  - Validación en tiempo real (JavaScript)
  - Mensajes de error informativos
  - Redirige al dashboard si ya está autenticado
- **Archivos relacionados**:
  - `js/login.js` (validación)
  - `css/styles.css` (diseño)
  - `includes/auth.php` (autenticación)

### 3. **pages/dashboard.php**
- **Acceso**: Usuarios autenticados (empleados y admin)
- **Funcionalidad**:
  - Tarjetas con estadísticas:
    - Total de empleados
    - Presentes hoy
    - Registros del día
    - Tasa de asistencia
  - Formulario para registrar entrada/salida
  - Tabla de asistencias recientes (últimos 10)
  - Navegación diferenciada por rol
- **Archivos relacionados**:
  - `js/dashboard.js` (funcionalidad AJAX)
  - `pages/api_asistencia.php` (API backend)

### 4. **pages/empleados.php**
- **Acceso**: Solo administradores
- **Funcionalidad**:
  - Tabla de todos los empleados
  - Información: ID, nombre, usuario, email, teléfono, rol, estado
  - Indicadores visuales de estado (activo/inactivo)
  - Badges de rol (admin/empleado)
  - Base para edición (botones preparados)
- **Restricción**: Redirige a dashboard si no es admin

### 5. **pages/reportes.php**
- **Acceso**: Solo administradores
- **Funcionalidad**:
  - Filtros por fecha (inicio y fin)
  - Cálculo automático de días hábiles
  - Tabla con resumen por empleado:
    - Días asistidos
    - Total de registros
    - Tasa de asistencia (con código de colores)
    - Primera entrada y última salida
  - Función de impresión
  - Base para exportación a Excel
- **Restricción**: Redirige a dashboard si no es admin

### 6. **pages/logout.php**
- **Acceso**: Usuarios autenticados
- **Funcionalidad**:
  - Cierra la sesión actual
  - Destruye variables de sesión
  - Registra el logout en el log
  - Redirige a login.php

### 7. **pages/api_asistencia.php**
- **Tipo**: Endpoint API REST
- **Método**: POST
- **Funcionalidad**:
  - Registra entrada o salida
  - Valida que no haya entradas duplicadas
  - Valida que exista entrada para registrar salida
  - Retorna JSON con resultado
  - Actualiza estadísticas automáticamente

## Componentes Compartidos

### Backend (PHP)
- **includes/database.php**: Conexión a BD (Singleton)
- **includes/auth.php**: Funciones de autenticación y seguridad
- **config/config.php**: Configuración general

### Frontend
- **css/styles.css**: Estilos compartidos (colores, botones, formularios)
- **js/login.js**: Validación de formulario de login
- **js/dashboard.js**: Funcionalidad del dashboard y AJAX

## Flujo de Autenticación

```
Usuario ingresa credenciales
        ↓
Validación JavaScript
        ↓
POST a login.php
        ↓
Validación PHP (includes/auth.php)
        ↓
Verificación en BD (includes/database.php)
        ↓
password_verify() con bcrypt
        ↓
    ┌───┴───┐
    ↓       ↓
Éxito    Fallo
    ↓       ↓
Crear    Mensaje
Sesión   de error
    ↓       ↓
Redirigir  Mostrar
Dashboard  en login
```

## Flujo de Registro de Asistencia

```
Usuario en Dashboard
        ↓
Selecciona Entrada/Salida
        ↓
Click en "Registrar"
        ↓
JavaScript captura evento
        ↓
AJAX POST a api_asistencia.php
        ↓
Validación en backend
        ↓
    ┌───┴───┐
    ↓       ↓
Éxito    Error
    ↓       ↓
Insertar  Mensaje
en BD     de error
    ↓       ↓
JSON      JSON
success   error
    ↓       ↓
Mostrar   Mostrar
alerta    alerta
success   error
    ↓
Recargar página
(actualizar estadísticas)
```

## Roles y Permisos

| Página          | Empleado | Admin |
|-----------------|----------|-------|
| login.php       | ✅       | ✅    |
| dashboard.php   | ✅       | ✅    |
| empleados.php   | ❌       | ✅    |
| reportes.php    | ❌       | ✅    |
| logout.php      | ✅       | ✅    |

## Seguridad por Capa

### Capa de Presentación (Frontend)
- Validación de entrada en JavaScript
- Sanitización de salida con htmlspecialchars()
- HTTPS recomendado

### Capa de Aplicación (PHP)
- Verificación de autenticación en cada página
- Control de roles
- Validación de entrada en servidor
- Prepared statements (SQL Injection)
- Password hashing (bcrypt)
- Session timeout

### Capa de Datos (MySQL)
- Índices para optimización
- Integridad referencial
- Validación de tipos de datos
- UTF-8 encoding

## Base de Datos

### Tablas

1. **usuarios**
   - Almacena empleados y administradores
   - Contraseñas hasheadas
   - Estado activo/inactivo
   - Roles (admin/empleado)

2. **asistencias**
   - Registros de entrada/salida
   - Relación con usuarios
   - Timestamps automáticos
   - Notas opcionales

3. **actividad_usuarios**
   - Log de acciones
   - IP address
   - Descripción de actividad
   - Timestamps

## Próximos Desarrollos

1. **Gestión de empleados completa**
   - Agregar nuevos empleados
   - Editar información
   - Desactivar/activar empleados
   - Cambio de contraseñas

2. **Reportes avanzados**
   - Exportación a Excel/PDF
   - Gráficas de tendencias
   - Reportes personalizados

3. **Notificaciones**
   - Alertas por email
   - Recordatorios de registro
   - Notificaciones de ausencias

4. **Funcionalidades adicionales**
   - Justificación de ausencias
   - Geolocalización
   - App móvil
   - Reconocimiento facial
