<?php
// auth.php comprueba que haya una sesión iniciada antes de mostrar esta página.
require_once "auth.php";
exigirSesion();

// htmlspecialchars evita que un nombre se interprete como código HTML.
// Ejemplo: convierte < en texto visible en vez de permitir que el navegador lo ejecute.
$nombre = htmlspecialchars($_SESSION["Nombre"] ?? "Usuario", ENT_QUOTES, "UTF-8");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mi cuenta | Veterinaria Animalada</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
      --clr-veterinaria: #02A2EC;
      --clr-principal: #02A2EC;
      --clr-peligro: #ff7782;
      --clr-exito: #41f1b6;
      --clr-blanco: #fff;
      --clr-info-oscuro: #7d8da1;
      --clr-info-claro: #dce1eb;
      --clr-oscuro: #363949;
      --clr-advertencia: #ff4edc;
      --clr-claro: rgba(132, 139, 200, 0.18);
      --clr-variante-principal: #111e88;
      --clr-variante-oscura: #677483;
      --clr-fondo: #f6f6f9;
      --sombra-caja: 0 2rem 3rem var(--clr-claro);
    }
    body { min-height: 100vh; background: var(--clr-fondo); color: var(--clr-oscuro); font-family: "Segoe UI", Arial, sans-serif; }
    .barra { background: var(--clr-blanco); border-bottom: 1px solid var(--clr-info-claro); }
    .contenido { max-width: 760px; }
    .bienvenida { border: 1px solid var(--clr-info-claro); border-radius: 10px; background: var(--clr-blanco); box-shadow: var(--sombra-caja); }
    .btn-animalada { border: 0; background: var(--clr-principal); color: var(--clr-blanco); font-weight: 700; }
    .btn-animalada:hover { background: var(--clr-variante-principal); color: var(--clr-blanco); }
    .navbar-brand { color: var(--clr-oscuro); }
    .navbar-brand:hover { color: var(--clr-principal); }
  </style>
</head>
<body>
  <!-- Barra con un enlace a la portada y el botón para terminar la sesión. -->
  <nav class="navbar barra">
    <div class="container">
      <a class="navbar-brand fw-bold" href="menu.php">Veterinaria Animalada</a>
      <a class="btn btn-outline-secondary btn-sm" href="cerrar_sesion.php">Cerrar sesión</a>
    </div>
  </nav>
  <!-- Solo se muestra esta bienvenida después de pasar la comprobación de sesión. -->
  <main class="container contenido py-5">
    <section class="bienvenida p-4 p-md-5">
      <p class="text-success fw-semibold text-uppercase small mb-2">Área de clientes</p>
      <h1 class="h2 fw-bold">¡Hola, <?php echo $nombre; ?>!</h1>
      <p class="text-secondary mb-4">Iniciaste sesión correctamente. Desde aquí podés volver a conocer la veterinaria.</p>
      <a class="btn btn-animalada" href="menu.php">Ir al sitio de Animalada</a>
    </section>
  </main>
</body>
</html>
