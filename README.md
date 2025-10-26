# Sistema de Control de Asistencia de Personal

Sistema web desarrollado en PHP, JavaScript y CSS para el control y gestión de asistencia de personal de una empresa.

## Características

- **Inicio de sesión seguro**: Autenticación con contraseñas encriptadas usando bcrypt
- **Control de asistencia**: Registro de entradas y salidas del personal
- **Panel de control (Dashboard)**: Visualización de estadísticas y registros recientes
- **Gestión de sesiones**: Control de timeout y actividad de usuarios
- **Diseño responsive**: Interfaz moderna y adaptable a diferentes dispositivos
- **Registro de actividad**: Log de acciones de usuarios para auditoría

## Estructura del Proyecto

```
DPSI/
├── config/
│   └── config.php              # Configuración de la base de datos y sistema
├── css/
│   └── styles.css              # Estilos compartidos (colores, tipografía, botones)
├── database/
│   └── schema.sql              # Esquema de la base de datos
├── includes/
│   ├── auth.php                # Funciones de autenticación y seguridad
│   └── database.php            # Clase de conexión a la base de datos
├── js/
│   ├── dashboard.js            # Funcionalidad del panel de control
│   └── login.js                # Validación del formulario de login
├── pages/
│   ├── api_asistencia.php      # API para registrar asistencias
│   ├── dashboard.php           # Panel principal
│   ├── login.php               # Página de inicio de sesión
│   └── logout.php              # Cerrar sesión
└── index.php                   # Página principal (redirige al login)
```

## Requisitos

- PHP 7.4 o superior
- MySQL 5.7 o superior / MariaDB 10.2 o superior
- Servidor web (Apache, Nginx, etc.)
- Extensiones PHP requeridas:
  - mysqli
  - session
  - password_hash (incluido por defecto)

## Instalación

### 1. Configurar la base de datos

```bash
# Importar el esquema de la base de datos
mysql -u root -p < database/schema.sql
```

O manualmente desde phpMyAdmin o cualquier cliente MySQL:
- Abrir el archivo `database/schema.sql`
- Ejecutar el script SQL

### 2. Configurar la conexión

Editar el archivo `config/config.php` con las credenciales de tu base de datos:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_contraseña');
define('DB_NAME', 'sistema_asistencia');
```

### 3. Configurar el servidor web

**Apache (.htaccess recomendado)**:
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [L]
```

**Nginx**:
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### 4. Acceder al sistema

Abre tu navegador y accede a: `http://localhost/DPSI/`

## Credenciales por defecto

**Administrador**:
- Usuario: `admin`
- Contraseña: `admin123`

**Empleados de prueba**:
- Usuario: `empleado1`, `empleado2`, `empleado3`
- Contraseña: `123456`

⚠️ **IMPORTANTE**: Cambiar las contraseñas por defecto en producción.

## Uso del Sistema

### Iniciar Sesión
1. Acceder a la página de login
2. Ingresar usuario y contraseña
3. Click en "Iniciar Sesión"

### Registrar Asistencia
1. Desde el dashboard, seleccionar tipo de registro (Entrada/Salida)
2. Agregar notas opcionales
3. Click en "Registrar"

### Ver Estadísticas
El dashboard muestra:
- Total de empleados
- Empleados presentes hoy
- Registros del día
- Tasa de asistencia
- Tabla de asistencias recientes

## Características de Seguridad

- ✅ Contraseñas encriptadas con bcrypt (cost 12)
- ✅ Protección contra SQL Injection (prepared statements)
- ✅ Validación de entrada de datos
- ✅ Control de sesiones con timeout
- ✅ Sanitización de salida HTML (XSS protection)
- ✅ Registro de actividad de usuarios
- ✅ Token CSRF (funciones preparadas)
- ✅ Verificación de autenticación en cada página

## Tecnologías Utilizadas

- **PHP**: Estructura del backend y lógica de negocio
- **JavaScript**: Funcionalidades interactivas del frontend
- **CSS3**: Diseño visual con variables CSS para colores y estilos compartidos
- **MySQL**: Base de datos relacional
- **Patrón Singleton**: Para la conexión a la base de datos

## Personalización

### Colores y Estilos
Los colores y estilos se pueden personalizar editando las variables CSS en `css/styles.css`:

```css
:root {
    --primary-color: #2c3e50;
    --secondary-color: #3498db;
    --success-color: #27ae60;
    /* ... más colores */
}
```

### Timeout de Sesión
Modificar en `config/config.php`:

```php
define('SESSION_TIMEOUT', 3600); // En segundos
```

## Contribuir

Las contribuciones son bienvenidas. Por favor:
1. Fork del repositorio
2. Crear una rama para tu feature
3. Commit de tus cambios
4. Push a la rama
5. Crear un Pull Request

## Licencia

Este proyecto es de código abierto y está disponible bajo la licencia MIT.

## Soporte

Para reportar problemas o solicitar nuevas características, por favor abrir un issue en el repositorio.