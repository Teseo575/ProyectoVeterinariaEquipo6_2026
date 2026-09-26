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
    body { min-height: 100vh; background: #f4f7f4; color: #263a32; font-family: "Segoe UI", Arial, sans-serif; }
    .barra { background: #fff; border-bottom: 1px solid #e3eae5; }
    .contenido { max-width: 760px; }
    .bienvenida { border: 1px solid #e3eae5; border-radius: 10px; background: #fff; }
    .btn-animalada { border: 0; background: #245e4b; color: #fff; font-weight: 700; }
    .btn-animalada:hover { background: #194a39; color: #fff; }
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
