# Gestor Hotelero · BookTech

Aplicación web para gestionar habitaciones, clientes y reservas de un hotel, desarrollada con **PHP, Twig y MySQL**. Incluye un sistema de roles que diferencia las operaciones disponibles para clientes, administradores y superadministradores.

El proyecto puede ejecutarse en local mediante Docker. **Actualmente no dispone de una demo pública alojada.**

## Funcionalidades

### Clientes
- Registro e inicio de sesión.
- Consulta de habitaciones disponibles.
- Solicitud de reservas.
- Consulta del historial de reservas y sus estados.
- Cancelación de reservas solicitadas o aceptadas.

### Administración
- Consulta de clientes y reservas.
- Aceptación de reservas.
- Creación de habitaciones.
- Operaciones adicionales para el superadministrador: cancelación y finalización de reservas, y eliminación de habitaciones.

## Gestión de reservas

Las reservas siguen un flujo de estados:

1. El cliente solicita una reserva, con entrada a partir del día siguiente.
2. La reserva queda en estado **solicitada**.
3. Un administrador puede aceptarla.
4. El cliente puede cancelar una reserva solicitada o aceptada.
5. El superadministrador puede cancelar o finalizar reservas.

Una habitación deja de mostrarse como disponible para clientes cuando tiene una reserva aceptada. Al eliminar una habitación desde la cuenta de superadministrador, también se eliminan sus reservas asociadas.

**Nota sobre las fechas:** en el formulario de reserva, la fecha de entrada está fijada y el cliente puede modificar la fecha de salida. Es una limitación de la implementación actual.

## Tecnologías

- PHP 8.2 y Apache
- Twig
- MySQL 8
- Docker y Docker Compose
- phpMyAdmin

La aplicación utiliza una estructura MVC sencilla, con `index.php` como punto de entrada, sesiones y control de acceso por roles.

## Ejecutar el proyecto en local

### Requisitos

- Git
- Docker Desktop, o Docker Engine con Docker Compose

### Instalación

Clona el repositorio:

```bash
git clone https://github.com/DDDBBBPPP/DavidBellon-php.git
cd DavidBellon-php
```

Desde la carpeta que contiene `docker-compose.yml`, construye y arranca los servicios:

```bash
docker compose up --build -d
```

Docker prepara el entorno de PHP, instala las dependencias de Composer y arranca MySQL. La base de datos se inicializa con los datos de ejemplo incluidos en `docker/init/hotel.sql` cuando el volumen de MySQL está vacío.

### Acceso

| Servicio | Dirección |
| --- | --- |
| Aplicación | http://localhost:8080 |
| phpMyAdmin | http://localhost:8888 |

Para detener los contenedores:

```bash
docker compose down
```

Este comando **no elimina el volumen de MySQL**, por lo que los datos se conservan entre arranques.

## Cuentas de demostración

La base de datos inicial incluye cuentas para probar los distintos roles:

| Rol | Correo | Contraseña |
| --- | --- | --- |
| Administrador | `antonio@hotel.com` | `admin123` |
| Superadministrador | `david@hotel.com` | `admin123` |
| Cliente | `cliente1@hotel.com` | `cliente123` |

Estas credenciales son **exclusivamente para el entorno de demostración local**. No deben utilizarse en un despliegue de producción.

## Alcance del proyecto

BookTech es un proyecto de demostración funcional, no un sistema preparado para gestionar un hotel real. Su configuración de Docker y sus credenciales de ejemplo están orientadas al desarrollo local.

El código fuente permite explorar la gestión de habitaciones y reservas, las diferencias entre roles y la integración de PHP con MySQL y Twig.