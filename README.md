GestorHotelero BookTech


Aplicación web sencilla para gestionar habitaciones y reservas de un hotel.
Proyecto en PHP con Twig, usando un MVC simple (index.php como front controller), htaccess sesiones y roles.
La base de datos MySQL se precarga automáticamente con docker/init/hotel.sql.

Qué te vas a encontrar.
- Login y registro (el registro solo crea un cliente y no inicia sesión automáticamente)
- Registro si va bien redirige a Login
- Roles: cliente y admin
- Dentro de admin hay 2 niveles: básico y super (no se crean desde la aplicación, ya vienen en la base de datos)
- El cliente puede ver habitaciones y crear reservas con las disponibles.
- El admin puede ver a todos los clientes, todas las reservas, aceptar reservas y crear habitaciones.
- Si es el superadmin tiene algunas funcionalidades extras.
- Los admin pueden ver todos los clientes de la base de datos.



Cómo funciona una reserva (resumen)
- El cliente puede solicitar una reserva desde la lista de habitaciones
- La fecha de entrada es a partir del día siguiente
- La reserva se crea como “solicitada”
- Un admin debe aceptar la reserva para que pase a “aceptada”
- Una habitación deja de aparecer como disponible para clientes cuando tiene una reserva “aceptada”
- El cliente puede cancelar su reserva si está en “solicitada” o “aceptada”
- El superAdmin puede cancelar y finalizar reservas (el admin básico solo acepta)
- A cada cliente les aparecen sus reservas (en todos los estados)

Habitaciones desde admin
- Puede crear una nueva habitación eligiendo el tipo. 
- En la misma vista en la que se te ofrece crear una habitacion, tienes todas las que hay en ese momento
- Si además eres superAdmin, puedes eliminar habitaciones, y se eliminan todas sus reservas asociadas.


Usuarios de prueba (precargados en hotel.sql)

Aquí te doy sus contraseñas (están hasheadas también en la base de datos)
Admins
- Antonio (admin básico): antonio@hotel.com / admin123 (puede aceptar)
- David (admin super): david@hotel.com / admin123 (puede aceptar, cancelar y finalizar)

Clientes
- cliente1@hotel.com / cliente123
- cliente2@hotel.com / cliente123
- cliente3@hotel.com / cliente123


Explicación de las reservas extra sobre "La fecha de entrada es a partir del día siguiente" :

La fecha de entrada a la habitación cuando el cliente quiere hacer la reserva es fija,
solo se puede cambiar la de salida. Esto se hizo por varias razones:

- Quería que el admin tuviera alguna funcionalidad. (Aceptar,cancelar,finalizar), y para eso no se podía
gestionar sola.

- En un principio no tuve en cuenta lo difícil que resultarían esas comprobaciones de fechas para 
  saber si una habitación está disponible o no segun las reservas de cada cliente. 


Aquí tienes enlaces útiles:

- Despliegue con Railway: https://davidbellon-php-production.up.railway.app
- Drive: https://drive.google.com/drive/folders/1enD5_G6wgNkEmlZL0Ln-NDmclhRh8E8t?usp=drive_link

En el drive encontrarás un tutorial en vídeo y el modelo entidad-relación de la base de datos.
Según tengo entendido, Railway tiene un tiempo de inactividad si no se usa la app en un tiempo,
así que puede que la primera vez que entres tarde un poco en cargar. Pero funciona perfectamente.

Si por lo que sea sigue fallando, aquí te dejo de manera sencilla 
cómo hacerlo en local.

Cómo desplegar con Docker
1) Descargar el proyecto
2) En la carpeta donde está docker-compose.yml ejecutar: docker compose up --build
3) Esto ya te carga la base de datos de manera automática.

URLs
- App: http://localhost:8080
- phpMyAdmin: http://localhost:8888


Cómo usar la app (rápido)
- Entrar en http://localhost:8080
- Probar con un cliente o admin de prueba que tienes.
