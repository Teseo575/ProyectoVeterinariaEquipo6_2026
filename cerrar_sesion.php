<?php
// Abrimos la sesión para poder vaciar los datos de la persona conectada.
session_start();

// Borra los datos guardados, como el nombre y la marca de autenticación.
session_unset();

// Invalida la sesión; al volver a una página privada tendrá que iniciar sesión otra vez.
session_destroy();

// Después de salir, volvemos a la portada pública.
header("Location: menu.php");
exit();
