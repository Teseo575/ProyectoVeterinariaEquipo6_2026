
## Inicio

Abre el proyecto desde XAMPP en `http://localhost/KAT0/`. El servidor carga `index.php`, que incluye `menu.php` como portada. No abras los archivos PHP desde el explorador ni uses Live Server: PHP, MySQL y las sesiones necesitan Apache.

Para iniciar desde cero, importa `BaseDeDatos1.0.sql` en phpMyAdmin con el nombre `bdd_equipo6`, revisa las credenciales locales en `conexion.php` e inicia Apache y MySQL.

## Convencion de nombres

Las variables propias del proyecto se nombran en castellano (`$conexion`, `$nombre`, `$mascota`). Los nombres que pertenecen a PHP, MySQL, Bootstrap o a la estructura oficial de la base se conservan como los define cada tecnologia: por ejemplo `$_POST`, `$_SESSION`, `bind_param`, las clases CSS de Bootstrap y columnas como `ID_Mascotas` o `Gmail`. Esos nombres son contratos del lenguaje, de la libreria o del esquema, no variables locales que podamos traducir sin romper el programa.

## Archivos

- `index.php`: entrada predeterminada; carga `menu.php` usando una ruta basada en el directorio del proyecto.
- `menu.php`: portada publica. Usa la sesion para mostrar el perfil del cliente y sus acciones cuando inicio sesion.
- `index.html`: formulario de registro. Envia `Nombre`, `Apellido`, `CI`, `Direccion`, `Departamento`, `Gmail` y `Contrasena` a `registro.php`.
- `registro.php`: valida duplicados y guarda la persona en `persona` y su rol en `cliente`, usando una transaccion y consultas preparadas.
- `login.php`: busca el correo en `persona`, compara directamente la contrasena y crea la sesion si coincide.
- `auth.php`: inicia la sesion cuando hace falta y ofrece `exigirSesion()`, que devuelve al login si alguien intenta abrir una pagina privada sin identificarse.
- `usuario.php`: pagina privada de bienvenida para una persona que inicio sesion.
- `mascota.php`: formulario privado para clientes; guarda la mascota con la CI de la sesion y los campos de la tabla `mascotas`.
- `cerrar_sesion.php`: vacia y destruye la sesion, y vuelve a la portada.
- `conexion.php`: crea la conexion MySQL con `mysqli` y configura la codificacion `utf8mb4`.
- `KAT0 (Principal).png`: logo utilizado en las paginas.

## Como se registra una persona

### Crear una cuenta

1. En la portada se pulsa **Crear cuenta**. Se abre el formulario HTML `index.html`.
2. Cada campo tiene un `name`, por ejemplo `name="Nombre"` o `name="CI"`. Al enviar el formulario, el navegador manda esos nombres y valores por `POST` a `registro.php`.
3. `registro.php` lee cada campo con `$_POST`, quita espacios externos con `trim` y convierte el Gmail a minusculas. Ejemplo ficticio: `Ana@Ejemplo.com` se procesa como `ana@ejemplo.com`.
4. Comprueba que los campos obligatorios tengan valor, que la CI tenga solo digitos y como maximo ocho caracteres, y que el correo tenga formato valido.
5. Consulta `persona` para comprobar si ya existe la CI o el Gmail. Si encuentra una fila, termina el proceso y no crea otra cuenta.
6. En esta etapa, `registro.php` guarda en `persona.Contraseña` el mismo texto que se recibio en el formulario. No se genera ni se compara un hash.
7. Se inicia una transaccion: primero se inserta la persona en `persona` y luego se inserta la misma CI en `cliente`. La CI relaciona ambas filas. Los `?` del SQL reciben los valores de `bind_param` en el mismo orden que las columnas.
8. Si ambas inserciones funcionan, `commit` las confirma y el navegador va a `login.php`. Si ocurre un error, `rollback` deshace la transaccion.

Una cuenta creada queda guardada en MariaDB aunque la persona cierre el navegador. Crear la cuenta no inicia sesion automaticamente: debe ingresar su Gmail y contrasena en `login.php`. El sistema no envia correo de confirmacion.

### Iniciar sesion y conservar al usuario

1. El formulario de `login.php` manda `Gmail` y `Contrasena` por `POST`.
2. El codigo busca ese Gmail en `persona`. Si no encuentra fila, o la contrasena no coincide exactamente con `persona.Contraseña`, muestra el mensaje de credenciales incorrectas.
3. Si coincide, PHP guarda en `$_SESSION` valores como `autenticado = true`, `idPersona = CI`, `Nombre` y `Gmail`. La sesion mantiene esos datos entre paginas mientras siga activa; la cuenta permanente sigue estando en las tablas `persona` y `cliente`.
4. `login.php` redirige a `menu.php`. El menu lee `$_SESSION["Nombre"]` para mostrar el perfil y ofrecer **Cerrar sesion** y **Registrar mascota**.
5. Al cerrar sesion, `cerrar_sesion.php` borra y destruye la sesion. Esto no borra la persona de la base: solo termina su acceso actual.

Ejemplo: una cuenta con CI ficticia `12345678` inicia sesion. La sesion guarda `idPersona = 12345678`; al navegar, `menu.php` puede mostrar su nombre sin volver a pedirlo a la base en cada pantalla.

Las paginas privadas llaman a `exigirSesion()` en `auth.php`. Esa funcion comprueba `$_SESSION["autenticado"]`; si no existe, redirige al login. Los enlaces solo permiten navegar y no crean la sesion.

## Como se registra una mascota

1. Un cliente conectado pulsa **Registrar mascota** en el perfil. El navegador abre `mascota.php`.
2. `mascota.php` carga `auth.php` y llama `exigirSesion()`. Luego obtiene la CI desde `$_SESSION["idPersona"]`; la CI no viene de un campo editable del formulario.
3. Comprueba que esa CI exista en `cliente`. Si no, no muestra el formulario.
4. El formulario manda `Nombre`, `Especie`, `Raza`, `Estado` y `Sexo` por `POST`. Ejemplo ficticio: `Milo`, `Perro`, `Labrador`, `Saludable`, `Macho`.
5. Antes del alta, busca una mascota de ese mismo dueño con igual nombre, especie y raza. Si ya existe, muestra un aviso y no inserta otra fila. Cambiar solo el estado o el sexo no crea una segunda mascota; otra CI sí puede registrar una mascota con esos mismos datos.
6. Si no hay duplicado, calcula `MAX(ID_Mascotas) + 1`, porque la columna oficial no usa `AUTO_INCREMENT`.
7. El `INSERT` guarda el ID, los cinco campos del formulario y la CI de la sesion en `mascotas`. Despues redirige a la misma pagina con el aviso de registro correcto.

La comprobacion de mascota duplicada se hace desde PHP y compara CI + nombre + especie + raza. Es una regla sencilla de esta aplicacion; no agrega indices ni modifica la estructura oficial de la base.

## Base de datos

`conexion.php` usa `bdd_equipo6` en `localhost` con la configuracion habitual de XAMPP (`root` sin contrasena). El flujo utiliza `persona`, `cliente` y `mascotas` del esquema oficial.

## Estado temporal de contrasenas

Por pedido del proyecto, las contrasenas nuevas se guardan y comparan como texto directo. Esto es solo para desarrollo local: no debe usarse con cuentas reales ni publicarse en internet. Las cuentas que fueron creadas antes con un hash no coincidiran con la comparacion directa; sus datos no se modificaron y deben actualizarse antes de volver a usarlas.

## Recursos y requisitos

- PHP con las extensiones `mysqli` y soporte de sesiones.
- MySQL o MariaDB con la base `bdd_equipo6` importada desde `BaseDeDatos1.0.sql`.
- Apache/PHP, como XAMPP, o un servidor equivalente que procese archivos `.php`.
- Conexion a internet para cargar Bootstrap, Bootstrap Icons, Google Fonts y la imagen remota de la portada.

## Seguridad y limites

- Las consultas que reciben datos del formulario usan parametros preparados.
- En esta etapa las contrasenas se guardan como texto directo, tal como solicito el proyecto.
- La CI propietaria de la mascota se toma de la sesion, no de un dato enviado por el navegador.
- El alta de mascotas esta limitada a personas relacionadas con `cliente`.
- La tabla oficial no impone unicidad sobre Gmail; el registro comprueba duplicados desde PHP.
- La mascota se considera repetida cuando coinciden CI del cliente, nombre, especie y raza.
- Esta version permite registrar mascotas, pero no incluye pantallas para listarlas, editarlas o eliminarlas.
- `conexion.php` contiene credenciales locales de XAMPP; no las uses tal cual en un servidor publico.