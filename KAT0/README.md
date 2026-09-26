# KAT0 - Veterinaria Animalada

Aplicacion web sencilla para presentar la veterinaria, registrar personas e iniciar sesion en una cuenta de cliente.

## Inicio del sitio

Abre el proyecto desde un servidor que ejecute PHP, por ejemplo XAMPP: `http://localhost/KAT0/`. El servidor carga `index.php`, que incluye `menu.php` como portada publica. No abras los archivos PHP directamente desde el explorador de archivos ni uses una vista estatica como Live Server: PHP y las sesiones necesitan un servidor compatible.

`index.html` se conserva como formulario de creacion de cuenta. La portada enlaza a ese formulario con los botones para crear una cuenta.

## Archivos

- `index.php`: entrada predeterminada; carga `menu.php` usando una ruta basada en el directorio del proyecto.
- `menu.php`: portada publica. Contiene la navegacion, la presentacion, las secciones de cuidado y nosotros, y enlaces al registro y al inicio de sesion. Usa Bootstrap, Bootstrap Icons, Google Fonts e imagen de Unsplash mediante CDN, por lo que esos recursos requieren internet.
- `index.html`: formulario de registro. Envia los campos `Nombre`, `Apellido`, `CI`, `FechaNacimiento`, `Gmail` y `Contrasena` a `registro.php`.
- `registro.php`: acepta el formulario solo mediante POST, comprueba que la CI y el correo no esten registrados, crea un hash de la contrasena y agrega la persona a MySQL con consultas preparadas. Si la insercion funciona, dirige a `login.php`.
- `login.php`: valida el correo y la contrasena contra la tabla `personas`, crea la sesion y dirige a `usuario.php`. Verifica hashes con `password_verify`. Para cuentas antiguas que guardaban la contrasena sin hash, actualiza el valor a un hash al iniciar sesion correctamente.
- `auth.php`: inicia la sesion cuando hace falta y ofrece `exigirSesion()`, que devuelve al login si alguien intenta abrir una pagina privada sin identificarse.
- `usuario.php`: pagina privada de bienvenida para una persona que inicio sesion.
- `cerrar_sesion.php`: vacia y destruye la sesion, y vuelve a la portada.
- `conexion.php`: crea la conexion MySQL con `mysqli` y configura la codificacion `utf8mb4`.
- `KAT0 (Principal).png`: logo utilizado en las paginas.

## Recorridos principales

### Crear una cuenta

1. La persona abre `index.html` desde el enlace de registro de la portada.
2. El navegador envia el formulario por POST a `registro.php`.
3. El servidor revisa duplicados, genera un hash de la contrasena con `password_hash` y guarda los datos mediante una consulta preparada.
4. Al completarse el registro, la persona inicia sesion desde `login.php`.

### Iniciar y cerrar sesion

`login.php` compara el correo y la contrasena y guarda en la sesion `autenticado`, `idPersona`, `Nombre` y `Gmail`. Si son correctos, envia a `usuario.php`. Las paginas privadas llaman a `exigirSesion()` antes de mostrar su contenido. Cerrar sesion se hace en `cerrar_sesion.php`.

Los enlaces HTML solo sirven para navegar; no otorgan permisos. Cada pagina privada debe comprobar en PHP que la sesion este iniciada.

## Base de datos

`conexion.php` espera una base de datos MySQL llamada `kat0` y usa la configuracion local habitual de XAMPP (`localhost`, usuario `root` y contrasena vacia). La tabla `personas` debe contener al menos `id`, `Nombre`, `Apellido`, `CI`, `FechaNacimiento`, `Gmail` y `Contrasena`. El codigo de inicio de sesion ya no consulta la columna `Rol`; si esa columna existe en una base de datos anterior, puede quedarse como esta.

## Nota de seguridad

El inicio de sesion siempre requiere el correo y la contrasena. Las credenciales de MySQL estan escritas directamente en `conexion.php`; para un entorno publicado, usa variables de entorno y credenciales limitadas.

La contrasena de las cuentas nuevas se guarda con `password_hash`; nunca se debe guardar texto plano. Las consultas que reciben datos del usuario usan parametros preparados. Los valores mostrados en las paginas deben seguir escapandose con `htmlspecialchars`.

## Recursos y requisitos

- PHP con las extensiones `mysqli` y soporte de sesiones.
- MySQL o MariaDB con la base `kat0` y la tabla `personas` preparada.
- Apache/PHP, como XAMPP, o un servidor equivalente que procese archivos `.php`.
- Conexion a internet para cargar Bootstrap, Bootstrap Icons, Google Fonts y la imagen remota de la portada.

El formulario antiguo de registro referencia `Veterinaria.jpg`; ese archivo no aparece actualmente en el proyecto. Si no se agrega, la zona fotografica de ese formulario quedara sin esa imagen.