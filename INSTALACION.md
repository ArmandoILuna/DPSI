# Guía Rápida de Instalación

## Pasos para instalar el Sistema de Asistencia

### 1. Requisitos previos
- Servidor web (Apache/Nginx) con PHP 7.4+
- MySQL 5.7+ o MariaDB 10.2+
- Extensiones PHP: mysqli, session

### 2. Instalación de la base de datos

**Opción A: Desde línea de comandos**
```bash
mysql -u root -p < database/schema.sql
```

**Opción B: Desde phpMyAdmin**
1. Accede a phpMyAdmin
2. Clic en "Importar"
3. Selecciona el archivo `database/schema.sql`
4. Clic en "Continuar"

### 3. Configurar credenciales de base de datos

Edita el archivo `config/config.php`:

```php
define('DB_HOST', 'localhost');     // Tu servidor MySQL
define('DB_USER', 'root');          // Tu usuario MySQL
define('DB_PASS', '');              // Tu contraseña MySQL
define('DB_NAME', 'sistema_asistencia');
```

### 4. Configurar permisos (Linux)

```bash
chmod 755 -R /ruta/al/proyecto
chmod 644 /ruta/al/proyecto/config/config.php
```

### 5. Acceder al sistema

Abre tu navegador y accede a:
```
http://localhost/DPSI/
```

### 6. Iniciar sesión

**Usuario administrador:**
- Usuario: `admin`
- Contraseña: `admin123`

**Usuarios de prueba:**
- Usuario: `empleado1`, `empleado2`, `empleado3`
- Contraseña: `123456`

⚠️ **IMPORTANTE:** Cambia estas contraseñas inmediatamente después del primer acceso.

## Solución de problemas comunes

### Error de conexión a la base de datos
- Verifica que MySQL esté ejecutándose
- Confirma las credenciales en `config/config.php`
- Asegúrate de que la base de datos `sistema_asistencia` exista

### Error 500 - Internal Server Error
- Verifica los permisos de archivos
- Revisa el log de errores del servidor: `/var/log/apache2/error.log`
- Verifica que PHP tenga la extensión mysqli habilitada

### La página no carga CSS/JS
- Verifica que las rutas en los archivos HTML sean correctas
- Asegúrate de que el servidor tenga permisos de lectura en carpetas `css/` y `js/`

### Sesión expira muy rápido
- Ajusta `SESSION_TIMEOUT` en `config/config.php`
- Verifica la configuración de `session.gc_maxlifetime` en php.ini

## Características de seguridad implementadas

✅ Contraseñas hasheadas con bcrypt (cost 12)
✅ Protección SQL Injection (prepared statements)
✅ Protección XSS (sanitización HTML)
✅ Control de sesiones con timeout
✅ Logging de actividad de usuarios
✅ Validación de entrada en cliente y servidor

## Próximos pasos

Después de la instalación, considera:
1. Cambiar todas las contraseñas por defecto
2. Agregar más empleados desde la interfaz de administración
3. Configurar backups automáticos de la base de datos
4. Implementar SSL/HTTPS para conexiones seguras
5. Revisar y ajustar los límites de timeout según necesidades

## Soporte

Para ayuda adicional, consulta el README.md o abre un issue en GitHub.
